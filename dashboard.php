<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'system/config/db.php';

// Get user details from database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found (shouldn't happen but just in case)
if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }


        .logo {
            color: var(--primary);
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 28px;
        }

        .user-actions a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .user-actions a:hover {
            background-color: rgba(37, 211, 102, 0.1);
        }

        .user-actions a.logout {
            color: var(--error);
            margin-left: 10px;
        }

        .user-actions a.logout:hover {
            background-color: rgba(255, 71, 87, 0.1);
        }

        .dashboard-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        .profile-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
            text-align: center;
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--light);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--white);
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            position: relative;
            overflow: hidden;
        }

        .profile-pic span {
            position: relative;
            z-index: 2;
        }

        .profile-pic::after {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.3));
            transform: rotate(45deg);
        }

        .user-name {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--secondary);
        }

        .user-email, .user-phone {
            color: var(--text-light);
            margin-bottom: 5px;
            font-size: 14px;
        }

        .user-joined {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 15px;
        }

        .user-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .stat-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-content {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--secondary);
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid var(--light);
            display: flex;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(37, 211, 102, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
        }

        .activity-details {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .activity-time {
            font-size: 12px;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }

            .user-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container mt-5">
      

        <div class="dashboard-content">
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-pic">
                        <span><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                    </div>
                    <h2 class="user-name"><?php echo htmlspecialchars($user['name']); ?></h2>
                    <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="user-phone">+<?php echo htmlspecialchars($user['phone']); ?></div>
                    <div class="user-joined">
                        Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                    </div>

                    <div class="user-stats">
                        <div class="stat-card">
                            <div class="stat-value">12</div>
                            <div class="stat-label">Projects</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">24</div>
                            <div class="stat-label">Connections</div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="main-content">
                <h3 class="section-title">Recent Activity</h3>
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Successful login</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Account verified</div>
                            <div class="activity-time">5 minutes ago</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">New notification</div>
                            <div class="activity-time">1 hour ago</div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Updated profile</div>
                            <div class="activity-time">Yesterday</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>