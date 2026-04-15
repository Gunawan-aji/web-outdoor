<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = getOrderById($id);
$items = getOrderItems($id);

if (!$order) {
    redirect('index.php');
}

// Handle status update
if (isset($_POST['update_status'])) {
    $new_status = sanitize($_POST['status']);
    updateOrderStatus($id, $new_status);
    redirect('view.php?id=' . $id);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Admin <?= $settings['site_name'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6F4E37;
            --primary-dark: #5D4037;
            --secondary: #C4A77D;
            --dark: #2C1810;
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
            font-family: 'Segoe UI', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--dark);
            color: var(--white);
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
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
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border-left-color: var(--secondary);
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
            padding: 15px 30px;
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
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }

        .btn-logout {
            padding: 8px 15px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .content {
            padding: 30px;
        }

        .btn-back {
            padding: 10px 20px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background: var(--gray-300);
        }

        .card {
            background: var(--white);
            border-radius: 15px;
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
            padding: 25px;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {}

        .info-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.selesai {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.diproses {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.dibatalkan {
            background: #f8d7da;
            color: #721c24;
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

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
        }

        .table th {
            background: var(--gray-100);
            font-weight: 600;
            color: var(--dark);
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        .table td {
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-600);
        }

        .total-row td {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
            border-top: 2px solid var(--dark);
        }

        .form-group {
            margin-top: 20px;
        }

        .form-group select {
            padding: 10px 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            margin-right: 10px;
        }

        .btn-update {
            padding: 10px 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-update:hover {
            background: var(--primary-dark);
        }

        .catatan-box {
            background: #fff9e6;
            border: 1px solid #ffd966;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .catatan-box h4 {
            color: #856404;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
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
            <div class="logo-icon"><i class="fas fa-coffee"></i></div>
            <span>Admin Panel</span>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">Dashboard</div>
            <a href="../index.php" class="menu-item"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <div class="menu-section">Manajemen Data</div>
            <a href="../products/index.php" class="menu-item"><i class="fas fa-coffee"></i><span>Produk</span></a>
            <a href="../categories/index.php" class="menu-item"><i class="fas fa-tags"></i><span>Kategori</span></a>
            <a href="../users/index.php" class="menu-item"><i class="fas fa-users"></i><span>Karyawan</span></a>
            <a href="index.php" class="menu-item active"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
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
            <h1 class="header-title">Detail Pesanan</h1>
            <div class="header-user">
                <div class="user-info">
                    <div class="user-name">
                        <?= $_SESSION['admin_nama'] ?>
                    </div>
                    <div class="user-role">Administrator</div>
                </div>
                <div class="user-avatar"><i class="fas fa-user"></i></div>
                <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <div class="content">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Informasi Pesanan</h2>
                    <div>
                        <span class="tipe-badge <?= $order['tipe_order'] ?>">
                            <?= $order['tipe_order'] == 'online' ? '<i class="fas fa-globe"></i> Online' : '<i class="fas fa-store"></i> Offline' ?>
                        </span>
                        <span class="status-badge <?= $order['status_order'] ?>" style="margin-left: 10px;">
                            <?= strtoupper($order['status_order']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="order-info">
                        <div class="info-item">
                            <div class="info-label">Kode Order</div>
                            <div class="info-value">
                                <?= $order['kode_order'] ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pelanggan</div>
                            <div class="info-value">
                                <?= $order['nama_pelanggan'] ?>
                            </div>
                        </div>
                        <?php if ($order['tipe_order'] == 'online'): ?>
                            <div class="info-item">
                                <div class="info-label">Nomor Meja/Kursi</div>
                                <div class="info-value">
                                    <?= $order['nomor_meja'] ?: '-' ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <div class="info-label">Kasir</div>
                            <div class="info-value">
                                <?= $order['nama_kasir'] ?: '-' ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Metode Pembayaran</div>
                            <div class="info-value">
                                <?= ucfirst($order['metode_pembayaran']) ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value">
                                <?= date('d-m-Y H:i', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan untuk pesanan online -->
                    <?php if ($order['tipe_order'] == 'online' && !empty($order['catatan'])): ?>
                        <div class="catatan-box">
                            <h4><i class="fas fa-sticky-note"></i> Catatan:</h4>
                            <p><?= nl2br(htmlspecialchars($order['catatan'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="form-group">
                        <label><strong>Update Status:</strong></label>
                        <select name="status">
                            <option value="pending" <?= $order['status_order'] == 'pending' ? 'selected' : '' ?>>Pending
                            </option>
                            <option value="diproses" <?= $order['status_order'] == 'diproses' ? 'selected' : '' ?>>Diproses
                            </option>
                            <option value="selesai" <?= $order['status_order'] == 'selesai' ? 'selected' : '' ?>>Selesai
                            </option>
                            <option value="dibatalkan" <?= $order['status_order'] == 'dibatalkan' ? 'selected' : '' ?>>
                                Dibatalkan</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-update"><i class="fas fa-save"></i>
                            Update</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Detail Item Pesanan</h2>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?= $item['nama_produk'] ?>
                                    </td>
                                    <td>
                                        <?= formatCurrency($item['harga_saat_pesan']) ?>
                                    </td>
                                    <td>
                                        <?= $item['jumlah'] ?>
                                    </td>
                                    <td>
                                        <?= formatCurrency($item['subtotal']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Total:</td>
                                <td>
                                    <?= formatCurrency($order['total_harga']) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>