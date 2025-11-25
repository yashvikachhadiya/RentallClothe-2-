<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleShare - Rent Premium Clothing</title>
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

        /* Hero Section */
        .hero {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, #EADDCA 0%, #e9ecef 100%);
        }

        .hero-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hero-content {
            flex: 1;
            padding-right: 50px;
        }

        .hero-image {
            flex: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        /* Features Section */
        .features {
            background-color: white;
        }

        .features-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            text-align: center;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ff6b6b;
        }

        .feature-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-description {
            color: #666;
        }

        /* Popular Items Section */
        .popular-items {
            background-color: #f8f9fa;
        }

        .items-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .item-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .item-card:hover {
            transform: translateY(-10px);
        }

        .item-image {
            height: 300px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }

        .item-details {
            padding: 20px;
        }

        .item-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .item-price {
            color: #ff6b6b;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .item-meta {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 0.9rem;
        }

        /* How It Works Section */
        .how-it-works {
            background-color: white;
        }

        .steps-container {
            display: flex;
            justify-content: space-between;
            max-width: 1000px;
            margin: 0 auto;
        }

        .step {
            text-align: center;
            padding: 0 20px;
            position: relative;
            flex: 1;
        }

        .step:not(:last-child):after {
            content: "";
            position: absolute;
            top: 40px;
            right: -30px;
            width: 60px;
            height: 2px;
            background-color: #ddd;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background-color: #ff6b6b;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 auto 30px;
        }

        .step-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        .step-description {
            color: #666;
        }

        /* Testimonials Section */
        .testimonials {
            background-color: #f8f9fa;
        }

        .testimonials-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            color: #555;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #eee;
            margin-right: 15px;
            background-size: cover;
            background-position: center;
        }

        .author-details h4 {
            margin-bottom: 5px;
        }

        .author-details p {
            color: #666;
            font-size: 0.9rem;
        }

        /* CTA Section */
        .cta {
            background-color:  #caa9a6;
            color: white;
            text-align: center;
        }

        .cta .section-title {
            color: white;
        }

        .cta .section-subtitle {
            color: rgba(255, 255, 255, 0.8);
        }

        .cta .btn {
            background-color: white;
            color: #ff6b6b;
            font-size: 1.1rem;
            padding: 15px 30px;
        }

        .cta .btn:hover {
            background-color: #f8f9fa;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            padding: 80px 0 30px;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }

        .footer-column h3 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #ff6b6b;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #ccc;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #ff6b6b;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: #ff6b6b;
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #999;
            font-size: 0.9rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }

            .steps-container {
                flex-direction: column;
                gap: 50px;
            }

            .step:not(:last-child):after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }

            .nav-links {
                margin: 20px 0;
            }

            .nav-links li {
                margin: 0 15px;
            }

            .hero-container {
                flex-direction: column;
                text-align: center;
            }

            .hero-content {
                padding-right: 0;
                margin-bottom: 50px;
            }

            .hero-buttons {
                justify-content: center;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }

            .nav-links li {
                margin: 5px 0;
            }

            .header-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .header-buttons .btn {
                margin-left: 0;
            }

            .hero-title {
                font-size: 2.2rem;
            }

            .hero {
                padding: 150px 0 70px;
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
                <a href="login.php" class="btn">Log in</a>
                <a href="signup.php" class="btn">Sign Up</a>
				<a href="admin.php" class="btn">Admin</a>
            </div>
        </div> 	
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
                <h1 class="hero-title">Rent Designer Clothes for Any Occasion</h1>
                <p class="hero-subtitle">Access premium fashion without the premium price tag. Rent, wear, return - it's that simple.</p>
                <div class="hero-buttons">
                    <a href="collection.html" class="btn">Browse Collection</a>
                    <a href="#" class="btn secondary">Learn More</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://media.istockphoto.com/id/1294193718/photo/young-beautiful-blonde-girl-wearing-an-off-the-shoulder-full-length-sky-blue-satin-slit-prom.jpg?s=612x612&w=0&k=20&c=-JMNLqE3nlUJKJsZ2-9CfxtcQZrcTEDOnu8VtAgM-mQ=" alt="Stylish clothing collection">
            </div>
        </div>
    </section>

   

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Renting with StyleShare is quick and easy. Just follow these simple steps.</p>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Browse & Select</h3>
                    <p class="step-description">Explore our collection and choose the perfect outfit for your occasion.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Receive & Enjoy</h3>
                    <p class="step-description">Your items arrive clean and ready to wear. Enjoy your rental period!</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Return</h3>
                    <p class="step-description">Use our prepaid return label to send everything back. No cleaning necessary!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Don't just take our word for it - hear from our satisfied customers.</p>
            <div class="testimonials-container">
                <div class="testimonial-card">
                    <p class="testimonial-text">"StyleShare saved my wedding! I found the perfect dress for my rehearsal dinner without breaking the bank. The quality was amazing and the process was so simple."</p>
                    <div class="testimonial-author">
                        <div class="author-image" style="background-image: url('https://www.shutterstock.com/image-photo/serious-young-ambitious-indian-businessman-260nw-2598795817.jpg');"></div>
                        <div class="author-details">
                            <h4>Sarah Johnson</h4>
                            <p>New York, NY</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"As someone who attends a lot of corporate events, StyleShare has been a game-changer. I always have something new to wear without filling my closet with one-time outfits."</p>
                    <div class="testimonial-author">
                        <div class="author-image" style="background-image: url('https://img.freepik.com/free-photo/medium-shot-smiley-man-outdoors_23-2149201362.jpg');"></div>
                        <div class="author-details">
                            <h4>Michael Chen</h4>
                            <p>San Francisco, CA</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-text">"The quality of clothes is outstanding! Everything arrives in perfect condition, and the styles are current and trendy. I've recommended StyleShare to all my friends."</p>
                    <div class="testimonial-author">
                        <div class="author-image" style="background-image: url('https://media.istockphoto.com/id/1317804578/photo/one-businesswoman-headshot-smiling-at-the-camera.jpg?s=612x612&w=0&k=20&c=EqR2Lffp4tkIYzpqYh8aYIPRr-gmZliRHRxcQC5yylY=');"></div>
                        <div class="author-details">
                            <h4>Emma Rodriguez</h4>
                            <p>Miami, FL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2 class="section-title">Ready to Refresh Your Wardrobe?</h2>
            <p class="section-subtitle">Join thousands of fashion-forward individuals who are renting instead of buying.</p>
            <a href="#" class="btn">Get Started Today</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-column">
                    <h3>StyleShare</h3>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">How It Works</a></li>
                        <li><a href="#">Sustainability</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Help</h3>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Damage Protection</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul class="footer-links">
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="#">Rental Agreement</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Connect With Us</h3>
                    <div class="social-links">
                        <a href="#"><span>FB</span></a>
                        <a href="#"><span>IG</span></a>
                        <a href="#"><span>TW</span></a>
                        <a href="#"><span>PT</span></a>
                    </div>
                    <p style="margin-top: 20px; color: #ccc;">Subscribe to our newsletter for exclusive offers and fashion tips.</p>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 StyleShare. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>