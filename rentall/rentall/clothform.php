<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search query
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $name = $conn->real_escape_string($_POST['name']);
    $size = $conn->real_escape_string($_POST['size']);
    $price = $conn->real_escape_string($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    

    // Handle uploaded image
    //$targetDir = "uploads/"; // Make sure this directory exists and is writable
    // $photoName = basename($_FILES["photo"]["name"]);
    // $targetFile = $targetDir . time() . "_" . $photoName; // Avoid filename conflicts
    // $uploadOk = 1;

    // $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    // $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // if (!in_array($imageFileType, $allowedTypes)) {
    //     echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
    //     $uploadOk = 0;
    // }

    // if ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {
    //     echo "File is too large. Max 2MB allowed.";
    //     $uploadOk = 0;
    // }

    //if ($uploadOk && move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO add_product ( product_name, size, product_image1, product_price, qty) VALUES (?, ?, ?, ?, ?)");
       // $stmt->bind_param("sssii", 'test', '1',"test",12, 1);
        //if ($stmt->execute()) {
            echo "<script>alert('Product added successfully.'); window.location.href='clothform.php';</script>";
        //} else {
            echo "Error: " . $stmt->error;
        }

       // $stmt->close();
    // //} else {
    //    echo "Error uploading file.";
    //}
//}
//$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 24px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background: #34495e;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        .form-group {
            position: relative;
            margin-bottom: 30px;
            margin-right: 38px;
        }

        .form-group label {
            position: absolute;
            top: 12px;
            left: 16px;
            transition: 0.3s;
            pointer-events: none;
            color: #777;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: #667eea;
        }

        .form-group input:focus + label, 
        .form-group input:not(:placeholder-shown) + label {
            top: -8px;
            left: 12px;
            font-size: 12px;
            color: #667eea;
            background: #fff;
            padding: 0 5px;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
		          <li><a href="clothform.php">Add Product</a></li>
            <li><a href="user_details.php">View User Details</a></li>
           
            <li><a href="viewr.php">View Registrations</a></li>
            <li><a href="viewproduct.php">View Products</a></li>
              <li><a href="#" onclick="confirmLogout()">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>Product Form</h2>
            <form id="productForm" action="clothform.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" id="name" name="name" required placeholder=" ">
                    <label for="name">Name</label>
                </div>
                <div class="form-group">
                    <input type="file" id="photo" name="photo" required>
                    <label for="photo">Photo</label>
                </div>
                <div class="form-group">
                    <input type="text" id="size" name="size" required placeholder=" ">
                    <label for="size">Size</label>
                </div>
                <div class="form-group">
                    <input type="text" id="price" name="price" required placeholder=" ">
                    <label for="price">Price</label>
                </div>
                <div class="form-group">
                    <input type="text" id="category" name="category" required placeholder=" ">
                    <label for="category">Category</label>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
				<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "index.php";
        }
    }
</script>
</body>
</html>