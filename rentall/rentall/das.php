<?php 

if (session_status() == PHP_SESSION_NONE) {
 session_start();
}
if(!isset($_SESSION['aname']))
{
 header("location:login.php");
}


    include 'dbconnection.php';
    
    $sqql = "SELECT * FROM `user_data`";
        $result= mysqli_query($con, $sqql);
        $row = mysqli_num_rows($result);

        $sql = "SELECT * FROM `add_product`";
        $resultt= mysqli_query($con, $sql);
        $roww = mysqli_num_rows($resultt);   
        
        $sq = "SELECT * FROM `product_order`";
        $resul= mysqli_query($con, $sq);
        $ro = mysqli_num_rows($resul); 
        
        $s = "SELECT DISTINCT Product_Brand FROM `add_product`";
        $resu= mysqli_query($con, $s);
        $r = mysqli_num_rows($resu); 


        






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .dashboard {
            width: 90%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            columns: 2;
            margin-left: 25px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 20px;
            filter: drop-shadow(2px 4px 6px black);
          
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h2 {
            font-size: 48px;
            margin-bottom: 10px;
            color: #fff;
        }

        .card p {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }

        .card a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            width: 100%;
            background-color: #f4f4f4;
            color: #666;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .card a:hover {
            background-color: #e2e2e2;
        }

        .blue { background-color: #007bff; }
        .green { background-color: #28a745; }
        .orange { background-color: #fd7e14; }
    </style>
</head>
<body>
    <?php 
    include 'ad_main.php';
    ?>
<div class="dashboard">
        <div class="card blue">
        <h2> <?php echo $row; ?></h2>
        <p>REG USERS</p>
        <a href='rege.php'>FULL DETAIL ➔</a>
    </div>
    <div class='card green'>
        <h2> <?php echo $roww;?></h2>
        <p>LISTED PRODUCT</p>
        <a href='remove_product.php'>FULL DETAIL ➔</a>
    </div>
    <div class='card blue'>
        <h2><?php echo $ro;?></h2>
        <p>TOTAL ORDER</p>
        <a href='confirm.php'>FULL DETAIL ➔</a>
    </div>
    <div class='card orange'>
        <h2><?php echo $r;?></h2>
        <p>LISTED BRANDS</p>
        <a href='brandlist.php'>FULL DETAIL ➔</a>
    </div>
</div> 
</body>
</html>