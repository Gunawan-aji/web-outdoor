<?php
require_once '../../../config/functions.php';
requireLogin();

$settings = getSettings();
$bookings = getAllTripBookings();

// Filter by trip_id if provided
$trip_id = isset($_GET['trip_id']) ? (int) $_GET['trip_id'] : null;
if ($trip_id) {
    $bookings = array_filter($bookings, function ($b) use ($trip_id) {
        return $b['trip_id'] == $trip_id;
    });
}

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
if ($status_filter) {
    $bookings = array_filter($bookings, function ($b) use ($status_filter) {
        return $b['status_booking'] === $status_filter;
    });
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if (deleteTripBooking($id)) {
        echo "<script>alert('Booking berhasil dihapus'); window.location.href='index.php';</script>";
    }
}

// Handle update status
if (isset($_POST['update_status'])) {
    $id = (int) $_POST['booking_id'];
    $status_booking = sanitize($_POST['status_booking']);
    $status_pembayaran = sanitize($_POST['status_pembayaran'] ?? '');

    if (updateTripBookingStatus($id, $status_booking, $status_pembayaran)) {
        echo "<script>alert('Status berhasil diperbarui'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking Trip - Admin <?= $settings['site_name'] ?></title>
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

        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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

        .payment-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .payment-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payment-badge.confirmed {
            background: #cce5ff;
            color: #004085;
        }

        .payment-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .payment-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
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

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            width: 90%;
            max-width: 400px;
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-submit {
            padding: 10px 20px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
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
            <h1 class="header-title">Kelola Booking Trip</h1>
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
            <div class="filters">
                <a href="index.php" class="filter-btn <?= $status_filter == '' ? 'active' : '' ?>">Semua</a>
                <a href="?status=pending"
                    class="filter-btn <?= $status_filter == 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="?status=confirmed"
                    class="filter-btn <?= $status_filter == 'confirmed' ? 'active' : '' ?>">Confirmed</a>
                <a href="?status=completed"
                    class="filter-btn <?= $status_filter == 'completed' ? 'active' : '' ?>">Completed</a>
                <a href="?status=cancelled"
                    class="filter-btn <?= $status_filter == 'cancelled' ? 'active' : '' ?>">Cancelled</a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Daftar Booking Trip</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>Peserta</th>
                            <th>Trip</th>
                            <th>Tanggal</th>
                            <th>Jml</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $index => $booking): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= $booking['kode_booking'] ?></strong></td>
                                    <td>
                                        <?= $booking['nama_peserta'] ?><br>
                                        <small><?= $booking['no_hp'] ?></small>
                                    </td>
                                    <td><?= $booking['nama_trip'] ?></td>
                                    <td><?= date('d-m-Y', strtotime($booking['tanggal_mulai'])) ?></td>
                                    <td><?= $booking['jumlah_orang'] ?></td>
                                    <td><?= formatCurrency($booking['total_harga']) ?></td>
                                    <td><span
                                            class="status-badge <?= $booking['status_booking'] ?>"><?= $booking['status_booking'] ?></span>
                                    </td>
                                    <td><span
                                            class="payment-badge <?= $booking['status_pembayaran'] ?>"><?= $booking['status_pembayaran'] ?></span>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn-action btn-edit" title="Update Status"
                                                onclick="openModal(<?= $booking['id'] ?>, '<?= $booking['status_booking'] ?>', '<?= $booking['status_pembayaran'] ?>')"><i
                                                    class="fas fa-edit"></i></button>
                                            <a href="?delete=<?= $booking['id'] ?>" class="btn-action btn-delete" title="Hapus"
                                                onclick="return confirm('Yakin hapus booking ini?')"><i
                                                    class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 40px;">Tidak ada data booking</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Update Status -->
    <div class="modal" id="statusModal">
        <div class="modal-content">
            <h3 class="modal-title">Update Status Booking</h3>
            <form method="POST">
                <input type="hidden" name="booking_id" id="modalBookingId">
                <div class="form-group">
                    <label>Status Booking</label>
                    <select name="status_booking" id="modalStatusBooking">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Pembayaran</label>
                    <select name="status_pembayaran" id="modalStatusPembayaran">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" name="update_status" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, statusBooking, statusPembayaran) {
            document.getElementById('modalBookingId').value = id;
            document.getElementById('modalStatusBooking').value = statusBooking;
            document.getElementById('modalStatusPembayaran').value = statusPembayaran;
            document.getElementById('statusModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('statusModal').classList.remove('active');
        }
    </script>
</body>

</html>