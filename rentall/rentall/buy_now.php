<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #dfdfe8;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 100px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            padding: 17px;
            gap: 30px;
        }
        .left, .right {
            flex: 1;
        }
        .left img {
            width: 100%;
            border-radius: 10px;
        }
        .right h1 {
            margin-top: 0;
            font-size: 2em;
            color: #222;
        }
        .right p {
            font-size: 1.1em;
            margin: 10px 0;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #ff5252;
        }
        .btn.secondary {
            background-color: transparent;
            color: #ff6b6b;
            border: 2px solid #ff6b6b;
        }
        .btn.secondary:hover {
            background-color: #ff6b6b;
            color: white;
        }
        .buy-form {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .buy-form h2 {
            margin-top: 0;
        }
        .buy-form label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
        }
        .buy-form input,
        .buy-form textarea,
        .buy-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .buy-form button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
        }
        .buy-form button:hover {
            background-color: #ff6b6b;
        }
        @media (max-width: 668px) {
            .container {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<?php
if ($product_id > 0) {
    $sql = "SELECT * FROM add_product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $product_price = intval($row['product_price']); // Price in ₹
        $price_in_paise = $product_price * 100;

        echo "<div class='container'>";
        echo "<div class='left'>";
        echo "<img src='clothes/img/" . htmlspecialchars($row['product_image1']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>";
        echo "</div>";
        echo "<div class='right'>";
        echo "<h1>" . htmlspecialchars($row['product_name']) . "</h1>";
        echo "<p><strong>Size:</strong> " . htmlspecialchars($row['size']) . "</p>";
        echo "<p><strong>Price:</strong> ₹" . htmlspecialchars($row['product_price']) . "</p>";
        echo "<a href='javascript:history.back()' class='btn secondary'>← Back</a>";
        echo "<button class='btn' onclick='toggleForm()'>Buy Now</button>";
        echo "</div>";
        echo "</div>";

        echo "<div class='buy-form' id='buyNowForm' style='display:none;'>";
        echo "<h2>Enter Your Details</h2>";
        echo "<form id='orderForm'>";
        echo "<input type='hidden' name='product_id' value='" . $row['product_id'] . "'>";

        echo "<label for='name'>Full Name</label>";
        echo "<input type='text' id='name' name='name' required>";

        echo "<label for='address'>Address</label>";
        echo "<textarea id='address' name='address' required></textarea>";

        echo "<label for='phone'>Contact Number</label>";
        echo "<input type='text' id='phone' name='phone' required>";

        echo "<label for='rental_date'>Rental Date</label>";
        echo "<input type='date' id='rental_date' name='rental_date' required>";

        echo "<label for='confirm_size'>Confirm Size</label>";
        echo "<select id='confirm_size' name='confirm_size' required>";
        echo "<option value=''>Select Size</option>";
        echo "<option value='S'>S</option>";
        echo "<option value='M'>M</option>";
        echo "<option value='L'>L</option>";
        echo "<option value='XL'>XL</option>";
        echo "</select>";

        echo "<label for='return_date'>Return Date</label>";
        echo "<input type='date' id='return_date' name='return_date' required>";

        echo "<button type='button' onclick='payNow($price_in_paise)'>Pay & Order</button>";
        echo "</form>";
        echo "</div>";
    } else {
        echo "<p style='text-align: center;'>Item not found.</p>";
    }
    $stmt->close();
} else {
   // echo "<p style='text-align: center;'>Invalid item.</p>";
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
    today.setHours(0, 0, 0, 0);
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

function payNow(product_price) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        alert('Please log in or sign up to place an order.');
        return;
    <?php endif; ?>

    if (!validateForm()) return;

    const form = document.getElementById('orderForm');
    const formData = new FormData(form);

    const options = {
        key: 'rzp_test_UpIEIXEWRj5It2', // Your Razorpay test key
        amount: product_price,
        currency: 'INR',
        name: 'StyleShare',
        description: 'Rental Payment',
        handler: function (response) {
                    formData.append('razorpay_payment_id', response.razorpay_payment_id);
            formData.append('user_id', '<?php echo $_SESSION["user_id"]; ?>');

            fetch('submit-order.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                alert('Order placed successfully!');
                window.location.href = 'index.php';
            })
            .catch(err => {
                alert('Payment was successful, but order failed. Please contact support.');
                console.error('Order Error:', err);
            });
        },
        prefill: {
            name: document.getElementById('name').value,
            email: '', // You can prefill email if you store it in session
            contact: document.getElementById('phone').value
        },
        theme: {
            color: '#ff6b6b'
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>
   
