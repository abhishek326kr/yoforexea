<?php
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? ($_SESSION['user_name'] ?? 'User') : '';
$profileInitial = $isLoggedIn ? strtoupper(substr($username, 0, 1)) : '';
$profilePic = $isLoggedIn ? ($_SESSION['profile_pic'] ?? '') : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* Profile dropdown styles - matching original design */
        .profile-dropdown {
            position: relative;
        }
        
        .profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: transparent;
            border: none;
            color: inherit;
        }

        .profile-btn span {
            font-size: 14px;
            color: black;
        }
        
        .profile-btn:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .profile-menu {
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            margin-top: 10px;
            z-index: 1000;
            display: none;
        }
        
        /* Show on hover for desktop */
        @media (min-width: 992px) {
            .profile-dropdown:hover .profile-menu {
                display: block;
            }
        }
        
        /* Show when active (clicked) */
        .profile-dropdown.active .profile-menu {
            display: block;
        }
        
        .profile-header {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-name {
            font-weight: 600;
            margin-bottom: 2px;
            color: #333;
        }
        
        .profile-email {
            font-size: 12px;
            color: #666;
        }
        
        .profile-menu-item {
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .profile-menu-item:hover {
            background: #f5f5f5;
            color: #25D366;
        }
        
        .profile-menu-divider {
            border-top: 1px solid #eee;
            margin: 5px 0;
        }
        
        /* Original navbar styles preserved */
        .navbar {
            background: white;
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            height: 30px;
        }
        
        .nav-link {
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s;
            color: #333;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #128C7E;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 20px;
        }
        
        /* Auth buttons - original preserved */
        .login-btn {
            background: transparent;
            border: 1px solid #128C7E;
            color: #128C7E;
        }
        
        .login-btn:hover {
            background: rgba(18, 140, 126, 0.1);
        }
        
        .signup-btn {
            background: #128C7E;
            color: white;
            font-weight: 500;
        }
        
        .signup-btn:hover {
            background: #0e766a;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="./index.php">
            <img src="./image/logo.png" alt="Logo" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <li><a class="dropdown-item" href="#">Sustainable Living</a></li>
                        <li><a class="dropdown-item" href="#">Zero Waste</a></li>
                        <li><a class="dropdown-item" href="#">Renewable Energy</a></li>
                        <li><a class="dropdown-item" href="#">Eco Products</a></li>
                        <li><a class="dropdown-item" href="#">Urban Gardening</a></li>
                        <li><a class="dropdown-item" href="#">Green Technology</a></li>
                        <li><a class="dropdown-item" href="#">Climate Change</a></li>
                        <li><a class="dropdown-item" href="#">Recycling Tips</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./blog.php">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./contact.php">Contact</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <form class="search-form d-none d-lg-block">
                    <i class="fas fa-search search-icon"></i>
                    <input class="form-control search-input" type="search" placeholder="Search articles...">
                </form>
                
                <?php if ($isLoggedIn): ?>
                    <div class="profile-dropdown ms-3">
                        <button class="profile-btn">
                            <?php if (!empty($profilePic)): ?>
                                <img src="./uploads/profile/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile" class="profile-avatar">
                            <?php else: ?>
                                <div class="profile-avatar"><?php echo $profileInitial; ?></div>
                            <?php endif; ?>
                            <span class="d-none d-lg-inline"><?php echo htmlspecialchars($username); ?></span>
                            <i class="fas fa-chevron-down small d-none d-lg-inline"></i>
                        </button>
                        
                        <div class="profile-menu">
                            <div class="profile-header">
                                <div class="profile-name"><?php echo htmlspecialchars($username); ?></div>
                                <?php if (isset($_SESSION['user_email'])): ?>
                                    <div class="profile-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="./dashboard.php" class="profile-menu-item">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a href="./profile.php" class="profile-menu-item">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="./settings.php" class="profile-menu-item">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            
                            <div class="profile-menu-divider"></div>
                            
                            <a href="./logout.php" class="profile-menu-item text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="./login.php" class="btn btn-sm login-btn me-2">Login</a>
                        <a href="./signup.php" class="btn btn-sm signup-btn">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enhanced profile dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const profileDropdown = document.querySelector('.profile-dropdown');
        const profileMenu = document.querySelector('.profile-menu');
        let isMenuOpen = false;
        
        // Toggle menu on click
        profileDropdown.addEventListener('click', function(e) {
            if (e.target.closest('.profile-btn')) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle menu state
                isMenuOpen = !isMenuOpen;
                if (isMenuOpen) {
                    profileDropdown.classList.add('active');
                } else {
                    profileDropdown.classList.remove('active');
                }
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
                isMenuOpen = false;
            }
        });
        
        // Close menu when clicking on menu items
        const menuItems = document.querySelectorAll('.profile-menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                profileDropdown.classList.remove('active');
                isMenuOpen = false;
            });
        });
        
        // Make search input expand on focus
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.style.width = '250px';
            });
            
            searchInput.addEventListener('blur', function() {
                if (!this.value) {
                    this.style.width = '200px';
                }
            });
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                // On desktop, close menu if it was open from mobile
                if (isMenuOpen) {
                    profileDropdown.classList.remove('active');
                    isMenuOpen = false;
                }
            }
        });
    });
</script>
</body>
</html>