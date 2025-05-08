<?php 
session_start();
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
    

    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <?php include 'header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Side: Image -->
                <div class="col-md-6 hero_image" >
                    <img src="./image/Digital_Trading_Setup_with_YoForex-removebg-preview.png" alt="Hero Image" class="img-fluid">
                </div>
                <!-- Right Side: Content -->
                <div class="col-md-6 text-center text-md-start">
                    <h1 class="display-4 fw-bold mb-3" style="color:black">Learn Forex, Automate Trades,<span style="color: #198754 ">Grow Faster</span></h1>
                    <p class="lead mb-4" style="color:black">Explore our in-depth blogs and top-performing trading bots made for every trader.</p>
                    <button class="btn btn-light btn-lg px-4 me-2">Explore Articles</button>
                    <button class="btn btn-outline-light btn-lg px-4 hero-btn">Learn More</button>
                </div>
            </div>
        </div>  
    </section>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                <h2 class="mb-4">Latest Articles</h2>
                
                <div class="row">
                    <!-- Article Card 1 -->
                    <div class="col-md-6">
                        <div class="card">
                            <img src="./image/ChatGPT Image Apr 9, 2025, 04_38_11 PM.png" class="card-img-top" alt="...">
                            <div class="card-body">
                                <span class="badge category-badge mb-2">Sustainability</span>
                                <h5 class="card-title">10 Ways to Reduce Plastic Waste</h5>
                                <p class="card-text">Discover simple changes you can make today to significantly reduce your plastic consumption.</p>
                                <a href="#" class="btn btn-sm" style="background-color: var(--primary); color: white;">Read More</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article Card 2 -->
                    <div class="col-md-6">
                        <div class="card">
                            <img src="./image/Urban Gardening for Beginners.png" class="card-img-top" alt="...">
                            <div class="card-body">
                                <span class="badge category-badge mb-2" style="background-color: var(--accent);">Green Living</span>
                                <h5 class="card-title">Urban Gardening for Beginners</h5>
                                <p class="card-text">Learn how to start your own garden even in small urban spaces with these practical tips.</p>
                                <a href="#" class="btn btn-sm" style="background-color: var(--primary); color: white;">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Subscribe to Newsletter</h5>
                        <p class="card-text">Get the latest eco-friendly tips delivered to your inbox.</p>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your Email">
                            <button class="btn" type="button" style="background-color: var(--primary); color: white;">Subscribe</button>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Popular Categories</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sustainable Living
                                <span class="badge rounded-pill" style="background-color: var(--primary);">24</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Zero Waste
                                <span class="badge rounded-pill" style="background-color: var(--primary);">18</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Renewable Energy
                                <span class="badge rounded-pill" style="background-color: var(--primary);">12</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Eco Products
                                <span class="badge rounded-pill" style="background-color: var(--primary);">9</span>
                            </li>
                        </ul>
                    </div>
                </div>   

                <div class="card">
                  <div class="card-body" style="overflow: hidden; ">
                     
                      <img src="https://mql5.software/images/banar.jpg" alt="banar" style="overflow: hidden;">
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
                    <p>Your trusted source for sustainable living tips and environmental awareness.</p>
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
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Blog Posts</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>

                <!-- Footer Section 3 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#">Popular Forex Articles</a></li>
                        <li><a href="#">Latest Market Updates</a></li>
                        <li><a href="#">EA - MT4 Articles</a></li>
                        <li><a href="#">Indicator-MT4 Articles</a></li>
                    </ul>
                </div>

                <!-- Footer Section 4 -->
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Policy Pages</h3>
                    <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>

                <!-- Footer Section 5 -->
                <div class="col-lg-4 col-md-12">
                    <h3 class="footer-heading">Newsletter</h3>
                    <p>Subscribe to get updates on our latest articles.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control newsletter-input" placeholder="Your Email">
                        <button class="btn newsletter-btn" type="button">Subscribe</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center mt-4">
                    <p>&copy; 2023 EcoBlog. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>