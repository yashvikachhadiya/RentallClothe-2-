<?php
session_start();
$conn = new mysqli("localhost", "root", "", "rentalcloth");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = null;

if ($id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = $id";
    $result = $conn->query($sql);
    if($result->num_rows > 0) $item = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $item ? $item['product_name'] : 'Product Details'; ?> | StyleShare</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* =========================
       UNIQUE VARIABLES & RESET
       ========================= */
    :root {
        --primary: #FF006E;
        --primary-dark: #C2185B;
        --primary-light: #FF4D8F;
        --secondary: #8338EC;
        --accent: #3A86FF;
        --accent-2: #06FFA5;
        --text-main: #0D1B2A;
        --text-light: #415A77;
        --bg-dark: #0A0E27;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        --gradient-2: linear-gradient(135deg, #FF006E 0%, #8338EC 50%, #3A86FF 100%);
        --gradient-3: linear-gradient(135deg, #06FFA5 0%, #3A86FF 100%);
        --shadow-neon: 0 0 20px rgba(255, 0, 110, 0.5), 0 0 40px rgba(131, 56, 236, 0.3);
        --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.1);
        --shadow-float: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
    }
    
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
        background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        color: var(--text-main);
        min-height: 100vh;
        padding: 0;
        position: relative;
        overflow-x: hidden;
        line-height: 1.6;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    /* Animated Background Particles */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(131, 56, 236, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 40% 20%, rgba(58, 134, 255, 0.1) 0%, transparent 50%);
        animation: particleFloat 20s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }
    
    @keyframes particleFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    a { 
        text-decoration: none; 
        color: inherit; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    }

    h1, h2, h3 { 
        font-family: 'Playfair Display', serif; 
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    /* =========================
       UNIQUE GLASSMORPHIC HEADER
       ========================= */
    header {
        background: rgba(10, 14, 39, 0.7);
        backdrop-filter: blur(30px) saturate(180%);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    header.scrolled {
        background: rgba(10, 14, 39, 0.95);
        box-shadow: 0 12px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 0, 110, 0.2);
    }
    .header-container { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        padding: 20px 0; 
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
        padding-left: 30px;
        padding-right: 30px;
    }
    .logo { 
        font-size: 2rem; 
        font-weight: 700; 
        font-family: 'Playfair Display', serif;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.4s ease;
        position: relative;
    }
    .logo:hover {
        transform: scale(1.08) translateY(-2px);
        filter: drop-shadow(0 0 10px rgba(255, 0, 110, 0.8));
    }
    .logo span { 
        background: var(--gradient-3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .back-link {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
        font-size: 14px;
        padding: 10px 20px;
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.4s ease;
    }
    .back-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.4);
        text-shadow: 0 0 10px rgba(255, 0, 110, 0.8);
    }

    /* =========================
       PRODUCT DETAIL CONTAINER
       ========================= */
    .detail-wrapper {
        max-width: 1200px;
        margin: 120px auto 60px;
        padding: 0 30px;
        position: relative;
        z-index: 1;
    }

    .detail-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: start;
    }

    /* Product Image Card */
    .product-image-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        padding: 30px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
        height: fit-content;
    }
    .product-image-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }
    .product-image-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 0, 110, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.6s ease;
    }
    .product-image-card:hover::after {
        opacity: 1;
    }

    .product-image-box {
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(255, 0, 110, 0.1) 0%, rgba(131, 56, 236, 0.1) 100%);
        aspect-ratio: 3/4;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .product-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .product-image-card:hover .product-image-box img {
        transform: scale(1.05);
    }

    /* Product Info Card */
    .product-info-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px) saturate(180%);
        padding: 50px 40px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }
    .product-info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--gradient-2);
        box-shadow: 0 0 20px var(--primary);
    }

    .product-name {
        font-size: 2.8rem;
        margin-bottom: 20px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5));
    }

    .product-price {
        font-size: 2.2rem;
        color: var(--accent-2);
        font-weight: 700;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .product-price span {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 400;
    }

    .product-description {
        color: rgba(255, 255, 255, 0.8);
        line-height: 1.9;
        margin-bottom: 40px;
        font-size: 1rem;
    }

    /* Meta Information Grid */
    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
        padding: 30px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
    .meta-item {
        position: relative;
    }
    .meta-label {
        display: block;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 8px;
        letter-spacing: 1px;
    }
    .meta-value {
        font-weight: 600;
        font-size: 1.3rem;
        color: white;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .meta-value.status {
        color: var(--accent-2);
        background: none;
        -webkit-text-fill-color: unset;
    }

    /* Buttons */
    .btn-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    .btn {
        padding: 18px 30px;
        text-align: center;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 1px;
        font-size: 14px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-rent {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px) saturate(180%);
        color: white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    .btn-rent::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient-2);
        transition: left 0.5s ease;
        z-index: -1;
    }
    .btn-rent:hover::before {
        left: 0;
    }
    .btn-rent:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.4);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-back {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .btn-back::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transition: left 0.5s ease;
        z-index: -1;
    }
    .btn-back:hover::before {
        left: 0;
    }
    .btn-back:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.15);
    }

    /* Not Found Section */
    .not-found {
        grid-column: 1 / -1;
        text-align: center;
        padding: 100px 30px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(30px);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .not-found h2 {
        font-size: 2.5rem;
        margin-bottom: 30px;
        background: var(--gradient-2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .not-found p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-container {
            padding-left: 20px;
            padding-right: 20px;
        }
        .logo { font-size: 1.5rem; }
        
        .detail-wrapper {
            margin: 100px auto 40px;
            padding: 0 20px;
        }
        
        .detail-container {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .product-name {
            font-size: 2rem;
        }

        .product-price {
            font-size: 1.8rem;
        }

        .meta-grid {
            grid-template-columns: 1fr;
        }

        .btn-row {
            grid-template-columns: 1fr;
        }

        .btn {
            padding: 15px 20px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        header { padding: 15px 20px; }
        .header-container { padding: 15px; }
        
        .detail-wrapper {
            margin: 90px auto 30px;
            padding: 0 15px;
        }

        .product-name {
            font-size: 1.5rem;
        }

        .product-price {
            font-size: 1.4rem;
        }

        .product-info-card {
            padding: 30px 20px;
        }

        .meta-grid {
            padding: 20px;
        }

        .meta-label {
            font-size: 0.75rem;
        }

        .meta-value {
            font-size: 1rem;
        }

        .btn {
            padding: 12px 15px;
            font-size: 12px;
        }
    }
</style>
</head>
<body>

    <header>
        <div class="header-container">
            <div class="logo">Style<span>Share</span></div>
            <div class="header-actions">
                <a href="collection.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Collection</a>
            </div>
        </div>
    </header>

    <div class="detail-wrapper">
        <?php if ($item): ?>
            <div class="detail-container">
                <!-- Product Image Section -->
                <div class="product-image-card">
                    <div class="product-image-box">
                        <?php 
                            $productImage = '';
                            if (!empty($item['product_image1'])) {
                                $productImage = $item['product_image1'];
                            } elseif (!empty($item['product_image'])) {
                                $productImage = $item['product_image'];
                            } elseif (!empty($item['image'])) {
                                $productImage = $item['image'];
                            }
                            $imagePath = !empty($productImage) ? 'clothes/img/' . htmlspecialchars($productImage) : '';
                        ?>
                        <?php if ($imagePath): ?>
                            <img src="<?php echo $imagePath; ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                 loading="eager"
                                 onerror="this.src='https://via.placeholder.com/600x800?text=<?php echo urlencode($item['product_name']); ?>'">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="font-size: 3rem; color: rgba(255, 255, 255, 0.3);"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Info Section -->
                <div class="product-info-card">
                    <h1 class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></h1>
                    
                    <div class="product-price">
                        <span style="font-size: 2.2rem; margin-bottom: 0;">₹<?php echo number_format($item['product_price']); ?></span>
                        <span>/ day</span>
                    </div>

                    <p class="product-description">
                        <?php echo nl2br(htmlspecialchars($item['Description'] ?? 'Premium rental item. Experience luxury fashion with StyleShare.')); ?>
                    </p>

                    <!-- Meta Information -->
                    <div class="meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Size</span>
                            <span class="meta-value"><?php echo htmlspecialchars($item['size'] ?? 'Standard'); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Deposit Required</span>
                            <span class="meta-value">₹<?php echo number_format($item['deposite'] ?? 0); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Availability</span>
                            <span class="meta-value status">In Stock</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Product ID</span>
                            <span class="meta-value">#<?php echo $item['product_id']; ?></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-row">
                        <a href="collection.php" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Keep Browsing
                        </a>
                        <a href="rent_now.php?id=<?php echo $item['product_id']; ?>" class="btn btn-rent">
                            <i class="fas fa-tag"></i> Rent This Outfit
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="detail-container">
                <div class="not-found">
                    <h2><i class="fas fa-search"></i> Product Not Found</h2>
                    <p>Sorry, we couldn't find the product you're looking for.</p>
                    <a href="collection.php" class="btn btn-rent" style="display: inline-flex;">
                        <i class="fas fa-arrow-left"></i> Return to Collection
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Header scroll effect
        let lastScroll = 0;
        let ticking = false;

        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const header = document.querySelector('header');
                    const scrollY = window.scrollY;
                    
                    if (scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                    
                    lastScroll = scrollY;
                    ticking = false;
                });
                ticking = true;
            }
        });
    </script>

</body>
</html>