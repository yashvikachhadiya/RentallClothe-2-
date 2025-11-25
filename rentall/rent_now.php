<?php
session_start();
$user_name = $_SESSION['user_name'] ?? '';

$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = null;

if ($item_id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    }
    $stmt->close();
}

$deposit_amount = isset($item['deposite']) ? $item['deposite'] : 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | StyleShare</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #FF006E;
            --secondary: #8338EC;
            --accent: #3A86FF;
            --accent-2: #06FFA5;
            --bg-dark: #0A0E27;
            --bg-dark-2: #0F1535;
            --text-light: rgba(255, 255, 255, 0.7);
            --text-lighter: rgba(255, 255, 255, 0.4);
            --gradient-1: linear-gradient(135deg, #FF006E, #8338EC);
            --gradient-2: linear-gradient(135deg, #FF006E, #3A86FF);
            --shadow-glass: 0 8px 32px 0 rgba(255, 0, 110, 0.1);
            --shadow-neon: 0 0 20px rgba(255, 0, 110, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: white;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(51, 56, 236, 0.15) 0%, transparent 50%),
                        var(--bg-dark);
            z-index: -1;
        }

        header {
            background: rgba(15, 21, 53, 0.6);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-glass);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--accent-2);
            font-weight: 600;
            font-size: 14px;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 35px;
            padding: 0 25px;
        }

        .form-section {
            background: rgba(15, 21, 53, 0.5);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 22px;
            padding: 45px;
            box-shadow: var(--shadow-glass);
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-2);
            border-radius: 22px 22px 0 0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-group {
            margin-bottom: 25px;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--text-light);
            letter-spacing: 0.5px;
        }

        .input-group input,
        .input-group textarea,
        .input-group select {
            width: 100%;
            padding: 15px 18px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            font-size: 14px;
            outline: none;
            color: white;
            transition: all 0.4s ease;
            font-family: inherit;
        }

        .input-group input::placeholder,
        .input-group textarea::placeholder {
            color: var(--text-lighter);
        }

        .input-group input:focus,
        .input-group textarea:focus,
        .input-group select:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2);
            transform: translateY(-2px);
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .summary-card {
            background: rgba(15, 21, 53, 0.5);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 22px;
            padding: 35px;
            box-shadow: var(--shadow-glass);
            height: fit-content;
            position: sticky;
            top: 100px;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-2);
            border-radius: 22px 22px 0 0;
        }

        .item-preview {
            display: flex;
            gap: 18px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            align-items: center;
        }

        .item-img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid rgba(255, 0, 110, 0.2);
            flex-shrink: 0;
        }

        .item-info h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
        }

        .item-info p {
            font-size: 13px;
            color: var(--text-lighter);
            margin: 5px 0;
        }

        .cost-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 14px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-light);
        }

        .cost-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .cost-row.total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 18px;
            font-weight: 700;
            color: white;
        }

        .cost-value {
            font-weight: 600;
            color: white;
        }

        .total-amount {
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 22px;
            font-weight: 700;
        }

        .free-tag {
            color: var(--accent-2);
            font-weight: 700;
        }

        .confirm-btn {
            width: 100%;
            background: var(--gradient-2);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 30px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-neon);
        }

        .confirm-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
            z-index: -1;
        }

        .confirm-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .confirm-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.4);
        }

        .security-note {
            font-size: 12px;
            color: var(--text-lighter);
            margin-top: 18px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .security-note i {
            color: var(--accent-2);
        }

        .error-box {
            grid-column: 1/-1;
            text-align: center;
            padding: 80px 40px;
            background: rgba(255, 71, 87, 0.1);
            border: 1px solid rgba(255, 71, 87, 0.2);
            border-radius: 22px;
            margin: 40px auto;
        }

        .error-box h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #FF4757;
        }

        .error-box a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .error-box a:hover {
            text-shadow: 0 0 10px rgba(255, 0, 110, 0.5);
        }

        @media (max-width: 1024px) {
            .checkout-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .summary-card {
                position: relative;
                top: 0;
                order: -1;
            }

            .form-section {
                padding: 35px;
            }
        }

        @media (max-width: 768px) {
            header { padding: 20px 25px; }
            .checkout-container { padding: 0 20px; margin: 25px auto; }
            .form-section { padding: 30px; }
            .summary-card { padding: 30px; }
            .row { grid-template-columns: 1fr; }
            .item-img { width: 90px; height: 110px; }
        }

        @media (max-width: 480px) {
            header { flex-direction: column; gap: 15px; }
            .checkout-container { padding: 0 15px; }
            .form-section, .summary-card { padding: 25px; border-radius: 18px; }
            .section-title { font-size: 20px; }
            .item-img { width: 80px; height: 100px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">Style<span style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Share</span></div>
        <div class="secure-badge">
            <i class="fas fa-lock"></i> Secure Checkout
        </div>
    </header>

    <div class="checkout-container">
        
        <?php if ($item): ?>
        
        <div class="form-section">
            <h2 class="section-title">Shipping Details</h2>
            <form action="submit-order.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <input type="hidden" name="price_per_day" id="price_per_day" value="<?php echo $item['product_price']; ?>">
                <input type="hidden" name="deposit_amount" value="<?php echo $deposit_amount; ?>">

                <div class="row">
                    <div class="input-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="customer_name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="phone" placeholder="+91 98765 43210" required>
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                    <textarea name="address" rows="3" placeholder="Flat No, Street, City, Pincode" required></textarea>
                </div>

                <h2 class="section-title" style="margin-top: 35px;">Rental Period</h2>
                <div class="row">
                    <div class="input-group">
                        <label><i class="fas fa-calendar-check"></i> Start Date</label>
                        <input type="date" name="start_date" id="start_date" required onchange="calculateTotal()">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-calendar-times"></i> End Date</label>
                        <input type="date" name="end_date" id="end_date" required onchange="calculateTotal()">
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-credit-card"></i> Payment Method</label>
                    <select name="payment_mode">
                        <option value="COD">Cash on Delivery (COD)</option>
                        <option value="UPI">UPI / GPay / Paytm</option>
                    </select>
                </div>

                <button type="submit" class="confirm-btn">
                    <i class="fas fa-check"></i> Confirm Order
                </button>
            </form>
        </div>

        <div class="summary-card">
            <div class="item-preview">
                <img src="clothes/img/<?php echo htmlspecialchars($item['product_image1']); ?>" class="item-img" onerror="this.src='https://via.placeholder.com/100?text=Product'">
                <div class="item-info">
                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                    <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                    <p>Category: <?php echo htmlspecialchars(isset($item['category']) ? $item['category'] : 'N/A'); ?></p>
                </div>
            </div>

            <div class="cost-row">
                <span>Daily Rental Rate</span>
                <span class="cost-value">₹<?php echo number_format($item['product_price']); ?></span>
            </div>

            <div class="cost-row">
                <span>Rental Duration</span>
                <span class="cost-value" id="day_count">1 Day</span>
            </div>

            <div class="cost-row">
                <span>Total Rent</span>
                <span class="cost-value" id="total_rent">₹<?php echo number_format($item['product_price']); ?></span>
            </div>

            <div class="cost-row">
                <span>Refundable Deposit</span>
                <span class="cost-value">₹<?php echo number_format($deposit_amount); ?></span>
            </div>

            <div class="cost-row">
                <span>Shipping Fee</span>
                <span class="cost-value free-tag">FREE</span>
            </div>

            <div class="cost-row total">
                <span>Total Payable</span>
                <span class="total-amount" id="grand_total">₹<?php echo number_format($item['product_price'] + $deposit_amount); ?></span>
            </div>

            <div class="security-note">
                <i class="fas fa-shield-alt"></i> 100% Secure Payment
            </div>
        </div>

        <?php else: ?>
            <div class="error-box">
                <h2><i class="fas fa-exclamation-circle"></i> Item Not Found</h2>
                <p style="color: var(--text-light); margin-top: 15px;">The product you're looking for doesn't exist or has been removed.</p>
                <a href="collection.php" style="display: inline-block; margin-top: 20px;">
                    <i class="fas fa-arrow-left"></i> Back to Collection
                </a>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function calculateTotal() {
            const startDateStr = document.getElementById('start_date').value;
            const endDateStr = document.getElementById('end_date').value;
            const pricePerDay = parseFloat(document.getElementById('price_per_day').value);
            const deposit = <?php echo $deposit_amount; ?>;

            if (startDateStr && endDateStr) {
                const start = new Date(startDateStr);
                const end = new Date(endDateStr);
                const timeDiff = end - start;
                let days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;

                if (days < 1) days = 1;

                const totalRent = days * pricePerDay;
                const grandTotal = totalRent + deposit;

                document.getElementById('day_count').innerText = days + " Day" + (days > 1 ? "s" : "");
                document.getElementById('total_rent').innerText = "₹" + totalRent.toLocaleString();
                document.getElementById('grand_total').innerText = "₹" + grandTotal.toLocaleString();
            }
        }
    </script>

    <?php $conn->close(); ?>

</body>
</html>
