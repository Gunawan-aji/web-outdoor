<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();
$trips = getAllTrips();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if (deleteTrip($id)) {
        echo "<script>alert('Trip berhasil dihapus'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Open Trip - Admin <?= $settings['site_name'] ?></title>
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
            font-family: 'Poppins', sans-serif;
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

        .btn-add {
            padding: 12px 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
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

        .status-badge.open {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.full {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .difficulty-badge.easy {
            background: #d4edda;
            color: #155724;
        }

        .difficulty-badge.moderate {
            background: #fff3cd;
            color: #856404;
        }

        .difficulty-badge.hard {
            background: #f8d7da;
            color: #721c24;
        }

        .difficulty-badge.extreme {
            background: #dc3545;
            color: white;
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

        .btn-booking {
            background: #e7f3ff;
            color: #0066cc;
        }

        .btn-booking:hover {
            background: #0066cc;
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
            <a href="index.php" class="menu-item active"><i class="fas fa-mountain"></i><span>Open Trip</span></a>
            <a href="../orders/index.php" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
            <a href="bookings/index.php" class="menu-item"><i class="fas fa-users"></i><span>Booking Trip</span></a>
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
            <h1 class="header-title">Kelola Open Trip</h1>
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
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Open Trip</h2>
                    <a href="add.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Trip</a>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Trip</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Harga</th>
                            <th>Kapasitas</th>
                            <th>Tingkat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trips as $index => $trip): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= $trip['nama_trip'] ?></strong></td>
                                <td><?= $trip['lokasi'] ?></td>
                                <td>
                                    <?= date('d-m-Y', strtotime($trip['tanggal_mulai'])) ?>
                                    <?php if ($trip['tanggal_mulai'] != $trip['tanggal_selesai']): ?>
                                        <br>s/d <?= date('d-m-Y', strtotime($trip['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatCurrency($trip['harga']) ?></td>
                                <td><?= $trip['terisi'] ?>/<?= $trip['kapasitas'] ?></td>
                                <td><span
                                        class="difficulty-badge <?= $trip['tingkat_kesulitan'] ?>"><?= ucfirst($trip['tingkat_kesulitan']) ?></span>
                                </td>
                                <td><span class="status-badge <?= $trip['status'] ?>"><?= $trip['status'] ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="bookings/index.php?trip_id=<?= $trip['id'] ?>"
                                            class="btn-action btn-booking" title="Lihat Booking"><i
                                                class="fas fa-users"></i></a>
                                        <a href="edit.php?id=<?= $trip['id'] ?>" class="btn-action btn-edit" title="Edit"><i
                                                class="fas fa-edit"></i></a>
                                        <a href="?delete=<?= $trip['id'] ?>" class="btn-action btn-delete" title="Hapus"
                                            onclick="return confirm('Yakin hapus trip ini?')"><i
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