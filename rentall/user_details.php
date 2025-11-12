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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Normalize incoming search
$search = isset($_GET['search']) && trim($_GET['search']) !== '' ? trim($_GET['search']) : '';

// --------------- determine product_order columns dynamically ---------------
$orderColumns = [];
$colRes = $conn->query("SHOW COLUMNS FROM `product_order`");
if ($colRes) {
    while ($c = $colRes->fetch_assoc()) {
        $orderColumns[] = $c['Field'];
    }
}
// helper checks
$hasSize = in_array('confirm_size', $orderColumns) || in_array('size', $orderColumns) || in_array('order_size', $orderColumns);
$sizeCol = in_array('confirm_size', $orderColumns) ? 'confirm_size' : (in_array('size', $orderColumns) ? 'size' : (in_array('order_size', $orderColumns) ? 'order_size' : null));
$hasReturn = in_array('return_date', $orderColumns) || in_array('Return_Date', $orderColumns) || in_array('order_return_date', $orderColumns);
$returnCol = in_array('return_date', $orderColumns) ? 'return_date' : (in_array('Return_Date', $orderColumns) ? 'Return_Date' : (in_array('order_return_date', $orderColumns) ? 'order_return_date' : null));

// ----------------- Stats: total registered users -----------------
$total_in_db = 0;
$countR = $conn->query("SELECT COUNT(*) AS cnt FROM `user_data`");
if ($countR) {
    $row = $countR->fetch_assoc();
    $total_in_db = intval($row['cnt']);
}

// ----------------- Build main SELECT -----------------
// Base select columns
$selectCols = [
    "u.User_Id AS user_id",
    "u.First_Name",
    "u.Last_Name",
    "u.Email",
    "u.Mobile_No",
    "u.Address",
    // order columns (if exist) map to known alias
    "o.Order_Id",
    "o.Order_Date",
    "o.Order_Time",
    "o.product_Name",
    "o.product_price",
    "o.product_image",
    "o.Payment_Method",
    "o.Total_price"
];

// add size / return place-holders in select if they exist (otherwise still safe)
if ($hasSize && $sizeCol) {
    $selectCols[] = "o.`$sizeCol` AS order_size";
} else {
    // optional: select NULL so alias exists
    $selectCols[] = "NULL AS order_size";
}
if ($hasReturn && $returnCol) {
    $selectCols[] = "o.`$returnCol` AS return_date";
} else {
    $selectCols[] = "NULL AS return_date";
}

$selectSql = implode(",\n    ", $selectCols);

$sql = "SELECT
    {$selectSql}
FROM `user_data` u
LEFT JOIN `product_order` o ON u.User_Id = o.User_Id
";

// build where when search present
$where = '';
if ($search !== '') {
    // safe escaping
    $s = $conn->real_escape_string($search);
    // search first_name + last_name, email, mobile
    // Use LOWER() for case-insensitive matching
    $where = " WHERE (
        LOWER(CONCAT(u.First_Name, ' ', u.Last_Name)) LIKE LOWER('%{$s}%')
        OR LOWER(u.Email) LIKE LOWER('%{$s}%')
        OR CAST(u.Mobile_No AS CHAR) LIKE '%{$s}%'
    )";
}
$sql .= $where;
$sql .= " ORDER BY u.User_Id DESC, o.Order_Date DESC, o.Order_Time DESC";

$result = $conn->query($sql);

if (!$result) {
    // debug-friendly message but don't show raw SQL in production
    echo "<p style='color:#f88'>Query error: " . htmlspecialchars($conn->error) . "</p>";
    $result = false;
    $total_users = 0;
} else {
    // compute total users for the current search (distinct)
    if ($search !== '') {
        $s = $conn->real_escape_string($search);
        $count_sql = "SELECT COUNT(DISTINCT u.User_Id) AS total FROM `user_data` u
                      WHERE (
                        LOWER(CONCAT(u.First_Name, ' ', u.Last_Name)) LIKE LOWER('%{$s}%')
                        OR LOWER(u.Email) LIKE LOWER('%{$s}%')
                        OR CAST(u.Mobile_No AS CHAR) LIKE '%{$s}%'
                      )";
    } else {
        $count_sql = "SELECT COUNT(*) AS total FROM `user_data`";
    }
    $count_result = $conn->query($count_sql);
    $total_users = 0;
    if ($count_result) {
        $c = $count_result->fetch_assoc();
        $total_users = intval($c['total']);
    }
}

