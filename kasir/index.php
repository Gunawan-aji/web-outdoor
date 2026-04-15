<?php
require_once '../config/functions.php';
requireLogin();

$settings = getSettings();

// Get statistics for cashier
$today_orders = getDailyReport();
$today_total = array_sum(array_column($today_orders, 'total_harga'));
$today_count = count($today_orders);
$products = getAllProducts();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - <?= $settings['site_name'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2d5a27;
            --primary-dark: #1e3d1a;
            --primary-light: #4a7c44;
            --primary-accent: #8bc34a;
            --dark: #1a1a2e;
            --dark-secondary: #16213e;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--dark-secondary);
            color: var(--white);
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .sidebar-brand span {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 1px;
        }

        .menu-item {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border-left-color: var(--primary-accent);
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .admin-header {
            background: var(--white);
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }

        .btn-logout {
            padding: 10px 18px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .content {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 28px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #28a745, #34ce57);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, var(--info), #6dd5ed);
        }

        .stat-info h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-info p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .quick-action {
            background: var(--white);
            padding: 24px;
            border-radius: 14px;
            box-shadow: var(--shadow);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s;
        }

        .quick-action:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .quick-action i {
            width: 48px;
            height: 48px;
            background: var(--light-secondary, #e8f5e9);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
        }

        .quick-action span {
            font-weight: 600;
            color: var(--dark);
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon"><i class="fas fa-mountain"></i></div>
            <span>Kasir</span>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Menu</div>
            <a href="index.php" class="menu-item active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="pos.php" class="menu-item"><i class="fas fa-cash-register"></i><span>Kasir / POS</span></a>
            <a href="orders/index.php" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Riwayat
                    Pesanan</span></a>
            <div class="menu-section">Lainnya</div>
            <a href="../index.php" class="menu-item" target="_blank"><i class="fas fa-external-link-alt"></i><span>Lihat
                    Website</span></a>
            <a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Dashboard Kasir</h1>
            <div class="header-user">
                <div class="user-info">
                    <div class="user-name">
                        <?= $_SESSION['admin_nama'] ?>
                    </div>
                    <div class="user-role">Kasir</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <h3><?= $today_count ?></h3>
                        <p>Pesanan Hari Ini</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3><?= formatCurrency($today_total) ?></h3>
                        <p>Pendapatan Hari Ini</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <a href="pos.php" class="quick-action">
                    <i class="fas fa-plus-circle"></i>
                    <span>Transaksi Baru</span>
                </a>
                <a href="orders/index.php" class="quick-action">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Pesanan</span>
                </a>
            </div>
        </div>
    </main>
</body>

</html>