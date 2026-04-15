<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();
$categories = getAllCategories();
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = sanitize($_POST['nama_produk']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $kategori_id = (int)$_POST['kategori_id'];
    $status = sanitize($_POST['status']);
    
    if (empty($nama_produk) || empty($harga)) {
        $error = 'Nama produk dan harga wajib diisi!';
    } else {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO products (nama_produk, deskripsi, harga, kategori_id, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nama_produk, $deskripsi, $harga, $kategori_id, $status);
        
        if ($stmt->execute()) {
            echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='index.php';</script>";
            exit();
        } else {
            $error = 'Gagal menambahkan produk!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin <?= $settings['site_name'] ?></title>
    <meta name="robots" content="noindex, nofollow">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #6F4E37;
            --primary-dark: #5D4037;
            --dark: #2C1810;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --danger: #dc3545;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            background: linear-gradient(135deg, var(--primary), #C4A77D);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand span { font-size: 1.25rem; font-weight: 700; }
        
        .sidebar-menu { padding: 20px 0; }
        
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
        
        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border-left-color: #C4A77D;
        }
        
        .menu-item i { width: 20px; text-align: center; }
        
        .main-content { margin-left: 260px; min-height: 100vh; }
        
        .admin-header {
            background: var(--white);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }
        
        .header-title { font-size: 1.25rem; font-weight: 600; color: var(--dark); }
        
        .header-user { display: flex; align-items: center; gap: 15px; }
        
        .user-info { text-align: right; }
        .user-name { font-weight: 600; color: var(--dark); }
        .user-role { font-size: 0.875rem; color: var(--gray-600); }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), #C4A77D);
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
        }
        
        .content { padding: 30px; }
        
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 700px;
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title { font-size: 1.125rem; font-weight: 600; color: var(--dark); }
        
        .btn-back {
            padding: 8px 15px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .card-body { padding: 25px; }
        
        .form-group { margin-bottom: 20px; }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .form-group textarea { min-height: 100px; resize: vertical; }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn-submit {
            padding: 12px 30px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover { background: var(--primary-dark); }
        
        .btn-group { display: flex; gap: 10px; }
        
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
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
            <a href="../index.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="menu-section">Manajemen Data</div>
            <a href="index.php" class="menu-item active">
                <i class="fas fa-coffee"></i>
                <span>Produk</span>
            </a>
            <a href="../categories/index.php" class="menu-item">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
            <a href="../messages/index.php" class="menu-item">
                <i class="fas fa-envelope"></i>
                <span>Pesan</span>
            </a>
            
            <div class="menu-section">Lainnya</div>
            <a href="../../index.php" class="menu-item" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
            <a href="../logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="admin-header">
            <h1 class="header-title">Tambah Produk</h1>
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
                    <h2 class="card-title">Form Tambah Produk</h2>
                    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>
                
                <div class="card-body">
                    <?php if($error): ?>
                    <div class="error-message"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nama_produk">Nama Produk *</label>
                            <input type="text" id="nama_produk" name="nama_produk" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="harga">Harga (Rp) *</label>
                            <input type="number" id="harga" name="harga" min="0" step="100" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kategori_id">Kategori</label>
                            <select id="kategori_id" name="kategori_id">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['nama_kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="index.php" class="btn-back">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

