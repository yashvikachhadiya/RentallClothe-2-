+
<?php
// =================================================================
// 1. PHP SESSION & DATABASE CONNECTION (Updated - deduplicate products)
// =================================================================
session_start();
$user_name = $_SESSION['user_name'] ?? '';
$is_logged_in = !empty($_SESSION['user_id']); // boolean now

// Database Connection
$conn = new mysqli("localhost", "root", "", "rentalcloth");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Fetch Products from Database (raw)
$sql = "SELECT * FROM add_product ORDER BY product_id DESC";
$result = $conn->query($sql);

// Build unique product list (dedupe by image file name first, fallback to product name)
$products = [];
if ($result && $result->num_rows > 0) {
    $seen = [];
    while ($row = $result->fetch_assoc()) {
        // Key preference: image name (trim & lower), if empty use product name
        $imgKey = isset($row['product_image1']) ? strtolower(trim($row['product_image1'])) : '';
        $nameKey = isset($row['product_name']) ? strtolower(trim($row['product_name'])) : '';
        $key = $imgKey !== '' ? $imgKey : $nameKey;

        // if key is empty, fallback to product_id to avoid accidental skips
        if ($key === '') {
            $key = 'id_' . intval($row['product_id']);
        }

        if (isset($seen[$key])) {
            // skip duplicate
            continue;
        }
        $seen[$key] = true;
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>The Collection | StyleShare Luxury</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* (all your CSS unchanged — omitted here for brevity in the example)
       Paste your existing <style> block here unchanged. */
    /* =========================
       UNIQUE VARIABLES & RESET
       ========================= */
    :root {
        --primary: #FF006E;
        --primary-dark: #C2185B;
        --primary-light: #FF4D8F;
        --secondary: #8338EC;
        --accent: #3A86FF;
        --accent-2: #06FFA5;
        --text-main: #0D1B2A;
        --text-light: #415A77;
        --bg-body: #F8F9FA;
        --bg-dark: #0A0E27;
        --bg-glass: rgba(255, 255, 255, 0.1);
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        --gradient-2: linear-gradient(135deg, #FF006E 0%, #8338EC 50%, #3A86FF 100%);
        --gradient-3: linear-gradient(135deg, #06FFA5 0%, #3A86FF 100%);
        --shadow-neon: 0 0 20px rgba(255, 0, 110, 0.5), 0 0 40px rgba(131, 56, 236, 0.3);
        --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.1);
        --shadow-float: 0 20px 60px rgba(0, 0, 0, 0.15);

        --header-height: 80px;
        --category-height: 70px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; line-height: 1.7; color: var(--text-main); background: linear-gradient(135deg, #0A0E27 0%, #1A1F3A 50%, #0A0E27 100%); background-size: 400% 400%; animation: gradientShift 15s ease infinite; overflow-x: hidden; scroll-behavior: smooth; position: relative; }
    @keyframes gradientShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    body::before { content: ''; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.15) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(131, 56, 236, 0.15) 0%, transparent 50%), radial-gradient(circle at 40% 20%, rgba(58, 134, 255, 0.1) 0%, transparent 50%); animation: particleFloat 20s ease-in-out infinite; pointer-events: none; z-index: 0; }
    @keyframes particleFloat { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(30px, -30px) scale(1.1); } 66% { transform: translate(-20px, 20px) scale(0.9); } }
    a { text-decoration: none; color: inherit; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    button { cursor: pointer; border: none; outline: none; font-family: inherit; }
    ul { list-style: none; }
    img { max-width: 100%; display: block; }
    h1, h2, h3 { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
    .container { width: 90%; max-width: 1400px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 1; }

    header { background: rgba(10, 14, 39, 0.7); backdrop-filter: blur(30px) saturate(180%); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1); position: fixed; width: 100%; top: 0; z-index: 1000; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); border-bottom: 1px solid rgba(255, 255, 255, 0.1); height: var(--header-height); display: flex; align-items: center; }
    header.scrolled { background: rgba(10, 14, 39, 0.95); box-shadow: 0 12px 50px rgba(0, 0, 0, 0.4), 0 0 30px rgba(255, 0, 110, 0.2); height: var(--header-height); }
    .header-container { display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0 20px; }
    .logo { font-size: 2.2rem; font-weight: 700; font-family: 'Playfair Display', serif; background: var(--gradient-2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; transition: all 0.4s ease; position: relative; }
    .logo::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 3px; background: var(--gradient-2); transition: width 0.4s ease; }
    .logo:hover::after { width: 100%; }
    .logo:hover { transform: scale(1.08) translateY(-2px); filter: drop-shadow(0 0 10px rgba(255, 0, 110, 0.8)); }
    .logo span { background: var(--gradient-3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .nav-links { display: flex; gap: 40px; }
    .nav-links a { font-weight: 500; font-size: 15px; color: rgba(255, 255, 255, 0.9); position: relative; padding: 8px 0; text-transform: uppercase; letter-spacing: 0.5px; }
    .nav-links a::before { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 0; height: 2px; background: var(--gradient-2); transition: width 0.4s ease; box-shadow: 0 0 10px var(--primary); }
    .nav-links a:hover::before, .nav-links a.active::before { width: 100%; }
    .nav-links a:hover, .nav-links a.active { color: white; text-shadow: 0 0 10px rgba(255, 0, 110, 0.8); }
    .user-actions { display: flex; align-items: center; gap: 15px; }
    .user-badge { font-size: 14px; font-weight: 600; color: rgba(255, 255, 255, 0.9); padding: 10px 20px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.2); }

    .category-header { position: fixed; left: 0; right: 0; height: var(--category-height); top: var(--header-height); background: rgba(10, 14, 39, 0.8); backdrop-filter: blur(30px) saturate(180%); border-bottom: 1px solid rgba(255, 255, 255, 0.1); z-index: 999; display: flex; align-items: center; overflow-x: auto; padding: 0 30px; scrollbar-width: none; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); }
    .category-header::-webkit-scrollbar { display: none; }
    .cat-nav { display: flex; gap: 15px; margin: 0 auto; max-width: 1400px; width: 100%; }
    .cat-link { padding: 10px 20px; border-radius: 30px; font-size: 13px; font-weight: 500; color: rgba(255, 255, 255, 0.7); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; gap: 8px; white-space: nowrap; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
    .cat-link:hover { background: rgba(255, 255, 255, 0.1); color: white; border-color: rgba(255, 255, 255, 0.2); transform: translateY(-2px); }
    .cat-link.active { background: var(--gradient-2); color: white; box-shadow: var(--shadow-neon); border-color: transparent; transform: translateY(-2px); }

    .main-container { max-width: 1400px; margin: 0 auto; padding: calc(var(--header-height) + var(--category-height) + 40px) 30px 60px; min-height: 80vh; position: relative; z-index: 1; }
    .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; flex-wrap: wrap; gap: 30px; }
    .page-title h1 { font-family: 'Playfair Display'; font-size: 3.5rem; margin-bottom: 10px; background: var(--gradient-2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 0 20px rgba(255, 0, 110, 0.5)); }
    .page-title p { color: rgba(255, 255, 255, 0.7); font-size: 1.1rem; font-weight: 300; }
    .tools-right { display: flex; gap: 15px; align-items: center; }

    .sort-wrapper { position: relative; }
    .sort-select { appearance: none; padding: 14px 40px 14px 20px; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); font-family: inherit; font-size: 13px; color: white; cursor: pointer; outline: none; box-shadow: var(--shadow-glass); transition: all 0.4s ease; }
    .sort-select:hover { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.3); }
    .sort-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2); }
    .sort-arrow { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 12px; color: rgba(255, 255, 255, 0.7); }

    .search-wrapper { position: relative; width: 280px; }
    .search-wrapper input { width: 100%; padding: 14px 20px 14px 45px; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(20px); outline: none; font-family: inherit; color: white; transition: all 0.4s ease; box-shadow: var(--shadow-glass); }
    .search-wrapper input::placeholder { color: rgba(255, 255, 255, 0.5); }
    .search-wrapper input:focus { border-color: var(--primary); background: rgba(255, 255, 255, 0.15); box-shadow: 0 0 0 4px rgba(255, 0, 110, 0.2), var(--shadow-glass); }
    .search-wrapper i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); }

    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; }
    .card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px) saturate(180%); border-radius: 25px; position: relative; transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1); overflow: hidden; animation: fadeInUp 0.8s ease-out both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .card:hover { transform: translateY(-15px) scale(1.02); box-shadow: var(--shadow-neon), 0 25px 60px rgba(0, 0, 0, 0.3); border-color: rgba(255, 255, 255, 0.3); background: rgba(255, 255, 255, 0.08); }

    .card-media { position: relative; aspect-ratio: 3/4; overflow: hidden; border-radius: 20px; background: #1a1a2e; }
    .card-media img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1); filter: brightness(0.9) contrast(1.1); }
    .card:hover .card-media img { transform: scale(1.15) rotate(1deg); filter: brightness(1) contrast(1.2); }

    .card-actions { position: absolute; bottom: 15px; left: 15px; right: 15px; display: flex; gap: 10px; transform: translateY(30px); opacity: 0; transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
    .card:hover .card-actions { transform: translateY(0); opacity: 1; }
    .action-btn { flex: 1; padding: 12px; text-align: center; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); backdrop-filter: blur(10px); }
    .btn-view { background: rgba(255, 255, 255, 0.95); color: #0D1B2A; border: 1px solid rgba(255, 255, 255, 0.3); }
    .btn-view:hover { background: white; transform: scale(1.05); box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3); }
    .btn-rent { background: var(--gradient-2); color: white; border: 1px solid transparent; }
    .btn-rent:hover { transform: scale(1.05); box-shadow: var(--shadow-neon); }
    .btn-share { width: 45px; padding: 0; background: #25D366; color: white; font-size: 16px; flex: none; border: 1px solid transparent; }
    .btn-share:hover { background: #1da851; transform: scale(1.1) rotate(5deg); }

    .badge { position: absolute; top: 15px; left: 15px; padding: 6px 14px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); font-size: 10px; font-weight: 800; text-transform: uppercase; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 2; color: var(--primary); }

    .card-info { padding: 20px 15px; }
    .card-title { font-family: 'Playfair Display', serif; font-size: 18px; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: white; }
    .rating { font-size: 12px; color: #FFD700; margin-bottom: 10px; font-weight: 600; }
    .rating span { color: rgba(255, 255, 255, 0.6); font-weight: 400; margin-left: 5px; font-size: 11px; }
    .tags { font-size: 10px; color: rgba(255, 255, 255, 0.5); margin-bottom: 10px; display: block; letter-spacing: 0.5px; }
    .price { font-size: 20px; font-weight: 700; color: white; background: var(--gradient-2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .price-tag { font-size: 12px; color: rgba(255, 255, 255, 0.6); }

    #scrollTopBtn { position: fixed; bottom: 30px; right: 30px; z-index: 1000; background: var(--gradient-2); color: white; width: 55px; height: 55px; border-radius: 50%; border: none; font-size: 20px; box-shadow: var(--shadow-neon); cursor: pointer; opacity: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); transform: translateY(20px) scale(0.8); pointer-events: none; }
    #scrollTopBtn.visible { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
    #scrollTopBtn:hover { transform: translateY(-5px) scale(1.1); box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.5); }

    .login-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); z-index: 2000; display: none; align-items: center; justify-content: center; animation: fadeIn 0.3s ease; }
    .login-modal.active { display: flex; }
    .modal-box { background: rgba(10, 14, 39, 0.95); backdrop-filter: blur(30px); width: 90%; max-width: 450px; padding: 50px 40px; border-radius: 30px; text-align: center; box-shadow: var(--shadow-neon), 0 30px 80px rgba(0, 0, 0, 0.4); border: 1px solid rgba(255, 255, 255, 0.1); transform: translateY(20px); animation: slideUp 0.4s ease forwards; color: white; }
    @keyframes slideUp { to { transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .modal-icon { width: 80px; height: 80px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto 25px; border: 2px solid rgba(255, 255, 255, 0.2); }
    .modal-box h2 { font-size: 2rem; margin-bottom: 15px; background: var(--gradient-2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .modal-box p { color: rgba(255, 255, 255, 0.7); margin-bottom: 30px; line-height: 1.8; }
    .modal-btn { display: block; width: 100%; padding: 16px; border-radius: 50px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 14px; margin-bottom: 15px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; border: none; cursor: pointer; }
    .btn-login-modal { background: var(--gradient-2); color: white; box-shadow: var(--shadow-neon); }
    .btn-login-modal:hover { transform: translateY(-3px); box-shadow: var(--shadow-neon), 0 15px 40px rgba(255, 0, 110, 0.5); }
    .btn-close-modal { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); color: rgba(255, 255, 255, 0.8); border: 1px solid rgba(255, 255, 255, 0.2); }
    .btn-close-modal:hover { background: rgba(255, 255, 255, 0.15); color: white; }

    footer { background: rgba(10, 14, 39, 0.95); backdrop-filter: blur(30px); color: #fff; padding: 80px 0 40px; margin-top: 100px; position: relative; overflow: hidden; border-top: 1px solid rgba(255, 255, 255, 0.1); }
    footer::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: var(--gradient-2); box-shadow: 0 0 20px var(--primary); }
    footer::after { content: ''; position: absolute; bottom: -50%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(131, 56, 236, 0.2) 0%, transparent 70%); border-radius: 50%; filter: blur(60px); animation: footerOrb 20s ease-in-out infinite; }
    @keyframes footerOrb { 0%, 100% { transform: translate(0, 0) scale(1); } 50% { transform: translate(-30px, -30px) scale(1.1); } }
    .footer-content { max-width: 1400px; margin: 0 auto; padding: 0 30px; text-align: center; position: relative; z-index: 1; }
    .footer-logo { font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; margin-bottom: 20px; background: var(--gradient-2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; display: inline-block; }
    .footer-text { font-size: 14px; color: rgba(255, 255, 255, 0.6); line-height: 1.8; }
    .footer-links { display: flex; justify-content: center; gap: 30px; margin-top: 30px; flex-wrap: wrap; }
    .footer-links a { color: rgba(255, 255, 255, 0.7); font-size: 14px; transition: all 0.3s ease; position: relative; }
    .footer-links a::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 0; height: 2px; background: var(--gradient-2); transition: width 0.3s ease; }
    .footer-links a:hover::after { width: 100%; }
    .footer-links a:hover { color: white; text-shadow: 0 0 10px rgba(255, 0, 110, 0.8); }
    .footer-copyright { margin-top: 40px; padding-top: 30px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.5); font-size: 13px; }

    /* Responsive rules (as in your original file) */
    @media (max-width: 1024px) {
        :root { --header-height: 70px; --category-height: 60px; }
        .grid { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; }
        .page-title h1 { font-size: 2.8rem; }
    }
    @media (max-width: 768px) {
        :root { --header-height: 60px; --category-height: 60px; }
        .header-container { padding: 0 15px; }
        .nav-links { display: none; }
        .category-header { top: var(--header-height); height: var(--category-height); padding: 0 20px; }
        .main-container { padding: calc(var(--header-height) + var(--category-height) + 20px) 20px 40px; }
        .toolbar { flex-direction: column; align-items: flex-start; gap: 20px; }
        .page-title h1 { font-size: 2.2rem; }
        .tools-right { width: 100%; flex-direction: column; gap: 15px; }
        .search-wrapper { width: 100%; }
        .grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px; }
        .card-actions { opacity: 1; transform: translateY(0); position: relative; bottom: 0; left: 0; right: 0; padding: 15px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); display: flex; margin-top: 10px; }
        .footer-links { flex-direction: column; gap: 15px; }
        #scrollTopBtn { width: 50px; height: 50px; bottom: 20px; right: 20px; }
    }
    @media (max-width: 480px) {
        :root { --header-height: 56px; --category-height: 56px; }
        .grid { grid-template-columns: 1fr; }
        .page-title h1 { font-size: 1.8rem; }
        .footer-logo { font-size: 2rem; }
    }
</style>
</head>
<body>

    <header>
        <div class="container header-container">
            <div class="logo">Style<span>Share</span></div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="collection.php" class="active">Collection</a>
                <a href="aboutus.php">About</a>
                <a href="contactus.php">Contact</a>
            </nav>
            <div class="user-actions">
                <?php if (!empty($user_name)): ?>
                    <div class="user-badge"><i class="far fa-user"></i> <?= htmlspecialchars($user_name) ?></div>
                    <a href="logout.php" title="Logout" style="color: rgba(255,255,255,0.9); font-size: 18px;"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="login.php" style="font-weight:600; color:rgba(255,255,255,0.9); padding: 10px 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 25px; border: 1px solid rgba(255,255,255,0.2);">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="category-header">
        <nav class="cat-nav" id="chipContainer">
            <button class="cat-link active" data-cat="all" onclick="filterItems('all', this)"><i class="fas fa-th-large"></i> All</button>
            <button class="cat-link" data-cat="lahenga" onclick="filterItems('lahenga', this)"><i class="fas fa-venus"></i> Lahenga</button>
            <button class="cat-link" data-cat="saree" onclick="filterItems('saree', this)"><i class="fas fa-infinity"></i> Saree</button>
            <button class="cat-link" data-cat="gown" onclick="filterItems('gown', this)"><i class="fas fa-female"></i> Gown</button>
            <button class="cat-link" data-cat="choli" onclick="filterItems('choli', this)"><i class="fas fa-star"></i> Choli</button>
            <button class="cat-link" data-cat="kurti" onclick="filterItems('kurti', this)"><i class="fas fa-tshirt"></i> Kurti</button>
            <button class="cat-link" data-cat="jewellery" onclick="filterItems('jewellery', this)"><i class="far fa-gem"></i> Jewellery</button>
            <button class="cat-link" data-cat="co-ords" onclick="filterItems('co-ords', this)"><i class="fas fa-layer-group"></i> Co-ords</button>
            <button class="cat-link" data-cat="suit" onclick="filterItems('suit', this)"><i class="fas fa-user-tie"></i> Men's Suits</button>
            <button class="cat-link" data-cat="sherwani" onclick="filterItems('sherwani', this)"><i class="fas fa-crown"></i> Sherwani</button>
            <button class="cat-link" data-cat="kurta" onclick="filterItems('kurta', this)"><i class="fas fa-vest"></i> Men's Kurta</button>
            <button class="cat-link" data-cat="kids" onclick="filterItems('kids', this)"><i class="fas fa-child"></i> Kids</button>
        </nav>
    </div>

    <div class="main-container">
        
        <div class="toolbar">
            <div class="page-title">
                <h1>The Collection</h1>
                <p>Discover premium rentals for your special moments</p>
            </div>
            <div class="tools-right">
                <div class="sort-wrapper">
                    <select id="sortSelect" class="sort-select" onchange="sortProducts()">
                        <option value="newest">New Arrivals</option>
                        <option value="low-high">Price: Low to High</option>
                        <option value="high-low">Price: High to Low</option>
                    </select>
                    <i class="fas fa-chevron-down sort-arrow"></i>
                </div>
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search styles, colors...">
                </div>
            </div>
        </div>

        <div class="grid" id="productGrid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <?php 
                        // Random Data for Visuals
                        $rating = number_format(mt_rand(45, 50) / 10, 1);
                        $reviews = rand(12, 98);
                        
                        // Generate Occasion Tags based on name (Mock logic)
                        $tag = "#Wedding";
                        if(stripos($row['product_name'], 'party') !== false) $tag = "#Party";
                        if(stripos($row['product_name'], 'haldi') !== false) $tag = "#Haldi";

                        $productId = intval($row['product_id']);
                        $imgFile = htmlspecialchars($row['product_image1']);
                        $productNameEsc = htmlspecialchars($row['product_name']);
                        $productPrice = number_format($row['product_price']);
                    ?>
                    
                    <div class="card" 
                         data-name="<?php echo strtolower($productNameEsc); ?>" 
                         data-price="<?php echo $row['product_price']; ?>">
                         
                        <div class="card-media">
                            <span class="badge">Premium</span>
                            <img src="clothes/img/<?php echo $imgFile; ?>" 
                                 alt="<?php echo $productNameEsc; ?>"
                                 onerror="this.src='https://via.placeholder.com/400x500?text=No+Image'">
                            
                            <div class="card-actions">
                                <button class="action-btn btn-view" onclick="handleUserAction('collectiondetail.php?id=<?=$productId;?>')">
                                    <i class="far fa-eye"></i> View
                                </button>
                                <button class="action-btn btn-rent" onclick="handleUserAction('rent_now.php?id=<?=$productId;?>')">
                                    Rent Now
                                </button>
                                <button class="action-btn btn-share" onclick="shareProduct('<?php echo addslashes($productNameEsc); ?>')" title="Share">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-info">
                            <span class="tags"><?= $tag ?></span>
                            <h3 class="card-title"><?php echo $productNameEsc; ?></h3>
                            
                            <div class="rating">
                                <i class="fas fa-star"></i> <?= $rating ?> <span>(<?= $reviews ?>)</span>
                            </div>

                            <div class="price-box">
                                <span class="price">₹<?php echo $productPrice; ?></span>
                                <span class="price-tag">/ day</span>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: rgba(255,255,255,0.3); margin-bottom: 20px;"></i>
                    <p style="color: rgba(255,255,255,0.7); font-size: 1.1rem;">No items found in the collection.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div id="loginModal" class="login-modal" onclick="if(event.target === this) closeLoginModal()">
        <div class="modal-box">
            <div class="modal-icon"><i class="fas fa-lock"></i></div>
            <h2>Exclusive Access</h2>
            <p>Join our premium community to view full details, check availability, and rent designer outfits.</p>
            <a href="login.php" class="modal-btn btn-login-modal">Log In to Continue</a>
            <button class="modal-btn btn-close-modal" onclick="closeLoginModal()">Browse as Guest</button>
        </div>
    </div>

    <button id="scrollTopBtn" onclick="scrollToTop()"><i class="fas fa-arrow-up"></i></button>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">StyleShare</div>
            <p class="footer-text">Your premium destination for designer fashion rentals</p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="collection.php">Collection</a>
                <a href="aboutus.php">About Us</a>
                <a href="contactus.php">Contact</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2025 StyleShare Luxury Rentals. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // 1. LOGIN STATUS (now a real boolean)
        const isUserLoggedIn = <?php echo json_encode($is_logged_in); ?>;

        // 2. LOGIN GATE (View/Rent)
        function handleUserAction(targetUrl) {
            if (isUserLoggedIn) {
                window.location.href = targetUrl;
            } else {
                document.getElementById('loginModal').classList.add('active');
            }
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
        }

        // 3. SHARE FUNCTION
        function shareProduct(name) {
            const message = "Check out this amazing outfit: " + name + " on StyleShare!";
            const url = "https://wa.me/?text=" + encodeURIComponent(message);
            window.open(url, '_blank');
        }

        // 4. SMART AUTO-FILTER (From Home Page)
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const catParam = urlParams.get('category');
            
            if(catParam) {
                const btn = document.querySelector(`.cat-link[data-cat="${catParam.toLowerCase()}"]`);
                if(btn) filterItems(catParam, btn);
            } else {
                const allBtn = document.querySelector('.cat-link[data-cat="all"]');
                if(allBtn) allBtn.classList.add('active');
            }
        };

        // 5. SORTING LOGIC
        function sortProducts() {
            const grid = document.getElementById('productGrid');
            const cards = Array.from(grid.getElementsByClassName('card'));
            const sortValue = document.getElementById('sortSelect').value;

            if (sortValue === 'low-high') {
                cards.sort((a, b) => parseInt(a.dataset.price) - parseInt(b.dataset.price));
            } else if (sortValue === 'high-low') {
                cards.sort((a, b) => parseInt(b.dataset.price) - parseInt(a.dataset.price));
            } else {
                // newest -> keep server order (reload recommended)
                location.reload(); 
            }

            cards.forEach(card => grid.appendChild(card));
        }

        // 6. SEARCH & FILTER
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterGrid(searchTerm);
        });

        function filterItems(category, btn) {
            document.querySelectorAll('.cat-link').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            const term = category === 'all' ? '' : category.toLowerCase();
            filterGrid(term);
        }

        function filterGrid(term) {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                const productName = (card.getAttribute('data-name') || '').toLowerCase();
                if (productName.includes(term)) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.5s ease-out';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // 7. SCROLL TO TOP
        window.onscroll = function() {
            const btn = document.getElementById("scrollTopBtn");
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btn.classList.add("visible");
            } else {
                btn.classList.remove("visible");
            }
        };
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                setTimeout(() => {
                    observer.observe(card);
                }, index * 50);
            });
        });
    </script>   
</body>
</html>
