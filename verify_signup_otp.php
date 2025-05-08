<?php
session_start();
require 'blog-dashboard/config/db_main.php';

// Check if signup data exists
if (!isset($_SESSION['signup_data'])) {
    header("Location: signup.php");
    exit();
}

$signupData = $_SESSION['signup_data'];

// Handle OTP verification
if (isset($_POST['verify_otp'])) {
    $userOtp = trim($_POST['otp']);
    
    if ($userOtp === $signupData['otp_code']) {
        // OTP is correct - create user account
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, country, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", 
            $signupData['name'],
            $signupData['email'],
            $signupData['phone'],
            $signupData['country'],
            $signupData['password_hash']
        );
        
        if ($stmt->execute()) {
            // Success - log user in
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $signupData['name'];
            $_SESSION['logged_in'] = true;
            
            // Clear signup data
            unset($_SESSION['signup_data']);
            
            // Redirect to download page or dashboard
            $redirect = $_SESSION['redirect_url'] ?? 'dashboard.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect");
            exit();
        } else {
            $error = "Failed to create account. Please try again.";
        }
    } else {
        $error = "Invalid OTP code. Please try again.";
    }
}

// Handle resend OTP
if (isset($_POST['resend_otp'])) {
    $otp = str_pad(rand(0,9999),4,'0',STR_PAD_LEFT);
    $_SESSION['signup_data']['otp_code'] = $otp;
    $_SESSION['signup_data']['otp_expiry'] = time() + 600;
    
    $res = sendWhatsAppOTP($signupData['phone'], $otp);
    if ($res['status'] === 'success') {
        $message = "New OTP sent to your WhatsApp";
    } else {
        $error = "Failed to resend OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Forex Factory</title>
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

        input {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: var(--light);
            text-align: center;
            letter-spacing: 5px;
            font-size: 20px;
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

        .error i {
            margin-right: 8px;
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

        .success i {
            margin-right: 8px;
        }

        .phone-masked {
            font-weight: bold;
            color: var(--secondary);
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
            
            input, .btn {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="https://forexfactory.cc/images/newyoforexlogo.png" alt="Forexfactory Logo" class="logo-img">
        </div>
        
        <h2>Verify Your WhatsApp Number</h2>
        <p>We've sent a 4-digit code to your WhatsApp number ending with <span class="phone-masked"><?= substr($signupData['phone'], -3) ?></span></p>
        
        <?php if (isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($message)): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="otp">Enter OTP Code</label>
                <input type="text" id="otp" name="otp" maxlength="4" pattern="\d{4}" required
                       placeholder="0000" inputmode="numeric">
            </div>
            
            <button type="submit" name="verify_otp" class="btn">
                <i class="fas fa-check"></i> Verify & Continue
            </button>
            
            <button type="submit" name="resend_otp" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i> Resend OTP
            </button>
        </form>
    </div>

    <script>
        // Auto-focus OTP input and move between fields
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
            }
            
            // Auto move to next input (if you had multiple OTP fields)
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 4) {
                    this.blur();
                    document.querySelector('button[name="verify_otp"]').focus();
                }
            });
        });
    </script>
</body>
</html>