<?php
// =================================================================
// PHP SESSION & VALIDATION LOGIC
// =================================================================
session_start();
$user_name = $_SESSION['user_name'] ?? '';

// Initialize variables
$name = $email = $subject = $message = "";
$nameErr = $emailErr = $subjectErr = $messageErr = "";
$successMsg = "";
$isSuccess = false;

// FORM SUBMISSION HANDLING
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Validate Name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = clean_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }
  
    // 2. Validate Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = clean_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // 3. Validate Subject
    if (empty($_POST["subject"])) {
        $subjectErr = "Subject is required";
    } else {
        $subject = clean_input($_POST["subject"]);
    }

    // 4. Validate Message
    if (empty($_POST["message"])) {
        $messageErr = "Message is required";
    } else {
        $message = clean_input($_POST["message"]);
    }

    // 5. If No Errors
    if (empty($nameErr) && empty($emailErr) && empty($subjectErr) && empty($messageErr)) {
        $isSuccess = true;
        $successMsg = "Thank you, $name! Your message has been sent successfully.";
        $name = $email = $subject = $message = ""; // Reset form
    }
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Contact Us | StyleShare Luxury</title>

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
        --danger: #ff4757;
        --success: #06FFA5;
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
       MAIN LAYOUT
       ========================= */
    .main-wrapper { 
        max-width: 1200px; 
        margin: 140px auto 80px; 
        padding: 0 30px; 
        animation: fadeInUp 0.8s ease-out;
        position: relative;
        z-index: 1;
    }
    @keyframes fadeInUp { 
        from { opacity: 0; transform: translateY(30px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    .page-header { 
        text-align: center; 
        margin-bottom: 80px; 
    }
    .page-header h1 { 
        font-family: 'Playfair Display'; 
        font-size: 4.5rem; 
        margin-bottom: 20px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 30px rgba(255, 0, 110, 0.5));
    }
    .page-header p { 
        color: rgba(255, 255, 255, 0.7); 
        font-size: 1.2rem;
        max-width: 600px; 
        margin: 0 auto;
        font-weight: 300;
    }

    .contact-grid { 
        display: grid; 
        grid-template-columns: 1.2fr 1fr; 
        gap: 60px; 
        align-items: start; 
    }

    /* =========================
       GLASSMORPHIC FORM CARD
       ========================= */
    .form-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        padding: 50px 45px; 
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        position: relative; 
        overflow: hidden;
    }
    .form-card::before {
        content: ''; 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 6px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    /* Success Message */
    .success-banner {
        background: rgba(6, 255, 165, 0.15);
        backdrop-filter: blur(10px);
        color: var(--success);
        padding: 18px 20px; 
        border-radius: 15px;
        margin-bottom: 30px; 
        border: 1px solid rgba(6, 255, 165, 0.3);
        font-weight: 600;
        display: flex; 
        align-items: center; 
        gap: 12px;
        box-shadow: 0 0 20px rgba(6, 255, 165, 0.2);
    }

    .input-group { 
        margin-bottom: 30px; 
    }
    .input-group label { 
        display: block; 
        margin-bottom: 10px; 
        font-weight: 600; 
        font-size: 13px; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        color: rgba(255, 255, 255, 0.7); 
    }
    
    .input-group input, 
    .input-group textarea {
        width: 100%; 
        padding: 18px 20px; 
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        font-family: 'Inter', sans-serif; 
        font-size: 15px; 
        outline: none; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        color: white;
    }
    .input-group input::placeholder,
    .input-group textarea::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    .input-group input:focus, 
    .input-group textarea:focus { 
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2), var(--shadow-glass);
        transform: translateY(-2px);
    }
    
    /* Error Styling */
    .error-text { 
        color: var(--danger); 
        font-size: 12px; 
        margin-top: 8px; 
        display: block; 
        font-weight: 500;
        text-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
    }
    .input-error { 
        border-color: var(--danger) !important; 
        background-color: rgba(255, 71, 87, 0.1) !important;
        box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.2) !important;
    }

    .btn-send {
        width: 100%; 
        padding: 20px; 
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        color: white; 
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px; 
        font-weight: 700; 
        text-transform: uppercase;
        cursor: pointer; 
        letter-spacing: 1.5px; 
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
        box-shadow: var(--shadow-glass);
        position: relative;
        overflow: hidden;
    }
    .btn-send::before {
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
    .btn-send:hover::before {
        left: 0;
    }
    .btn-send:hover { 
        transform: translateY(-5px); 
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }

    /* =========================
       GLASSMORPHIC INFO CARDS
       ========================= */
    .info-card { 
        padding: 20px 0; 
    }
    .info-item { 
        display: flex; 
        gap: 25px; 
        margin-bottom: 40px; 
        align-items: center;
        padding: 30px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .info-item:hover {
        transform: translateY(-10px);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(0, 0, 0, 0.3);
    }
    
    .icon-circle {
        width: 70px; 
        height: 70px; 
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex; 
        align-items: center; 
        justify-content: center;
        font-size: 28px; 
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2), inset 0 0 30px rgba(255, 0, 110, 0.2);
        flex-shrink: 0;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
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
        transition: width 0.6s, height 0.6s;
    }
    .info-item:hover .icon-circle::before {
        width: 150%;
        height: 150%;
    }
    .info-item:hover .icon-circle { 
        transform: scale(1.15) rotate(360deg);
        box-shadow: var(--shadow-neon), inset 0 0 40px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .icon-circle i {
        position: relative;
        z-index: 1;
        transition: all 0.4s;
    }
    .info-item:hover .icon-circle i {
        transform: scale(1.2);
        filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.8));
    }

    .info-text h3 { 
        margin: 0 0 8px; 
        font-size: 1.3rem; 
        font-weight: 700;
        color: white;
    }
    .info-text p { 
        margin: 0; 
        color: rgba(255, 255, 255, 0.7); 
        font-size: 15px;
        line-height: 1.8;
    }
    .info-text span {
        color: rgba(255, 255, 255, 0.5);
        font-size: 13px;
    }

    /* Map */
    .map-container {
        margin-top: 30px; 
        border-radius: 25px; 
        overflow: hidden;
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(0, 0, 0, 0.3);
        height: 320px; 
        border: 2px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
    }
    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
        filter: brightness(0.8) contrast(1.1);
    }

    /* FAQ Section */
    .faq-section { 
        margin-top: 100px; 
        max-width: 900px; 
        margin-left: auto; 
        margin-right: auto; 
    }
    .faq-title { 
        text-align: center; 
        font-family: 'Playfair Display'; 
        font-size: 3.5rem; 
        margin-bottom: 60px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    
    details { 
        margin-bottom: 20px; 
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 20px; 
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    details:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
        box-shadow: var(--shadow-neon), 0 15px 40px rgba(0, 0, 0, 0.3);
    }
    details[open] {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.3);
    }
    summary { 
        padding: 25px 30px; 
        cursor: pointer; 
        font-weight: 600; 
        list-style: none; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        transition: all 0.4s ease;
        color: white;
        font-size: 16px;
    }
    summary:hover { 
        color: rgba(255, 255, 255, 0.9);
    }
    summary::after { 
        content: '+'; 
        font-size: 24px; 
        color: var(--primary);
        font-weight: 300;
        transition: transform 0.4s ease;
    }
    details[open] summary::after { 
        content: '-';
        transform: rotate(180deg);
    }
    .faq-content { 
        padding: 0 30px 30px; 
        color: rgba(255, 255, 255, 0.7); 
        line-height: 1.9;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 10px;
        padding-top: 20px;
        font-size: 15px;
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
        .contact-grid {
            grid-template-columns: 1fr;
            gap: 50px;
        }
        .page-header h1 {
            font-size: 3.5rem;
        }
        .faq-title {
            font-size: 2.8rem;
        }
    }
    
    @media (max-width: 768px) {
        .header-container { 
            padding: 15px 20px; 
        }
        .nav-links { 
            display: none; 
        }
        .main-wrapper {
            margin-top: 120px;
            padding: 0 20px;
        }
        .page-header {
            margin-bottom: 60px;
        }
        .page-header h1 {
            font-size: 2.8rem;
        }
        .page-header p {
            font-size: 1rem;
        }
        .form-card {
            padding: 35px 25px;
        }
        .info-item {
            flex-direction: column;
            text-align: center;
            padding: 25px;
        }
        .faq-title {
            font-size: 2.2rem;
        }
        .footer-links {
            flex-direction: column;
            gap: 15px;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 2.2rem;
        }
        .faq-title {
            font-size: 1.8rem;
        }
        .footer-logo {
            font-size: 2rem;
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
                <a href="aboutus.php">About</a>
                <a href="contactus.php" class="active">Contact</a>
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

    <div class="main-wrapper">
        
        <div class="page-header">
            <h1>Get In Touch</h1>
            <p>Have a question or need styling advice? We're here to help.</p>
        </div>

        <div class="contact-grid">
            
            <div class="form-card">
                <?php if ($isSuccess): ?>
                    <div class="success-banner">
                        <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" novalidate>
                    
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo $name; ?>" 
                               class="<?php echo !empty($nameErr) ? 'input-error' : ''; ?>" placeholder="e.g. John Doe">
                        <span class="error-text"><?php echo $nameErr; ?></span>
                    </div>

                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo $email; ?>"
                               class="<?php echo !empty($emailErr) ? 'input-error' : ''; ?>" placeholder="e.g. john@example.com">
                        <span class="error-text"><?php echo $emailErr; ?></span>
                    </div>

                    <div class="input-group">
                        <label>Subject</label>
                        <input type="text" name="subject" value="<?php echo $subject; ?>"
                               class="<?php echo !empty($subjectErr) ? 'input-error' : ''; ?>" placeholder="e.g. Rental Inquiry">
                        <span class="error-text"><?php echo $subjectErr; ?></span>
                    </div>

                    <div class="input-group">
                        <label>Message</label>
                        <textarea name="message" rows="5" class="<?php echo !empty($messageErr) ? 'input-error' : ''; ?>" 
                                  placeholder="How can we help you?"><?php echo $message; ?></textarea>
                        <span class="error-text"><?php echo $messageErr; ?></span>
                    </div>

                    <button type="submit" class="btn-send">Send Message <i class="fas fa-paper-plane" style="margin-left:8px;"></i></button>
                </form>
            </div>

            <div class="info-card">
                
                <div class="info-item">
                    <div class="icon-circle"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <h3>Visit Our Boutique</h3>
                        <p>123 Luxury Avenue, Fashion District<br>Rajkot, Gujarat 360001</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon-circle"><i class="fas fa-phone-alt"></i></div>
                    <div class="info-text">
                        <h3>Call Us</h3>
                        <p>+91 98765 43210<br><span>Mon-Sat, 9am-9pm</span></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="icon-circle"><i class="fas fa-envelope"></i></div>
                    <div class="info-text">
                        <h3>Email Us</h3>
                        <p>support@styleshare.com<br><span>We reply within 2 hours</span></p>
                    </div>
                </div>

                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d118147.8161935165!2d70.74838539163933!3d22.27347193458361!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3959c98ac71cdf0f%3A0x76dd15cfbe93ad3b!2sRajkot%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

            </div>
        </div>

        <div class="faq-section">
            <h2 class="faq-title">Frequently Asked Questions</h2>
            
            <details>
                <summary>Do you offer home delivery?</summary>
                <div class="faq-content">Yes! We offer free doorstep delivery and pickup for all rentals within the city limits.</div>
            </details>

            <details>
                <summary>What if the dress doesn't fit?</summary>
                <div class="faq-content">We provide a custom fitting service. Our delivery partner will wait while you try it on. If it doesn't fit, instant returns are available.</div>
            </details>

            <details>
                <summary>Is the deposit refundable?</summary>
                <div class="faq-content">Absolutely. The security deposit is 100% refundable and is returned to your original payment method within 24 hours of returning the item.</div>
            </details>
        </div>
    </div>

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
                        entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements on load
        document.addEventListener('DOMContentLoaded', function() {
            const infoItems = document.querySelectorAll('.info-item');
            const faqDetails = document.querySelectorAll('details');
            
            infoItems.forEach((item, index) => {
                item.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(item);
                }, index * 150);
            });
            
            faqDetails.forEach((detail, index) => {
                detail.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(detail);
                }, index * 100);
            });
        });
    </script>

</body>
</html>
