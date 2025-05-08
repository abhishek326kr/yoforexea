<?php
session_start();
require 'blog-dashboard/config/db_main.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Function to safely extract and decode URL
function extractDownloadUrl($url) {
    // Handle direct download URLs (/download?https...)
    if (strpos($url, '/download?') !== false) {
        $query_part = substr($url, strpos($url, '/download?') + 10);
        return urldecode(urldecode($query_part));
    }
    return false;
}

// --- 1) Capture & validate the redirect URL ---
if (isset($_GET['redirect'])) {
    try {
        $rawRedirect = urldecode($_GET['redirect']);
        
        // First check if it's a direct download link
        $downloadUrl = extractDownloadUrl($rawRedirect);
        if ($downloadUrl !== false) {
            $_SESSION['redirect_url'] = '/download?' . urlencode($downloadUrl);
        } 
        // If not, check if it's a normal redirect URL
        else {
            $parts = parse_url($rawRedirect);
            
            // Only allow forexfactory.cc domains
            if (isset($parts['host']) && 
                (strpos($parts['host'], 'mql5.software') !== false || 
                 strpos($parts['host'], 'localhost') !== false)) {
                $_SESSION['redirect_url'] = $rawRedirect;
            } else {
                $_SESSION['error'] = "Invalid redirect URL provided";
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error processing redirect URL";
    }
}

// --- 2) Fetch country list for forms ---
function getCountries() {
    $cacheFile = 'blog-dashboard/cache/countries.json';
    $cacheTime = 86400; // 24h

    try {
        if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $cachedData;
            }
        }

        $json = file_get_contents('https://restcountries.com/v3.1/all?fields=name,cca2,idd,flags');
        if ($json === false) {
            throw new Exception('Failed to fetch country data');
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid country data format');
        }
        
        if (empty($data)) {
            throw new Exception('Empty country list');
        }

        $list = [];
        foreach ($data as $c) {
            if (!isset($c['idd']['root'])) continue;
            $code = $c['idd']['root'];
            if (!empty($c['idd']['suffixes']) && count($c['idd']['suffixes']) === 1) {
                $code .= $c['idd']['suffixes'][0];
            }
            $list[] = [
                'code'      => $c['cca2'],
                'name'      => $c['name']['common'],
                'dial_code' => $code,
                'flag'      => $c['flags']['emoji'] ?? ''
            ];
        }
        usort($list, fn($a,$b) => strcmp($a['name'],$b['name']));
        
        $jsonResult = json_encode($list);
        if ($jsonResult === false) {
            throw new Exception('Failed to encode country data');
        }
        
        if (file_put_contents($cacheFile, $jsonResult) === false) {
            throw new Exception('Failed to cache country data');
        }
        
        return $list;
    } catch(Exception $e) {
        error_log("Country API: ".$e->getMessage());
        return [
          ['code'=>'US','name'=>'United States','dial_code'=>'+1','flag'=>'🇺🇸'],
          ['code'=>'GB','name'=>'United Kingdom','dial_code'=>'+44','flag'=>''],
          ['code'=>'IN','name'=>'India','dial_code'=>'+91','flag'=>'🇮🇳'],
          ['code'=>'CA','name'=>'Canada','dial_code'=>'+1','flag'=>''],
          ['code'=>'AU','name'=>'Australia','dial_code'=>'+61','flag'=>'🇦🇺'],
        ];
    }
}

$countries = getCountries();


