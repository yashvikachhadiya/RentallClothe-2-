<?php
// edit_profile.php
if (session_status() === PHP_SESSION_NONE) session_start();

// login check
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'rentalcloth';

$userIdentifier = $_SESSION['user_id'];

$success = '';
$error = '';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch user safely (id or email)
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

// fallback to session info if DB row missing
if (!$user) {
    if (!empty($_SESSION['user_name'])) {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_phone'] ?? '',
            'address' => $_SESSION['user_address'] ?? ''
        ];
    } else {
        $conn->close();
        header("Location: login.php");
        exit();
    }
}

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') {
        $error = "Name cannot be empty.";
    } else {
        $userId = (int)($user['id'] ?? 0);
        $upd = $conn->prepare("UPDATE signup SET name = ?, phone = ?, address = ? WHERE id = ?");
        if ($upd) {
            $upd->bind_param("sssi", $name, $phone, $address, $userId);
            if ($upd->execute()) {
                $success = "Profile updated successfully.";
                $_SESSION['user_name'] = $name;
                $user['name'] = $name;
                $user['phone'] = $phone;
                $user['address'] = $address;
            } else {
                $error = "Update failed: " . $upd->error;
            }
            $upd->close();
        } else {
            // fallback: update name only
            $upd2 = $conn->prepare("UPDATE signup SET name = ? WHERE id = ?");
            if ($upd2) {
                $upd2->bind_param("si", $name, $userId);
                if ($upd2->execute()) {
                    $success = "Profile updated (name only).";
                    $_SESSION['user_name'] = $name;
                    $user['name'] = $name;
                } else {
                    $error = "Update failed: " . $upd2->error;
                }
                $upd2->close();
            } else {
                $error = "Update prepare failed: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | StyleShare Luxury</title>

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
       EDIT PROFILE CONTAINER
       ========================= */
    .edit-wrapper {
        max-width: 800px;
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

    /* Edit Profile Card */
    .edit-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        padding: 50px 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }
    .edit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    .card-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .card-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2.8rem;
        margin-bottom: 10px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .card-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1rem;
        font-weight: 300;
    }

    /* Messages */
    .msg {
        padding: 18px 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
        backdrop-filter: blur(10px);
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .success {
        background: rgba(6, 255, 165, 0.15);
        color: #06FFA5;
        border: 1px solid rgba(6, 255, 165, 0.3);
        box-shadow: 0 0 20px rgba(6, 255, 165, 0.2);
    }
    .error {
        background: rgba(255, 71, 87, 0.15);
        color: #ff4757;
        border: 1px solid rgba(255, 71, 87, 0.3);
        box-shadow: 0 0 20px rgba(255, 71, 87, 0.2);
    }

    /* Form Input Groups */
    .form-group {
        margin-bottom: 30px;
        position: relative;
    }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 10px;
    }
    .form-group label i {
        margin-right: 8px;
        color: var(--primary);
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group textarea {
        width: 100%;
        padding: 16px 20px;
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        font-family: inherit;
        font-size: 15px;
        outline: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        color: white;
    }
    .form-group input[type="text"]::placeholder,
    .form-group input[type="email"]::placeholder,
    .form-group textarea::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    .form-group input[type="text"]:focus,
    .form-group input[type="email"]:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2), var(--shadow-glass);
        transform: translateY(-2px);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    /* Buttons */
    .actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
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
        .edit-wrapper {
            margin-top: 100px;
            padding: 0 20px;
        }
        .edit-card {
            padding: 40px 30px;
        }
        .card-header h2 {
            font-size: 2.2rem;
        }
        .actions {
            flex-direction: column;
        }
        .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .card-header h2 {
            font-size: 1.8rem;
        }
        .edit-card {
            padding: 35px 25px;
        }
    }
</style>
</head>
<body>

    <header>
        <div class="header-container">
            <a href="index.php" class="logo">Style<span>Share</span></a>
            <div class="header-actions">
                <a href="profile.php" class="btn secondary" style="padding: 10px 20px; font-size: 13px;">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="btn" style="padding: 10px 20px; font-size: 13px;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="edit-wrapper">
        <div class="edit-card">
            <div class="card-header">
                <h2>Edit Profile</h2>
                <p>Update your personal information</p>
            </div>

            <?php if ($success): ?>
                <div class="msg success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="msg error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email (cannot change here)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="Your email" disabled>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Enter your phone number">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                    <textarea name="address" placeholder="Enter your delivery address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="profile.php" class="btn secondary">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                </div>
            </form>
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
    </script>

</body>
</html>