// Close connection at end of file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>User Details | StyleShare Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* paste your existing CSS (kept concise for brevity) */
        :root{--gradient-2:linear-gradient(135deg,#FF006E 0%,#8338EC 50%,#3A86FF 100%);--shadow-neon:0 0 20px rgba(255,0,110,0.5);}
        *{box-sizing:border-box}body{font-family:Inter,Arial;background:linear-gradient(135deg,#0A0E27 0%,#1A1F3A 50%,#0A0E27 100%);color:#fff;margin:0;display:flex;min-height:100vh}
        .sidebar{width:280px;background:rgba(10,14,39,.75);padding:30px 0;position:fixed;height:100vh;left:0;top:0;overflow:auto}
        .sidebar h2{font-family:'Playfair Display';text-align:center;background:var(--gradient-2);-webkit-background-clip:text;color:transparent}
        .sidebar ul{list-style:none;padding:0 16px}
        .sidebar ul li{margin:10px 0}
        .sidebar a{display:flex;gap:12px;padding:12px;border-radius:10px;color:rgba(255,255,255,.85);text-decoration:none;border:1px solid rgba(255,255,255,.03)}
        .main-content{margin-left:280px;flex:1;padding:40px;display:flex;justify-content:center}
        .container{width:100%;max-width:1400px;background:rgba(255,255,255,.03);padding:36px;border-radius:20px;border:1px solid rgba(255,255,255,.06)}
        .header-section{display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap}
        .stat-box{background:rgba(255,0,110,.06);padding:12px 18px;border-radius:10px;text-align:center}
        .stat-box .number{font-weight:700;background:var(--gradient-2);-webkit-background-clip:text;color:transparent}
        .search-form{display:flex;gap:12px;margin-top:18px}
        .search-form input{flex:1;padding:12px;border-radius:12px;border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.02);color:#fff}
        .search-form button{padding:12px 20px;border-radius:50px;border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.03);color:#fff}
        .table-wrapper{margin-top:22px;overflow:auto;border-radius:12px;border:1px solid rgba(255,255,255,.04)}
        table{width:100%;border-collapse:collapse;min-width:1000px}
        thead th{padding:14px 12px;text-align:left;background:linear-gradient(90deg,rgba(255,0,110,.08),transparent);font-weight:700}
        td{padding:12px;border-top:1px solid rgba(255,255,255,.03);vertical-align:top}
        .no-data{padding:40px;text-align:center;color:rgba(255,255,255,.5)}
        @media(max-width:768px){.sidebar{display:none}.main-content{margin-left:0;padding:20px}}
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h2>Admin<span style="background:var(--gradient-2);-webkit-background-clip:text;color:transparent">Panel</span></h2>
        <ul>
            <li><a href="clothform.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <li><a href="user_details.php"><i class="fas fa-users"></i> User Details</a></li>
            <li><a href="viewr.php"><i class="fas fa-user-check"></i> Registrations</a></li>
            <li><a href="viewproduct.php"><i class="fas fa-box"></i> View Products</a></li>
            <li><a href="#" onclick="confirmLogout()" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="header-section">
                <div>
                    <h2><i class="fas fa-users-cog"></i> User Details</h2>
                    <div class="search-form" style="margin-top:12px">
                        <form method="GET" action="user_details.php" style="display:flex;gap:12px;width:100%">
                            <input type="text" name="search" placeholder="🔍 Search user by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit"><i class="fas fa-search"></i> Search</button>
                            <?php if ($search !== ''): ?>
                                <a href="user_details.php" style="display:inline-flex;align-items:center;padding:10px 18px;border-radius:50px;background:rgba(255,0,110,.06);color:#FF6B9D;text-decoration:none;margin-left:6px"><i class="fas fa-times"></i> Clear</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="number"><?php echo intval($total_in_db); ?></div>
                    <div style="font-size:12px;color:rgba(255,255,255,.65)">Total Registered Users</div>
                    <div style="font-size:12px;margin-top:6px;color:rgba(255,255,255,.6)"><?php echo intval($total_users); ?> shown</div>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Full Name</th>
                            <th><i class="fas fa-envelope"></i> Email</th>
                            <th><i class="fas fa-phone"></i> Phone</th>
                            <th><i class="fas fa-map-marker-alt"></i> Address</th>
                            <th><i class="fas fa-calendar"></i> Rental Date</th>
                            <th><i class="fas fa-tape"></i> Size</th>
                            <th><i class="fas fa-undo"></i> Return Date</th>
                            <th><i class="fas fa-box"></i> Product</th>
                            <th><i class="fas fa-rupee-sign"></i> Price</th>
                            <th><i class="fas fa-receipt"></i> Order ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            // We'll print each row — since LEFT JOIN, user rows without orders will repeat only once with NULL order fields
                            while ($row = $result->fetch_assoc()) {
                                $userId = htmlspecialchars($row['user_id'] ?? $row['User_Id'] ?? '');
                                $fullName = htmlspecialchars(trim(($row['First_Name'] ?? '') . ' ' . ($row['Last_Name'] ?? '')));
                                $email = htmlspecialchars($row['Email'] ?? '');
                                $phone = htmlspecialchars($row['Mobile_No'] ?? '');
                                $address = htmlspecialchars($row['Address'] ?? '');

                                // Order fields (may be null)
                                $orderDate = !empty($row['Order_Date']) ? htmlspecialchars($row['Order_Date']) : '-';
                                $orderSize = !empty($row['order_size']) ? htmlspecialchars($row['order_size']) : '-';
                                $returnDate = !empty($row['return_date']) ? htmlspecialchars($row['return_date']) : '-';
                                $productName = !empty($row['product_Name']) ? htmlspecialchars($row['product_Name']) : '-';
                                $productPrice = isset($row['product_price']) && $row['product_price'] !== null && $row['product_price'] !== '' ? '₹'.number_format($row['product_price']) : '-';
                                $orderId = !empty($row['Order_Id']) ? htmlspecialchars($row['Order_Id']) : '-';

                                echo "<tr>";
                                echo "<td>{$userId}</td>";
                                echo "<td>{$fullName}</td>";
                                echo "<td>{$email}</td>";
                                echo "<td>{$phone}</td>";
                                echo "<td>{$address}</td>";
                                echo "<td>{$orderDate}</td>";
                                echo "<td>{$orderSize}</td>";
                                echo "<td>{$returnDate}</td>";
                                echo "<td>{$productName}</td>";
                                echo "<td>{$productPrice}</td>";
                                echo "<td>{$orderId}</td>";
                                echo "</tr>";
                            }
                        } else {
                            if ($search !== '') {
                                echo "<tr><td colspan='11'><div class='no-data'><i class='fas fa-search'></i><p>No users found matching \"" . htmlspecialchars($search) . "\". Try a different search term.</p></div></td></tr>";
                            } else {
                                echo "<tr><td colspan='11'><div class='no-data'><i class='fas fa-inbox'></i><p>No user data available. No users registered yet.</p></div></td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

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

<?php
$conn->close();
?>
