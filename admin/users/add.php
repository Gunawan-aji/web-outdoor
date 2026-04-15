<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $email = sanitize($_POST['email']);
    $role = sanitize($_POST['role']);
    
    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        $error = 'Semua field harus diisi!';
    } else {
        if (addUser($username, $password, $nama_lengkap, $email, $role)) {
            redirect('index.php');
        } else {
            $error = 'Gagal menambah karyawan. Username mungkin sudah ada.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Karyawan - Admin <?= $settings['site_name'] ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6F4E37; --primary-dark: #5D4037; --secondary: #C4A77D;
            --dark: #2C1810; --white: #ffffff; --gray-100: #f8f9fa;
            --gray-200: #e9ecef; --gray-600: #6c757d; --danger: #dc3545;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--gray-100); min-height: 100vh; }
        
        .sidebar { position: fixed; top: 0; left: 0; width: 260px; height: 100vh; background: var(--dark); color: var(--white); z-index: 100; }
        .sidebar-brand { padding: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .sidebar-brand span { font-size: 1.25rem; font-weight: 700; }
        .sidebar-menu { padding: 20px 0; }
        .menu-section { padding: 10px 20px; font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.5); letter-spacing: 1px; }
        .menu-item { padding: 12px 20px; display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,0.1); color: var(--white); border-left-color: var(--secondary); }
        .menu-item i { width: 20px; text-align: center; }
        
        .main-content { margin-left: 260px; min-height: 100vh; }
        .admin-header { background: var(--white); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow); position: sticky; top: 0; z-index: 50; }
        .header-title { font-size: 1.25rem; font-weight: 600; color: var(--dark); }
        .header-user { display: flex; align-items: center; gap: 15px; }
        .user-info { text-align: right; }
        .user-name { font-weight: 600; color: var(--dark); }
        .user-role { font-size: 0.875rem; color: var(--gray-600); }
        .user-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); }
        .btn-logout { padding: 8px 15px; background: var(--danger); color: var(--white); border: none; border-radius: 8px; cursor: pointer; text-decoration: none; }
        
        .content { padding: 30px; }
        .card { background: var(--white); border-radius: 15px; box-shadow: var(--shadow); overflow: hidden; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-200); }
        .card-title { font-size: 1.125rem; font-weight: 600; color: var(--dark); }
        .card-body { padding: 30px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark); }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 2px solid var(--gray-200); border-radius: 8px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
        
        .btn-submit { padding: 12px 30px; background: var(--primary); color: var(--white); border: none; border-radius: 8px; font-weight: 500; cursor: pointer; }
        .btn-submit:hover { background: var(--primary-dark); }
        .btn-back { padding: 12px 30px; background: var(--gray-200); color: var(--dark); border: none; border-radius: 8px; font-weight: 500; cursor: pointer; text-decoration: none; margin-left: 10px; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } }
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
            <a href="index.php" class="menu-item active"><i class="fas fa-users"></i><span>Karyawan</span></a>
            <a href="../orders/index.php" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Pesanan</span></a>
            <a href="../messages/index.php" class="menu-item"><i class="fas fa-envelope"></i><span>Pesan</span></a>
            <a href="../reports/index.php" class="menu-item"><i class="fas fa-chart-bar"></i><span>Laporan</span></a>
            <div class="menu-section">Lainnya</div>
            <a href="../../index.php" class="menu-item" target="_blank"><i class="fas fa-external-link-alt"></i><span>Lihat Website</span></a>
            <a href="../logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Tambah Karyawan</h1>
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
                    <h2 class="card-title">Form Tambah Karyawan</h2>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="kasir">Kasir</option>
                                <option value="pengelola">Pengelola</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan</button>
                        <a href="index.php" class="btn-back">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

