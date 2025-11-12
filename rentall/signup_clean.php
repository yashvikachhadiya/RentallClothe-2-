<?php
session_start();
require 'dbconnection.php';

// Initialize variables
$errorMessage = '';
$successMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $address = htmlspecialchars(trim($_POST['address'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name)) {
        $errorMessage = "Full name is required.";
    } elseif (empty($email)) {
        $errorMessage = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $errorMessage = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errorMessage = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $errorMessage = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM signup WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $errorMessage = "This email is already registered. Please use a different email or <a href='login.php' style='color: #FF006E; font-weight: 600;'>sign in</a>.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql = "INSERT INTO signup (name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssss", $name, $email, $phone, $address, $hashed_password);

                if ($stmt->execute()) {
                    $successMessage = "✓ Account created successfully! Redirecting to login...";
                    // Redirect after 2 seconds
                    header("Refresh: 2; url=login.php");
                } else {
                    $errorMessage = "Error creating account: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errorMessage = "Database error: " . $conn->error;
            }
        }
        $check_email->close();
    }
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - StyleShare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        /* CSS Variables */
        :root {
            --primary: #FF006E;
            --secondary: #8338EC;
            --accent: #3A86FF;
            --accent-2: #06FFA5;
            --bg-dark: #0A0E27;
            --bg-dark-2: #0F1535;
            --danger: #FF4757;
            --success: #06FFA5;
            --text-light: rgba(255, 255, 255, 0.7);
            --text-lighter: rgba(255, 255, 255, 0.4);
            --gradient-1: linear-gradient(135deg, #FF006E, #8338EC);
            --gradient-2: linear-gradient(135deg, #FF006E, #3A86FF);
            --gradient-3: linear-gradient(135deg, #8338EC, #06FFA5);
            --shadow-glass: 0 8px 32px 0 rgba(255, 0, 110, 0.1);
            --shadow-neon: 0 0 20px rgba(255, 0, 110, 0.3);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-dark);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(51, 56, 236, 0.15) 0%, transparent 50%),
                        var(--bg-dark);
            z-index: -1;
        }

        .signup-container { 
            width: 100%; 
            max-width: 600px;
            background: rgba(15, 21, 53, 0.5);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 60px 50px;
            box-shadow: var(--shadow-glass), 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        /* Top Decoration */
        .signup-container::before {
            content: ''; 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 6px;
            background: var(--gradient-2);
            box-shadow: 0 0 20px var(--primary);
            border-radius: 25px 25px 0 0;
        }

        .header-section { 
            text-align: center; 
            margin-bottom: 40px; 
        }
        .logo { 
            font-family: 'Playfair Display', serif; 
            font-size: 3rem; 
            font-weight: 700; 
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
            margin-bottom: 10px;
        }
        .subtitle { 
            color: var(--text-light); 
            font-size: 15px; 
            font-weight: 300;
        }

        /* Messages */
        .msg-box { 
            padding: 18px 20px; 
            border-radius: 15px; 
            font-size: 14px; 
            margin-bottom: 25px; 
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
        .error { 
            background: rgba(255, 71, 87, 0.15);
            color: var(--danger); 
            border: 1px solid rgba(255, 71, 87, 0.3);
            box-shadow: 0 0 20px rgba(255, 71, 87, 0.2);
        }
        .success { 
            background: rgba(6, 255, 165, 0.15);
            color: var(--success); 
            border: 1px solid rgba(6, 255, 165, 0.3);
            box-shadow: 0 0 20px rgba(6, 255, 165, 0.2);
        }

        /* Inputs */
        .form-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
        }
        .input-group { 
            margin-bottom: 25px; 
            position: relative; 
        }
        .input-group.full { 
            grid-column: span 2; 
        }
        
        .input-group label { 
            display: block; 
            font-size: 12px; 
            font-weight: 600; 
            text-transform: uppercase; 
            color: var(--text-light); 
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        
        .input-field {
            width: 100%; 
            padding: 16px 20px 16px 45px; 
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            font-family: inherit; 
            font-size: 14px; 
            outline: none; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }
        .input-field::placeholder {
            color: var(--text-lighter);
        }
        .input-field:focus { 
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2), var(--shadow-glass);
            transform: translateY(-2px);
        }
        .input-field.error {
            border-color: var(--danger);
            background: rgba(255, 71, 87, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 71, 87, 0.2);
        }
        
        /* Icons inside inputs */
        .input-icon { 
            position: absolute; 
            left: 18px; 
            top: 42px; 
            color: rgba(255, 255, 255, 0.5); 
            transition: all 0.4s ease;
            pointer-events: none;
        }
        .input-field:focus ~ .input-icon { 
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Password Toggle */
        .toggle-pwd { 
            position: absolute; 
            right: 18px; 
            top: 42px; 
            cursor: pointer; 
            color: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            z-index: 2;
        }
        .toggle-pwd:hover { 
            color: white;
            transform: scale(1.1);
        }

        /* Password Strength */
        .password-strength { 
            height: 5px; 
            background: rgba(255, 255, 255, 0.1);
            margin-top: 10px; 
            border-radius: 3px; 
            overflow: hidden; 
            transition: all 0.4s ease;
            backdrop-filter: blur(5px);
        }
        .strength-bar { 
            height: 100%; 
            width: 0%; 
            background: var(--danger);
            transition: width 0.4s ease, background 0.4s ease;
        }

        /* Button */
        .btn-signup { 
            width: 100%; 
            padding: 18px; 
            margin-top: 30px; 
            border: 1px solid rgba(255, 0, 110, 0.5); 
            border-radius: 15px; 
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white; 
            font-size: 15px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            cursor: pointer; 
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-neon);
        }
        .btn-signup::before { 
            content: ''; 
            position: absolute; 
            top: 50%; 
            left: 50%; 
            width: 0; 
            height: 0; 
            background: rgba(255, 255, 255, 0.3); 
            border-radius: 50%; 
            transform: translate(-50%, -50%); 
            transition: width 0.6s, height 0.6s; 
            z-index: -1;
        }
        .btn-signup:hover::before {
            width: 300px;
            height: 300px;
        }
        .btn-signup:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-neon), 0 20px 50px rgba(255, 0, 110, 0.4);
            border-color: rgba(255, 255, 255, 0.4);
        }
        .btn-signup:active {
            transform: translateY(-2px);
        }

        /* Footer */
        .footer-links { 
            text-align: center; 
            margin-top: 30px; 
            font-size: 14px; 
            color: var(--text-light);
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
            text-decoration: none;
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

        /* Checkbox */
        .terms { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-size: 13px; 
            color: var(--text-light); 
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .terms input { 
            accent-color: var(--primary);
            cursor: pointer;
            width: 18px;
            height: 18px;
        }
        .terms label {
            cursor: pointer;
            user-select: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .signup-container {
                padding: 40px 30px;
                max-width: 100%;
            }
            .logo {
                font-size: 2.5rem;
            }
            .form-grid { 
                grid-template-columns: 1fr; 
            }
            .input-group.full { 
                grid-column: span 1; 
            }
            body {
                padding: 20px 15px;
            }
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 35px 25px;
            }
            .logo {
                font-size: 2rem;
            }
            .header-section {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <div class="header-section">
            <div class="logo">Style<span style="background: var(--gradient-3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Share</span></div>
            <p class="subtitle">Join the luxury rental community</p>
        </div>

        <!-- PHP Messages -->
        <?php if ($errorMessage): ?>
            <div class="msg-box error">
                <i class="fas fa-exclamation-circle"></i> 
                <span><?= $errorMessage ?></span>
            </div>
        <?php endif; ?>
        <?php if ($successMessage): ?>
            <div class="msg-box success">
                <i class="fas fa-check-circle"></i> 
                <span><?= $successMessage ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            
            <div class="form-grid">
                <!-- Full Name -->
                <div class="input-group full">
                    <label>Full Name *</label>
                    <input type="text" name="name" class="input-field" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    <i class="fas fa-user input-icon"></i>
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="input-field" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <!-- Phone -->
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="input-field" placeholder="+91 " value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    <i class="fas fa-phone input-icon"></i>
                </div>

                <!-- Address -->
                <div class="input-group full">
                    <label>Delivery Address</label>
                    <input type="text" name="address" class="input-field" placeholder="Flat No, Street, City..." value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    <i class="fas fa-map-marker-alt input-icon"></i>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label>Password *</label>
                    <input type="password" name="password" id="password" class="input-field" placeholder="Min. 6 characters" required>
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye toggle-pwd" onclick="togglePwd('password')"></i>
                    
                    <!-- Strength Meter -->
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="input-field" placeholder="Re-enter password" required>
                    <i class="fas fa-lock input-icon"></i>
                    <i class="fas fa-eye toggle-pwd" onclick="togglePwd('confirm_password')"></i>
                </div>
            </div>

            <!-- Terms Checkbox -->
            <div class="terms">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the Terms & Conditions and Privacy Policy.</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-signup">
                <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                Create Account
            </button>

        </form>

        <!-- Login Link -->
        <div class="footer-links">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>

    <!-- JAVASCRIPT -->
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

        // 2. Password Strength Meter
        const pwdInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        pwdInput.addEventListener('input', function() {
            const val = pwdInput.value;
            let strength = 0;
            
            if (val.length > 5) strength += 20;
            if (val.length > 8) strength += 20;
            if (/[A-Z]/.test(val)) strength += 20;
            if (/[0-9]/.test(val)) strength += 20;
            if (/[^A-Za-z0-9]/.test(val)) strength += 20;

            strengthBar.style.width = strength + '%';
            strengthBar.className = 'strength-bar';
            
            if (strength < 40) {
                strengthBar.style.backgroundColor = '#ff4757'; // Red
            } else if (strength < 80) {
                strengthBar.style.backgroundColor = '#FFD700'; // Yellow
                strengthBar.classList.add('medium');
            } else {
                strengthBar.style.backgroundColor = '#06FFA5'; // Green
                strengthBar.classList.add('strong');
            }
        });

        // 3. Real-time Password Match Validation
        const confirmPwd = document.getElementById('confirm_password');
        confirmPwd.addEventListener('input', function() {
            const p1 = pwdInput.value;
            const p2 = confirmPwd.value;
            
            if (p2.length > 0) {
                if (p1 !== p2) {
                    confirmPwd.classList.add('error');
                } else {
                    confirmPwd.classList.remove('error');
                }
            } else {
                confirmPwd.classList.remove('error');
            }
        });

        // 4. Form Submission Validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const p1 = pwdInput.value;
            const p2 = confirmPwd.value;
            const terms = document.getElementById('terms').checked;
            
            if (p1 !== p2) {
                e.preventDefault();
                alert("Passwords do not match!");
                confirmPwd.focus();
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert("Please agree to the Terms & Conditions to continue.");
                return false;
            }
        });

        // 5. Input Focus Animations
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // 6. Form Validation on Blur
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('error');
                } else {
                    this.classList.remove('error');
                }
            });
        });
    </script>

</body>
</html>
