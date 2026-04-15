<?php
require_once '../config/functions.php';
requireLogin();

$settings = getSettings();

// Get statistics
$products_count = count(getAllProducts());
$categories_count = count(getAllCategories());
$messages_count = count(getAllMessages());
$unread_messages = getUnreadMessagesCount();
$gallery_count = count(getAllGallery());
$users_count = count(getAllUsers());
$orders_count = count(getAllOrders());
$recent_products = getAllProducts(5);
$recent_messages = getAllMessages();
$recent_orders = getAllOrders(5);
$daily_sales = getDailySalesData();
$trips_count = count(getAllTrips());

// Calculate today's sales
$today_sales = getDailyReport();
$today_total = array_sum(array_column($today_sales, 'total_harga'));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin <?= $settings['site_name'] ?></title>
    <meta name="robots" content="noindex, nofollow">

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
            --light: #f0f7f0;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
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
            transition: all 0.3s ease;
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
            font-size: 1.2rem;
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
            transition: all 0.3s ease;
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

        .badge {
            margin-left: auto;
            background: var(--danger);
            color: var(--white);
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
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
            font-size: 1.25rem;
        }

        .btn-logout {
            padding: 10px 18px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
            text-decoration: none;
            font-weight: 600;
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
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
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

        .stat-icon.warning {
            background: linear-gradient(135deg, #ffc107, #ffcd39);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #17a2b8, #6dd5ed);
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, var(--danger), #e4606d);
        }

        .stat-icon.mountain {
            background: linear-gradient(135deg, var(--primary), var(--primary-accent));
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

        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-body {
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 16px 25px;
            text-align: left;
        }

        .table th {
            background: var(--gray-100);
            font-weight: 600;
            color: var(--dark);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-600);
        }

        .table tbody tr:hover {
            background: var(--gray-100);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.active,
        .status-badge.available {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.read {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.unread {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.selesai,
        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending,
        .status-badge.open {
            background: #fff3cd;
            color: #856404;
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-edit {
            background: var(--gray-200);
            color: var(--primary);
        }

        .btn-edit:hover {
            background: var(--primary);
            color: var(--white);
        }

        .btn-delete {
            background: #f8d7da;
            color: var(--danger);
        }

        .btn-delete:hover {
            background: var(--danger);
            color: var(--white);
        }

        .btn-view {
            background: #d1ecf1;
            color: var(--info);
        }

        .btn-view:hover {
            background: var(--info);
            color: var(--white);
        }

        .btn-add {
            padding: 10px 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
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
            transition: all 0.3s ease;
        }

        .quick-action:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .quick-action i {
            width: 48px;
            height: 48px;
            background: var(--light);
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

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .admin-header {
                padding: 15px 20px;
            }

            .content {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 0.875rem;
            }

            .table th,
            .table td {
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon"><i class="fas fa-mountain"></i></div>
            <span>Admin Panel</span>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-section">Dashboard</div>
            <a href="index.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a>

            <div class="menu-section">Manajemen Data</div>
            <a href="products/index.php" class="menu-item">
                <i class="fas fa-campground"></i><span>Alat Outdoor</span>
            </a>
            <a href="categories/index.php" class="menu-item">
                <i class="fas fa-tags"></i><span>Kategori</span>
            </a>
            <a href="trips/index.php" class="menu-item">
                <i class="fas fa-mountain"></i><span>Open Trip</span>
            </a>
            <a href="orders/index.php" class="menu-item">
                <i class="fas fa-shopping-cart"></i><span>Pesanan Sewa</span>
            </a>
            <a href="trips/bookings/index.php" class="menu-item">
                <i class="fas fa-hiking"></i><span>Booking Trip</span>
            </a>
            <a href="messages/index.php" class="menu-item">
                <i class="fas fa-envelope"></i><span>Pesan</span>
            </a>
            <a href="reports/index.php" class="menu-item">
                <i class="fas fa-chart-bar"></i><span>Laporan</span>
            </a>

            <div class="menu-section">Manajemen User</div>
            <a href="users/index.php" class="menu-item">
                <i class="fas fa-users"></i><span>Karyawan</span>
            </a>

            <div class="menu-section">Lainnya</div>
            <a href="../index.php" class="menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i><span>Lihat Website</span>
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Dashboard</h1>
            <div class="header-user">
                <div class="user-info">
                    <div class="user-name"><?= $_SESSION['admin_nama'] ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <div class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon primary"><i class="fas fa-campground"></i></div>
                    <div class="stat-info">
                        <h3><?= $products_count ?></h3>
                        <p>Alat Outdoor</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon mountain"><i class="fas fa-mountain"></i></div>
                    <div class="stat-info">
                        <h3><?= $trips_count ?></h3>
                        <p>Open Trip</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon success"><i class="fas fa-tags"></i></div>
                    <div class="stat-info">
                        <h3><?= $categories_count ?></h3>
                        <p>Kategori</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon warning"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <h3><?= $orders_count ?></h3>
                        <p>Total Pesanan</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon info"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <h3><?= formatCurrency($today_total) ?></h3>
                        <p>Pendapatan Hari Ini</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon danger"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $users_count ?></h3>
                        <p>Karyawan</p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <a href="products/add.php" class="quick-action">
                    <i class="fas fa-plus"></i><span>Tambah Alat</span>
                </a>
                <a href="trips/add.php" class="quick-action">
                    <i class="fas fa-hiking"></i><span>Tambah Trip</span>
                </a>
                <a href="users/add.php" class="quick-action">
                    <i class="fas fa-user-plus"></i><span>Tambah Karyawan</span>
                </a>
                <a href="reports/index.php" class="quick-action">
                    <i class="fas fa-file-export"></i><span>Laporan</span>
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Pesanan Terbaru</h2>
                    <a href="orders/index.php" class="btn-add"><i class="fas fa-list"></i> Lihat Semua</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Order</th>
                                <th>Pelanggan</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $index => $order): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $order['kode_order'] ?></td>
                                    <td><?= $order['nama_pelanggan'] ?></td>
                                    <td><?= $order['nama_kasir'] ?? '-' ?></td>
                                    <td><?= formatCurrency($order['total_harga']) ?></td>
                                    <td><span
                                            class="status-badge <?= $order['status_order'] ?>"><?= $order['status_order'] ?></span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="orders/view.php?id=<?= $order['id'] ?>" class="btn-action btn-view"
                                                title="Lihat"><i class="fas fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Alat Outdoor Terbaru</h2>
                    <a href="products/index.php" class="btn-add"><i class="fas fa-list"></i> Lihat Semua</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Alat</th>
                                <th>Kategori</th>
                                <th>Harga Sewa/Hari</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_products as $index => $product): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $product['nama_produk'] ?></td>
                                    <td><?= $product['nama_kategori'] ?></td>
                                    <td><?= formatCurrency($product['harga_sewa_harian'] ?? $product['harga']) ?></td>
                                    <td><span
                                            class="status-badge <?= $product['status'] ?? 'active' ?>"><?= $product['status'] ?? 'active' ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>