// --- WhatsApp OTP login (keep redirect) ---
if (isset($_POST['send_otp'])) {
    try {
        $cc    = $_POST['country_code'];
        $phone = preg_replace('/\D/','',$_POST['phone_number']);
        $full  = preg_replace('/\D/','',$cc).preg_replace('/^0/','',$phone);

        if (!$phone || strlen($phone)<6) {
            $error = 'Valid phone is required';
        } else {
            $chk = $conn->prepare("SELECT id FROM users WHERE phone=?");
            if (!$chk) {
                handleDbError($conn, "Database preparation failed");
            }
            
            $chk->bind_param("s", $full);
            if (!$chk->execute()) {
                handleDbError($conn, "Database query failed");
            }
            
            $result = $chk->get_result();
            if (!$result) {
                handleDbError($conn, "Failed to get query result");
            }
            
            if (!$result->num_rows) {
                // User doesn't exist, redirect to signup page with phone number and redirect URL
                $_SESSION['signup_phone'] = $full;
                $_SESSION['signup_country_code'] = $cc;
                
                $redirectUrl = 'signup.php';
                if (isset($_SESSION['redirect_url'])) {
                    $redirectUrl .= '?redirect=' . urlencode($_SESSION['redirect_url']);
                }
                
                header("Location: " . $redirectUrl);
                exit();
            } else {
                $otp = str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT);
                $_SESSION['otp_code']     = $otp;
                $_SESSION['phone_number'] = $full;
                $_SESSION['otp_expiry']   = time()+600;

                $res = sendWhatsAppOTP($full, $otp);
                if ($res['status'] === 'success') {
                    $_SESSION['otp_sent'] = true;
                    $q = isset($_SESSION['redirect_url'])
                       ? '?redirect='.urlencode($_SESSION['redirect_url'])
                       : '';
                    header("Location: verify_otp.php{$q}");
                    exit();
                } else {
                    $error = 'OTP send failed, try again later';
                    error_log("WhatsApp OTP Error: ".print_r($res, true));
                    $_SESSION['error'] = "Failed to send OTP. Please try again.";
                }
            }
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
        error_log("OTP Login Error: " . $e->getMessage());
    }
}

