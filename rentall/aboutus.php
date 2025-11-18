<?php
// =================================================================
// PHP SESSION LOGIC
// =================================================================
session_start();
$user_name = $_SESSION['user_name'] ?? '';
// =================================================================
// END PHP
// =================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Our Story | StyleShare Luxury</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* =========================
       UNIQUE VARIABLES & RESET
       ========================= */
    :root {
        --primary: #FF006E;
        --primary-dark: #C2185B;
        --primary-light: #FF4D8F;
        --secondary: #8338EC;
        --accent: #3A86FF;
        --accent-2: #06FFA5;
        --text-main: #0D1B2A;
        --text-light: #415A77;
        --bg-body: #F8F9FA;
        --bg-dark: #0A0E27;
        --bg-glass: rgba(255, 255, 255, 0.1);
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        --gradient-2: linear-gradient(135deg, #FF006E 0%, #8338EC 50%, #3A86FF 100%);
        --gradient-3: linear-gradient(135deg, #06FFA5 0%, #3A86FF 100%);
        --shadow-neon: 0 0 20px rgba(255, 0, 110, 0.5), 0 0 40px rgba(131, 56, 236, 0.3);
        --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.1);
        --shadow-float: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
    }
    
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
        line-height: 1.7; 
        color: var(--text-main); 
        background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        overflow-x: hidden;
        scroll-behavior: smooth;
        position: relative;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    /* Animated Background Particles */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(131, 56, 236, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(58, 134, 255, 0.1) 0%, transparent 50%);
        animation: particleFloat 20s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }
    
    @keyframes particleFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    a { 
        text-decoration: none; 
        color: inherit; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    img { max-width: 100%; display: block; }
    
    h1, h2, h3 { 
        font-family: 'Playfair Display', serif; 
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .container { 
        width: 90%; 
        max-width: 1400px; 
        margin: 0 auto; 
        padding: 0 20px; 
        position: relative;
        z-index: 1;
    }

    /* =========================
       UNIQUE GLASSMORPHIC HEADER
       ========================= */
    header {
        background: rgba(10, 14, 39, 0.7);
        backdrop-filter: blur(30px) saturate(180%);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    header.scrolled {
        background: rgba(10, 14, 39, 0.95);
        box-shadow: 0 12px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 0, 110, 0.2);
    }
    .header-container { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 20px 0; 
    }
    .logo { 
        font-size: 2.2rem; 
        font-weight: 700; 
        font-family: 'Playfair Display', serif;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.4s ease;
        position: relative;
    }
    .logo::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 3px;
        background: var(--gradient-2);
        transition: width 0.4s ease;
    }
    .logo:hover::after {
        width: 100%;
    }
    .logo:hover {
        transform: scale(1.08) translateY(-2px);
        filter: drop-shadow(0 0 10px rgba(255, 0, 110, 0.8));
    }
    .logo span { 
        background: var(--gradient-3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .nav-links { 
        display: flex; 
        gap: 40px; 
    }
    .nav-links a { 
        font-weight: 500; 
        font-size: 15px; 
        color: rgba(255, 255, 255, 0.9);
        position: relative;
        padding: 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .nav-links a::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--gradient-2);
        transition: width 0.4s ease;
        box-shadow: 0 0 10px var(--primary);
    }
    .nav-links a:hover::before,
    .nav-links a.active::before {
        width: 100%;
    }
    .nav-links a:hover,
    .nav-links a.active { 
        color: white;
        text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
    }

    .user-actions { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
    }
    .user-badge { 
        font-size: 14px; 
        font-weight: 600; 
        color: rgba(255, 255, 255, 0.9);
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* =========================
       UNIQUE HERO SECTION
       ========================= */
    .hero {
        height: 85vh; 
        position: relative; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        text-align: center; 
        color: white;
        background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(10,14,39,0.8) 100%),
                    url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=1470&auto=format&fit=crop') no-repeat center/cover;
        background-attachment: fixed;
        margin-top: 70px;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 800px;
        height: 800px;
        background: radial-gradient(circle, rgba(255, 0, 110, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: orbFloat 25s ease-in-out infinite;
        filter: blur(80px);
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(131, 56, 236, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: orbFloat 20s ease-in-out infinite reverse;
        filter: blur(60px);
    }
    @keyframes orbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(50px, -50px) scale(1.1); }
        66% { transform: translate(-30px, 30px) scale(0.9); }
    }
    .hero-overlay { 
        position: absolute; 
        inset: 0; 
        background: rgba(0,0,0,0.4);
        z-index: 1;
    }
    .hero-content { 
        position: relative; 
        z-index: 2; 
        max-width: 900px; 
        padding: 40px; 
        animation: fadeUp 1.2s ease-out; 
    }
    
    .hero h1 { 
        font-family: 'Playfair Display'; 
        font-size: 5.5rem; 
        margin-bottom: 25px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 30px rgba(255, 0, 110, 0.5));
        animation: titleGlow 3s ease-in-out infinite;
    }
    @keyframes titleGlow {
        0%, 100% { filter: drop-shadow(0 0 30px rgba(255, 0, 110, 0.5)); }
        50% { filter: drop-shadow(0 0 50px rgba(131, 56, 236, 0.8)); }
    }
    .hero p { 
        font-size: 1.4rem; 
        opacity: 0.9; 
        margin-bottom: 30px; 
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.8;
    }
    
    @keyframes fadeUp { 
        from { opacity: 0; transform: translateY(30px); } 
        to { opacity: 1; transform: translateY(0); } 
    }

    /* =========================
       GLASSMORPHIC STATS STRIP
       ========================= */
    .stats-container {
        max-width: 1100px; 
        margin: -80px auto 100px; 
        position: relative; 
        z-index: 10;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        padding: 60px 50px; 
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 40px; 
        text-align: center;
    }
    .stat-item {
        padding: 20px;
        transition: all 0.4s ease;
    }
    .stat-item:hover {
        transform: translateY(-10px);
    }
    .stat-item h3 { 
        font-size: 4.5rem; 
        font-weight: 800; 
        margin-bottom: 10px; 
        font-family: 'Playfair Display';
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .stat-item p { 
        font-size: 14px; 
        text-transform: uppercase; 
        letter-spacing: 2px; 
        color: rgba(255, 255, 255, 0.7); 
        font-weight: 600; 
    }

    /* =========================
       ABOUT CONTENT SECTION
       ========================= */
    .section { 
        padding: 120px 0; 
        position: relative;
        z-index: 1;
    }
    
    .about-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 80px; 
        align-items: center; 
    }
    
    .about-text h2 { 
        font-family: 'Playfair Display'; 
        font-size: 3.5rem; 
        margin-bottom: 30px; 
        line-height: 1.2;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .about-text p { 
        color: rgba(255, 255, 255, 0.8); 
        margin-bottom: 25px; 
        font-size: 1.1rem; 
        line-height: 1.9; 
        font-weight: 300;
    }
    
    .about-img { 
        border-radius: 30px; 
        overflow: hidden; 
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.4);
        position: relative; 
        height: 550px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
    }
    .about-img::before {
        content: ''; 
        position: absolute; 
        inset: 0; 
        border: 3px solid rgba(255,255,255,0.2); 
        margin: 20px; 
        border-radius: 20px; 
        z-index: 2; 
        pointer-events: none;
        box-shadow: inset 0 0 30px rgba(255, 0, 110, 0.2);
    }
    .about-img img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        filter: brightness(0.9) contrast(1.1);
    }
    .about-img:hover img { 
        transform: scale(1.1) rotate(1deg);
        filter: brightness(1) contrast(1.2);
    }

    /* BUTTON */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 16px 40px; 
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        color: white;
        border-radius: 50px; 
        font-weight: 700; 
        margin-top: 30px; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        box-shadow: var(--shadow-glass);
        font-size: 14px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient-2);
        transition: left 0.5s ease;
        z-index: -1;
    }
    .btn-primary:hover::before {
        left: 0;
    }
    .btn-primary:hover { 
        transform: translateY(-5px); 
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* =========================
       VALUES & FEATURES SECTION
       ========================= */
    .features-section { 
        background: transparent;
        padding: 150px 0; 
        position: relative;
    }
    .section-title { 
        text-align: center; 
        margin-bottom: 90px; 
    }
    .section-title h2 { 
        font-family: 'Playfair Display'; 
        font-size: 3.5rem; 
        margin-bottom: 15px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .section-title p { 
        color: rgba(255, 255, 255, 0.7); 
        font-size: 1.2rem;
        font-weight: 300;
    }

    .features-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
        gap: 50px; 
    }
    .feature-card {
        padding: 60px 40px; 
        border-radius: 30px; 
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        text-align: center; 
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); 
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }
    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-2);
        transform: scaleX(0);
        transition: transform 0.6s ease;
    }
    .feature-card:hover::before {
        transform: scaleX(1);
    }
    .feature-card:hover { 
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-20px) scale(1.02);
        box-shadow: var(--shadow-neon), 0 25px 60px rgba(0, 0, 0, 0.3);
    }
    
    .icon-circle {
        width: 100px; 
        height: 100px; 
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin: 0 auto 30px;
        font-size: 40px; 
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2), inset 0 0 30px rgba(255, 0, 110, 0.2);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .icon-circle::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: var(--gradient-2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.8s, height 0.8s;
    }
    .feature-card:hover .icon-circle::before {
        width: 150%;
        height: 150%;
    }
    .feature-card:hover .icon-circle { 
        transform: rotate(360deg) scale(1.1);
        box-shadow: var(--shadow-neon), inset 0 0 40px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .icon-circle i {
        position: relative;
        z-index: 1;
        transition: all 0.4s;
    }
    .feature-card:hover .icon-circle i {
        transform: scale(1.2);
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));
    }
    
    .feature-card h3 { 
        font-size: 1.8rem; 
        margin-bottom: 20px; 
        font-weight: 700;
        color: white;
    }
    .feature-card p { 
        color: rgba(255, 255, 255, 0.7); 
        font-size: 1.05rem;
        line-height: 1.8;
    }

    /* =========================
       TEAM SECTION
       ========================= */
    .team-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 50px; 
        margin-top: 80px; 
    }
    .team-member { 
        text-align: center;
        padding: 30px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .team-member:hover {
        transform: translateY(-15px);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: var(--shadow-neon), 0 25px 60px rgba(0, 0, 0, 0.3);
    }
    .member-img {
        width: 240px; 
        height: 240px; 
        border-radius: 50%; 
        margin: 0 auto 30px;
        object-fit: cover; 
        border: 4px solid rgba(255, 255, 255, 0.2);
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(0, 0, 0, 0.3);
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .member-img::before {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: var(--gradient-2);
        opacity: 0;
        transition: opacity 0.6s ease;
        z-index: -1;
    }
    .team-member:hover .member-img::before {
        opacity: 0.6;
        filter: blur(20px);
    }
    .team-member:hover .member-img { 
        transform: scale(1.1) rotate(5deg);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: var(--shadow-neon), 0 30px 70px rgba(255, 0, 110, 0.4);
    }
    .team-member h3 { 
        font-size: 1.5rem; 
        font-weight: 700; 
        margin-bottom: 10px;
        color: white;
    }
    .member-role { 
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 14px; 
        text-transform: uppercase; 
        letter-spacing: 1.5px; 
        font-weight: 600; 
    }

    /* =========================
       UNIQUE FOOTER
       ========================= */
    footer { 
        background: rgba(10, 14, 39, 0.95);
        backdrop-filter: blur(30px);
        color: #fff; 
        padding: 80px 0 40px;
        margin-top: 100px;
        position: relative;
        overflow: hidden;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }
    footer::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(131, 56, 236, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        animation: footerOrb 20s ease-in-out infinite;
    }
    @keyframes footerOrb {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-30px, -30px) scale(1.1); }
    }
    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 30px;
        text-align: center;
        position: relative;
        z-index: 1;
    }
    .footer-logo {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
    }
    .footer-text {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.6);
        line-height: 1.8;
    }
    .footer-links {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 30px 0;
        flex-wrap: wrap;
    }
    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
    }
    .footer-links a::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--gradient-2);
        transition: width 0.3s ease;
    }
    .footer-links a:hover::after {
        width: 100%;
    }
    .footer-links a:hover {
        color: white;
        text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
    }
    .footer-copyright {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.5);
        font-size: 13px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .about-grid {
            grid-template-columns: 1fr;
            gap: 60px;
        }
        .about-text h2,
        .section-title h2 {
            font-size: 2.8rem;
        }
        .hero h1 {
            font-size: 4rem;
        }
    }
    
    @media (max-width: 768px) {
        .header-container { 
            padding: 15px 20px; 
        }
        .nav-links { 
            display: none; 
        }
        .hero {
            height: 70vh;
            margin-top: 60px;
        }
        .hero h1 {
            font-size: 3rem;
        }
        .hero p {
            font-size: 1.1rem;
        }
        .stats-container { 
            grid-template-columns: 1fr; 
            margin-top: 30px;
            margin-bottom: 60px;
            padding: 40px 30px;
            gap: 30px;
        }
        .stat-item h3 {
            font-size: 3.5rem;
        }
        .about-img { 
            height: 400px; 
        }
        .section {
            padding: 80px 0;
        }
        .features-section {
            padding: 100px 0;
        }
        .features-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        .team-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        .footer-links {
            flex-direction: column;
            gap: 15px;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 2.5rem;
        }
        .about-text h2,
        .section-title h2 {
            font-size: 2.2rem;
        }
        .footer-logo {
            font-size: 2rem;
        }
        .member-img {
            width: 200px;
            height: 200px;
        }
    }
