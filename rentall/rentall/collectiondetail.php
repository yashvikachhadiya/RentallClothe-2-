<?php
// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection</title>
    <style>
	body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: #ff6b6b;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn.secondary {
            background-color: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
        }

        .btn.secondary:hover {
            background-color: #ff6b6b;
            color: white;
        }

        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.5rem;
            color: #333;
        }

        .section-subtitle {
            text-align: center;
            margin-bottom: 60px;
            color: #666;
            font-size: 1.2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
	
	/* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff6b6b;
        }

        .logo span {
            color: #333;
        }

        .nav-links {
            display: flex;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ff6b6b;
        }

        .header-buttons {
            display: flex;
            align-items: center;
        }

        .header-buttons .btn {
            margin-left: 15px;
        }
		
 body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    text-align: center;
}

.collection-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 50px;
    padding: 20px;
    max-width: 1050px;
    margin: auto;
}

.item {
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.item:hover {
    transform: translateY(-5px);
    box-shadow: 0px 8px 20px black;
}

.item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    transition: opacity 0.3s ease;
}

.item img:hover {
    opacity: 0.9;
}

.item h3 {
    margin: 12px 0;
    font-size: 20px;
    color: #333;
}

.item p {
    color: #666;
    font-size: 16px;
}

@media (max-width: 1024px) {
    .collection-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .collection-grid {
        grid-template-columns: repeat(1, 1fr);
    }
}

    </style>
</head>
<body>
<!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">Style<span>Share</span></div>
            <ul class="nav-links">
                <li><a href="page1.php">Home</a></li>
                <li><a href="collection.html">Collection</a></li>
                <li><a href="aboutus.html">about us</a></li>
                <li><a href="contactus.php">Contact</a></li>
            </ul>
            <div class="header-buttons">
                <!-- <a href="#" class="btn secondary">Log out</a> -->
                 <a href="index.php" class="btn">Log out</a>
 
                <!-- <a href="signup.php" class="btn">Sign Up</a> -->
            </div>
        </div>
    </header> <br></br><br></br>

<div class='collection-grid'>
<?php
if ($category) {
    // Query to fetch items of selected category
    $sql="SELECT product_Id,product_name,product_price,product_image1,size from add_product";
  //  $sql = "SELECT id, name, photo, size, price FROM clothes WHERE category = ?";
    $stmt = $conn->prepare($sql);
   // $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<a href='item-details.php?id=" . urlencode($row['product_Id']) . "' style='text-decoration: none; color: inherit;'>";
     	echo "<div class='item'>";
        echo "<img src='clothes/img/" . htmlspecialchars($row['product_image1']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>";
        echo "<h3>" . htmlspecialchars($row['product_name']) . "</h3>";
        echo "<p>Size: " . htmlspecialchars($row['size']) . "</p>";
        echo "<p>Price: &#8377;" . htmlspecialchars($row['product_price']) . "</p>";
        echo "</div>";
    }

    $stmt->close();
} else {
    echo "<p>No category selected.</p>";
}
?>
</div>

</body>
</html>
<?php
//$conn->close();
?>
