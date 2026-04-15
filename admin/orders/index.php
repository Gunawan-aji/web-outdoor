<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();
$orders = getAllOrders();

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tipe_filter = isset($_GET['tipe']) ? $_GET['tipe'] : '';

if ($status_filter || $tipe_filter) {
    $orders = array_filter($orders, function ($order) use ($status_filter, $tipe_filter) {
        $status_match = !$status_filter || $order['status_order'] === $status_filter;
        $tipe_match = !$tipe_filter || $order['tipe_order'] === $tipe_filter;
        return $status_match && $tipe_match;
    });
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin <?= $settings['site_name'] ?></title>
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
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .filter-group label {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .filter-btn {
            padding: 8px 16px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            text-decoration: none;
            color: var(--gray-600);
            font-size: 0.875rem;
            transition: all 0.3s;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
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

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.selesai,
        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.diproses,
        .status-badge.confirmed {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.dibatalkan,
        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.rented {
            background: #e7f3ff;
            color: #0066cc;
        }

        .status-badge.returned {
            background: #d4edda;
            color: #155724;
        }

        .tipe-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .tipe-badge.online {
            background: #e7f3ff;
            color: #0066cc;
        }

        .tipe-badge.offline {
            background: #f0f0f0;
            color: #666;
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
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-view {
            background: #d1ecf1;
            color: var(--info);
        }

        .btn-view:hover {
            background: var(--info);
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
            <span>Admin Panel</span>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Dashboard</div>
            <a href="../index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <div class="menu-section">Manajemen Data</div>
            <a href="../products/index.php" class="menu-item"><i class="fas fa-campground"></i><span>Alat
                    Outdoor</span></a>
            <a href="../categories/index.php" class="menu-item"><i class="fas fa-tags"></i><span>Kategori</span></a>
            <a href="../trips/index.php" class="menu-item"><i class="fas fa-mountain"></i><span>Open Trip</span></a>
            <a href="index.php" class="menu-item active"><i class="fas fa-shopping-cart"></i><span>Pesanan
                    Sewa</span></a>
            <a href="../trips/bookings/index.php" class="menu-item"><i class="fas fa-hiking"></i><span>Booking
                    Trip</span></a>
            <a href="../messages/index.php" class="menu-item"><i class="fas fa-envelope"></i><span>Pesan</span></a>
            <a href="../reports/index.php" class="menu-item"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
            <div class="menu-section">Lainnya</div>
            <a href="../../index.php" class="menu-item" target="_blank"><i
                    class="fas fa-external-link-alt"></i><span>Lihat Website</span></a>
            <a href="../logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Kelola Pesanan Sewa</h1>
            <div class="header-user">
                <div class="user-info">
                    <div class="user-name"><?= $_SESSION['admin_nama'] ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <div class="content">
            <div class="filters">
                <div class="filter-group">
                    <label>Tipe:</label>
                    <a href="index.php" class="filter-btn <?= $tipe_filter == '' ? 'active' : '' ?>">Semua</a>
                    <a href="index.php?tipe=online"
                        class="filter-btn <?= $tipe_filter == 'online' ? 'active' : '' ?>"><i class="fas fa-globe"></i>
                        Online</a>
                    <a href="index.php?tipe=offline"
                        class="filter-btn <?= $tipe_filter == 'offline' ? 'active' : '' ?>"><i class="fas fa-store"></i>
                        Offline</a>
                </div>
            </div>

            <div class="filters">
                <div class="filter-group">
                    <label>Status:</label>
                    <a href="index.php<?= $tipe_filter ? '?tipe=' . $tipe_filter : '' ?>"
                        class="filter-btn <?= $status_filter == '' ? 'active' : '' ?>">Semua</a>
                    <a href="index.php?status=pending<?= $tipe_filter ? '&tipe=' . $tipe_filter : '' ?>"
                        class="filter-btn <?= $status_filter == 'pending' ? 'active' : '' ?>">Pending</a>
                    <a href="index.php?status=confirmed<?= $tipe_filter ? '&tipe=' . $tipe_filter : '' ?>"
                        class="filter-btn <?= $status_filter == 'confirmed' ? 'active' : '' ?>">Confirmed</a>
                    <a href="index.php?status=rented<?= $tipe_filter ? '&tipe=' . $tipe_filter : '' ?>"
                        class="filter-btn <?= $status_filter == 'rented' ? 'active' : '' ?>">Disewa</a>
                    <a href="index.php?status=returned<?= $tipe_filter ? '&tipe=' . $tipe_filter : '' ?>"
                        class="filter-btn <?= $status_filter == 'returned' ? 'active' : '' ?>">Dikembalikan</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Pesanan Sewa Alat</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Order</th>
                            <th>Pelanggan</th>
                            <th>Tgl Sewa</th>
                            <th>Tgl Kembali</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $index => $order): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= $order['kode_order'] ?></strong></td>
                                <td>
                                    <?= $order['nama_pelanggan'] ?><br>
                                    <small><?= $order['no_hp'] ?? '' ?></small>
                                </td>
                                <td><?= $order['tanggal_sewa'] ? date('d-m-Y', strtotime($order['tanggal_sewa'])) : '-' ?>
                                </td>
                                <td><?= $order['tanggal_kembali'] ? date('d-m-Y', strtotime($order['tanggal_kembali'])) : '-' ?>
                                </td>
                                <td><?= formatCurrency($order['total_harga']) ?></td>
                                <td><?= ucfirst($order['metode_pembayaran']) ?></td>
                                <td><span
                                        class="status-badge <?= $order['status_order'] ?>"><?= $order['status_order'] ?></span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="view.php?id=<?= $order['id'] ?>" class="btn-action btn-view"
                                            title="Lihat"><i class="fas fa-eye"></i></a>
                                        <a href="delete.php?id=<?= $order['id'] ?>" class="btn-action btn-delete"
                                            title="Hapus" onclick="return confirm('Yakin hapus pesanan ini?')"><i
                                                class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>