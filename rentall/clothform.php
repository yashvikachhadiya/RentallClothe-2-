<?php
session_start();

// Check if admin is logged in
if (empty($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

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

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $size = isset($_POST['size']) ? trim($_POST['size']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';

    $error_msg = '';
    $success_msg = '';

    // Validate inputs
    if ($name === "" || $size === "" || $price === "" || $category === "") {
        $error_msg = "Please fill all fields.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error_msg = "Please enter a valid price.";
    } else {
        // Handle uploaded image
        $photoName = "";
        if (!empty($_FILES["photo"]["name"])) {
            $targetDir = "clothes/img/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
            $targetFile = $targetDir . $photoName;

            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedTypes) && $_FILES["photo"]["size"] <= 5 * 1024 * 1024) {
                if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                    $error_msg = "File upload failed. Try again.";
                    $photoName = "";
                }
            } else {
                $error_msg = "Invalid file type or file size (max 5MB).";
                $photoName = "";
            }
        }

        // Insert into database if image uploaded and no errors
        if ($photoName != "" && $error_msg === "") {
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $qty = 1;
            $price = floatval($price); // Convert to float
            
            // Insert with description column
            $sql = "INSERT INTO add_product (product_name, size, product_image1, product_price, description, category, qty) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt === false) {
                $error_msg = "Database Error: " . $conn->error;
            } else {
                // Bind parameters in exact SQL order: product_name(s), size(s), product_image1(s), product_price(d), description(s), category(s), qty(i)
                $stmt->bind_param("sssdssi", $name, $size, $photoName, $price, $description, $category, $qty);
                
                if ($stmt->execute()) {
                    $success_msg = "Product added successfully!";
                    // Redirect after 1.5 seconds
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'viewproduct.php';
                        }, 1500);
                    </script>";
                } else {
                    $error_msg = "Database Error: " . $stmt->error;
                }
                
                $stmt->close();
            }
        } elseif ($error_msg === "") {
            $error_msg = "Please upload a product image.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - StyleShare Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF006E;
            --primary-dark: #C2185B;
            --secondary: #8338EC;
            --accent: #3A86FF;
            --accent-2: #06FFA5;
            --bg-dark: #0A0E27;
            --bg-dark-2: #0F1535;
            --text-light: #E8EAED;
            --text-muted: #A0A3A8;
            --gradient-2: linear-gradient(135deg, #FF006E 0%, #8338EC 50%, #3A86FF 100%);
            --gradient-3: linear-gradient(135deg, #06FFA5 0%, #3A86FF 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-dark-2) 100%);
            color: var(--text-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(131, 56, 236, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
            animation: particleFloat 20s ease-in-out infinite;
        }

        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(30px, -30px); }
            50% { transform: translate(-20px, 20px); }
            75% { transform: translate(20px, 30px); }
        }

        .page-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: rgba(15, 21, 53, 0.8);
            backdrop-filter: blur(30px);
            border-right: 1px solid rgba(255, 0, 110, 0.1);
            padding: 30px 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 25px 30px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
            text-align: center;
        }

        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar ul li a:hover {
            color: var(--primary);
            border-left-color: var(--primary);
            background: rgba(255, 0, 110, 0.1);
            padding-left: 30px;
        }

        .sidebar ul li a i {
            font-size: 18px;
            width: 20px;
        }

        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            backdrop-filter: blur(20px);
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-success {
            background: rgba(6, 255, 165, 0.1);
            border: 1px solid rgba(6, 255, 165, 0.3);
            color: var(--accent-2);
        }

        .alert-error {
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid rgba(255, 0, 110, 0.3);
            color: var(--primary);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.1);
        }

        .form-section {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label i {
            color: var(--primary);
            font-size: 1.1rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            color: var(--primary);
            font-size: 1.1rem;
            pointer-events: none;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 14px 15px 14px 45px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        textarea {
            padding: 14px 15px;
            resize: vertical;
            min-height: 100px;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            background: rgba(255, 0, 110, 0.1);
            border-color: rgba(255, 0, 110, 0.5);
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.2);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-muted);
        }

        select option {
            background: var(--bg-dark);
            color: var(--text-light);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        button[type="submit"],
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--gradient-2);
            color: white;
            position: relative;
            overflow: hidden;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-3);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(255, 0, 110, 0.4);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">StyleShare</div>
        </div>
        <ul>
            <li><a href="user_details.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clothform.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <li><a href="viewproduct.php"><i class="fas fa-cubes"></i> View Products</a></li>
            <li><a href="admin.php" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-plus-circle"></i> Add New Product</h1>
        </div>

        <!-- ALERTS -->
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <div class="glass-card">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-tag"></i> Product Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag"></i>
                                <input type="text" id="name" name="name" placeholder="e.g., Luxury Lehenga" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category"><i class="fas fa-list"></i> Category</label>
                            <div class="input-wrapper">
                                <i class="fas fa-list"></i>
                                <select id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="lahenga">Lahenga</option>
                                    <option value="saree">Saree</option>
                                    <option value="gown">Gown</option>
                                    <option value="choli">Choli</option>
                                    <option value="kurti">Kurti</option>
                                    <option value="jewellery">Jewellery</option>
                                    <option value="co-ords">Co-ords</option>
                                    <option value="suit">Men's Suits</option>
                                    <option value="sherwani">Sherwani</option>
                                    <option value="kurta">Men's Kurta</option>
                                    <option value="kids">Kids</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="size"><i class="fas fa-ruler"></i> Size</label>
                            <div class="input-wrapper">
                                <i class="fas fa-ruler"></i>
                                <input type="text" id="size" name="size" placeholder="e.g., S, M, L, XL" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="price"><i class="fas fa-rupee-sign"></i> Price (₹)</label>
                            <div class="input-wrapper">
                                <i class="fas fa-rupee-sign"></i>
                                <input type="number" id="price" name="price" placeholder="e.g., 5000" step="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo"><i class="fas fa-image"></i> Product Image</label>
                        <div class="input-wrapper">
                            <i class="fas fa-image"></i>
                            <input type="file" id="photo" name="photo" accept="image/*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-info-circle"></i> Description (Optional)</label>
                        <textarea id="description" name="description" placeholder="Add product details, material, care instructions, etc."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Product
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "admin.php";
        }
    }
</script>
</body>
</html>