</style>
</head>
<body>

    <header>
        <div class="container header-container">
            <div class="logo">Style<span>Share</span></div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="collection.php">Collection</a>
                <a href="aboutus.php" class="active">About Us</a>
                <a href="contactus.php">Contact</a>
            </nav>
            <div class="user-actions">
                <?php if (!empty($user_name)): ?>
                    <div class="user-badge"><i class="far fa-user"></i> <?= htmlspecialchars($user_name) ?></div>
                    <a href="logout.php" title="Logout" style="color: rgba(255,255,255,0.9); font-size: 18px;"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="login.php" style="font-weight:600; color:rgba(255,255,255,0.9); padding: 10px 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 25px; border: 1px solid rgba(255,255,255,0.2);">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Our Story</h1>
            <p>Redefining luxury fashion through sustainable, smart, and stylish rental solutions.</p>
        </div>
    </section>

    <div class="container">
        <div class="stats-container">
            <div class="stat-item">
                <h3>50k+</h3>
                <p>Rentals Completed</p>
            </div>
            <div class="stat-item">
                <h3>1,200+</h3>
                <p>Designer Pieces</p>
            </div>
            <div class="stat-item">
                <h3>4.9/5</h3>
                <p>Customer Rating</p>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>We Believe in <br>Fashion Freedom</h2>
                    <p>Welcome to StyleShare, where luxury meets accessibility. We started with a simple idea: why buy an expensive outfit you'll only wear once?</p>
                    <p>Our platform connects fashion lovers with premium designer wear at a fraction of the retail price. Whether it's a wedding, a gala, or a photoshoot, we ensure you look your best without the commitment of ownership.</p>
                    <a href="collection.php" class="btn-primary">
                        <span>View Collection</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="about-img">
                    <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?q=80&w=1000&auto=format&fit=crop" alt="Fashion">
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Core Values</h2>
                <p>Driven by passion, defined by quality.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon-circle"><i class="fas fa-leaf"></i></div>
                    <h3>Sustainability</h3>
                    <p>Reducing textile waste by promoting a circular fashion economy. Rent, wear, return, repeat.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-circle"><i class="fas fa-gem"></i></div>
                    <h3>Authenticity</h3>
                    <p>Every item is 100% authentic designer wear, carefully verified by our experts.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-circle"><i class="fas fa-heart"></i></div>
                    <h3>Community</h3>
                    <p>Building a network of style enthusiasts who believe in sharing and shining together.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Meet The Team</h2>
                <p>The creative minds behind the magic.</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400" class="member-img">
                    <h3>Sarah Johnson</h3>
                    <div class="member-role">Founder & CEO</div>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400" class="member-img">
                    <h3>David Chen</h3>
                    <div class="member-role">Head Stylist</div>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400" class="member-img">
                    <h3>Elena Rodriguez</h3>
                    <div class="member-role">Operations</div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">StyleShare</div>
            <p class="footer-text">Your premium destination for designer fashion rentals</p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="collection.php">Collection</a>
                <a href="aboutus.php">About Us</a>
                <a href="contactus.php">Contact</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2025 StyleShare Luxury Rentals. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.animation = 'fadeUp 0.8s ease-out forwards';
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements on load
        document.addEventListener('DOMContentLoaded', function() {
            const featureCards = document.querySelectorAll('.feature-card');
            const teamMembers = document.querySelectorAll('.team-member');
            const statItems = document.querySelectorAll('.stat-item');
            
            featureCards.forEach((card, index) => {
                card.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(card);
                }, index * 150);
            });
            
            teamMembers.forEach((member, index) => {
                member.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(member);
                }, index * 200);
            });
            
            statItems.forEach((item, index) => {
                item.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(item);
                }, index * 100);
            });
        });
    </script>

</body>
</html>
