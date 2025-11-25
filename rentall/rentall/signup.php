<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

session_start();
$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        $errorMessage = "Connection failed: " . $conn->connect_error;
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            $errorMessage = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Invalid email format.";
        } elseif ($password !== $confirmPassword) {
            $errorMessage = "Passwords do not match.";
        } else {
            // Check if email or name already exists
            $checkStmt = $conn->prepare("SELECT Password FROM signup WHERE email = ? OR name = ?");
            $checkStmt->bind_param("ss", $email, $name);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $errorMessage = "User with this email or name already exists.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user
                $insertStmt = $conn->prepare("INSERT INTO signup (name, email, password) VALUES (?, ?, ?)");
                $insertStmt->bind_param("sss", $name, $email, $hashedPassword);

                if ($insertStmt->execute()) {
                    $successMessage = "Signup successful! You can now <a href='login.php'>log in</a>.";
                } else {
                    $errorMessage = "Error creating account: " . $conn->error;
                }

                $insertStmt->close();
            }

            $checkStmt->close();
        }

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup - Rental Clothes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .signup-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .link {
            margin-top: 15px;
            text-align: center;
        }

        .link a {
            color: #007bff;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <h2>Create an Account</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="success"><?= $successMessage ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="input-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>

        <button type="submit">Sign Up</button>

        <div class="link">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </form>
</div>
</body>
</html>
