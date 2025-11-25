<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Initialize error message
$errorMessage = "";

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Connect to database
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Get user input
    $email = trim($_POST['email']);
    $passwordEntered = $_POST['password'];

    // Validate input
    if (empty($email) || empty($passwordEntered)) {
        $errorMessage = "Please enter both email and password.";
    } else {

        // Prepare SQL query to fetch user
     $stmt = $conn->prepare("SELECT id, name, password FROM signup WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();



        // Check if user exists
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $userName, $hashedPassword);
            $stmt->fetch();

            // Verify password
            if (password_verify($passwordEntered, $hashedPassword)) {
                // Success: Set session and redirect
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $userName;
                header("Location: index.php");
                exit();
            } else {
                $errorMessage = "Incorrect password.";
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
    <title>Login - Rental Clothes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }

        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>

    <?php if (!empty($errorMessage)) : ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
</div>
</body>
</html>
