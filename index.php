<?php 
session_start();
// Database connection
include 'blog-dashboard/config/db.php';

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");

// Fetch popular blogs (6 articles)
$popularBlogs = $conn->query("SELECT b.*, sm.seo_slug 
                             FROM blogs b 
                             LEFT JOIN seo_meta sm ON b.id = sm.post_id 
                             ORDER BY b.views DESC 
                             LIMIT 6");

// Fetch latest blogs (6 articles)
$latestBlogs = $conn->query("SELECT b.*, sm.seo_slug 
                            FROM blogs b 
                            LEFT JOIN seo_meta sm ON b.id = sm.post_id 
                            ORDER BY b.created_at DESC 
                            LIMIT 6");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogSphere | Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    
    <!-- TradingView Tickers -->
    <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
    {
      "symbols": [
        { "proName": "OANDA:XAUUSD", "title": "Gold (XAU/USD)" },
        { "proName": "BITSTAMP:BTCUSD", "title": "Bitcoin" },
        { "proName": "BITSTAMP:ETHUSD", "title": "Ethereum" }
      ],
      "colorTheme": "light",
      "isTransparent": true,
      "displayMode": "compact",
      "locale": "en"
    }
    </script>
    
    <style>
        /* Make entire card clickable */
        .clickable-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .clickable-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .card-title {
            color: #333;
            transition: color 0.3s ease;
        }
        .clickable-card:hover .card-title {
            color: var(--primary);
        }
        .card-link {
            text-decoration: none;
            color: inherit;
        }
        .card-link:hover {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <?php include 'header.php'; ?>
    
    <!-- Market Ticker -->
    <div class="container-fluid py-2" style="background-color: #f8f9fa;">
        <div class="tradingview-widget-container">
            <div class="tradingview-widget-container__widget"></div>
        </div>
    </div>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Side: Image -->
                <div class="col-md-6 hero_image">
                    <img src="./image/Digital_Trading_Setup_with_YoForex-removebg-preview.png" alt="Hero Image" class="img-fluid">
                </div>
                <!-- Right Side: Content -->
                <div class="col-md-6 text-center text-md-start">
                    <h1 class="display-4 fw-bold mb-3" style="color:black">Learn Forex, Automate Trades,<span style="color: #198754">Grow Faster</span></h1>
                    <p class="lead mb-4" style="color:black">Explore our in-depth blogs and top-performing trading bots made for every trader.</p>
                    <a href="#latest-articles" class="btn btn-light btn-lg px-4 me-2">Explore Articles</a>
                    <a href="#popular-articles" class="btn btn-outline-light btn-lg px-4 hero-btn">Popular Posts</a>
                </div>
            </div>
        </div>  
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                <!-- Popular Articles Section -->
                <section id="popular-articles" class="mb-5">
                    <h2 class="mb-4">Popular Articles</h2>
                    <div class="row">
                        <?php if ($popularBlogs->num_rows > 0): ?>
                            <?php while ($row = $popularBlogs->fetch_assoc()): ?>
                                <?php 
                                    $slug = $row['seo_slug'];
                                    $featured_image = !empty($row['featured_image']) ?
                                        "blog-dashboard/uploads/" . $row['featured_image'] :
                                        "./image/default-blog.jpg";
                                ?>
                                <div class="col-md-4 mb-4">
                                    <a href="posts/<?= $slug ?>" class="card-link">
                                        <div class="card h-100 clickable-card">
                                            <img src="<?= $featured_image ?>" class="card-img-top" alt="<?= $row['title'] ?>">
                                            <div class="card-body">
                                                <span class="badge category-badge mb-2">Popular</span>
                                                <h5 class="card-title"><?= $row['title'] ?></h5>
                                                <p class="card-text"><?= substr(strip_tags($row['content']), 0, 100) ?>...</p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <small class="text-muted">Posted on <?= date("M d, Y", strtotime($row['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p>No popular articles found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- Latest Articles Section -->
                <section id="latest-articles">
                    <h2 class="mb-4">Latest Articles</h2>
                    <div class="row">
                        <?php if ($latestBlogs->num_rows > 0): ?>
                            <?php while ($row = $latestBlogs->fetch_assoc()): ?>
                                <?php 
                                    $slug = $row['seo_slug'];
                                    $featured_image = !empty($row['featured_image']) ?
                                        "blog-dashboard/uploads/" . $row['featured_image'] :
                                        "./image/default-blog.jpg";
                                ?>
                                <div class="col-md-4 mb-4">
                                    <a href="posts/<?= $slug ?>" class="card-link">
                                        <div class="card h-100 clickable-card">
                                            <img src="<?= $featured_image ?>" class="card-img-top" alt="<?= $row['title'] ?>">
                                            <div class="card-body">
                                                <span class="badge category-badge mb-2" style="background-color: var(--accent);">New</span>
                                                <h5 class="card-title"><?= $row['title'] ?></h5>
                                                <p class="card-text"><?= substr(strip_tags($row['content']), 0, 100) ?>...</p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <small class="text-muted">Posted on <?= date("M d, Y", strtotime($row['created_at'])) ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p>No recent articles found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Subscribe to Newsletter</h5>
                        <p class="card-text">Get the latest trading insights delivered to your inbox.</p>
                        <form id="newsletter-form">
                            <div class="input-group mb-3">
                                <input type="email" class="form-control" placeholder="Your Email" required>
                                <button class="btn" type="submit" style="background-color: var(--primary); color: white;">Subscribe</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Categories Widget -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Popular Categories</h5>
                        <ul class="list-group list-group-flush">
                            <?php 
                            // Fetch categories with post counts
                            $categoryCounts = $conn->query("
                                SELECT c.category_id, c.name, COUNT(b.id) as post_count 
                                FROM categories c 
                                LEFT JOIN blogs b ON c.category_id = b.category_id 
                                GROUP BY c.category_id 
                                ORDER BY post_count DESC 
                                LIMIT 4
                            ");
                            
                            if ($categoryCounts->num_rows > 0): ?>
                                <?php while ($cat = $categoryCounts->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="blog.php?category=<?= urlencode($cat['name']) ?>" style="text-decoration: none; color: inherit;">
                                            <?= $cat['name'] ?>
                                        </a>
                                        <span class="badge rounded-pill" style="background-color: var(--primary);"><?= $cat['post_count'] ?></span>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item">No categories found</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>   

                <!-- Banner Ad -->
                <div class="card">
                    <div class="card-body" style="overflow: hidden;">
                        <img src="https://mql5.software/images/banar.jpg" alt="banner" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <!-- Footer Section 1 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">YoForex</h3>
                    <p>Your trusted source for forex trading tips and market analysis.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-telegram"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Footer Section 2 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="blog.php">Blog Posts</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <!-- Footer Section 3 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Categories</h3>
                    <ul class="footer-links">
                        <?php 
                        // Re-fetch categories for footer
                        $footerCats = $conn->query("SELECT * FROM categories LIMIT 4");
                        if ($footerCats->num_rows > 0): ?>
                            <?php while ($cat = $footerCats->fetch_assoc()): ?>
                                <li><a href="blog.php?category=<?= urlencode($cat['name']) ?>"><?= $cat['name'] ?></a></li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Footer Section 4 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Policy Pages</h3>
                    <ul class="footer-links">
                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="terms-of-service.php">Terms of Service</a></li>
                        <li><a href="cookie-policy.php">Cookie Policy</a></li>
                    </ul>
                </div>

                <!-- Footer Section 5 -->
                <div class="col-lg-4 col-md-12">
                    <h3 class="footer-heading">Newsletter</h3>
                    <p>Subscribe to get updates on our latest articles.</p>
                    <form id="footer-newsletter">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control newsletter-input" placeholder="Your Email" required>
                            <button class="btn newsletter-btn" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center mt-4">
                    <p>&copy; <?= date('Y') ?> BlogSphere. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Form submission handling
        $('#newsletter-form, #footer-newsletter').on('submit', function(e) {
            e.preventDefault();
            const email = $(this).find('input[type="email"]').val();
            
            // Simple validation
            if (email && email.includes('@')) {
                // In a real application, you would send this to your server
                alert('Thank you for subscribing!');
                $(this).find('input[type="email"]').val('');
            } else {
                alert('Please enter a valid email address');
            }
        });
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 20
                }, 800);
            }
        });
    });
    </script>
</body>
</html>