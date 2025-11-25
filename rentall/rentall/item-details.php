<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get item ID
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Details</title>
    <style>
	
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #dfdfe8;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 100px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            padding: 17px;
            gap: 30px;
        }

        .left, .right {
            flex: 1;
        }

        .left img {
            width: 100%;
            border-radius: 10px;
        }

        .right h1 {
            margin-top: 0;
            font-size: 2em;
            color: #222;
        }

        .right p {
            font-size: 1.1em;
            margin: 10px 0;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-right: 10px;
        }

        .btn:hover {
            background-color: #ff5252;
        }

        .btn.secondary {
            background-color: transparent;
            color: #ff6b6b;
            border: 2px solid #ff6b6b;
        }

        .btn.secondary:hover {
            background-color: #ff6b6b;
            color: white;
        }

        @media (max-width: 668px) {
            .container {
                flex-direction: column;
                text-align: center;
            }

            .right {
                text-align: center;
            }
        }
    </style>
</head>
<body>

<?php
if ($item_id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<div class='container'>";
        echo "<div class='left'>";
        echo "<img src='clothes/img/" . htmlspecialchars($row['product_image1']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>";
        echo "</div>";
        echo "<div class='right'>";
      //  echo "<h1>" . htmlsepecialchars($row['product_name']) . "</h1>";
		//echo "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($row['Description'])) . "</p>";
        echo "<p><strong>Size:</strong> " . htmlspecialchars($row['size']) . "</p>";
        echo "<p><strong>Price:</strong> ₹" . htmlspecialchars($row['product_price']) . "</p>";
       // echo "<p><strong>deposite:</strong> ₹" . htmlspecialchars($row['deposite']) . "</p>";
        echo "<a href='javascript:history.back()' class='btn secondary'>← Back</a>";
        echo '<a href="buy_now.php?id=' . $row['product_id'] . '" class="btn">Buy Now</a>';
        echo "</div>";
        echo "</div>";
    } else {
        echo "<p style='text-align: center;'>Item not found.</p>";
    }

    $stmt->close();
} else {
    //echo "<p style='text-align: center;'>Invalid item.</p>";
    //<a href="buy_now.php">confirm order</a>
}
//$conn->close();
?>

</body>
</html>
