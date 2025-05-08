<?php
session_start();
require 'blog-dashboard/config/db_main.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Function to safely extract and decode URL
function extractDownloadUrl($url) {
    if (strpos($url, '/download?') !== false) {
        $query_part = substr($url, strpos($url, '/download?') + 10);
        return urldecode(urldecode($query_part));
    }
    return false;
}

// --- Capture & validate the redirect URL ---
if (isset($_GET['redirect'])) {
    try {
        $rawRedirect = urldecode($_GET['redirect']);
        
        // Check if it's a direct download link
        $downloadUrl = extractDownloadUrl($rawRedirect);
        if ($downloadUrl !== false) {
            $_SESSION['redirect_url'] = '/download?' . urlencode($downloadUrl);
        } 
        // Check if it's a normal redirect URL
        else {
            $parts = parse_url($rawRedirect);
            
            // Only allow forexfactory.cc domains
            if (isset($parts['host']) && 
                (strpos($parts['host'], 'forexfactory.cc') !== false || 
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


// --- Fetch country list for forms ---
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

        $json = file_get_contents(
          'https://restcountries.com/v3.1/all?fields=name,cca2,idd,flags'
        );
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
          ['code'=>'US','name'=>'United States','dial_code'=>'+1','flag'=>''],
          ['code'=>'GB','name'=>'United Kingdom','dial_code'=>'+44','flag'=>''],
          ['code'=>'IN','name'=>'India','dial_code'=>'+91','flag'=>''],
          ['code'=>'CA','name'=>'Canada','dial_code'=>'+1','flag'=>'🇨🇦'],
          ['code'=>'AU','name'=>'Australia','dial_code'=>'+61','flag'=>'🇦'],
        ];
    }
}

try {
    $countries = getCountries();
} catch (Exception $e) {
    $countries = [
        ['code'=>'US','name'=>'United States','dial_code'=>'+1','flag'=>'🇺'],
        ['code'=>'GB','name'=>'United Kingdom','dial_code'=>'+44','flag'=>''],
        ['code'=>'IN','name'=>'India','dial_code'=>'+91','flag'=>''],
        ['code'=>'CA','name'=>'Canada','dial_code'=>'+1','flag'=>'🇨'],
        ['code'=>'AU','name'=>'Australia','dial_code'=>'+61','flag'=>'🇦'],
    ];
    $_SESSION['error'] = "Warning: Using default country list due to: " . $e->getMessage();
}

// --- Sign‐up flow ---
if (isset($_POST['signup'])) {
    try {
        $name         = trim($_POST['name']);
        $email        = trim($_POST['email']);
        $cc           = $_POST['country_code'];
        $phone        = preg_replace('/\D/','',$_POST['phone_number']);
        $password     = $_POST['password'] ?? '';
        $country      = $_POST['country'];

        $fullPhone = preg_replace('/\D/','',$cc).preg_replace('/^0/','',$phone);
        $errors = [];
        if (!$name)                             $errors[] = 'Name is required';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                $errors[] = 'Valid email is required';
        if (!$phone || strlen($phone) < 6)      $errors[] = 'Valid phone is required';
        if ($password && strlen($password)<6)   $errors[] = 'Password min 6 chars';

        if (empty($errors)) {
            $chk = $conn->prepare("SELECT id FROM users WHERE email=? OR phone=?");
            if (!$chk) {
                handleDbError($conn, "Database preparation failed");
            }
            
            $chk->bind_param("ss",$email,$fullPhone);
            if (!$chk->execute()) {
                handleDbError($conn, "Database query failed");
            }
            
            $result = $chk->get_result();
            if (!$result) {
                handleDbError($conn, "Failed to get query result");
            }
            
            if ($result->num_rows) {
                $errors[] = 'Email or phone already registered';
            } else {
                $otp = str_pad(rand(0,9999),4,'0',STR_PAD_LEFT);
                $_SESSION['signup_data'] = [
                    'name'          => $name,
                    'email'         => $email,
                    'phone'         => $fullPhone,
                    'country'       => $country,
                    'password_hash' => $password ? password_hash($password,PASSWORD_DEFAULT) : null,
                    'otp_code'      => $otp,
                    'otp_expiry'    => time()+600,
                    'attempts'      => 0 // Track OTP attempts
                ];
                
                // Send OTP via WhatsApp
                $res = sendWhatsAppOTP($fullPhone,$otp);
                if ($res['status']==='success') {
                    $q = isset($_SESSION['redirect_url'])
                       ? '?redirect='.urlencode($_SESSION['redirect_url'])
                       : '';
                    header("Location: verify_signup_otp.php{$q}");
                    exit();
                } else {
                    $errors[] = 'OTP send failed, try again later';
                    error_log("WhatsApp OTP Error: ".print_r($res,true));
                    $_SESSION['error'] = "Failed to send OTP. Please try again.";
                }
            }
        }
    } catch (Exception $e) {
        $errors[] = "An error occurred during signup: " . $e->getMessage();
        error_log("Signup Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Sign Up Gateway - YoForex</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            margin-bottom: 20px;
        }

        .logo-img {
            width: 150px;
            height: auto;
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

        .login-link {
            margin-top: 20px;
            color: var(--text-light);
            font-size: 14px;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

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

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 22px;
            }
            
            .logo-img {
                width: 120px;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Display system-wide errors if any -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="system-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="logo">
            <img src="https://forexfactory.cc/images/newyoforexlogo.png" alt="Forexfactory Logo" class="logo-img">
        </div>
        
        <h2>Create Your Account</h2>
        <p>Get started with your free account and start using your software</p>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" 
                       placeholder="John Doe" required
                       value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" 
                       placeholder="your@email.com" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="country">Country</label>
                <select name="country" id="country" class="country-select" onchange="updateCountryCode()">
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= htmlspecialchars($country['code']) ?>" 
                                data-dial-code="<?= htmlspecialchars($country['dial_code']) ?>"
                                data-flag="<?= htmlspecialchars($country['flag']) ?>"
                                <?= (isset($_POST['country']) && $_POST['country'] === $country['code']) || $country['code'] === 'IN' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="signup-phone">WhatsApp Number</label>
                <div class="phone-input-container">
                    <div class="country-select">
                        <select name="country_code" id="signup-country" onchange="updatePhonePrefix('signup')">
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= htmlspecialchars($country['dial_code']) ?>" 
                                        data-flag="<?= htmlspecialchars($country['flag']) ?>"
                                        <?= (isset($_POST['country_code']) && $_POST['country_code'] === $country['dial_code']) || $country['code'] === 'IN' ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($country['dial_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="country-flag" id="signup-flag">
                            <?= $countries[array_search('IN', array_column($countries, 'code'))]['flag'] ?? '🇮' ?>
                        </span>
                    </div>
                    <input type="tel" id="signup-phone" name="phone_number" 
                           class="phone-input"
                           placeholder="9876543210" required
                           pattern="[0-9]{6,12}"
                           value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       placeholder="••• (set for email login)">
                <small style="color: var(--text-light);">If you set a password, you can also login with email</small>
            </div>
            
            <?php if (isset($errors) && isset($_POST['signup'])): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php foreach ($errors as $err): ?>
                        <div><?php echo htmlspecialchars($err); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" name="signup" class="btn">
                <i class="fas fa-user-plus"></i> Sign Up
            </button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login instead</a>
        </div>
    </div>

    <script>
        // Initialize country flags
        document.addEventListener('DOMContentLoaded', function() {
            updatePhonePrefix('signup');
        });

        function updatePhonePrefix(formType) {
            const countrySelect = document.getElementById(`${formType}-country`);
            const flagElement = document.getElementById(`${formType}-flag`);
            flagElement.textContent = countrySelect.options[countrySelect.selectedIndex].getAttribute('data-flag');
        }

        function updateCountryCode() {
            const countrySelect = document.getElementById('country');
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const dialCode = selectedOption.getAttribute('data-dial-code');
            const flag = selectedOption.getAttribute('data-flag');
            
            const countryCodeSelect = document.getElementById('signup-country');
            for (let i = 0; i < countryCodeSelect.options.length; i++) {
                if (countryCodeSelect.options[i].value === dialCode) {
                    countryCodeSelect.selectedIndex = i;
                    document.getElementById('signup-flag').textContent = flag;
                    break;
                }
            }
        }

        // Auto-format phone number inputs
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>