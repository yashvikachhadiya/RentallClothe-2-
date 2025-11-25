<?php
// order.php (UPDATED)
// Start session + DB
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// get product id (from collection link)
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_image1 FROM add_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $product = $row;
    }
    $stmt->close();
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$CSRF = $_SESSION['csrf_token'];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {
    // CSRF check
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = "Invalid request (CSRF).";
    }

    // sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $rental_date = trim($_POST['rental_date'] ?? '');
    $return_date = trim($_POST['return_date'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $p_id = intval($_POST['product_id'] ?? 0);
    $payment_mode = trim($_POST['payment_mode'] ?? ($_POST['payment_mode'] ?? 'COD'));

    // validation
    if ($name === '') $errors[] = "Name required.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if ($address === '') $errors[] = "Address required.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "Enter valid 10-digit phone number.";
    if ($rental_date === '') $errors[] = "Rental date required.";
    if ($return_date === '') $errors[] = "Return date required.";
    if ($size === '') $errors[] = "Select size.";
    if ($p_id <= 0) $errors[] = "Invalid product.";

    // date logic (server-side)
    if (empty($errors)) {
        $r_dt = strtotime($rental_date);
        $ret_dt = strtotime($return_date);
        if ($r_dt === false || $ret_dt === false) {
            $errors[] = "Invalid dates.";
        } elseif ($ret_dt < $r_dt) {
            $errors[] = "Return date must be same or after rental date.";
        }
    }

    // get product price from DB (trust server)
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT product_name, product_price FROM add_product WHERE product_id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $prod_name = $row['product_name'];
            $prod_price = floatval($row['product_price']);
        } else {
            $errors[] = "Selected product not found.";
        }
        $stmt->close();
    }

    // compute days and total
    if (empty($errors)) {
        // inclusive days: if same day -> 1 day
        $start = new DateTime($rental_date);
        $end = new DateTime($return_date);
        // difference in days (inclusive)
        $interval = $start->diff($end);
        $days = (int)$interval->days + 1; // days difference + inclusive
        if ($days < 1) $days = 1;

        $total_amount = $days * $prod_price;
    }

    // insert into orders table (matching your DB structure)
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO orders (product_id, customer_name, phone, address, start_date, end_date, total_days, total_amount, payment_mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

            // bind types: i (prod_id), s (name), s (phone), s (address), s (start_date), s (end_date), i (days), d (total_amount), s (payment_mode)
            $stmt->bind_param(
                "issssidsd", // we'll adjust below since PHP requires matching count and types: fix to "issssids" and pass double as float
                $p_id,
                $name,
                $phone,
                $address,
                $rental_date,
                $return_date,
                $days,
                $total_amount,
                $payment_mode
            );
            // Correction because PHP expects a types string with same number of parameters:
            // use an alternate bind (rebind properly)
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO orders (product_id, customer_name, phone, address, start_date, end_date, total_days, total_amount, payment_mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "issssids",
                $p_id,
                $name,
                $phone,
                $address,
                $rental_date,
                $return_date,
                $days,
                $total_amount,
                $payment_mode
            );

            if (!$stmt->execute()) {
                throw new Exception("Insert failed: " . $stmt->error);
            }

            $order_id = $stmt->insert_id;
            $stmt->close();

            $success = true;
            // redirect to orders page or confirmation
            header("Location: orders.php?msg=order_success&id=" . intval($order_id));
            exit;
        } catch (Exception $e) {
            $errors[] = "Server error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Order Now — StyleShare</title>
  <style>
    /* (same styles as before) */
    :root{--accent:#ff6b6b;--muted:#6b7280;--card:#fff}
    *{box-sizing:border-box}
    body{font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;background:#f3f5f8;color:#111;margin:0;padding:0}
    .wrap{max-width:920px;margin:36px auto;padding:20px}
    .card{background:var(--card);border-radius:12px;padding:22px;border:1px solid #e9eef5;box-shadow:0 6px 30px rgba(16,24,40,0.04)}
    h1{margin:0 0 12px;font-size:22px;color:#172554}
    .grid{display:grid;grid-template-columns:1fr 360px;gap:18px}
    @media (max-width:900px){.grid{grid-template-columns:1fr}}
    form .field{margin-bottom:12px}
    label{display:block;font-weight:700;margin-bottom:6px;color:#334155}
    input[type="text"], input[type="email"], input[type="tel"], input[type="date"], select, textarea{
      width:100%;padding:10px;border-radius:8px;border:1px solid #e6eef6;background:#fff;outline:none;font-size:14px;
    }
    textarea{min-height:90px;resize:vertical}
    .row{display:flex;gap:12px}
    .row .field{flex:1}
    .btn{background:var(--accent);color:#fff;padding:11px 16px;border-radius:10px;border:0;font-weight:800;cursor:pointer}
    .btn.secondary{background:#fff;color:var(--accent);border:2px solid var(--accent)}
    .muted{color:var(--muted);font-size:14px}
    .product-card{border-radius:10px;padding:12px;background:linear-gradient(180deg,#ffffff,#fff);text-align:center;border:1px solid #f1f5f9}
    .product-card img{max-width:100%;height:260px;object-fit:cover;border-radius:8px;margin-bottom:8px}
    .error{background:#fff5f5;border:1px solid #ffd1d1;color:#9b1c1c;padding:10px;border-radius:8px;margin-bottom:12px}
    .success{background:#ecfdf5;border:1px solid #bbf7d0;color:#036a36;padding:10px;border-radius:8px;margin-bottom:12px}
    .small{font-size:13px;color:#667085}
    .back-link{display:inline-block;margin-top:10px;color:var(--muted)}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Place Your Order</h1>
      <p class="small">Fill details below to confirm rental. After order, we will contact you for confirmation and delivery.</p>

      <?php if (!empty($errors)): ?>
        <div class="error">
          <strong>Please fix following:</strong>
          <ul style="margin-top:8px">
            <?php foreach ($errors as $er): ?>
              <li><?php echo htmlspecialchars($er); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success">Order placed successfully. Redirecting...</div>
      <?php endif; ?>

      <div class="grid">
        <div>
          <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($CSRF); ?>">
            <input type="hidden" name="product_id" value="<?php echo intval($product_id ?: ($_POST['product_id'] ?? 0)); ?>">

            <div class="field">
              <label for="name">Full name</label>
              <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>

            <div class="field">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="field">
              <label for="address">Address</label>
              <textarea id="address" name="address" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>

            <div class="row">
              <div class="field">
                <label for="phone">Mobile (10 digits)</label>
                <input id="phone" name="phone" type="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
              </div>
              <div class="field">
                <label for="size">Confirm Size</label>
                <select id="size" name="size" required>
                  <option value="">Select size</option>
                  <option <?php if (($_POST['size'] ?? '') === 'S') echo 'selected'; ?>>S</option>
                  <option <?php if (($_POST['size'] ?? '') === 'M') echo 'selected'; ?>>M</option>
                  <option <?php if (($_POST['size'] ?? '') === 'L') echo 'selected'; ?>>L</option>
                  <option <?php if (($_POST['size'] ?? '') === 'XL') echo 'selected'; ?>>XL</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="field">
                <label for="rental_date">Rental Date</label>
                <input id="rental_date" name="rental_date" type="date" value="<?php echo htmlspecialchars($_POST['rental_date'] ?? ''); ?>" required>
              </div>
              <div class="field">
                <label for="return_date">Return Date</label>
                <input id="return_date" name="return_date" type="date" value="<?php echo htmlspecialchars($_POST['return_date'] ?? ''); ?>" required>
              </div>
            </div>

            <div class="field">
              <label for="payment_mode">Payment Method</label>
              <select id="payment_mode" name="payment_mode">
                <option value="COD" <?php if(($_POST['payment_mode'] ?? '')==='COD') echo 'selected'; ?>>Cash on Delivery (COD)</option>
                <option value="UPI" <?php if(($_POST['payment_mode'] ?? '')==='UPI') echo 'selected'; ?>>UPI / GPay / Paytm</option>
              </select>
            </div>

            <div style="display:flex;gap:10px;margin-top:14px">
              <button type="submit" name="buy_now" class="btn">Place Order</button>
              <a href="collection.php" class="btn secondary" style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center">Back to Collection</a>
            </div>
          </form>
        </div>

        <aside>
          <div class="product-card">
            <?php if ($product): 
                $img = htmlspecialchars('clothes/img/' . basename($product['product_image1'] ?? ''));
            ?>
              <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" onerror="this.src='assets/no-image.png'">
              <div style="font-weight:800;font-size:18px"><?php echo htmlspecialchars($product['product_name']); ?></div>
              <div class="small muted">Price: ₹ <?php echo number_format(floatval($product['product_price'] ?? 0),2); ?></div>
              <div style="margin-top:10px" class="small">Rental period: <?php echo htmlspecialchars($_POST['rental_date'] ?? '—'); ?> → <?php echo htmlspecialchars($_POST['return_date'] ?? '—'); ?></div>
            <?php else: ?>
              <img src="assets/no-image.png" alt="No product" style="height:220px;object-fit:contain">
              <div style="font-weight:800;font-size:18px;margin-top:8px">No product selected</div>
              <div class="small muted">Please go back to collection and choose an item.</div>
              <a href="collection.php" class="back-link">← Back to collection</a>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
