<?php
require_once '../config/functions.php';

$error = '';

// Generate CSRF token if not exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('index.php');
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } else {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password (using password_verify for bcrypt hash)
            if (password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['status'] != 'active') {
                    $error = 'Akun Anda tidak aktif! Silakan hubungi administrator.';
                } else {
                    // Regenerate session ID for security
                    session_regenerate_id(true);

                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_nama'] = $user['nama_lengkap'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['login_time'] = time();

                    // Redirect based on role
                    if ($user['role'] == 'kasir') {
                        redirect('../kasir/index.php');
                    } else {
                        redirect('index.php');
                    }
                }
            } else {
                $error = 'Password yang Anda masukkan salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin <?= getSettings()['site_name'] ?></title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #2d5a27;
            --primary-dark: #1e3d1a;
            --primary-light: #4a7c44;
            --primary-accent: #8bc34a;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --danger: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary), var(--primary-light));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 450px;
            padding: 48px 40px;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--primary-accent));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.5rem;
            color: var(--white);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .login-logo:hover {
            transform: rotate(0deg) scale(1.05);
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display:
                <?= $error ? 'block' : 'none' ?>
            ;
            border: 1px solid #f5c6cb;
        }

        .error-message i {
            margin-right: 8px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--gray-100);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.1);
        }

        .form-group .input-icon {
            position: relative;
        }

        .form-group .input-icon i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
            transition: color 0.3s ease;
        }

        .form-group .input-icon input {
            padding-left: 50px;
        }

        .form-group .input-icon:focus-within i {
            color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(45, 90, 39, 0.35);
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .back-to-site {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        .back-to-site a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-site a:hover {
            color: var(--primary-dark);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-mountain"></i>
            </div>
            <h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">Masuk ke panel <?= getSettings()['site_name'] ?></p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div class="back-to-site">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i> Kembali ke website
            </a>
        </div>
    </div>
</body>

</html>