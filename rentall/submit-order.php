<?php
// =================================================================
// PHP LOGIC: HANDLE FORM SUBMISSION
// =================================================================
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$order_success = false;
$error_msg = "";
$order_details = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Get Data from Form
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $name = $conn->real_escape_string($_POST['customer_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $payment_mode = $conn->real_escape_string($_POST['payment_mode']);

    // 2. Fetch Product Details (Price/Deposit) from DB again for Security
    if ($product_id > 0) {
        $sql_prod = "SELECT * FROM add_product WHERE product_id = $product_id";
        $res_prod = $conn->query($sql_prod);
        
        if ($res_prod && $res_prod->num_rows > 0) {
            $product = $res_prod->fetch_assoc();
            
            // 3. Calculate Logic
            $price_per_day = $product['product_price'];
            // Handle missing deposit column gracefully
            $deposit = isset($product['deposite']) ? $product['deposite'] : 0;

            // Calculate Days
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            $days_diff = $end - $start;
            $total_days = round($days_diff / (60 * 60 * 24)) + 1;

            if ($total_days < 1) $total_days = 1; // Minimum 1 day

            // Calculate Total
            $rent_cost = $total_days * $price_per_day;
            $final_total = $rent_cost + $deposit;

            // 4. Insert Order into Database
            // NOTE: Make sure your 'orders' table has all these columns!
            $sql_insert = "INSERT INTO orders (product_id, customer_name, phone, address, start_date, end_date, total_days, total_amount, payment_mode) 
                           VALUES ('$product_id', '$name', '$phone', '$address', '$start_date', '$end_date', '$total_days', '$final_total', '$payment_mode')";

            if ($conn->query($sql_insert) === TRUE) {
                $order_success = true;
                $order_id = $conn->insert_id; // Get the generated Order ID
                
                // Store details for display
                $order_details = [
                    'id' => $order_id,
                    'item' => $product['product_name'],
                    'image' => $product['product_image1'], // Ensure this column name matches your DB
                    'name' => $name,
                    'days' => $total_days,
                    'total' => $final_total,
                    'date' => date("d M Y")
                ];
            } else {
                $error_msg = "Database Insert Error: " . $conn->error;
            }

        } else {
            $error_msg = "Product not found in database (ID: $product_id).";
        }
    } else {
        $error_msg = "Invalid Product ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status | StyleShare</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Premium Dark Glasmorphic Design */
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
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

        .receipt-card {
            background: rgba(15, 21, 53, 0.5);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 550px;
            border-radius: 24px;
            box-shadow: var(--shadow-glass);
            overflow: hidden;
            text-align: center;
            position: relative;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .receipt-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--gradient-2);
            border-radius: 24px 24px 0 0;
        }

        /* Status Header */
        .status-header {
            background: linear-gradient(135deg, rgba(6, 255, 165, 0.1) 0%, rgba(51, 134, 236, 0.1) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px 30px;
            position: relative;
        }

        .status-header.error {
            background: linear-gradient(135deg, rgba(255, 71, 87, 0.1) 0%, rgba(255, 0, 110, 0.1) 100%);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: var(--gradient-2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
            box-shadow: var(--shadow-neon);
            animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .status-header.error .icon-box {
            background: linear-gradient(135deg, #FF4757, #FF006E);
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .status-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin: 0;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .status-header.error .status-title {
            background: linear-gradient(135deg, #FF4757, #FF006E);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-id {
            font-size: 12px;
            color: var(--text-lighter);
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Receipt Body */
        .receipt-body {
            padding: 40px;
        }

        .product-preview {
            display: flex;
            align-items: center;
            gap: 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
            text-align: left;
        }

        .product-preview img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(255, 0, 110, 0.2);
            flex-shrink: 0;
        }

        .product-preview strong {
            font-size: 16px;
            font-weight: 700;
            color: white;
        }

        .product-preview small {
            font-size: 13px;
            color: var(--text-lighter);
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            font-size: 14px;
            color: var(--text-light);
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .receipt-row:last-of-type {
            border-bottom: none;
        }

        .receipt-row.total {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: none;
            font-weight: 700;
            font-size: 18px;
            color: white;
        }

        .receipt-row.total span:last-child {
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 24px;
        }

        /* Buttons */
        .btn-group {
            margin-top: 35px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 16px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: var(--gradient-2);
            color: white;
            box-shadow: var(--shadow-neon);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
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
        }

        .btn-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--text-light);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            background: rgba(255, 0, 110, 0.1);
            color: white;
        }

        @media (max-width: 768px) {
            .receipt-card { max-width: 100%; }
            .receipt-body { padding: 30px; }
            .status-header { padding: 40px 20px; }
            .product-preview { gap: 12px; padding: 15px; }
            .product-preview img { width: 70px; height: 90px; }
            .status-title { font-size: 26px; }
        }

    </style>
</head>
<body>

    <div class="receipt-card">
        
        <?php if ($order_success): ?>
            
            <div class="status-header">
                <div class="icon-box"><i class="fas fa-check"></i></div>
                <h1 class="status-title">Order Confirmed!</h1>
                <p class="order-id">Order ID: #<?php echo $order_details['id']; ?></p>
            </div>

            <div class="receipt-body">
                <div class="product-preview">
                    <img src="clothes/img/<?php echo htmlspecialchars($order_details['image']); ?>" alt="Product" onerror="this.src='https://via.placeholder.com/100?text=Product'">
                    <div>
                        <strong><?php echo htmlspecialchars($order_details['item']); ?></strong>
                        <small style="display: block; margin-top: 4px;">Reserved for <?php echo $order_details['days']; ?> Day<?php echo $order_details['days'] > 1 ? 's' : ''; ?></small>
                    </div>
                </div>

                <div class="receipt-row">
                    <span>Customer Name</span>
                    <span style="color: white; font-weight: 600;"><?php echo htmlspecialchars($order_details['name']); ?></span>
                </div>
                <div class="receipt-row">
                    <span>Confirmation Date</span>
                    <span style="color: white; font-weight: 600;"><?php echo $order_details['date']; ?></span>
                </div>
                <div class="receipt-row">
                    <span>Payment Method</span>
                    <span style="color: var(--accent-2); font-weight: 600;">Cash on Delivery</span>
                </div>
                
                <div class="receipt-row total">
                    <span>Amount Payable</span>
                    <span>₹<?php echo number_format($order_details['total']); ?></span>
                </div>

                <div class="btn-group">
                    <a href="javascript:window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Receipt</a>
                    <a href="collection.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
                    <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Back to Home</a>
                </div>
            </div>

        <?php else: ?>

            <div class="status-header error">
                <div class="icon-box"><i class="fas fa-times"></i></div>
                <h1 class="status-title">Order Failed</h1>
            </div>

            <div class="receipt-body">
                <p style="color: var(--text-light); margin-bottom: 20px; line-height: 1.6;">
                    Something went wrong while placing your order. Please try again or contact support.
                </p>
                <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid rgba(255, 71, 87, 0.3); padding: 15px; border-radius: 12px; margin-bottom: 25px; text-align: left;">
                    <p style="font-size: 12px; color: #FF9999; font-family: 'Courier New', monospace; margin: 0; word-break: break-word;">
                        <strong>Error Details:</strong><br><?php echo htmlspecialchars($error_msg); ?>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="collection.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Back to Collection</a>
                    <a href="index.php" class="btn btn-outline"><i class="fas fa-home"></i> Go to Home</a>
                </div>
            </div>

        <?php endif; ?>

    </div>

</body>
</html>