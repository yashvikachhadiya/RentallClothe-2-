<?php
$password = "your_password_here";  // Replace with any password you want
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
