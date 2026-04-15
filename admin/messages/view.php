<?php
require_once '../../config/functions.php';
requireLogin();

$settings = getSettings();

// Get message ID
$message_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

global $conn;
$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();

// If message not found, redirect
if (!$message) {
    echo "<script>alert('Pesan tidak ditemukan!'); window.location.href='index.php';</script>";
    exit();
}

// Mark as read
if ($message['status'] == 'unread') {
    markMessageAsRead($message_id);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Pesan - Admin <?= $settings['site_name'] ?></title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            border-left-color: #C4A77D;
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

        .content {
            padding: 30px;
        }

        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
            max-width: 800px;
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

        .btn-back {
            padding: 8px 15px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 25px;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-200);
        }

        .message-info h3 {
            font-size: 1.25rem;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .message-info p {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .message-date {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .message-content {
            line-height: 1.8;
            color: var(--gray-600);
        }

        .message-actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        .btn-delete {
            padding: 10px 20px;
            background: var(--danger);
            color: var(--white);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            <a href="../products/index.php" class="menu-item">
                <i class="fas fa-coffee"></i>
                <span>Produk</span>
            </a>
            <a href="../categories/index.php" class="menu-item">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
            <a href="index.php" class="menu-item active">
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
            <h1 class="header-title">Baca Pesan</h1>
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
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Pesan dari
                        <?= $message['nama'] ?>
                    </h2>
                    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                </div>

                <div class="card-body">
                    <div class="message-header">
                        <div class="message-info">
                            <h3>
                                <?= $message['subject'] ?>
                            </h3>
                            <p>
                                <?= $message['nama'] ?>
                                <<?= $message['email'] ?>>
                            </p>
                        </div>
                        <div class="message-date">
                            <?= date('d F Y, H:i', strtotime($message['created_at'])) ?>
                        </div>
                    </div>

                    <div class="message-content">
                        <?= nl2br($message['pesan']) ?>
                    </div>

                    <div class="message-actions">
                        <a href="delete.php?id=<?= $message['id'] ?>" class="btn-delete"
                            onclick="return confirm('Yakin hapus pesan ini?')">
                            <i class="fas fa-trash"></i> Hapus Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>