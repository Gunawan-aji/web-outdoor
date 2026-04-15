<?php
require_once '../../../config/functions.php';
requireLogin();

$settings = getSettings();

// Get booking ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$booking = getTripBookingById($id);

if (!$booking) {
    echo "<script>alert('Booking tidak ditemukan'); window.location.href='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Booking Trip - Admin <?= $settings['site_name'] ?></title>
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

        .btn-back {
            padding: 10px 18px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background: var(--gray-600);
            color: var(--white);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }

        .card-body {
            padding: 25px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: var(--gray-600);
        }

        .detail-value {
            font-weight: 600;
            color: var(--dark);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.confirmed {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .detail-grid {
                grid-template-columns: 1fr;
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
            <a href="../../index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <div class="menu-section">Manajemen Data</div>
            <a href="../index.php" class="menu-item"><i class="fas fa-mountain"></i><span>Open Trip</span></a>
            <a href="index.php" class="menu-item active"><i class="fas fa-users"></i><span>Booking Trip</span></a>
            <a href="../../orders/index.php" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Pesanan
                    Sewa</span></a>
            <a href="../../messages/index.php" class="menu-item"><i class="fas fa-envelope"></i><span>Pesan</span></a>
            <a href="../../reports/index.php" class="menu-item"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
            <div class="menu-section">Lainnya</div>
            <a href="../../../index.php" class="menu-item" target="_blank"><i
                    class="fas fa-external-link-alt"></i><span>Lihat Website</span></a>
            <a href="../../logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Detail Booking Trip</h1>
            <div class="header-user">
                <div class="user-info">
                    <div class="user-name"><?= $_SESSION['admin_nama'] ?></div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <a href="../../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <div class="content">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>

            <div class="detail-grid">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Informasi Booking</h2>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Kode Booking</span>
                            <span class="detail-value"><?= $booking['kode_booking'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nama Peserta</span>
                            <span class="detail-value"><?= $booking['nama_peserta'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nomor HP</span>
                            <span class="detail-value"><?= $booking['no_hp'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?= $booking['email'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Jumlah Orang</span>
                            <span class="detail-value"><?= $booking['jumlah_orang'] ?> orang</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total Harga</span>
                            <span class="detail-value"><?= formatCurrency($booking['total_harga']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Metode Pembayaran</span>
                            <span class="detail-value"><?= ucfirst($booking['metode_pembayaran']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status Booking</span>
                            <span class="detail-value"><span
                                    class="status-badge <?= $booking['status_booking'] ?>"><?= $booking['status_booking'] ?></span></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status Pembayaran</span>
                            <span class="detail-value"><span
                                    class="status-badge <?= $booking['status_pembayaran'] ?>"><?= $booking['status_pembayaran'] ?></span></span>
                        </div>
                        <?php if ($booking['catatan']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Catatan</span>
                                <span class="detail-value"><?= $booking['catatan'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Informasi Trip</h2>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Nama Trip</span>
                            <span class="detail-value"><?= $booking['nama_trip'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value"><?= $booking['lokasi'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal Mulai</span>
                            <span class="detail-value"><?= date('d-m-Y', strtotime($booking['tanggal_mulai'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal Selesai</span>
                            <span
                                class="detail-value"><?= date('d-m-Y', strtotime($booking['tanggal_selesai'])) ?></span>
                        </div>
                        <?php if ($booking['nama_kasir']): ?>
                            <div class="detail-row">
                                <span class="detail-label">Dipesan Oleh</span>
                                <span class="detail-value"><?= $booking['nama_kasir'] ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal Booking</span>
                            <span
                                class="detail-value"><?= date('d-m-Y H:i', strtotime($booking['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>