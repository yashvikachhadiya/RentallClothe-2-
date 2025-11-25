<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Toggle dummy payment for testing
// true  = simulate payment (no Razorpay popup)
// false = use Razorpay (test/live key)
$DUMMY_PAYMENT = true;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Details</title>
    <?php if (!$DUMMY_PAYMENT): ?>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <?php endif; ?>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background-color:#dfdfe8; color:#333; }
        .container { max-width:900px; margin:100px auto; background:white; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.1); display:flex; padding:17px; gap:30px; }
        .left, .right { flex:1; }
        .left img { width:100%; border-radius:10px; }
        .right h1 { margin-top:0; font-size:2em; color:#222; }
        .right p { font-size:1.1em; margin:10px 0; line-height:1.6; }
        .btn { display:inline-block; margin-top:20px; padding:12px 25px; background-color:#ff6b6b; color:white; border:none; border-radius:30px; cursor:pointer; text-decoration:none; font-size:16px; margin-right:10px; }
        .btn.secondary { background:transparent; color:#ff6b6b; border:2px solid #ff6b6b; }
        .buy-form { max-width:600px; margin:30px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
        .buy-form h2 { margin-top:0; }
        .buy-form label { display:block; margin-bottom:5px; text-align:left; }
        .buy-form input, .buy-form textarea, .buy-form select { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:8px; }
        .buy-form button { background-color:#28a745; color:white; padding:12px 20px; border:none; border-radius:30px; cursor:pointer; font-size:16px; }
        .buy-form button:hover { background-color:#ff6b6b; }
        @media (max-width:668px) { .container { flex-direction:column; text-align:center; } }
    </style>
</head>
<body>

<?php
if ($product_id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo "<p style='text-align:center;color:red;'>DB error: " . htmlspecialchars($conn->error) . "</p>"; exit; }
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $product_price = floatval($row['product_price']); // Price in ₹
        $price_in_paise = intval(round($product_price * 100)); // integer paise

        // show item
        ?>
        <div class='container'>
            <div class='left'>
                <img src='clothes/img/<?php echo htmlspecialchars($row['product_image1']); ?>' alt='<?php echo htmlspecialchars($row['product_name']); ?>'>
            </div>
            <div class='right'>
                <h1><?php echo htmlspecialchars($row['product_name']); ?></h1>
                <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($row['product_price']); ?></p>
                <a href='javascript:history.back()' class='btn secondary'>← Back</a>
                <button class='btn' onclick='toggleForm()'>Buy Now</button>
            </div>
        </div>

        <div class='buy-form' id='buyNowForm' style='display:none;'>
            <h2>Enter Your Details</h2>
            <form id='orderForm'>
                <input type='hidden' name='product_id' value='<?php echo $row['product_id']; ?>'>

                <label for='name'>Full Name</label>
                <input type='text' id='name' name='name' required>

                <label for='address'>Address</label>
                <textarea id='address' name='address' required></textarea>

                <label for='phone'>Contact Number</label>
                <input type='text' id='phone' name='phone' required>

                <label for='rental_date'>Rental Date</label>
                <input type='date' id='rental_date' name='rental_date' required>

                <label for='confirm_size'>Confirm Size</label>
                <select id='confirm_size' name='confirm_size' required>
                    <option value=''>Select Size</option>
                    <option value='S'>S</option>
                    <option value='M'>M</option>
                    <option value='L'>L</option>
                    <option value='XL'>XL</option>
                </select>

                <label for='return_date'>Return Date</label>
                <input type='date' id='return_date' name='return_date' required>

                <button type='button' id='payBtn'>Pay & Order</button>
            </form>
        </div>
        <?php
    } else {
        echo "<p style='text-align:center;'>Item not found.</p>";
    }
    $stmt->close();
} else {
    echo "<p style='text-align:center;'>Invalid item.</p>";
}
$conn->close();
?>

<script>
function toggleForm() {
    document.getElementById("buyNowForm").style.display = "block";
}

function validateForm() {
    const form = document.getElementById('orderForm');
    const name = form.querySelector('#name').value.trim();
    const address = form.querySelector('#address').value.trim();
    const phone = form.querySelector('#phone').value.trim();
    const rentalDate = form.querySelector('#rental_date').value;
    const confirmSize = form.querySelector('#confirm_size').value;
    const returnDate = form.querySelector('#return_date').value;

    if (!name || !address || !phone || !rentalDate || !confirmSize || !returnDate) {
        alert('Please fill out all required fields.');
        return false;
    }

    if (!/^\d{10}$/.test(phone)) {
        alert('Please enter a valid 10-digit phone number.');
        return false;
    }

    const today = new Date();
    today.setHours(0,0,0,0);
    const rentalDateObj = new Date(rentalDate);
    if (rentalDateObj < today) {
        alert('Rental date cannot be in the past.');
        return false;
    }

    const returnDateObj = new Date(returnDate);
    if (returnDateObj <= rentalDateObj) {
        alert('Return date must be after the rental date.');
        return false;
    }

    return true;
}
</script>

<script>
const DUMMY_PAYMENT = <?php echo $DUMMY_PAYMENT ? 'true' : 'false'; ?>;
const AMOUNT_PAISA = <?php echo isset($price_in_paise) ? $price_in_paise : 0; ?>;
const RAZORPAY_KEY = 'rzp_test_UpIEIXEWRj5It2'; // replace with your key

document.getElementById('payBtn').addEventListener('click', function () {
    if (typeof <?php echo json_encode($_SESSION['user_id'] ?? null); ?> === 'undefined' || <?php echo isset($_SESSION['user_id']) ? 'false' : 'true'; ?>) {
        // if server-side session not set or user not logged in, JS check: use PHP guard in payNow as well
        alert('Please log in or sign up to place an order.');
        return;
    }

    if (!validateForm()) return;

    if (DUMMY_PAYMENT) {
        // Simulate payment success instantly
        const fakePaymentId = 'DUMMY_PAY_' + Date.now();
        submitOrder(fakePaymentId);
    } else {
        // Real Razorpay flow (test key)
        const options = {
            key: RAZORPAY_KEY,
            amount: AMOUNT_PAISA,
            currency: 'INR',
            name: 'StyleShare',
            description: 'Rental Payment',
            handler: function (response) {
                // on success submit to server with razorpay id
                submitOrder(response.razorpay_payment_id);
            },
            prefill: {
                name: document.getElementById('name').value,
                contact: document.getElementById('phone').value
            },
            theme: { color: '#ff6b6b' }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    }
});

function submitOrder(paymentId) {
    const form = document.getElementById('orderForm');
    const formData = new FormData(form);
    formData.append('razorpay_payment_id', paymentId);
    formData.append('user_id', '<?php echo addslashes($_SESSION["user_id"] ?? ""); ?>');

    fetch('submit-order.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        // you can parse JSON if submit-order returns JSON
        alert('Order placed successfully!');
        window.location.href = 'index.php';
    })
    .catch(err => {
        console.error('Order Error:', err);
        alert('Payment was successful, but order failed. Please contact support.');
    });
}
</script>

</body>
</html>
