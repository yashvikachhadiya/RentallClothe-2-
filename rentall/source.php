<?php
// db.php - Database connection
$host = 'localhost';
$db = 'rentalcloth';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// index.php - Homepage
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_GET['page'])) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rental Clothing</title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; }
            a { display: block; text-align: center; margin: 20px; }
        </style>
    </head>
    <body>
        <h1>Welcome to Rental Clothing</h1>
        <a href="?page=rent">Rent Clothing</a>
    </body>
    </html>';
}

// rent.php - Page to select clothing and rent
if (isset($_GET['page']) && $_GET['page'] == 'rent') {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rent Clothing</title>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; }
            .clothing-item { margin: 20px; text-align: center; }
            img { width: 150px; height: auto; }
        </style>
    </head>
    <body>
        <h1>Select Clothing to Rent</h1>
        <form action="?page=checkout" method="POST">';
    
    $stmt = $pdo->query("SELECT * FROM add_product");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="clothing-item">
                <img src="' . htmlspecialchars($row['product_image1']) . '" alt="' . htmlspecialchars($row['product_name']) . '" />
                <h2>' . htmlspecialchars($row['product_name']) . '</h2>
                             <p>Size: ' . htmlspecialchars($row['size']) . '</p>
                <p>Price: $' . htmlspecialchars($row['product_price']) . '</p>
                <input type="radio" name="clothing_id" value="' . $row['product_id'] . '" required>
                <label for="duration">Rental Duration (days):</label>
                <input type="number" name="rental_duration" min="1" required>
              </div>';
    }
    
    echo '<button type="submit">Proceed to Checkout</button>
        </form>
    </body>
    </html>';
}

// checkout.php - Payment processing
if (isset($_GET['page']) && $_GET['page'] == 'checkout' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $clothing_id = $_POST['product_id'];
    $rental_duration = $_POST['rental_duration'];
    $user_id = 1; // Replace with actual user ID from session or login

    // Insert rental into database
    $stmt = $pdo->prepare("INSERT INTO rentals (user_id, order_id) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $order_id]);

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thank You</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; }
        </style>
    </head>
    <body>
        <h1>Thank You for Your Rental!</h1>
        <p>Your rental has been processed successfully.</p>
        <a href="?">Return to Homepage</a>
    </body>
    </html>';
}
?>