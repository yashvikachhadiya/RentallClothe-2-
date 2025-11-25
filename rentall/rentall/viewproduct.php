<?php


// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Create connection
$conn = new mysqli($servername, $username, $password);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
if (!$conn->select_db($database)) {
    die("Database 'rentalcloth' not found. Please create the database.");
}

// Check if clothes table exists
// $table_check = $conn->query("SHOW TABLES LIKE 'clothes'");
// if ($table_check->num_rows == 0) {
//     die("Table 'clothes' not found. Please create the table with the correct structure.");
// }

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $edit_id = $conn->real_escape_string($_POST['product_id']);
    $name = $conn->real_escape_string($_POST['product_name']);
    $size = $conn->real_escape_string($_POST['size']);
    $price = $conn->real_escape_string($_POST['product_price']);
   // $category = $conn->real_escape_string($_POST['category']);

    $sql = "UPDATE add_product SET product_name = ?, size = ?, product_price = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $product_name, $size, $product_price, $product_id);
    if ($stmt->execute()) {
        header("Location: viewproduct.php");
        exit;
    } else {
        echo "<p class='error'>Error updating product: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: viewproduct.php");
        exit;
    } else {
        echo "<p class='error'>Error deleting product: " . $conn->error . "</p>";
    }
    $stmt->close();
}

// Fetch product for edit form
$edit_product = null;
if (isset($_GET['product_id'])) {
    $edit_id = $conn->real_escape_string($_GET['product_id']);
    $sql = "SELECT product_id, product_name, size, product_price FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
    $stmt->close();
}

// Initialize search query

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Fetch products from add_product table
$sql = "SELECT product_id, product_name, size, product_price FROM add_product";

if ($search) {
    // Use the correct column name 'product_name' in WHERE clause
    $sql .= " WHERE product_name LIKE '%$search%'";
}

$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
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
            width: 900px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
        }

        .search-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .search-form input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-form button {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .search-form button:hover {
            background: #5a6cd1;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .product-table th, .product-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .product-table th {
            background: #667eea;
            color: white;
        }

        .product-table tr:nth-child(even) {
            background: #f2f2f2;
        }

        .product-table td {
            font-size: 14px;
        }

        .delete-btn {
            padding: 6px 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .edit-btn {
            padding: 6px 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        .edit-form {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }

        .submit-btn {
            padding: 8px 16px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #27ae60;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 20px;
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
            
            <li><a href="Viewproduct.php">View Product</a></li>
            <li><a href="#" onclick="confirmLogout()">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>View Products</h2>
            <?php if ($edit_product): ?>
                <div class="edit-form">
                    <h3>Edit Product</h3>
                    <form method="POST" action="viewproduct.php">
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_product['id']); ?>">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_product['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="size">Size</label>
                            <input type="text" id="size" name="size" value="<?php echo htmlspecialchars($edit_product['size']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($edit_product['price']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($edit_product['category']); ?>" required>
                        </div>
                        <button type="submit" class="submit-btn">Update Product</button>
                    </form>
                </div>
            <?php endif; ?>
            <form class="search-form" method="GET" action="viewproduct.php">
                <input type="text" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Size</th>
                        <th>Price</th>  
                    <!--<th>Category</th>-->
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                      if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['size']) . "</td>";
                            echo "<td>$" . number_format($row['product_price']). "</td>";
                           // echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td><button class='edit-btn' onclick='window.location.href=\"viewproduct.php?product_id=" . $row['product_id'] . "\"'>Edit</button></td>";
                            echo "<td><button class='delete-btn' onclick='confirmDelete(" . $row['product_id'] . ")'>Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No products available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "index.php";
            }
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this product?")) {
                window.location.href = "viewproduct.php?delete_id=" + product_id;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>