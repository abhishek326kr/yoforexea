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
    <style>
    
    </style>
</head>
<body>
    <!-- Header/Navbar -->
       <!-- Header/Navbar -->
       <?php include 'header.php'; ?>



<!-- About Hero Section -->
<section class="about-hero py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" style="padding-right: 40px;">
                <h1 class="display-4 fw-bold mb-4">Precision Trading <span style="color: #01ae76;">Solutions</span></h1>
                <p class="lead mb-4">We combine artificial intelligence with proven strategies to deliver consistent results in volatile markets.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn  btn-lg px-4" style="background-color: #01ae76; color: white;">Our Services</a>
                    <a href="#" class="btn  btn-lg px-4" style="border: 1px solid #01ae76; color: #01ae76;">Free Consultation</a>
                </div>            
            </div>  
            <div class="col-lg-6">  
                <img src="./image/yoforexea-about.gif" 
                     alt="Trading Analytics" 
                     class="">
            </div>   
              
        </div>

    </div>
</section>


<!-- Services Section -->
<section class="services py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Trading Services</h2>
            <div class="underline mx-auto"></div>
            <p class="text-muted mt-3">Professional solutions for traders of all levels</p>
        </div>

        <div class="row g-4">
            <!-- Service 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mb-4">
                            <i class="fas fa-robot fa-2x"></i>
                        </div>
                        <h3>AI Trading Bot</h3>
                        <p class="text-muted">24/7 automated trading with machine learning algorithms that adapt to market conditions.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Backtested strategies</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Risk management</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Real-time execution</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger mb-4">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <h3>Prop Firm Passing</h3>
                        <p class="text-muted">Proven methods to successfully pass proprietary trading firm evaluations.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Challenge strategies</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Risk parameters</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Account management</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box bg-success bg-opacity-10 text-success mb-4">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                        <h3>VIP Signals</h3>
                        <p class="text-muted">High-probability trade alerts with detailed entry and exit points.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Daily signals</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Technical analysis</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Risk-reward ratios</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box bg-info bg-opacity-10 text-info mb-4">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3>Account Management</h3>
                        <p class="text-muted">Professional management of your trading account by our experts.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Transparent reporting</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Regular updates</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Capital protection</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Service 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning mb-4">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h3>1-on-1 Coaching</h3>
                        <p class="text-muted">Personalized trading mentorship from experienced professionals.</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Strategy development</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i>Psychology training</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i>Performance review</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats py-5 bg-dark text-white">
    <div class="container py-4">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <h2 class="display-4 fw-bold">95%</h2>
                <p class="mb-0">Prop Firm Pass Rate</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-4 fw-bold">24/7</h2>
                <p class="mb-0">Market Monitoring</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-4 fw-bold">500+</h2>
                <p class="mb-0">Satisfied Clients</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-4 fw-bold">8+</h2>
                <p class="mb-0">Years Experience</p>
            </div>
        </div>
    </div>
</section>

  

<!-- Testimonials -->
<section class="testimonials py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Client Success Stories</h2>
            <div class="underline mx-auto"></div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <img src="https://randomuser.me/api/portraits/men/22.jpg" 
                                 class="rounded-circle mb-3" 
                                 width="80" 
                                 alt="Client">
                            <h5>Vikram Mehta</h5>
                            <p class="text-muted">Forex Trader | 3+ years experience</p>
                        </div>
                        <p class="lead text-center fst-italic">
                            "The AI trading bot helped me achieve consistent returns while I focused on my day job. 
                            Their prop firm challenge strategy helped me pass on my first attempt. Highly recommended!"
                        </p>
                        <div class="text-center mt-4">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Add this CSS in your style.css or style tag -->
<style>
    .about-hero {
        background-color: #b4eddb;
        color: black;
    }
    
    .underline {
        width: 80px;
        height: 3px;
        background-color: #01ae76;
        margin: 0 auto;
    }
    
    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .stats {
        background-color: #2c3e50;
    }
    
    .social-links a {
        transition: all 0.3s;
    }
    
    .social-links a:hover {
        transform: translateY(-3px);
    }
</style>


   
    <!-- Footer -->
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
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 copyright">
                    <p>&copy; 2023 EcoBlog. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>