<?php
// =================================================================
// PHP SESSION & DATABASE LOGIC
// =================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$displayName = 'Guest';
$loggedIn = false;

// Simple login check
if (!empty($_SESSION['user_id'])) {
    $loggedIn = true;
    $displayName = !empty($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>StyleShare - Rent Premium Clothing</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
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
            --gradient-4: linear-gradient(180deg, rgba(255,0,110,0.1) 0%, rgba(131,56,236,0.1) 100%);
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
        ul { list-style: none; }
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
        
        /* Unique Glassmorphic Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px) saturate(180%);
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn::before {
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
        .btn:hover::before {
            left: 0;
        }
        .btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.4);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .btn:active {
            transform: translateY(-2px) scale(1.02);
        }
        .btn.secondary {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .btn.secondary::before {
            background: rgba(255, 255, 255, 0.1);
        }
        .btn.secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
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
            text-shadow: 0 0 30px rgba(255, 0, 110, 0.5);
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
        .nav-links a:hover::before {
            width: 100%;
        }
        .nav-links a:hover { 
            color: white;
            text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
        }

        .header-buttons { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }
        .welcome-text { 
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
            padding: 200px 0 120px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: transparent;
        }
        
        /* Animated Gradient Orbs */
        .hero::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -15%;
            width: 900px;
            height: 900px;
            background: radial-gradient(circle, rgba(255, 0, 110, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: orbFloat 25s ease-in-out infinite;
            filter: blur(80px);
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(131, 56, 236, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: orbFloat 20s ease-in-out infinite reverse;
            filter: blur(60px);
        }
        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -50px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.9); }
        }
        
        .hero-container { 
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 80px;
            position: relative;
            z-index: 2;
        }
        
        .hero-content { 
            animation: heroSlideIn 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes heroSlideIn {
            from {
                opacity: 0;
                transform: translateX(-50px) translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0) translateY(0);
            }
        }
        
        .hero-title { 
            font-size: 5.5rem; 
            line-height: 1.1; 
            margin-bottom: 35px; 
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
        .hero-title span { 
            background: var(--gradient-3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-style: italic;
            display: block;
            animation: spanPulse 2s ease-in-out infinite;
        }
        @keyframes spanPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .hero-subtitle { 
            font-size: 1.4rem; 
            color: rgba(255, 255, 255, 0.8); 
            margin-bottom: 45px; 
            max-width: 600px;
            line-height: 1.9;
            font-weight: 300;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        }
        .hero-buttons { 
            display: flex; 
            gap: 25px; 
            flex-wrap: wrap;
        }

        .hero-image { 
            position: relative; 
            display: flex; 
            justify-content: center;
            align-items: center;
            animation: imageFloat 1.5s ease-out 0.5s both;
        }
        @keyframes imageFloat {
            from {
                opacity: 0;
                transform: translateX(50px) translateY(30px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateX(0) translateY(0) scale(1);
            }
        }
        
        /* Unique Glassmorphic Frame */
        .hero-image::before {
            content: ''; 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%);
            width: 600px; 
            height: 600px; 
            background: var(--gradient-2);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            z-index: 1; 
            animation: morphBlob 15s infinite alternate;
            opacity: 0.4;
            filter: blur(60px);
        }
        .hero-image::after {
            content: ''; 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%);
            width: 500px; 
            height: 500px; 
            background: var(--gradient-3);
            border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
            z-index: 1; 
            animation: morphBlob 12s infinite alternate-reverse;
            opacity: 0.3;
            filter: blur(50px);
        }
        .hero-image img { 
            position: relative; 
            z-index: 2; 
            border-radius: 40px; 
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4), 0 0 60px rgba(255, 0, 110, 0.3);
            max-width: 95%;
            border: 3px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hero-image:hover img {
            transform: scale(1.05) rotate(2deg);
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5), 0 0 80px rgba(131, 56, 236, 0.5);
            border-color: rgba(255, 255, 255, 0.3);
        }

        @keyframes morphBlob {
            0% { 
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                transform: translate(-50%, -50%) rotate(0deg) scale(1);
            }
            50% {
                border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
                transform: translate(-50%, -50%) rotate(180deg) scale(1.1);
            }
            100% { 
                border-radius: 50% 50% 50% 50% / 60% 40% 60% 40%;
                transform: translate(-50%, -50%) rotate(360deg) scale(1);
            }
        }

        /* =========================
           UNIQUE STEPS SECTION
           ========================= */
        .steps-section { 
            padding: 150px 0; 
            text-align: center;
            background: transparent;
            position: relative;
        }
        .section-header { 
            margin-bottom: 90px; 
        }
        .section-header h2 { 
            font-size: 4rem; 
            margin-bottom: 20px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
        }
        .section-header p { 
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.3rem;
            font-weight: 300;
        }

        .steps-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 50px; 
        }
        .step-card { 
            padding: 60px 40px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-2);
            transform: scaleX(0);
            transition: transform 0.6s ease;
            box-shadow: 0 0 20px var(--primary);
        }
        .step-card:hover::before {
            transform: scaleX(1);
        }
        .step-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 0, 110, 0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.6s ease;
        }
        .step-card:hover::after {
            opacity: 1;
        }
        .step-card:hover { 
            transform: translateY(-20px) scale(1.03);
            box-shadow: var(--shadow-neon), 0 25px 60px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .icon-box { 
            width: 120px; 
            height: 120px; 
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 45px; 
            margin: 0 auto 30px; 
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2), inset 0 0 30px rgba(255, 0, 110, 0.2);
        }
        .icon-box::before {
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
        .step-card:hover .icon-box::before {
            width: 150%;
            height: 150%;
        }
        .step-card:hover .icon-box {
            transform: rotate(360deg) scale(1.15);
            box-shadow: var(--shadow-neon), inset 0 0 40px rgba(255, 0, 110, 0.4);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .icon-box i {
            position: relative;
            z-index: 1;
            transition: all 0.4s;
        }
        .step-card:hover .icon-box i {
            color: white;
            transform: scale(1.2);
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));
        }
        .step-card h3 { 
            font-size: 1.8rem; 
            margin-bottom: 20px;
            color: white;
            font-weight: 600;
        }
        .step-card p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.9;
            font-size: 1.05rem;
        }

        /* =========================
           UNIQUE CATEGORIES
           ========================= */
        .categories-section { 
            padding: 150px 0; 
            background: transparent;
            position: relative;
        }
        .categories-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 40px; 
        }
        
        .category-card { 
            position: relative; 
            height: 500px; 
            border-radius: 35px; 
            overflow: hidden; 
            cursor: pointer;
            background-color: #1a1a2e;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3), inset 0 0 50px rgba(255, 0, 110, 0.1);
            transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-2);
            opacity: 0;
            transition: opacity 0.7s ease;
            z-index: 1;
            mix-blend-mode: overlay;
        }
        .category-card:hover::before {
            opacity: 0.3;
        }
        .category-card::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--gradient-2);
            border-radius: 35px;
            opacity: 0;
            z-index: -1;
            filter: blur(20px);
            transition: opacity 0.7s ease;
        }
        .category-card:hover::after {
            opacity: 0.6;
        }
        .category-card:hover {
            transform: translateY(-15px) scale(1.03) rotate(1deg);
            box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .category-card img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 1s cubic-bezier(0.4, 0, 0.2, 1);
            filter: brightness(0.8) contrast(1.1);
        }
        .category-card:hover img { 
            transform: scale(1.2) rotate(-2deg);
            filter: brightness(1) contrast(1.2);
        }
        
        .cat-overlay {
            position: absolute; 
            bottom: 0; 
            left: 0; 
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.6) 50%, transparent 100%);
            padding: 50px 40px;
            color: white;
            z-index: 2;
            transform: translateY(30px);
            transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }
        .category-card:hover .cat-overlay {
            transform: translateY(0);
            background: linear-gradient(to top, rgba(0,0,0,0.98) 0%, rgba(0,0,0,0.7) 50%, transparent 100%);
        }
        .cat-overlay h3 { 
            font-size: 2.3rem; 
            margin-bottom: 12px; 
            font-weight: 700;
            text-shadow: 0 4px 20px rgba(0,0,0,0.5), 0 0 30px rgba(255, 0, 110, 0.5);
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .cat-overlay p { 
            font-size: 1.15rem; 
            opacity: 0.9;
            font-weight: 400;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        /* =========================
           UNIQUE FOOTER (RESPONSIVE FIXES)
           ========================= */
        footer { 
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(30px);
            color: #fff; 
            padding: 80px 0 40px;
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
        /* Decorative orb - hide on small screens to avoid layout issues */
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

        /* Make columns responsive and allow collapsing */
        .footer-content { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 32px; 
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
            align-items: start;
        }
        .footer-column {
            min-width: 0; /* prevents overflow in flex/grid children */
            word-wrap: break-word;
        }
        .footer-column h3 { 
            font-size: 18px; 
            margin-bottom: 18px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            color: #fff;
            position: relative;
            padding-bottom: 12px;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 4px;
            background: var(--gradient-2);
            border-radius: 2px;
            box-shadow: 0 0 10px var(--primary);
        }
        .footer-column ul { margin: 0; padding: 0; }
        .footer-column ul li { 
            margin-bottom: 12px; 
        }
        .footer-column ul li a { 
            color: rgba(255, 255, 255, 0.75); 
            font-size: 15px; 
            transition: all 0.32s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
            width: 100%;
            white-space: normal;
            word-break: break-word;
        }
        .footer-column ul li a::before {
            content: '→';
            position: absolute;
            left: -20px;
            opacity: 0;
            transition: all 0.25s ease;
        }
        .footer-column ul li a:hover {
            color: #fff; 
            transform: translateX(4px);
            text-shadow: 0 0 8px rgba(255, 0, 110, 0.6);
        }
        
        .social-icons { 
            display: flex;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .social-icons a { 
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            color: #fff; 
            font-size: 18px; 
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            overflow: hidden;
        }
        .social-icons a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--gradient-2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }
        .social-icons a:hover::before {
            width: 120%;
            height: 120%;
        }
        .social-icons a i {
            position: relative;
            z-index: 1;
            transition: transform 0.25s;
        }
        .social-icons a:hover { 
            transform: translateY(-6px) scale(1.06);
            box-shadow: var(--shadow-neon);
            border-color: rgba(255, 255, 255, 0.18);
        }
        .social-icons a:hover i {
            transform: scale(1.08) rotate(-6deg);
        }
        
        .copyright { 
            text-align: center; 
            padding-top: 34px; 
            border-top: 1px solid rgba(255,255,255,0.06); 
            color: rgba(255, 255, 255, 0.6); 
            font-size: 13px;
            position: relative;
            z-index: 1;
        }

        /* Advanced Scroll Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(60px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Parallax Effect */
        .parallax-element {
            transition: transform 0.1s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero-title {
                font-size: 4rem;
            }
            .hero-image {
                margin-top: 50px;
            }
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }

            /* Footer adjustments for tablet */
            footer {
                padding: 60px 0 30px;
            }
            .footer-content {
                gap: 24px;
                margin-bottom: 30px;
            }
            .footer-column h3 {
                font-size: 16px;
            }
        }
        
        @media (max-width: 768px) {
            .header-container { 
                flex-direction: column; 
                gap: 20px; 
            }
            .nav-links {
                gap: 25px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .hero {
                padding: 160px 0 80px;
                min-height: auto;
            }
            .hero-title { 
                font-size: 3rem; 
            }
            .hero-subtitle {
                font-size: 1.1rem;
            }
            .hero-buttons { 
                justify-content: center;
                flex-direction: column;
                width: 100%;
            }
            .hero-buttons .btn {
                width: 100%;
            }
            .hero-image::before,
            .hero-image::after { 
                width: 350px; 
                height: 350px; 
            }
            .categories-grid { 
                grid-template-columns: 1fr; 
            }
            .category-card {
                height: 400px;
            }
            .section-header h2 {
                font-size: 2.8rem;
            }
            .steps-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .step-card {
                padding: 40px 30px;
            }

            /* Footer mobile styling */
            footer {
                padding: 40px 0 24px;
            }
            footer::after {
                display: none; /* hide decorative orb on small screens */
            }
            .footer-content {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }
            .footer-column h3 {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .footer-column ul li { margin-bottom: 10px; }
            .social-icons { gap: 10px; }
            .social-icons a { width: 44px; height: 44px; font-size: 16px; border-radius: 8px; }
            .copyright {
                font-size: 12px;
                padding-top: 20px;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="container header-container">
            <div class="logo">Style<span>Share</span></div>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="collection.php">Collection</a></li>
                <li><a href="aboutus.php">About Us</a></li>
                <li><a href="contactus.php">Contact</a></li>
            </ul>

            <div class="header-buttons">
                <?php if ($loggedIn): ?>
                    <span class="welcome-text">Hi, <?= $displayName ?></span>
                    <a href="profile.php" class="btn secondary" style="padding: 8px 18px; font-size: 13px;">Profile</a>
                    <a href="logout.php" class="btn" style="padding: 8px 18px; font-size: 13px;">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn secondary" style="padding: 8px 18px; font-size: 13px;">Log in</a>
                    <a href="signup.php" class="btn" style="padding: 8px 18px; font-size: 13px;">Sign Up</a>
                     <a href="admin.php" class="btn secondary" style="padding: 8px 18px; font-size: 13px;">Admin</a>

                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container hero-container">
            <div class="hero-content">
            <h1 class="hero-title">Rent<span>Luxury</span>Wear Confidence.</h1>
                <p class="hero-subtitle">Access premium designer fashion without the premium price tag. The smarter way to dress for your special moments.</p>
                <div class="hero-buttons">
                    <a href="collection.php" class="btn">Explore Collection</a>
                    <a href="aboutus.php" class="btn secondary">How it Works</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Stylish clothing">
            </div>
        </div>
    </section>

    <section class="steps-section">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Renting your dream outfit is easier than you think</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="icon-box"><i class="fas fa-search"></i></div>
                    <h3>1. Select</h3>
                    <p>Browse our curated collection of designer outfits and pick your favorite.</p>
                </div>
                <div class="step-card">
                    <div class="icon-box"><i class="fas fa-tshirt"></i></div>
                    <h3>2. Wear</h3>
                    <p>We deliver it dry-cleaned and ready. Shine at your event with confidence.</p>
                </div>
                <div class="step-card">
                    <div class="icon-box"><i class="fas fa-box-open"></i></div>
                    <h3>3. Return</h3>
                    <p>Pack it up and we'll pick it up. No laundry, no hassle.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="categories-section">
        <div class="container">
            <div class="section-header" style="text-align: center;">
                <h2>Trending Categories</h2>
                <p>Find the perfect look for every occasion</p>
            </div>
            
            <div class="categories-grid">
                
                <a href="collection.php" class="category-card">
                    <img src="clothes/img/bride.jpg" alt="Wedding Collection">
                    <div class="cat-overlay">
                        <h3>Bridal & Wedding</h3>
                        <p>Explore Lahengas & Sherwanis</p>
                    </div>
                </a>

                <a href="collection.php" class="category-card">
                    <img src="clothes/img/partywear.jpg" alt="Party Wear">
                    <div class="cat-overlay">
                        <h3>Party Wear</h3>
                        <p>Gowns, Suits & Cocktails</p>
                    </div>
                </a>

                <a href="collection.php" class="category-card">
                    <img src="clothes/img/festive.jpg" alt="Festive Wear">
                    <div class="cat-overlay">
                        <h3>Festive Vibes</h3>
                        <p>Kurtas & Indo-Western</p>
                    </div>
                </a>

    
           </div> 

        <div style="text-align: center; margin-top: 60px; margin-bottom: 20px;">
            <a href="collection.php" class="btn" style="display: inline-flex; align-items: center; gap: 10px; padding: 15px 40px; font-size: 16px; letter-spacing: 1px;">
                View All Collections <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        </div> </section> 
    </section>
    

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>StyleShare</h3>
                    <ul>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="collection.php">Collection</a></li>
                        <li><a href="blog.html">Blog</a></li>
                        <li><a href="sustainability.html">Sustainability</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="contactus.php">Contact Us</a></li>
                        <li><a href="faq.html">FAQ</a></li>
                        <li><a href="shipping.html">Shipping & Returns</a></li>
                        <li><a href="terms.html">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Connect With Us</h3>
                    <div class="social-icons">
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 StyleShare. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // ============================================
        // HEADER SCROLL EFFECT WITH THROTTLE
        // ============================================
        let lastScroll = 0;
        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const header = document.querySelector('header');
                    const scrollY = window.scrollY;
                    
                    if (scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                    
                    lastScroll = scrollY;
                    ticking = false;
                });
                ticking = true;
            }
        });

        // ============================================
        // PARALLAX SCROLL EFFECTS
        // ============================================
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            const heroImage = document.querySelector('.hero-image');
            
            if (hero && scrolled < window.innerHeight) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
                if (heroImage) {
                    heroImage.style.transform = `translateY(${scrolled * 0.3}px)`;
                }
            }
        });

        // ============================================
        // INTERSECTION OBSERVER WITH STAGGER
        // ============================================
        const observerOptions = {
            threshold: 0.15,
            rootMargin: '0px 0px -150px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.animation = 'slideInUp 0.9s cubic-bezier(0.4, 0, 0.2, 1) forwards';
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // ============================================
        // INITIALIZE ON DOM LOAD
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            // Observe step cards
            const stepCards = document.querySelectorAll('.step-card');
            stepCards.forEach((card, index) => {
                card.style.opacity = '0';
                observer.observe(card);
            });
            
            // Observe category cards with stagger
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach((card, index) => {
                card.style.opacity = '0';
                observer.observe(card);
            });
            
            // Observe section headers
            const sectionHeaders = document.querySelectorAll('.section-header');
            sectionHeaders.forEach(header => {
                header.style.opacity = '0';
                observer.observe(header);
            });

            // Dynamic text reveal for hero title
            const heroTitle = document.querySelector('.hero-title');
            if (heroTitle) {
                const text = heroTitle.textContent;
                heroTitle.innerHTML = '';
                text.split('').forEach((char, index) => {
                    const span = document.createElement('span');
                    span.textContent = char === ' ' ? '\u00A0' : char;
                    span.style.opacity = '0';
                    span.style.display = 'inline-block';
                    span.style.animation = `fadeInScale 0.5s ease forwards ${index * 0.05}s`;
                    heroTitle.appendChild(span);
                });
            }
        });

        // ============================================
        // ADVANCED BUTTON RIPPLE EFFECT
        // ============================================
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 2;
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 800);
            });
        });

        // ============================================
        // MOUSE TRACKING ON CARDS
        // ============================================
        document.querySelectorAll('.category-card, .step-card').forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-15px) scale(1.03)`;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });

        // ============================================
        // SMOOTH SCROLL FOR ANCHOR LINKS
        // ============================================
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // ============================================
        // SCROLL PROGRESS INDICATOR
        // ============================================
        window.addEventListener('scroll', function() {
            const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (window.scrollY / windowHeight) * 100;
            
            // Create progress bar if it doesn't exist
            let progressBar = document.querySelector('.scroll-progress');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.className = 'scroll-progress';
                progressBar.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    height: 4px;
                    background: var(--gradient-2);
                    width: ${scrolled}%;
                    z-index: 10000;
                    transition: width 0.1s ease;
                    box-shadow: 0 0 10px var(--primary);
                `;
                document.body.appendChild(progressBar);
            } else {
                progressBar.style.width = scrolled + '%';
            }
        });

        // ============================================
        // KEYBOARD NAVIGATION ENHANCEMENT
        // ============================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Reset any active states
                document.querySelectorAll('.category-card, .step-card').forEach(card => {
                    card.style.transform = '';
                });
            }
        });

        // ============================================
        // RESIZE HANDLER FOR RESPONSIVE
        // ============================================
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Recalculate parallax on resize
                const hero = document.querySelector('.hero');
                if (hero) {
                    hero.style.transform = '';
                }
            }, 250);
        });

        // ============================================
        // PERFORMANCE OPTIMIZATION
        // ============================================
        let rafId = null;
        function optimizedScroll() {
            if (rafId) return;
            rafId = requestAnimationFrame(function() {
                // Scroll-based animations here
                rafId = null;
            });
        }
        window.addEventListener('scroll', optimizedScroll, { passive: true });
    </script>

    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: var(--gradient-2);
            z-index: 10000;
            transition: width 0.1s ease;
            box-shadow: 0 0 10px var(--primary);
        }
    </style>
</body>
</html>