// --- Email/password login ---
if (isset($_POST['email_login'])) {
    try {
        $email = trim($_POST['email']);
        $pass  = $_POST['password'] ?? '';

        if (!$email || !$pass) {
            $email_error = 'Email and password are required';
        } else {
            $stmt = $conn->prepare("SELECT id,name,password FROM users WHERE email=?");
            if (!$stmt) {
                throw new Exception("Database preparation failed");
            }
            
            $stmt->bind_param("s", $email);
            if (!$stmt->execute()) {
                throw new Exception("Database query failed");
            }
            
            $res = $stmt->get_result();
            if (!$res) {
                throw new Exception("Failed to get query result");
            }

            if (!$res->num_rows) {
                $email_error = 'Email not registered';
            } else {
                $u = $res->fetch_assoc();
                if (empty($u['password'])) {
                    $email_error = 'No password set, use WhatsApp login';
                } elseif (!password_verify($pass, $u['password'])) {
                    $email_error = 'Invalid password';
                } else {
                    // Login successful
                    $_SESSION['user_id']   = $u['id'];
                    $_SESSION['user_name'] = $u['name'];
                    $_SESSION['logged_in'] = true;

                    // Redirect back to downloadpage URL, or fallback
                    $go = $_SESSION['redirect_url'] ?? 'dashboard.php';
                    unset($_SESSION['redirect_url']);
                    header("Location: " . $go);
                    exit();
                }
            }
        }
    } catch (Exception $e) {
        $email_error = "An error occurred during login: " . $e->getMessage();
        error_log("Email Login Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login Gateway - YoForex</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico" />
    <style>
          :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --secondary: #075E54;
            --accent: #34B7F1;
            --text: #333333;
            --text-light: #666666;
            --light: #f5f5f5;
            --white: #ffffff;
            --error: #ff4757;
            --success: #2ed573;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-image: linear-gradient(135deg, rgba(37, 211, 102, 0.05) 0%, rgba(7, 94, 84, 0.05) 100%);
        }

        .auth-container {
            background-color: var(--white);
            width: 100%;
            max-width: 450px;
            padding: 40px 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .auth-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            z-index: -1;
        }

        .logo {
            color: var(--primary);
            font-size: 60px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.05);
        }

        h2 {
            color: var(--secondary);
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 24px;
        }

        p {
            color: var(--text-light);
            margin-bottom: 25px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
            font-size: 14px;
        }

        input, select {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
        }

        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.2);
        }

        .btn {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background: rgba(37, 211, 102, 0.1);
        }

        .error {
            color: var(--error);
            margin: 15px 0;
            font-size: 14px;
            padding: 12px;
            background: rgba(255, 71, 87, 0.1);
            border-radius: var(--border-radius);
            text-align: left;
            border-left: 4px solid var(--error);
        }

        .error div {
            margin: 5px 0;
        }

        .error i {
            margin-right: 8px;
        }

        .phone-input-container {
            display: flex;
            gap: 10px;
        }

        .country-select {
            position: relative;
            width: 120px;
        }

        .country-select select {
            width: 100%;
            padding: 14px 15px 14px 40px;
            appearance: none;
            cursor: pointer;
        }

        .country-flag {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .phone-input {
            flex: 1;
        }

        .toggle-login {
            margin-top: 20px;
            color: var(--text-light);
            font-size: 14px;
        }

        .toggle-login a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .toggle-login a:hover {
            text-decoration: underline;
        }

        .email-login-form {
            display: none;
            margin-top: 20px;
            animation: fadeIn 0.5s ease;
        }

        .email-login-form.active {
            display: block;
        }

        .signup-redirect {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .signup-redirect p {
            color: var(--text-light);
            margin-bottom: 15px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 22px;
            }
            
            .logo {
                font-size: 50px;
            }
            
            input, .btn, select {
                padding: 12px 15px;
            }
            
            .country-select {
                width: 100px;
            }
            
            .country-select select {
                padding-left: 35px;
                font-size: 14px;
            }
            
            .country-flag {
                left: 10px;
            }
        }

        /* Added styles for system error messages */
        .system-error {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            color: #d32f2f;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            text-align: left;
            font-size: 14px;
        }

        .system-error i {
            margin-right: 10px;
            color: #f44336;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="system-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="logo">
            <img src="https://forexfactory.cc/images/newyoforexlogo.png" alt="Forexfactory Logo" class="logo-img" style="width: 100px;">
        </div>
        
        <h2>Welcome Back</h2>
        <p>You must register an account or login to access your software.</p>
        
        <form method="POST" id="whatsapp-login-form">
            <div class="form-group">
                <label for="login-phone">Verify using Whatsapp</label>
                <div class="phone-input-container">
                    <div class="country-select">
                        <select name="country_code" id="login-country" onchange="updatePhonePrefix('login')">
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= htmlspecialchars($country['dial_code']) ?>" 
                                        data-flag="<?= htmlspecialchars($country['flag']) ?>"
                                        <?= $country['code'] === 'IN' ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($country['dial_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="country-flag" id="login-flag"><?= $countries[array_search('IN', array_column($countries, 'code'))]['flag'] ?? '' ?></span>
                    </div>
                    <input type="tel" id="login-phone" name="phone_number" 
                           class="phone-input"
                           placeholder="9876543210" required
                           pattern="[0-9]{6,12}">
                </div>
            </div>
            
            <?php if (isset($error) && isset($_POST['send_otp'])): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" name="send_otp" class="btn">
                <i class="fab fa-whatsapp"></i> Send Verification Code
            </button>
        </form>
        
        <div class="toggle-login">
            <a onclick="toggleEmailLogin()">Instead use email login</a>
        </div>
        
        <form method="POST" class="email-login-form" id="email-login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="•••••" required>
            </div>
            
            <?php if (isset($email_error) && isset($_POST['email_login'])): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($email_error); ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" name="email_login" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login with Email
            </button>
            
            <div class="toggle-login">
                <a onclick="toggleWhatsAppLogin()">Back to WhatsApp login</a>
            </div>
        </form>
        
        <div class="signup-redirect">
            <p>Don't have an account?</p>
            <a href="signup.php<?= isset($_SESSION['redirect_url']) ? '?redirect=' . urlencode($_SESSION['redirect_url']) : '' ?>" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Sign Up
            </a>
        </div>
    </div>

    <script>
        function updatePhonePrefix(formType) {
            const countrySelect = document.getElementById(`${formType}-country`);
            const flagElement = document.getElementById(`${formType}-flag`);
            flagElement.textContent = countrySelect.options[countrySelect.selectedIndex].getAttribute('data-flag');
        }

        function toggleEmailLogin() {
            document.getElementById('whatsapp-login-form').style.display = 'none';
            document.getElementById('email-login-form').classList.add('active');
            document.getElementById('email').focus();
        }
        
        function toggleWhatsAppLogin() {
            document.getElementById('whatsapp-login-form').style.display = 'block';
            document.getElementById('email-login-form').classList.remove('active');
            document.getElementById('login-phone').focus();
        }

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (this.querySelector('input[name="phone_number"]')) {
                    const countryCode = this.querySelector('select[name="country_code"]').value;
                    const phoneInput = this.querySelector('input[name="phone_number"]');
                    
                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'full_phone_number';
                    hiddenInput.value = countryCode.replace(/[^0-9]/g, '') + phoneInput.value.replace(/[^0-9]/g, '');
                    this.appendChild(hiddenInput);
                }
            });
        });
    </script>
</body>
</html>