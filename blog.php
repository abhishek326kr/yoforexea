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
    <link rel="stylesheet" href="./css/blog.css">
    
</head>
<body>
    <!-- Header/Navbar start -->
    <!-- Header/Navbar -->
    <?php include 'header.php'; ?>
<!-- Header/Navbar close -->
   
   <section class="blog_hero">
        <div class="blog-container">
            <h1>Ready to Level Up Your <br><span style="color: #127a58">Forex Game?</span></h1>
            <p>From AI trading bots to daily market tips — our blogs break down forex like never before. Learn, apply, and trade smarter.</p>    

            <div>
                <button>Get Started</button>
                <button>Learn More</button>
            </div>
        </div>
   </section>

   <section class="blog-cards">
    <div class="container">
        <h2 class="section-title">Latest Blogs</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=1" alt="Blog 1" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Sustainable Living Tips</h5>
                        <p class="card-text">Learn how to live sustainably and reduce your carbon footprint.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=2" alt="Blog 2" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Zero Waste Lifestyle</h5>
                        <p class="card-text">Discover practical tips for adopting a zero-waste lifestyle.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=3" alt="Blog 3" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Renewable Energy Sources</h5>
                        <p class="card-text">Explore the benefits of renewable energy for a greener future.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=4" alt="Blog 4" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Eco-Friendly Products</h5>
                        <p class="card-text">Find out the best eco-friendly products for everyday use.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=5" alt="Blog 5" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Urban Gardening Ideas</h5>
                        <p class="card-text">Learn how to create a thriving garden in small urban spaces.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card">
                    <img src="https://picsum.photos/400/300?random=6" alt="Blog 6" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Climate Change Awareness</h5>
                        <p class="card-text">Understand the impact of climate change and how to combat it.</p>
                        <a href="#" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Footer start-->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">EcoBlog</h3>
                    <p>Your trusted source for sustainable living tips and environmental awareness.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Blog Posts</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h3 class="footer-heading">Categories</h3>
                    <ul class="footer-links">
                        <li><a href="#">Sustainable Living</a></li>
                        <li><a href="#">Zero Waste</a></li>
                        <li><a href="#">Renewable Energy</a></li>
                        <li><a href="#">Eco Products</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h3 class="footer-heading">Newsletter</h3>
                    <p>Subscribe to get updates on our latest articles.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control newsletter-input" placeholder="Your Email">
                        <button class="btn newsletter-btn" type="button">Subscribe</button>
                    </div>
                <div class="col-12 copyright">
                    <p>&copy; 2023 EcoBlog. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

     <!-- Footer start-->

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>