<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$item_id = $_POST['item_id'];
//$rental_date = $_POST['rental_date'];
//$return_date = $_POST['return_date'];

$sql = "SELECT * FROM product_order
        WHERE product_id = ? 
        AND (rental_date <= ? AND return_date >= ?)";

//$stmt = $conn->prepare($sql);
//$stmt->bind_param("iss", $item_id, $return_date, $rental_date);
//$stmt->execute();
//$result = $stmt->get_result();

//if ($result->num_rows > 0) {
//    echo "unavailable";
//} else {
  //  echo "available";
//}

//$stmt->close();
$conn->close();
?>
