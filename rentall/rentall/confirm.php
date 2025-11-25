<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['aname']))
{
    header("location:buy_now.php");
}

include 'dbconnection.php';
if(isset($_GET['vd']) && is_numeric($_GET['vd']))
{
                      $ud=$_GET['vd'];
                      $sqll="DELETE FROM `add_product` WHERE Product_Id=$ud";
  
                      if ($con->query($sqll) === true) 
                     {
                        echo " <script>alert('deleted recored.')</script>";
                     }
                     else{
                         echo " <script>alert('cancle .')</script>";
                     }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registered Users</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .container {
        width: 80%;
        margin: 20px auto;
        text-align: center;
    }
    .title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .table-container {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 20px;
        background-color: #f9f9f9;
        width: 119%;
        margin-left: -98px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        tr:nth-child(even)
        {
            background-color: #f0eae3;
        }
        margin-left:10px;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f1f1f1;
        font-weight: bold;
    }
    tr:hover {
        background-color: #f5f5f5;
    }
    .search-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 10px;
    }
    .search-container input {
        padding: 5px;
        width: 200px;
    }
    .pagination {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }
    .pagination button {
        padding: 8px 12px;
        border: none;
        background-color: #ddd;
        cursor: pointer;
        margin-left: 5px;
        border-radius: 4px;
    }
    .pagination button:hover {
        background-color: #ccc;
    }
    a{
        text-decoration: none;
    }
    a:hover{
        color: red;
    }
</style>
<script>
// JavaScript function to handle delete confirmation
function confirmDelete(id) {
    if (confirm("Do you really want to delete this record?")) {
      
       window.location.href = `buy_now.php?vd=${id}`;
    }

}
</script>
</head>
<body>
<?php 
    include 'admin.php';
    ?>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="clothform.php">Add Product</a></li>
            <li><a href="user_details.php">View User Details</a></li>
            <li><a href="viewr.php">View Registrations</a></li>
            <li><a href="confirm.php">confirm order</a></li>
     
            <li><a href="viewproduct.php">View Products</a></li>
                       <li><a href="#" onclick="confirmLogout()">Logout</a></li>

        </ul>
    </div>
    
<div class="container">
    <div class="title">confirm order</div>
    <div class="table-container">
 
       
        <table>
            <thead>
                <tr>
                    <th>User_ID</th>
                    <th>Order_ID</th>
                    <th>Order_Date</th>
                    <th>Order_Time</th>
                    <th>P_ID</th>
                    <th>P_Name</th>
                    <th>P_image</th>
                    <th>P_Price</th>
                
                   
                    <th>Payment_Method</th>
                    
                    <th>Total_Price</th>
                   
                </tr>
            </thead>
            <tbody>
            <?php
        include 'dbconnection.php'; 
         $result = mysqli_query($con, "SELECT * FROM `product_order`") or die('Error');
        
         while ($row = mysqli_fetch_array($result)) 
         {
                $id=$row['User_Id'];
                $o_id=$row['Order_Id'];
                $o_date=$row['Order_Date'];
                $o_date=$row['Order_Time']; 
               
                $p_id=$row['Product_Id'];
                $p_name=$row['Product_Name'];
                $p_image=$row['Product_image'];
              
                $p_price=$row['Product_Price'];
                $p_method=$row['Payment_Method'];
                $t_price=$row['Total_Price'];
              
              
              
                echo "
                <tr>
                    <td>$U_id</td>
                    <td>$o_id</td>
                    <td>$o_date</td>
                    <td>$o_time</td>
                    <td>$p_id</td>
                    <td>$p_name</td>
                    <td>$p_image</td>
                    <td>$p_price</td>
                    
                    <td>$p_method</td>
                
                    <td>$t_price</td>
                </tr>
                ";
         }
        

        
        ?>
            
            </tbody>
        </table>
 
    </div>
</div>

</body>
</html>
