<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | StyleRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: #ff6b6b;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn.secondary {
            background-color: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
        }

        .btn.secondary:hover {
            background-color: #ff6b6b;
            color: white;
        }

        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            color: #333;
        }

        .section-subtitle {
            text-align: center;
            margin-bottom: 60px;
            color: #666;
            font-size: 1.2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
	
	/* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff6b6b;
        }

        .logo span {
            color: #333;
        }

        .nav-links {
            display: flex;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff6b6b;
        }

        .header-buttons {
            display: flex;
            align-items: center;
        }

        .header-buttons .btn {
            margin-left: 15px;
        }
        
        /* Hero Section Styles */
        .hero {
            height: 60vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            margin-top: 80px;
        }
        
        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .hero-content p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Contact Section Styles */
        .contact-section {
            padding: 80px 0;
        }
        
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
        }
        
        .contact-form {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .contact-form h2 {
            margin-bottom: 30px;
            font-size: 32px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d4af37;
        }
        
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: #d4af37;
            color: #fff;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: #c49f2f;
        }
        
        .contact-info {
            flex: 1;
            min-width: 300px;
        }
        
        .contact-info h2 {
            margin-bottom: 30px;
            font-size: 32px;
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .info-icon {
            font-size: 20px;
            color: #d4af37;
            margin-right: 15px;
            margin-top: 5px;
        }
        
        .info-content h3 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .info-content p, .info-content a {
            color: #666;
        }
        
        .social-links {
            margin-top: 40px;
        }
        
        .social-links h3 {
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
        }
        
        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #f5f5f5;
            border-radius: 50%;
            color: #333;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background-color: #d4af37;
            color: #fff;
        }
        
        /* Footer Styles */
        footer {
            background-color: #333;
            color: #fff;
            padding: 50px 0 20px;
        }
        
        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
        }
        
        .footer-logo {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-logo h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .footer-logo p {
            color: #ccc;
            max-width: 300px;
        }
        
        .footer-links {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-links h3 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .footer-links h3::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background-color: #d4af37;
            bottom: -8px;
            left: 0;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links ul li {
            margin-bottom: 10px;
        }
        
        .footer-links ul li a {
            color: #ccc;
            transition: color 0.3s ease;
        }
        
        .footer-links ul li a:hover {
            color: #d4af37;
        }
        
        .footer-social {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-social h3 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .footer-social h3::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background-color: #d4af37;
            bottom: -8px;
            left: 0;
        }
        
        .footer-social-icons {
            display: flex;
            gap: 15px;
        }
        
        .footer-social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: #fff;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        
        .footer-social-icon:hover {
            background-color: #d4af37;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #ccc;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .header-container {
                padding: 15px 0;
            }
            
            nav ul {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background-color: #fff;
                flex-direction: column;
                padding: 20px 0;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            }
            
            nav ul.active {
                display: flex;
            }
            
            nav ul li {
                margin: 0;
                padding: 15px 30px;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero {
                height: 50vh;
            }
            
            .hero-content h1 {
                font-size: 36px;
            }
            
            .hero-content p {
                font-size: 16px;
            }
            
            .contact-form, .contact-info {
                flex: 100%;
            }
            
            .footer-logo, .footer-links, .footer-social {
                flex: 100%;
                text-align: center;
            }
            
            .footer-links h3::after, .footer-social h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .footer-social-icons, .social-icons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">Style<span>Share</span></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="collection.html">Collection</a></li>
                <li><a href="aboutus.html">about us</a></li>
                <li><a href="contactus.php">Contact</a></li>
            </ul>
            <div class="header-buttons">
                <a href="#" class="btn secondary">Log In</a>
                <a href="signup.php" class="btn">Sign Up</a>
								<a href="admin.php" class="btn">Admin</a>

            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <h1>Get in Touch</h1>
            <p>Have questions? We're here to help you find the perfect rental for your next occasion.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container contact-container">
            <div class="contact-form">
                <h2>Send Us a Message</h2>
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
            <div class="contact-info">
                <h2>Contact Information</h2>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Our Location</h3>
                        <p>123 Fashion Street, rajkot</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Phone Number</h3>
                        <p><a href="tel:+1234567890">(+91)9090909090</a></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email Address</h3>
                        <p><a href="mailto:contact@stylerent.com">contact@stylerent.com</a></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Working Hours</h3>
                        <p>Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm</p>
                    </div>
                </div>
                <div class="social-links">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container footer-container">
            <div class="footer-logo">
                <h2>StyleRent</h2>
                <p>Rent designer clothes for any occasion. Look your best without breaking the bank.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Shop</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h3>Connect With Us</h3>
                <div class="footer-social-icons">
                    <a href="#" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-social-icon"><i class="fab fa-pinterest-p"></i></a>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; 2023 StyleRent. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            menu.classList.toggle('active');
            mobileMenuBtn.innerHTML = menu.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
    </script>
</body>
</html>