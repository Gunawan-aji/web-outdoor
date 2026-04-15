<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();

// Get trip ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$trip = getTripById($id);

if (!$trip) {
    echo "<script>alert('Trip tidak ditemukan'); window.location.href='index.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_trip = sanitize($_POST['nama_trip']);
    $slug = sanitize($_POST['slug'] ?: strtolower(str_replace(' ', '-', preg_replace('/[^a-zA-Z0-9\s]/', '', $nama_trip))));
    $deskripsi = sanitize($_POST['deskripsi']);
    $itinerary = sanitize($_POST['itinerary']);
    $harga = (float) $_POST['harga'];
    $kapasitas = (int) $_POST['kapasitas'];
    $tanggal_mulai = sanitize($_POST['tanggal_mulai']);
    $tanggal_selesai = sanitize($_POST['tanggal_selesai']);
    $tingkat_kesulitan = sanitize($_POST['tingkat_kesulitan']);
    $lokasi = sanitize($_POST['lokasi']);
    $include = sanitize($_POST['include']);
    $exclude = sanitize($_POST['exclude']);
    $status = sanitize($_POST['status']);

    // Handle image upload
    $gambar = $trip['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $new_gambar = uploadImage($_FILES['gambar'], '../../assets/images/trips/');
        if ($new_gambar) {
            $gambar = $new_gambar;
        }
    }

    if (updateTrip($id, $nama_trip, $slug, $deskripsi, $itinerary, $harga, $kapasitas, $tanggal_mulai, $tanggal_selesai, $tingkat_kesulitan, $lokasi, $gambar, $include, $exclude, $status)) {
        echo "<script>alert('Trip berhasil diperbarui'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui trip');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Open Trip - Admin <?= $settings['site_name'] ?></title>
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

        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 800px;
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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-submit {
            padding: 14px 28px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        .btn-cancel {
            padding: 14px 28px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: var(--gray-600);
            color: var(--white);
        }

        .current-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 8px;
        }

        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .form-grid {
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
            <h1 class="header-title">Edit Open Trip</h1>
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
                    <h2 class="card-title">Form Edit Open Trip</h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nama Trip *</label>
                                <input type="text" name="nama_trip" required
                                    value="<?= htmlspecialchars($trip['nama_trip']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Slug URL</label>
                                <input type="text" name="slug" value="<?= htmlspecialchars($trip['slug']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Lokasi *</label>
                                <input type="text" name="lokasi" required
                                    value="<?= htmlspecialchars($trip['lokasi']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Harga per Orang *</label>
                                <input type="number" name="harga" required value="<?= $trip['harga'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Kapasitas *</label>
                                <input type="number" name="kapasitas" required value="<?= $trip['kapasitas'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Mulai *</label>
                                <input type="date" name="tanggal_mulai" required value="<?= $trip['tanggal_mulai'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Selesai *</label>
                                <input type="date" name="tanggal_selesai" required
                                    value="<?= $trip['tanggal_selesai'] ?>">
                            </div>
                            <div class="form-group">
                                <label>Tingkat Kesulitan</label>
                                <select name="tingkat_kesulitan">
                                    <option value="easy" <?= $trip['tingkat_kesulitan'] == 'easy' ? 'selected' : '' ?>>Easy
                                        (Mudah)</option>
                                    <option value="moderate" <?= $trip['tingkat_kesulitan'] == 'moderate' ? 'selected' : '' ?>>Moderate (Sedang)</option>
                                    <option value="hard" <?= $trip['tingkat_kesulitan'] == 'hard' ? 'selected' : '' ?>>Hard
                                        (Sulit)</option>
                                    <option value="extreme" <?= $trip['tingkat_kesulitan'] == 'extreme' ? 'selected' : '' ?>>Extreme (Sangat Sulit)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="open" <?= $trip['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="full" <?= $trip['status'] == 'full' ? 'selected' : '' ?>>Full</option>
                                    <option value="cancelled" <?= $trip['status'] == 'cancelled' ? 'selected' : '' ?>>
                                        Cancelled</option>
                                    <option value="completed" <?= $trip['status'] == 'completed' ? 'selected' : '' ?>>
                                        Completed</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi"><?= htmlspecialchars($trip['deskripsi']) ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Itinerary (Plan Perjalanan)</label>
                                <textarea name="itinerary"><?= htmlspecialchars($trip['itinerary']) ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Include (Yang Sudah Include)</label>
                                <textarea name="include"><?= htmlspecialchars($trip['include']) ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Exclude (Yang Tidak Include)</label>
                                <textarea name="exclude"><?= htmlspecialchars($trip['exclude']) ?></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label>Gambar Trip</label>
                                <input type="file" name="gambar" accept="image/*">
                                <?php if ($trip['gambar']): ?>
                                    <img src="../../assets/images/trips/<?= $trip['gambar'] ?>" alt="Current"
                                        class="current-image">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Trip</button>
                            <a href="index.php" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>