<?php
// rent.php
session_start();

// DB connection
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'rentalcloth';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// require user login (optional but recommended)
if (empty($_SESSION['user_id'])) {
    // Redirect to login page with return url
    header("Location: login.php?return=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
$user_id = (int) $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? '';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$CSRF = $_SESSION['csrf_token'];

// get product id
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_image1 FROM add_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc() ?: null;
    $stmt->close();
}

// handle POST
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_now'])) {
    // CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = "Invalid request.";
    }

    // sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $rental_date = trim($_POST['rental_date'] ?? '');
    $return_date = trim($_POST['return_date'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $p_id = intval($_POST['product_id'] ?? 0);

    // validations
    if ($name === '') $errors[] = "Name is required.";
    if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "Enter valid 10-digit phone.";
    if ($address === '') $errors[] = "Address is required.";
    if ($rental_date === '') $errors[] = "Select rental date.";
    if ($return_date === '') $errors[] = "Select return date.";
    if ($size === '') $errors[] = "Select size.";
    if ($p_id <= 0) $errors[] = "Invalid product.";

    // date check
    if (empty($errors)) {
        $r_ts = strtotime($rental_date);
        $ret_ts = strtotime($return_date);
        if ($r_ts === false || $ret_ts === false || $ret_ts <= $r_ts) {
            $errors[] = "Return date must be after rental date.";
        }
    }

    // fetch product details again for price/name
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT product_name, product_price FROM add_product WHERE product_id = ?");
        $stmt->bind_param("i", $p_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $prod_name = $row['product_name'];
            $prod_price = floatval($row['product_price']);
        } else {
            $errors[] = "Product not found.";
        }
        $stmt->close();
    }

    // insert rental
    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $sql = "INSERT INTO rentals 
                (user_id, user_name, product_id, product_name, amount, rent_start, rent_end, size, name, phone, address, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

            $status = 'pending'; // pending until admin confirms / payment
            $amount = $prod_price;
            $stmt->bind_param(
                "isisdssissss",
                $user_id,
                $user_name,
                $p_id,
                $prod_name,
                $amount,
                $rental_date,
                $return_date,
                $size,
                $name,
                $phone,
                $address,
                $status
            );
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $rental_id = $stmt->insert_id;
            $stmt->close();

            $conn->commit();
            $success = true;

            // redirect to my rentals or show success
            header("Location: my_rentals.php?msg=rent_success&id=" . $rental_id);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Server error: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rent Now — StyleShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root{--accent:#ff6b6b;--muted:#6b7280}
    *{box-sizing:border-box}
    body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;background:#f5f7fb;margin:0;color:#111}
    .container{max-width:980px;margin:26px auto;padding:18px}
    .card{background:#fff;border-radius:12px;padding:18px;border:1px solid #eef2f7;box-shadow:0 8px 30px rgba(16,24,40,0.04)}
    h1{margin:0 0 8px;font-size:22px}
    .muted{color:var(--muted);font-size:14px;margin-bottom:12px}
    .grid{display:grid;grid-template-columns:1fr 360px;gap:16px}
    @media(max-width:900px){.grid{grid-template-columns:1fr}}
    label{display:block;font-weight:700;margin-bottom:6px;color:#2b3440}
    input[type="text"], input[type="tel"], input[type="date"], select, textarea{
      width:100%;padding:10px;border-radius:8px;border:1px solid #e6eef6;background:#fff;margin-bottom:10px;
    }
    textarea{min-height:90px;resize:vertical}
    .btn{background:var(--accent);color:#fff;border:0;padding:10px 16px;border-radius:10px;font-weight:800;cursor:pointer}
    .btn.ghost{background:#fff;color:var(--accent);border:2px solid var(--accent)}
    .product-box{text-align:center;padding:12px;border-radius:10px;border:1px solid #f1f5f9;background:#fff}
    .product-box img{max-width:100%;height:260px;object-fit:cover;border-radius:8px;margin-bottom:10px}
    .error{background:#fff5f5;border:1px solid #ffd1d1;color:#9b1c1c;padding:10px;border-radius:8px;margin-bottom:12px}
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Rent Item</h1>
      <div class="muted">Confirm rental details and we will contact you for delivery & confirmation.</div>

      <?php if (!empty($errors)): ?>
        <div class="error">
          <strong>Fix these:</strong>
          <ul style="margin-top:8px">
            <?php foreach ($errors as $er): ?>
              <li><?php echo htmlspecialchars($er); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="grid">
        <!-- form -->
        <div>
          <form method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($CSRF); ?>">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">

            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($_POST['name'] ?? $user_name); ?>" required>

            <label for="phone">Mobile (10 digits)</label>
            <input id="phone" name="phone" type="tel" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>

            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>

            <label for="size">Size</label>
            <select id="size" name="size" required>
              <option value="">Select Size</option>
              <option value="S" <?php if (($_POST['size'] ?? '')==='S') echo 'selected'; ?>>S</option>
              <option value="M" <?php if (($_POST['size'] ?? '')==='M') echo 'selected'; ?>>M</option>
              <option value="L" <?php if (($_POST['size'] ?? '')==='L') echo 'selected'; ?>>L</option>
              <option value="XL" <?php if (($_POST['size'] ?? '')==='XL') echo 'selected'; ?>>XL</option>
            </select>

            <label for="rental_date">Rental Date</label>
            <input id="rental_date" name="rental_date" type="date" value="<?php echo htmlspecialchars($_POST['rental_date'] ?? ''); ?>" required>

            <label for="return_date">Return Date</label>
            <input id="return_date" name="return_date" type="date" value="<?php echo htmlspecialchars($_POST['return_date'] ?? ''); ?>" required>

            <div style="margin-top:12px;display:flex;gap:10px">
              <button type="submit" name="rent_now" class="btn">Confirm Rent</button>
              <a href="collection.php" class="btn ghost" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none">Back</a>
            </div>
          </form>
        </div>

        <!-- product summary -->
        <aside>
          <div class="product-box">
            <?php if ($product): ?>
              <?php
                $img_path = 'clothes/img/' . (basename($product['product_image1'] ?? ''));
              ?>
              <img src="<?php echo htmlspecialchars($img_path); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" onerror="this.src='assets/no-image.png'">
              <div style="font-weight:800;font-size:18px"><?php echo htmlspecialchars($product['product_name']); ?></div>
              <div style="color:#6b7280;margin-top:6px">Approx. Rent: ₹<?php echo number_format(floatval($product['product_price'] ?? 0),2); ?></div>
              <div style="margin-top:8px;color:#6b7280;font-size:13px">Product ID: <?php echo (int)$product['product_id']; ?></div>
            <?php else: ?>
              <img src="assets/no-image.png" alt="No product">
              <div style="font-weight:800;font-size:18px;margin-top:8px">No product selected</div>
              <div style="color:#6b7280;margin-top:6px">Please go back and choose an item from collection.</div>
              <a href="collection.php" style="display:inline-block;margin-top:10px;color:var(--accent);font-weight:700">← Back to collection</a>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </div>
  </div>
</body>
</html>
