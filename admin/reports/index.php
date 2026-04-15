<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();

// Get filter parameters
$report_type = isset($_GET['type']) ? $_GET['type'] : 'daily';
$year = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int) $_GET['month'] : date('m');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get data based on report type
switch ($report_type) {
    case 'daily':
        $orders = getDailyReport($start_date);
        $summary = getReportSummary($start_date, $start_date);
        $title = 'Laporan Harian';
        break;
    case 'weekly':
        $orders = getWeeklyReport();
        $summary = getReportSummary(date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));
        $title = 'Laporan Mingguan';
        break;
    case 'monthly':
        $orders = getMonthlyReport($year, $month);
        $summary = getReportSummary("$year-$month-01", date('Y-m-t', strtotime("$year-$month-01")));
        $title = 'Laporan Bulanan';
        break;
    case 'yearly':
        $orders = getYearlyReport($year);
        $summary = getReportSummary("$year-01-01", "$year-12-31");
        $title = 'Laporan Tahunan';
        break;
    default:
        $orders = getDailyReport($start_date);
        $summary = getReportSummary($start_date, $start_date);
        $title = 'Laporan Harian';
}

// Handle export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    exportOrdersToExcel($orders, str_replace(' ', '_', $title));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin <?= $settings['site_name'] ?></title>
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
            margin-bottom: 30px;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 12px 20px;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            transition: all 0.3s;
        }

        .filter-tab:hover,
        .filter-tab.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .filter-form {
            background: var(--white);
            padding: 24px;
            border-radius: 14px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
        }

        .btn-filter {
            padding: 12px 24px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-filter:hover {
            background: var(--primary-dark);
        }

        .btn-export {
            padding: 12px 24px;
            background: var(--success);
            color: var(--white);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-export:hover {
            background: #218838;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: 14px;
            box-shadow: var(--shadow);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
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
            padding: 14px 20px;
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
            <a href="../orders/index.php" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Pesanan
                    Sewa</span></a>
            <a href="../trips/bookings/index.php" class="menu-item"><i class="fas fa-hiking"></i><span>Booking
                    Trip</span></a>
            <a href="../messages/index.php" class="menu-item"><i class="fas fa-envelope"></i><span>Pesan</span></a>
            <a href="index.php" class="menu-item active"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
            <div class="menu-section">Lainnya</div>
            <a href="../../index.php" class="menu-item" target="_blank"><i
                    class="fas fa-external-link-alt"></i><span>Lihat Website</span></a>
            <a href="../logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Laporan</h1>
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
                <div class="filter-tabs">
                    <a href="?type=daily" class="filter-tab <?= $report_type == 'daily' ? 'active' : '' ?>">Harian</a>
                    <a href="?type=weekly"
                        class="filter-tab <?= $report_type == 'weekly' ? 'active' : '' ?>">Mingguan</a>
                    <a href="?type=monthly"
                        class="filter-tab <?= $report_type == 'monthly' ? 'active' : '' ?>">Bulanan</a>
                    <a href="?type=yearly"
                        class="filter-tab <?= $report_type == 'yearly' ? 'active' : '' ?>">Tahunan</a>
                </div>

                <form class="filter-form" method="GET">
                    <input type="hidden" name="type" value="<?= $report_type ?>">

                    <?php if ($report_type == 'daily'): ?>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="start_date" value="<?= $start_date ?>" required>
                        </div>
                    <?php elseif ($report_type == 'monthly'): ?>
                        <div class="form-group">
                            <label>Bulan</label>
                            <select name="month">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tahun</label>
                            <select name="year">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    <?php elseif ($report_type == 'yearly'): ?>
                        <div class="form-group">
                            <label>Tahun</label>
                            <select name="year">
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
                    <a href="?type=<?= $report_type ?>&export=excel<?= $report_type == 'monthly' ? '&month=' . $month . '&year=' . $year : '' ?><?= $report_type == 'yearly' ? '&year=' . $year : '' ?>"
                        class="btn-export"><i class="fas fa-file-excel"></i> Export Excel</a>
                </form>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Pesanan</div>
                    <div class="stat-value"><?= $summary['total_order'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value"><?= formatCurrency($summary['total_pendapatan'] ?? 0) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Rata-rata per Pesanan</div>
                    <div class="stat-value"><?= formatCurrency($summary['rata_rata'] ?? 0) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Tunai</div>
                    <div class="stat-value"><?= $summary['jumlah_tunai'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">QRIS</div>
                    <div class="stat-value"><?= $summary['jumlah_qris'] ?? 0 ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Transfer</div>
                    <div class="stat-value"><?= $summary['jumlah_transfer'] ?? 0 ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><?= $title ?></h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Order</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= $order['kode_order'] ?></strong></td>
                                    <td><?= $order['nama_pelanggan'] ?></td>
                                    <td><?= $order['nama_kasir'] ?? '-' ?></td>
                                    <td><?= formatCurrency($order['total_harga']) ?></td>
                                    <td><?= ucfirst($order['metode_pembayaran']) ?></td>
                                    <td><span
                                            class="status-badge <?= $order['status_order'] ?>"><?= $order['status_order'] ?></span>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">Tidak ada data pesanan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>