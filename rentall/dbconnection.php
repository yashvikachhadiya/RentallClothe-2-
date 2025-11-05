<?php
    $server="localhost";
    $username="root";
    $password="";
    $database = "rentalcloth";

    $con=mysqli_connect($server, $username, $password , $database);

    if(!$con)
    {
        die("connection failed: " . mysqli_connect_error());
    }
   
    
  
   
    ?>