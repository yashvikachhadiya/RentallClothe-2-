<?php
// =================================================================
// 1. PHP LOGIN LOGIC WITH VALIDATION
// =================================================================
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Connect to database
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Sanitize Inputs
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $passwordEntered = $_POST['password'] ?? '';

    // 1. Basic Validation
    if (empty($email) || empty($passwordEntered)) {
        $errorMessage = "Please enter both email and password.";
    } 
    // 2. Email Format Validation
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    }
    else {
        // Fetch User
        $stmt = $conn->prepare("SELECT id, name, password FROM signup WHERE email = ? LIMIT 1");
        if (!$stmt) { die("SQL Error: " . $conn->error); }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $userName, $hashedPassword);
            $stmt->fetch();

            // Verify Password (supports both hash and plain text for migration)
            $loginOk = false;
            if (password_verify($passwordEntered, $hashedPassword)) {
                $loginOk = true;
            } elseif ($passwordEntered === $hashedPassword) {
                $loginOk = true;
                // Auto-migrate to secure hash
                $newHash = password_hash($passwordEntered, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE signup SET password = ? WHERE id = ?");
                $upd->bind_param("si", $newHash, $userId);
                $upd->execute();
            }

            if ($loginOk) {
                // Success! Set Session
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $userName;
                header("Location: index.php");
                exit();
            } else {
                $errorMessage = "Incorrect password. Please try again.";
            }
        } else {
            $errorMessage = "No account found with that email.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | StyleShare Luxury</title>

<!-- Fonts & Icons -->
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
        background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        color: var(--text-main);
        min-height: 100vh;
        display: flex; 
        align-items: center; 
        justify-content: center;
        padding: 40px 20px;
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

    /* Glassmorphic Login Container */
    .login-container {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        width: 100%; 
        max-width: 480px;
        padding: 60px 50px;
        border-radius: 35px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.4);
        text-align: center;
        position: relative; 
        overflow: hidden;
        animation: slideUp 0.8s ease-out;
        z-index: 1;
    }
    @keyframes slideUp { 
        from { opacity: 0; transform: translateY(40px) scale(0.95); } 
        to { opacity: 1; transform: translateY(0) scale(1); } 
    }

    /* Top Bar Decoration */
    .login-container::before {
        content: ''; 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 6px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    .logo {
        font-family: 'Playfair Display', serif; 
        font-size: 3.5rem; 
        font-weight: 700; 
        margin-bottom: 15px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }
    .logo span {
        background: var(--gradient-3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .subtitle { 
        color: rgba(255, 255, 255, 0.7); 
        font-size: 15px; 
        margin-bottom: 40px;
        font-weight: 300;
    }

    /* Error Box */
    .error-box {
        background: rgba(255, 71, 87, 0.15);
        backdrop-filter: blur(10px);
        color: var(--danger); 
        padding: 18px 20px; 
        border-radius: 15px;
        font-size: 14px; 
        font-weight: 500; 
        margin-bottom: 30px; 
        text-align: left;
        display: flex; 
        align-items: center; 
        gap: 12px;
        border: 1px solid rgba(255, 71, 87, 0.3);
        box-shadow: 0 0 20px rgba(255, 71, 87, 0.2);
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Inputs */
    .input-group { 
        margin-bottom: 25px; 
        text-align: left; 
    }
    .input-group label { 
        display: block; 
        font-size: 12px; 
        font-weight: 600; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        color: rgba(255, 255, 255, 0.7); 
        margin-bottom: 10px;
    }
    
    .input-wrapper { 
        position: relative; 
    }
    .input-wrapper i { 
        position: absolute; 
        left: 18px; 
        top: 50%; 
        transform: translateY(-50%); 
        color: rgba(255, 255, 255, 0.5); 
        transition: all 0.4s ease;
        pointer-events: none;
    }
    
    .input-field {
        width: 100%; 
        padding: 16px 20px 16px 45px; 
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
    .input-field::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    .input-field:focus { 
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2), var(--shadow-glass);
        transform: translateY(-2px);
    }
    .input-field:focus ~ i { 
        color: var(--primary);
        transform: translateY(-50%) scale(1.1);
    }
    .input-field.error {
        border-color: var(--danger);
        background: rgba(255, 71, 87, 0.1);
        box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.2);
    }

    /* Password Toggle */
    .toggle-pwd { 
        position: absolute; 
        right: 18px; 
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer; 
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
        z-index: 2;
    }
    .toggle-pwd:hover { 
        color: white;
        transform: translateY(-50%) scale(1.1);
    }

    /* Validation Message */
    .validate-msg { 
        color: var(--danger); 
        font-size: 12px; 
        margin-top: 8px; 
        display: none;
        font-weight: 500;
        text-shadow: 0 0 10px rgba(255, 71, 87, 0.5);
    }

    /* Button */
    .btn-login {
        width: 100%; 
        padding: 18px; 
        border-radius: 50px; 
        border: 2px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        color: white; 
        font-size: 15px; 
        font-weight: 700;
        text-transform: uppercase; 
        letter-spacing: 1.5px; 
        cursor: pointer;
        margin-top: 15px; 
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-glass);
        position: relative;
        overflow: hidden;
    }
    .btn-login::before {
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
    .btn-login:hover::before {
        left: 0;
    }
    .btn-login:hover { 
        transform: translateY(-5px); 
        box-shadow: var(--shadow-neon), 0 20px 50px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.4);
    }
    .btn-login:active {
        transform: translateY(-2px);
    }

    /* Footer Links */
    .footer-links { 
        margin-top: 30px; 
        font-size: 14px; 
        color: rgba(255, 255, 255, 0.6);
    }
    .footer-links a { 
        color: white;
        font-weight: 600;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
        position: relative;
    }
    .footer-links a::after {
        content: '';
        position: absolute;
        bottom: -2px;
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
        text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
    }

    .divider { 
        height: 1px; 
        background: rgba(255, 255, 255, 0.1);
        margin: 30px 0;
        position: relative;
    }
    .divider::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 1px;
        background: var(--gradient-2);
    }

    .back-home {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        padding: 10px 15px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .back-home:hover {
        color: white;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateX(-5px);
    }

    .forgot-link {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }
    .forgot-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 1px;
        background: var(--gradient-2);
        transition: width 0.3s ease;
    }
    .forgot-link:hover {
        color: white;
    }
    .forgot-link:hover::after {
        width: 100%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .login-container {
            padding: 50px 35px;
            max-width: 100%;
        }
        .logo {
            font-size: 2.8rem;
        }
        body {
            padding: 20px 15px;
        }
    }

    @media (max-width: 480px) {
        .login-container { 
            padding: 40px 30px; 
        }
        .logo {
            font-size: 2.5rem;
        }
        .subtitle {
            font-size: 14px;
        }
    }
</style>
</head>
<body>

    <div class="login-container">
        <div class="logo">Style<span>Share</span></div>
        <p class="subtitle">Welcome back! Please enter your details.</p>

        <!-- PHP Error Display -->
        <?php if (!empty($errorMessage)) : ?>
            <div class="error-box">
                <i class="fas fa-exclamation-circle"></i> 
                <span><?= htmlspecialchars($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm" novalidate>
            
            <div class="input-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <input type="email" name="email" id="email" class="input-field" 
                           placeholder="Enter your email" 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                           required>
                    <i class="fas fa-envelope"></i>
                </div>
                <span class="validate-msg" id="emailError">Please enter a valid email address</span>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="input-field" 
                           placeholder="Enter your password" 
                           required>
                    <i class="fas fa-lock"></i>
                    <i class="fas fa-eye toggle-pwd" onclick="togglePwd('password')"></i>
                </div>
                <div style="text-align:right; margin-top:8px;">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Sign In
            </button>

        </form>

        <div class="footer-links">
            Don't have an account? <a href="signup.php">Create account</a>
        </div>
        
        <div class="divider"></div>
        
        <div style="text-align: center;">
            <a href="index.php" class="back-home">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- JAVASCRIPT VALIDATION -->
    <script>
        // 1. Password Toggle
        function togglePwd(id) {
            const input = document.getElementById(id);
            const icons = input.parentElement.querySelectorAll('.toggle-pwd');
            const icon = icons[icons.length - 1];
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye'); 
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash'); 
                icon.classList.add('fa-eye');
            }
        }

        // 2. Form Validation
        const form = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailError = document.getElementById('emailError');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Email Validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value.trim())) {
                emailError.style.display = 'block';
                emailInput.classList.add('error');
                isValid = false;
            } else {
                emailError.style.display = 'none';
                emailInput.classList.remove('error');
            }

            // Password Validation
            if (passwordInput.value.trim() === '') {
                passwordInput.classList.add('error');
                isValid = false;
            } else {
                passwordInput.classList.remove('error');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // 3. Remove error on typing
        emailInput.addEventListener('input', function() {
            emailError.style.display = 'none';
            this.classList.remove('error');
        });

        passwordInput.addEventListener('input', function() {
            this.classList.remove('error');
        });

        // 4. Input Focus Animations
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });
        });

        // 5. Form Loading State
        form.addEventListener('submit', function() {
            const btn = this.querySelector('.btn-login');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Signing In...';
                btn.disabled = true;
            }
        });
    </script>

</body>
</html>
