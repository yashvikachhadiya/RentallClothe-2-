<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);
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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ---------------- ADD PRODUCT ---------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($conn->real_escape_string($_POST['product_name']));
    $size = trim($conn->real_escape_string($_POST['size']));
    $price = floatval($_POST['product_price']);

    if (!empty($name) && !empty($size) && $price > 0) {
        $sql = "INSERT INTO add_product (product_name, size, product_price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $name, $size, $price);
        if ($stmt->execute()) {
            $success_msg = "Product added successfully!";
        } else {
            $error_msg = "Error adding product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill all fields with valid data.";
    }
}

/* ---------------- EDIT PRODUCT ---------------- */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $name = trim($conn->real_escape_string($_POST['product_name']));
    $size = trim($conn->real_escape_string($_POST['size']));
    $price = floatval($_POST['product_price']);

    if (!empty($name) && !empty($size) && $price > 0) {
        $sql = "UPDATE add_product SET product_name = ?, size = ?, product_price = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $name, $size, $price, $id);
        if ($stmt->execute()) {
            $success_msg = "Product updated successfully!";
            $edit_product = null;
        } else {
            $error_msg = "Error updating product: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "Please fill all fields with valid data.";
    }
}

/* ---------------- DELETE PRODUCT ---------------- */
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_msg = "Product deleted successfully!";
    } else {
        $error_msg = "Error deleting product.";
    }
    $stmt->close();
}

/* ---------------- FETCH PRODUCT FOR EDIT ---------------- */
$edit_product = null;
if (isset($_GET['product_id'])) {
    $edit_id = intval($_GET['product_id']);
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
    $stmt->close();
}

/* ---------------- SEARCH & FETCH ALL PRODUCTS ---------------- */
$search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';
$sql = "SELECT * FROM add_product";
if ($search) {
    $sql .= " WHERE product_name LIKE '%$search%' OR size LIKE '%$search%'";
}
$sql .= " ORDER BY product_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View & Manage Products - StyleShare Admin</title>
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

        /* Animated Background Particles */
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

        /* SIDEBAR */
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

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 10px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            color: var(--primary);
            border-left-color: var(--primary);
            background: rgba(255, 0, 110, 0.1);
            padding-left: 30px;
        }

        .sidebar-menu a i {
            font-size: 18px;
            width: 20px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
        }

        /* HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid rgba(255, 0, 110, 0.3);
            border-radius: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .back-link:hover {
            background: rgba(255, 0, 110, 0.2);
            border-color: var(--primary);
            transform: translateX(-5px);
        }

        /* ALERTS */
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

        .alert i {
            font-size: 20px;
        }

        /* CONTAINER */
        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* SECTION */
        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: var(--primary);
            font-size: 1.6rem;
        }

        /* GLASS CARD */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.1);
        }

        /* FORM GROUP */
        .form-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-template-columns: 1fr;
        }

        .form-group.three-col {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        input[type="email"],
        textarea,
        select {
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

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            background: rgba(255, 0, 110, 0.1);
            border-color: rgba(255, 0, 110, 0.5);
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.2);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-muted);
        }

        /* BUTTONS */
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
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

        .btn-secondary {
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid rgba(255, 0, 110, 0.3);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: rgba(255, 0, 110, 0.2);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 10px 16px;
            font-size: 0.85rem;
        }

        .btn-edit {
            background: rgba(58, 134, 255, 0.2);
            border: 1px solid rgba(58, 134, 255, 0.5);
            color: var(--accent);
        }

        .btn-edit:hover {
            background: rgba(58, 134, 255, 0.3);
            border-color: var(--accent);
        }

        .btn-delete {
            background: rgba(255, 0, 110, 0.2);
            border: 1px solid rgba(255, 0, 110, 0.5);
            color: var(--primary);
        }

        .btn-delete:hover {
            background: rgba(255, 0, 110, 0.35);
            border-color: var(--primary);
        }

        /* SEARCH */
        .search-form {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        .search-form input {
            flex: 1;
            max-width: 400px;
        }

        /* TABLE */
        .table-wrapper {
            overflow-x: auto;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
        }

        .products-table thead {
            background: rgba(255, 0, 110, 0.1);
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
        }

        .products-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .products-table td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-light);
        }

        .products-table tbody tr {
            transition: all 0.3s ease;
        }

        .products-table tbody tr:hover {
            background: rgba(255, 0, 110, 0.08);
        }

        .products-table tbody tr:last-child td {
            border-bottom: none;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .no-data i {
            display: block;
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--text-muted);
            opacity: 0.5;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .main-content {
                padding: 25px;
            }

            .form-group {
                grid-template-columns: 1fr;
            }

            .form-group.three-col {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .form-group {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .table-wrapper {
                font-size: 0.9rem;
            }

            .products-table th,
            .products-table td {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 20px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .products-table th,
            .products-table td {
                padding: 8px;
                font-size: 0.8rem;
            }

            .btn-sm {
                padding: 8px 12px;
                font-size: 0.75rem;
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
        <ul class="sidebar-menu">
            <li><a href="user_details.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clothform.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <li><a href="viewproduct.php"><i class="fas fa-cubes"></i> View Products</a></li>
            <li><a href="admin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-container">
            <!-- HEADER -->
            <div class="page-header">
                <h1 class="page-title"><i class="fas fa-cubes"></i> Product Inventory</h1>
                <a href="user_details.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
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

            <!-- ADD/EDIT FORM -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-<?php echo $edit_product ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?>
                </h2>
                <div class="glass-card">
                    <form method="POST">
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit_product['product_id']) ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_product" value="1">
                        <?php endif; ?>

                        <div class="form-group three-col">
                            <div class="input-group">
                                <label for="product_name">Product Name</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" id="product_name" name="product_name" placeholder="e.g., Luxury Lehenga" value="<?= $edit_product ? htmlspecialchars($edit_product['product_name']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="size">Size</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-ruler"></i>
                                    <input type="text" id="size" name="size" placeholder="e.g., S, M, L, XL" value="<?= $edit_product ? htmlspecialchars($edit_product['size']) : '' ?>" required>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="product_price">Price (₹)</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-rupee-sign"></i>
                                    <input type="number" id="product_price" name="product_price" placeholder="e.g., 5000" step="0.01" value="<?= $edit_product ? htmlspecialchars($edit_product['product_price']) : '' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
                            </button>
                            <?php if ($edit_product): ?>
                                <a href="viewproduct.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SEARCH SECTION -->
            <div class="section">
                <h2 class="section-title"><i class="fas fa-search"></i> Search Products</h2>
                <div class="search-form">
                    <form method="GET" style="display: flex; gap: 12px; width: 100%;">
                        <div class="input-wrapper" style="flex: 1; max-width: 400px;">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" placeholder="Search by name or size..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Search</button>
                        <?php if ($search): ?>
                            <a href="viewproduct.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- PRODUCTS TABLE -->
            <div class="section">
                <h2 class="section-title"><i class="fas fa-list"></i> All Products</h2>
                <div class="table-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['product_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['size']) ?></td>
                                        <td>₹<?= number_format($row['product_price'], 2) ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a class="btn btn-edit btn-sm" href="viewproduct.php?product_id=<?= $row['product_id'] ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a class="btn btn-delete btn-sm" href="viewproduct.php?delete_id=<?= $row['product_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="no-data">
                                            <i class="fas fa-inbox"></i>
                                            <p><?php echo $search ? 'No products found matching your search.' : 'No products available. Add one to get started!'; ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        
        // Close sidebar on mobile when clicking links
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>
<?php $conn->close(); ?>
