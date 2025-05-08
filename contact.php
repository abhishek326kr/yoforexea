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
    .contact-section {
        background-color: #dddddd;
        padding: 60px 60px;
        margin: 80px 150px;
        border-radius: 12px;
    }

    .contact-section h2 {
        color: #127a58;
        font-size: 2.5rem;
    }

    .contact-section .form-label {
        font-weight: bold;
        color: #555;
        font-size: 1rem;
    }

    .contact-section .custom-input {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .contact-section .custom-input:focus {
        border-color: #127a58;
        box-shadow: 0 0 8px rgba(18, 122, 88, 0.3);
        outline: none;
    }

    .contact-section .custom-btn {
        background-color: #127a58;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .contact-section .custom-btn:hover {
        background-color: #0e5d44;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .contact-section img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Responsive Styles */
    @media (max-width: 992px) { /* Tablets */
        .contact-section {
            padding: 40px 30px;
            margin: 40px 50px;
        }

        .contact-section h2 {
            font-size: 2rem;
        }

        .contact-section .custom-input {
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .contact-section .custom-btn {
            padding: 10px 16px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 768px) { /* Mobile Devices */
        .contact-section {
            padding: 30px 20px;
            margin: 20px 20px;
        }

        .contact-section h2 {
            font-size: 1.8rem;
            text-align: center;
        }

        .contact-section p {
            text-align: center;
        }

        .contact-section .custom-input {
            padding: 8px 10px;
            font-size: 0.85rem;
        }

        .contact-section .custom-btn {
            padding: 8px 14px;
            font-size: 0.85rem;
        }

        .contact-section img {
            margin-top: 80px;
            width: 250px;
        }
    }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
      <!-- Header/Navbar -->
      <?php include 'header.php'; ?>
    <!-- End Header/Navbar -->
    
    <section class="contact-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Side: Contact Form -->
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Get in Touch</h2>
                    <p class="text-muted mb-4">Have questions or need assistance? Fill out the form below, and we’ll get back to you as soon as possible.</p>
                    <form>
                        <div class="mb-4">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control custom-input" id="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control custom-input" id="email" placeholder="Enter your email" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control custom-input" id="message" rows="5" placeholder="Write your message here" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary custom-btn px-4">Send Message</button>
                    </form>
                </div>

                <!-- Right Side: Image -->
                <div class="col-lg-6 text-center">
                    <img src="./image/contactus.png.png" alt="Contact Us" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

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