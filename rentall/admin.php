<?php
session_start();

// Redirect to user_details.php if already logged in
// if (isset($_SESSION['admin_id'])) {
//     header("Location: user_details.php");
//     exit;
// }

// Hardcoded admin credentials
$admin_email = "admin@gmail.com";
$admin_password = "admin123"; // In production, use password hashing

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_email = $_POST['email'];
    $input_password = $_POST['password'];

    // Check credentials
    if ($input_email === $admin_email && $input_password === $admin_password) {
        $_SESSION['admin_id'] = 1; // Arbitrary ID since no database
        $_SESSION['admin_email'] = $input_email;
        header("Location: user_details.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | StyleShare</title>
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
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
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
        .login-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
            max-width: 500px;
            width: 100%;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            font-size: 3rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 30px rgba(255, 0, 110, 0.5));
            margin-bottom: 15px;
            transition: all 0.4s ease;
        }

        .logo:hover {
            transform: scale(1.08) translateY(-5px);
            filter: drop-shadow(0 0 50px rgba(131, 56, 236, 0.8));
        }

        .logo span {
            background: var(--gradient-3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tagline {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Login Container */
        .container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px) saturate(180%);
            padding: 50px 40px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out 0.2s both;
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

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-2);
            box-shadow: 0 0 20px var(--primary);
        }

        .container h2 {
            margin-bottom: 15px;
            font-size: 2rem;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 10px rgba(255, 0, 110, 0.3));
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
            margin-bottom: 30px;
            font-weight: 300;
        }

        /* Error Message */
        .error {
            color: #FF6B9D;
            background: rgba(255, 107, 157, 0.1);
            border: 1px solid rgba(255, 107, 157, 0.3);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInDown 0.4s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Login Form */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .login-form input {
            width: 100%;
            padding: 14px 18px 14px 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 15px;
            color: white;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .login-form input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .login-form input:focus {
            background: rgba(255, 0, 110, 0.1);
            border-color: rgba(255, 0, 110, 0.5);
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .login-form input:focus + .input-icon {
            color: var(--primary);
            transform: scale(1.2);
            text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
        }

        /* Login Button */
        .login-form button {
            padding: 16px 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px) saturate(180%);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-form button::before {
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

        .login-form button:hover::before {
            left: 0;
        }

        .login-form button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.4);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .login-form button:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Additional Info */
        .info-section {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-section p {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .login-wrapper {
                gap: 25px;
            }

            .logo {
                font-size: 2.2rem;
            }

            .container {
                padding: 35px 25px;
                max-width: 100%;
            }

            .container h2 {
                font-size: 1.5rem;
            }

            .login-form {
                gap: 15px;
            }

            .login-form input {
                padding: 12px 15px 12px 45px;
                font-size: 14px;
            }

            .login-form button {
                padding: 14px 20px;
                font-size: 14px;
            }

            .input-icon {
                left: 14px;
                font-size: 1rem;
            }
        }

        @media (max-width: 400px) {
            .logo {
                font-size: 1.8rem;
            }

            .container {
                padding: 25px 18px;
            }

            .container h2 {
                font-size: 1.3rem;
                margin-bottom: 10px;
            }

            .subtitle {
                font-size: 0.85rem;
                margin-bottom: 20px;
            }

            .login-form {
                gap: 12px;
            }

            .form-group label {
                font-size: 0.85rem;
            }

            .login-form input {
                padding: 10px 12px 10px 40px;
                font-size: 13px;
            }

            .login-form button {
                padding: 12px 15px;
                font-size: 12px;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="logo-section">
            <div class="logo">Style<span>Share</span></div>
            <p class="tagline">Admin Portal</p>
        </div>

        <div class="container">
            <h2>Admin Access</h2>
            <p class="subtitle">Secure login to manage your rental platform</p>

            <?php if ($error): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="admin.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            placeholder="admin@example.com" 
                            required
                            autocomplete="email"
                        >
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Enter your password" 
                            required
                            autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="info-section">
                <p>
                    <i class="fas fa-shield-alt"></i> 
                    Secure & Encrypted Connection
                </p>
            </div>
        </div>
    </div>
</body>
</html>