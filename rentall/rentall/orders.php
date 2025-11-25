<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <title>Order Now</title>
</head>
<body>
<main>
    <form action="" method="POST">
        <h1>Buy Now</h1>
       
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="address">Address:</label>
            <input type="text" name="address" id="address">
        </div>
        <div>
            <label for="mobile">Mobile_no:</label>
            <input type="mobile" name="mobile" id="mobile">
        </div>
        <div>
            <label for="product">Product:</label>
            
             <select name="category"class="c">
            <option value="kurta"selected>kurta</option>
            <option value="suit"selected>suit</option>
            
            
                      
        </select>
        </div>
       
        <button type="submit" name="buy_now">Buy Now</button>
        <footer>Already a member? <a href="login.php">Login here</a></footer>
    </form>
</main>
</body>
</html>
<?php
//error_reporting(0);
$con= mysqli_connect("localhost","root","","rentalcloth");
if(isset($_POST['buy_now']))
{
       $email = $_POST['email'];  
       $address= $_POST['address'];  
       $mobile_no= $_POST['mobile'];  
       //$product=$_POST['category'];
      
   
      
     $sql="insert into orders values('$name','$address','$phone')";

    if (mysqli_query($con, $sql)) {
        echo "<script>console.log('Order inserted successfully');</script>";
        error_log("Order inserted successfully.");
    } else {
        echo "<script>console.error('Insert failed: " . addslashes(mysqli_error($con)) . "');</script>";
        error_log("Insert failed: " . mysqli_error($con));
    }
}
?>