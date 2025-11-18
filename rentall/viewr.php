<?php
session_start();

// Check if admin is logged in
if (empty($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "rentalcloth";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Search functionality */
$search = isset($_GET['search']) ? trim($conn->real_escape_string($_GET['search'])) : '';

/* Fetch all registrations */
$sql = "SELECT * FROM signup";
if ($search) {
    $sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}
$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);

/* Get total users count */
$count_sql = "SELECT COUNT(*) as total FROM signup";
if ($search) {
    $count_sql .= " WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_registrations = $count_row['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations - StyleShare Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF006E;
            --primary-dark: #C2185B;
            --secondary: #8338EC;
            --accent: #3A86FF;
            --accent-2: #06FFA5;
            --bg-dark: #0A0E27;
            --bg-dark-2: #0F1535;
            --text-light: #E8EAED;
            --text-muted: #A0A3A8;
            --gradient-2: linear-gradient(135deg, #FF006E 0%, #8338EC 50%, #3A86FF 100%);
            --gradient-3: linear-gradient(135deg, #06FFA5 0%, #3A86FF 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-dark-2) 100%);
            color: var(--text-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 0, 110, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(131, 56, 236, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
            animation: particleFloat 20s ease-in-out infinite;
        }

        @keyframes particleFloat {
            0%, 100% { transform: translate(0, 0); }
            25% { transform: translate(30px, -30px); }
            50% { transform: translate(-20px, 20px); }
            75% { transform: translate(20px, 30px); }
        }

        .page-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: rgba(15, 21, 53, 0.8);
            backdrop-filter: blur(30px);
            border-right: 1px solid rgba(255, 0, 110, 0.1);
            padding: 30px 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 25px 30px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
            text-align: center;
        }

        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin: 10px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            color: var(--primary);
            border-left-color: var(--primary);
            background: rgba(255, 0, 110, 0.1);
            padding-left: 30px;
        }

        .sidebar-menu a i {
            font-size: 18px;
            width: 20px;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
        }

        /* HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            background: var(--gradient-2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid rgba(255, 0, 110, 0.3);
            border-radius: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .back-link:hover {
            background: rgba(255, 0, 110, 0.2);
            border-color: var(--primary);
            transform: translateX(-5px);
        }

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 0 20px rgba(255, 0, 110, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 0, 110, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* SEARCH */
        .search-section {
            margin-bottom: 30px;
        }

        .search-form {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-form input {
            flex: 1;
            max-width: 400px;
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            outline: none;
            background: rgba(255, 0, 110, 0.1);
            border-color: rgba(255, 0, 110, 0.5);
            box-shadow: 0 0 15px rgba(255, 0, 110, 0.2);
        }

        .search-form input::placeholder {
            color: var(--text-muted);
        }

        .search-btn, .clear-btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .search-btn {
            background: var(--gradient-2);
            color: white;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(255, 0, 110, 0.4);
        }

        .clear-btn {
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid rgba(255, 0, 110, 0.3);
            color: var(--primary);
        }

        .clear-btn:hover {
            background: rgba(255, 0, 110, 0.2);
        }

        /* TABLE */
        .table-wrapper {
            overflow-x: auto;
        }

        .registrations-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            overflow: hidden;
        }

        .registrations-table thead {
            background: rgba(255, 0, 110, 0.1);
            border-bottom: 1px solid rgba(255, 0, 110, 0.2);
        }

        .registrations-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .registrations-table td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-light);
        }

        .registrations-table tbody tr {
            transition: all 0.3s ease;
        }

        .registrations-table tbody tr:hover {
            background: rgba(255, 0, 110, 0.08);
        }

        .registrations-table tbody tr:last-child td {
            border-bottom: none;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .no-data i {
            display: block;
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--text-muted);
            opacity: 0.5;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .main-content {
                padding: 25px;
            }

            .page-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1001;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }

            .search-form input {
                max-width: 100%;
            }

            .registrations-table {
                font-size: 0.9rem;
            }

            .registrations-table th,
            .registrations-table td {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 15px;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .registrations-table th,
            .registrations-table td {
                padding: 8px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">StyleShare</div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="user_details.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
            <li><a href="clothform.php"><i class="fas fa-plus-circle"></i> Add Product</a></li>
            <li><a href="viewproduct.php"><i class="fas fa-cubes"></i> View Products</a></li>
            <li><a href="viewr.php"><i class="fas fa-users"></i> Registrations</a></li>
            <li><a href="admin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-users"></i> User Registrations</h1>
            <a href="user_details.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_registrations; ?></div>
                <div class="stat-label">Total Registrations</div>
            </div>
        </div>

        <!-- SEARCH SECTION -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if ($search): ?>
                    <a href="viewr.php" class="clear-btn">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- REGISTRATIONS TABLE -->
        <div class="table-wrapper">
            <table class="registrations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($row['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    <p><?php echo $search ? 'No registrations found matching your search.' : 'No user registrations available yet.'; ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    // Mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        
        // Close sidebar on mobile when clicking links
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>
<?php $conn->close(); ?>
