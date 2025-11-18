<?php
// =================================================================
// START PHP LOGIC TO GET USERNAME (Must be included on every page)
// =================================================================

// 1. Start the session to access $_SESSION variables
// NOTE: session_start() must be the very first thing executed.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// !!! REPLACE THESE WITH YOUR ACTUAL DATABASE CREDENTIALS !!!
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "your_database_name"; // <-- ***CHANGE THIS***
$user_table = "users";          // <-- ***CHANGE THIS***
$name_column = "username_column"; // <-- ***CHANGE THIS***
// !!! --------------------------------------------------- !!!

$user_name = null;

if (!empty($_SESSION['user_id'])) {
    // Attempt to connect to the database
    $conn = @new mysqli($servername, $db_username, $db_password, $dbname);

    if (!$conn->connect_error) {
        $user_id = $_SESSION['user_id'];
        
        // Use prepared statement to prevent SQL injection
        $sql = "SELECT {$name_column} FROM {$user_table} WHERE user_id = ?"; 
        
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $user_name = $row[$name_column];
            }
            $stmt->close();
        }
        $conn->close();
    }
}
// Set a default if null
$user_name = $user_name ?: 'User';
// =================================================================
// END PHP LOGIC
// =================================================================
?>
<header style="background-color:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.1); padding:10px 40px;">
    <div class="header-inner" style="display:flex; justify-content:space-between; align-items:center;">

        <div class="logo" style="font-size:26px; font-weight:700; color:#333;">Style<span style="color:#FF6B6B;">Share</span></div>

        <ul class="nav-links" style="display:flex; align-items:center; gap:24px; margin:0; padding:0; list-style:none;">
            <li><a href="index.php" style="text-decoration:none; color:#555; font-weight:500;">Home</a></li>
            <li><a href="collection.php" class="active" style="text-decoration:none; color:#555; font-weight:500;">Collection</a></li>
            <li><a href="aboutus.php" style="text-decoration:none; color:#555; font-weight:500;">About Us</a></li>
            <li><a href="contactus.php" style="text-decoration:none; color:#555; font-weight:500;">Contact</a></li>
        </ul>

        <div class="header-buttons" style="display:flex; align-items:center; gap:12px;">
            <?php if (!empty($_SESSION['user_id'])): ?>
                
                <div class="header-welcome" style="color:#555; font-size:16px;">Welcome, <span style="font-weight:700;"><?php echo htmlspecialchars($user_name); ?></span></div>
                
                <a href="profile.php" class="btn secondary" style="text-decoration:none; padding:10px 20px; border-radius:30px; background-color:#fff; color:#FF6B6B; border:2px solid #FF6B6B; font-weight:600; white-space:nowrap;">My Profile</a>
                
                <a href="logout.php" class="btn" style="text-decoration:none; padding:10px 20px; border-radius:30px; background-color:#FF6B6B; color:#fff; font-weight:600; white-space:nowrap;">Logout</a>

            <?php else: ?>
                <a href="login.php" class="btn" style="text-decoration:none; padding:10px 20px; border-radius:30px; background-color:#FF6B6B; color:#fff; font-weight:600; white-space:nowrap;">Log in</a>
                
                <a href="signup.php" class="btn secondary" style="text-decoration:none; padding:10px 20px; border-radius:30px; background-color:#fff; color:#FF6B6B; border:2px solid #FF6B6B; font-weight:600; white-space:nowrap;">Sign Up</a>
            <?php endif; ?>
             
            <a href="admin.php" class="btn" style="text-decoration:none; padding:10px 20px; border-radius:30px; background-color:#FF6B6B; color:#fff; font-weight:600; white-space:nowrap; margin-left:8px;">Admin</a>
        </div>
    </div>
</header>