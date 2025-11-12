<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'rentalcloth';

$userIdentifier = $_SESSION['user_id'];

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user = null;
if (is_numeric($userIdentifier)) {
    $q = $conn->prepare("SELECT id, name, email, phone, address FROM signup WHERE id = ? LIMIT 1");
    if ($q) {
        $q->bind_param("i", $userIdentifier);
        $q->execute();
        $res = $q->get_result();
        if ($res) $user = $res->fetch_assoc();
        $q->close();
    }
} else {
    $q = $conn->prepare("SELECT id, name, email, phone, address FROM signup WHERE email = ? LIMIT 1");
    if ($q) {
        $q->bind_param("s", $userIdentifier);
        $q->execute();
        $res = $q->get_result();
        if ($res) $user = $res->fetch_assoc();
        $q->close();
    }
}
$conn->close();

if (!$user) {
        if (!empty($_SESSION['user_name'])) {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => ''
        ];
    } else {
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | StyleShare Luxury</title>

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
        --bg-dark: #0A0E27;
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
        background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        color: var(--text-main);
        min-height: 100vh;
        padding: 0;
        position: relative;
        overflow-x: hidden;
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
    .header-container { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 20px 0; 
        max-width: 1400px;
        margin: 0 auto;
        padding-left: 30px;
        padding-right: 30px;
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
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* =========================
       PROFILE CONTAINER
       ========================= */
    .profile-wrapper {
        max-width: 1000px;
        margin: 120px auto 60px;
        padding: 0 30px;
        position: relative;
        z-index: 1;
        animation: fadeInUp 0.8s ease-out;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Profile Header Card */
    .profile-header {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        padding: 50px 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    .profile-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    .profile-greeting h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        margin-bottom: 10px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .profile-greeting p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
        font-weight: 300;
    }

    .header-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    /* Profile Info Card */
    .profile-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        padding: 50px 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }
    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    .info-item {
        padding: 25px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.4s ease;
        position: relative;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-item:hover {
        padding-left: 15px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 15px;
        margin: 0 -15px;
        padding-left: 30px;
        padding-right: 15px;
    }
    .info-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 60%;
        background: var(--gradient-2);
        border-radius: 0 5px 5px 0;
        transition: width 0.4s ease;
        opacity: 0.3;
    }
    .info-item:hover::before {
        width: 4px;
    }

    .label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .label i {
        font-size: 14px;
        color: var(--primary);
    }

    .value {
        font-size: 1.2rem;
        color: white;
        font-weight: 500;
        word-break: break-word;
    }
    .value.empty {
        color: rgba(255, 255, 255, 0.4);
        font-style: italic;
    }

    /* Action Buttons */
    .actions {
        margin-top: 40px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 30px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        color: white;
        text-decoration: none;
        position: relative;
        overflow: hidden;
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
        transform: translateY(-5px);
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .btn.secondary {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.2);
    }
    .btn.secondary::before {
        background: rgba(255, 255, 255, 0.1);
    }
    .btn.secondary:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-container {
            padding: 15px 20px;
        }
        .profile-wrapper {
            margin-top: 100px;
            padding: 0 20px;
        }
        .profile-header {
            padding: 40px 30px;
        }
        .profile-header-content {
            flex-direction: column;
            text-align: center;
        }
        .profile-greeting h1 {
            font-size: 2.5rem;
        }
        .header-buttons {
            width: 100%;
            justify-content: center;
        }
        .profile-card {
            padding: 40px 30px;
        }
        .actions {
            flex-direction: column;
        }
        .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .profile-greeting h1 {
            font-size: 2rem;
        }
        .profile-header,
        .profile-card {
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>

    <header>
        <div class="header-container">
            <a href="index.php" class="logo">Style<span>Share</span></a>
            <div class="header-actions">
                <a href="index.php" class="btn secondary" style="padding: 10px 20px; font-size: 13px;">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="logout.php" class="btn" style="padding: 10px 20px; font-size: 13px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="profile-wrapper">
        
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-greeting">
                    <h1>Hello, <?= htmlspecialchars($user['name'] ?: 'User') ?></h1>
                    <p>Welcome to your profile. Manage your details below.</p>
                </div>
                <div class="header-buttons">
                    <a href="collection.php" class="btn secondary">
                        <i class="fas fa-shopping-bag"></i> Browse Collection
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-card">
            <div class="info-item">
                <div class="label">
                    <i class="fas fa-user"></i> Full Name
                </div>
                <div class="value"><?= htmlspecialchars($user['name'] ?? 'Not provided') ?></div>
            </div>

            <div class="info-item">
                <div class="label">
                    <i class="fas fa-envelope"></i> Email Address
                </div>
                <div class="value"><?= htmlspecialchars($user['email'] ?? 'Not provided') ?></div>
            </div>

            <div class="info-item">
                <div class="label">
                    <i class="fas fa-phone"></i> Phone Number
                </div>
                <div class="value <?= empty($user['phone']) ? 'empty' : '' ?>">
                    <?= htmlspecialchars($user['phone'] ?? 'Not provided') ?>
                </div>
            </div>

            <div class="info-item">
                <div class="label">
                    <i class="fas fa-map-marker-alt"></i> Delivery Address
                </div>
                <div class="value <?= empty($user['address']) ? 'empty' : '' ?>">
                    <?= nl2br(htmlspecialchars($user['address'] ?? 'Not provided')) ?>
                </div>
            </div>

            <div class="actions">
                <a href="edit_profile.php" class="btn secondary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="change_password.php" class="btn">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </div>
    </div>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.background = 'rgba(10, 14, 39, 0.95)';
                header.style.boxShadow = '0 12px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 0, 110, 0.2)';
            } else {
                header.style.background = 'rgba(10, 14, 39, 0.7)';
                header.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1)';
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements on load
        document.addEventListener('DOMContentLoaded', function() {
            const profileCard = document.querySelector('.profile-card');
            if (profileCard) {
                profileCard.style.opacity = '0';
                observer.observe(profileCard);
            }
        });
    </script>

</body>
</html>
