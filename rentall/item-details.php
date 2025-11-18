<?php
// =================================================================
// 1. PHP SESSION & DATABASE CONNECTION
// =================================================================
session_start();
$user_name = $_SESSION['user_name'] ?? '';

// Database config
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

// Get item ID safely
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

// Fetch Product Data
if ($item_id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['product_name']) : 'Item Not Found'; ?> | StyleShare</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================
           VARIABLES & RESET
           ========================= */
        :root {
            --primary: #FF3F6C;
            --primary-hover: #d32f2f;
            --text-dark: #1a1a1a;
            --text-gray: #666;
            --bg-body: #f9f9f9;
            --white: #ffffff;
            --border: #eaeaec;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            color: var(--text-dark); 
            background-color: var(--bg-body); 
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; transition: 0.3s; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }

        /* =========================
           HEADER (Consistent with Collection)
           ========================= */
        header {
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .header-container {
            max-width: 1200px; margin: 0 auto; padding: 0 20px;
            display: flex; justify-content: space-between; align-items: center;
        }

        .logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; }
        .logo span { color: var(--primary); }

        .nav-links { display: flex; gap: 30px; }
        .nav-links a { font-weight: 500; font-size: 14px; }
        .nav-links a:hover { color: var(--primary); }

        .user-section { font-size: 14px; font-weight: 600; }

        /* =========================
           PRODUCT DETAIL LAYOUT
           ========================= */
        .main-container {
            max-width: 1200px; margin: 0 auto; padding: 120px 20px 60px;
        }

        /* Breadcrumb */
        .breadcrumb {
            font-size: 13px; color: var(--text-gray); margin-bottom: 30px;
        }
        .breadcrumb span { margin: 0 10px; color: #ccc; }
        .breadcrumb a:hover { color: var(--primary); }

        /* Grid */
        .product-wrapper {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 50px;
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        }

        /* Left: Image */
        .image-container {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background: #f0f0f0;
            height: 550px; /* Fixed height for elegance */
        }
        .image-container img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.5s;
        }
        .image-container:hover img { transform: scale(1.05); }

        /* Right: Details */
        .details-container { padding: 10px 0; }

        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px; line-height: 1.2;
            margin-bottom: 10px; color: var(--text-dark);
        }

        .prices-box {
            display: flex; align-items: baseline; gap: 15px;
            margin-bottom: 25px; padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .price { font-size: 28px; font-weight: 700; color: var(--primary); }
        .price span { font-size: 14px; font-weight: 400; color: var(--text-gray); }
        .deposit { font-size: 14px; background: #eef2ff; color: #4f46e5; padding: 5px 10px; border-radius: 4px; font-weight: 600; }

        .info-row { margin-bottom: 15px; font-size: 15px; }
        .info-label { font-weight: 700; color: var(--text-dark); width: 100px; display: inline-block; }
        .description { color: var(--text-gray); line-height: 1.6; margin-bottom: 30px; }

        /* Trust Badges */
        .trust-badges {
            display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;
        }
        .badge-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-gray); }
        .badge-item i { color: var(--primary); font-size: 16px; }

        /* Buttons */
        .action-buttons { display: flex; gap: 20px; margin-top: 20px; }
        
        .btn-rent {
            flex: 2;
            background-color: var(--primary); color: white;
            padding: 15px; border-radius: 8px;
            font-size: 16px; font-weight: 600; text-align: center;
            border: none; cursor: pointer; transition: 0.3s;
            text-transform: uppercase; letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(255, 63, 108, 0.3);
        }
        .btn-rent:hover { background-color: var(--primary-hover); transform: translateY(-3px); }

        .btn-back {
            flex: 1;
            background-color: white; color: var(--text-dark);
            padding: 15px; border-radius: 8px;
            font-size: 16px; font-weight: 600; text-align: center;
            border: 1px solid var(--border); cursor: pointer; transition: 0.3s;
        }
        .btn-back:hover { background-color: #f5f5f5; border-color: var(--text-dark); }

        /* Error Box */
        .error-box {
            text-align: center; padding: 100px 20px;
        }
        .error-box h2 { margin-bottom: 20px; }

        /* Responsive */
        @media (max-width: 900px) {
            .product-wrapper { grid-template-columns: 1fr; gap: 30px; }
            .image-container { height: 400px; }
            .product-title { font-size: 28px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="header-container">
            <div class="logo">Style<span>Share</span></div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="collection.php">Collection</a>
                <a href="aboutus.php">About</a>
                <a href="contactus.php">Contact</a>
            </nav>
            <div class="user-section">
                <?php if (!empty($user_name)) echo "Hi, " . htmlspecialchars($user_name); ?>
            </div>
        </div>
    </header>

    <div class="main-container">
        
        <?php if ($product): ?>
            
            <div class="breadcrumb">
                <a href="index.php">Home</a> <span>/</span> 
                <a href="collection.php">Collection</a> <span>/</span> 
                Current Item
            </div>

            <div class="product-wrapper">
                
                <div class="image-container">
                    <img src="clothes/img/<?php echo htmlspecialchars($product['product_image1']); ?>" 
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         onerror="this.src='https://via.placeholder.com/500x600?text=No+Image';">
                </div>

                <div class="details-container">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    
                    <div class="prices-box">
                        <div class="price">
                            ₹<?php echo number_format($product['product_price']); ?> 
                            <span>/ day</span>
                        </div>
                        <?php if(!empty($product['deposite'])): ?>
                            <div class="deposit">
                                Refundable Deposit: ₹<?php echo number_format($product['deposite']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Size:</span> 
                        <span style="background:#333; color:white; padding:4px 12px; border-radius:4px; font-size:13px;">
                            <?php echo htmlspecialchars($product['size']); ?>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Description:</span>
                    </div>
                    <p class="description">
                        <?php 
                            // If description is empty, show default text
                            echo !empty($product['Description']) ? nl2br(htmlspecialchars($product['Description'])) : "Experience luxury and comfort with this premium outfit. Perfect for weddings, parties, and special occasions. Dry cleaned and sanitized for your safety."; 
                        ?>
                    </p>

                    <div class="trust-badges">
                        <div class="badge-item"><i class="fas fa-tshirt"></i> Premium Quality</div>
                        <div class="badge-item"><i class="fas fa-pump-soap"></i> Dry Cleaned</div>
                        <div class="badge-item"><i class="fas fa-truck"></i> Fast Delivery</div>
                        <div class="badge-item"><i class="fas fa-check-circle"></i> Authenticity Guaranteed</div>
                    </div>

                    <div class="action-buttons">
                        <a href="javascript:history.back()" class="btn-back">Go Back</a>
                        <a href="buy_now.php?id=<?php echo $product['product_id']; ?>" class="btn-rent">Rent Now</a>
                    </div>

                </div>
            </div>

        <?php else: ?>
            
            <div class="error-box">
                <h2>Oops! Item Not Found.</h2>
                <p>The item you are looking for might have been removed or the ID is incorrect.</p>
                <br>
                <a href="collection.php" class="btn-rent" style="display:inline-block; width:auto; padding:10px 30px;">Go to Collection</a>
            </div>

        <?php endif; ?>

    </div>

    <?php $conn->close(); ?>

</body>
</html>