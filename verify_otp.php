<?php
session_start();
require 'blog-dashboard/config/db_main.php';

// 1) Ensure user came via OTP login flow
if (empty($_SESSION['otp_sent'])) {
    header("Location: login.php");
    exit();
}

// 2) Capture & decode the redirect URL from GET, if present
if (isset($_GET['redirect'])) {
    // e.g. redirect=https%3A%2F%2Fforexfactory.cc%2Fdownload%3Fhttps%253A%252F%252Ft.me%252FYoForexAsia%252F
    $_SESSION['redirect_url'] = urldecode($_GET['redirect']);
}

// Initialize messages
$error   = '';
$success = '';

// 3) Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3a) User clicked Verify OTP”
    if (isset($_POST['verify_otp'])) {
        $user_otp = trim($_POST['otp']);

        if ($user_otp === '') {
            $error = "OTP is required";
        } elseif (empty($_SESSION['otp_code']) || empty($_SESSION['otp_expiry'])) {
            $error = "OTP session expired. Please request a new one.";
        } elseif (time() > $_SESSION['otp_expiry']) {
            $error = "OTP has expired. Please request a new one.";
        } elseif ($user_otp !== $_SESSION['otp_code']) {
            $error = "Invalid OTP. Please try again.";
        } else {
            // OTP valid → fetch user by phone
            $phone = $_SESSION['phone_number'];
            $stmt  = $conn->prepare("SELECT id, name FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows === 1) {
                $user = $res->fetch_assoc();
                // 3a-i) Log them in
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['logged_in'] = true;

                // 3a-ii) Clean up OTP data
                unset($_SESSION['otp_code'], $_SESSION['otp_expiry'], $_SESSION['phone_number'], $_SESSION['otp_sent']);

                // 3a-iii) Redirect to saved URL or to download.php
                $target = $_SESSION['redirect_url'] ?? 'dashboard.php';
                unset($_SESSION['redirect_url']);
                header("Location: " . $target);
                exit();
            } else {
                $error = "User not found. Please sign up first.";
            }
        }
    }

    // 3b) User clicked “Resend OTP”
    if (isset($_POST['resend_otp'])) {
        if (empty($_SESSION['phone_number'])) {
            $error = "Unable to resend OTP. Please log in again.";
        } else {
            $newOtp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $_SESSION['otp_code']   = $newOtp;
            $_SESSION['otp_expiry'] = time() + 600;
            $result = sendWhatsAppOTP($_SESSION['phone_number'], $newOtp);
            if ($result['status'] === 'success') {
                $success = "A new OTP has been sent to your WhatsApp.";
            } else {
                $error = "Failed to resend OTP. Please try again later.";
                error_log("WhatsApp API Error: " . print_r($result, true));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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

        .logo-img {
            height: 80px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .logo-img:hover {
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

        input {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
            text-align: center;
            letter-spacing: 10px;
            font-size: 24px;
            font-weight: bold;
        }

        input:focus {
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

        .success {
            color: var(--success);
            margin: 15px 0;
            font-size: 14px;
            padding: 12px;
            background: rgba(46, 213, 115, 0.1);
            border-radius: var(--border-radius);
            text-align: left;
            border-left: 4px solid var(--success);
        }

        .error i, .success i {
            margin-right: 8px;
        }

        .otp-info {
            margin: 20px 0;
            padding: 15px;
            background: rgba(7, 94, 84, 0.05);
            border-radius: var(--border-radius);
            font-size: 14px;
        }

        .otp-info i {
            color: var(--primary);
            margin-right: 8px;
        }

        .timer {
            color: var(--text-light);
            margin-top: 15px;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 22px;
            }
            
            .logo-img {
                height: 70px;
            }
            
            input, .btn {
                padding: 12px 15px;
            }
            
            input {
                font-size: 20px;
                letter-spacing: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="https://forexfactory.cc/images/newyoforexlogo.png" alt="Yoforexea Logo" class="logo-img">
        </div>
        
        <h2>Verify Your Number</h2>
        <p>Enter the 4-digit code sent to your WhatsApp number</p>
        
        <div class="otp-info">
            <i class="fas fa-mobile-alt"></i>
            We've sent an OTP to <?php echo isset($_SESSION['phone_number']) ? htmlspecialchars($_SESSION['phone_number']) : 'your number'; ?>
        </div>
        
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="otp">Verification Code</label>
                <input type="text" id="otp" name="otp" 
                       placeholder="----" required
                       maxlength="4" pattern="\d{4}"
                       inputmode="numeric"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <button type="submit" name="verify_otp" class="btn">
                <i class="fas fa-check"></i> Verify & Continue
            </button>
            
            <div class="timer" id="timer">
                Didn't receive code? You can request a new one in <span id="countdown">05:00</span>
            </div>
            
            <button type="submit" name="resend_otp" class="btn btn-secondary" id="resend-btn" disabled>
                <i class="fas fa-redo"></i> Resend OTP
            </button>
        </form>
    </div>

    <script>
        // Countdown timer for OTP resend
        document.addEventListener('DOMContentLoaded', function() {
            const expiryTime = <?php echo isset($_SESSION['otp_expiry']) ? $_SESSION['otp_expiry'] : time() + 300; ?>;
            const now = Math.floor(Date.now() / 1000);
            let remainingTime = Math.max(0, expiryTime - now);
            
            const timerElement = document.getElementById('countdown');
            const resendBtn = document.getElementById('resend-btn');
            const timerContainer = document.getElementById('timer');
            
            if (remainingTime <= 0) {
                enableResend();
                return;
            }
            
            // Format initial time
            const initialMinutes = Math.floor(remainingTime / 60);
            const initialSeconds = remainingTime % 60;
            timerElement.textContent = 
                `${initialMinutes.toString().padStart(2, '0')}:${initialSeconds.toString().padStart(2, '0')}`;
            
            const timer = setInterval(function() {
                remainingTime--;
                
                if (remainingTime <= 0) {
                    clearInterval(timer);
                    enableResend();
                    return;
                }
                
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;
                timerElement.textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
            
            function enableResend() {
                timerContainer.style.display = 'none';
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend OTP';
            }
            
            // Auto-focus and auto-submit when 4 digits entered
            const otpInput = document.getElementById('otp');
            otpInput.focus();
            
            otpInput.addEventListener('input', function() {
                if (this.value.length === 4) {
                    document.querySelector('button[name="verify_otp"]').click();
                }
            });
        });
    </script>
</body>
</html>