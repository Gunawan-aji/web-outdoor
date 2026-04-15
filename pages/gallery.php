<?php
require_once '../config/functions.php';

$settings = getSettings();
$gallery = getAllGallery();

// Filter by category
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - <?= $settings['site_name'] ?></title>
    <meta name="description" content="Dokumentasi petualangan outdoor - Galeri foto hiking, camping, dan open trip">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.85) 0%, rgba(22, 33, 62, 0.7) 100%),
                url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920') center/cover;
            padding: 150px 0 80px;
            text-align: center;
            color: var(--white);
        }

        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .page-breadcrumb a:hover {
            color: var(--primary-accent);
        }

        .gallery-section {
            padding: 80px 0;
            background: var(--light);
        }

        .gallery-filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 12px 28px;
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-700);
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            aspect-ratio: 4/3;
            cursor: pointer;
            background: var(--gray-200);
        }

        .gallery-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--white);
            transition: var(--transition-slow);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(26, 26, 46, 0.9) 0%, transparent 60%);
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-item:hover .gallery-placeholder {
            transform: scale(1.1);
        }

        .gallery-title {
            color: var(--white);
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
        }

        .gallery-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            text-align: center;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="../index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <span><?= $settings['site_name'] ?></span>
                </a>

                <ul class="nav-menu">
                    <li><a href="../index.php#home" class="nav-link">Beranda</a></li>
                    <li><a href="../index.php#sewa" class="nav-link">Sewa Alat</a></li>
                    <li><a href="../index.php#trip" class="nav-link">Open Trip</a></li>
                    <li><a href="about.php" class="nav-link">Tentang</a></li>
                    <li><a href="gallery.php" class="nav-link active">Galeri</a></li>
                    <li><a href="contact.php" class="nav-link">Kontak</a></li>
                </ul>

                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Galeri Kami</h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <span>Galeri</span>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <!-- Filters -->
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="trip">Open Trip</button>
                <button class="filter-btn" data-filter="camping">Camping</button>
                <button class="filter-btn" data-filter="equipment">Peralatan</button>
                <button class="filter-btn" data-filter="team">Tim</button>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <?php foreach ($gallery as $item): ?>
                    <div class="gallery-item" data-category="<?= $item['kategori'] ?>">
                        <div class="gallery-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="gallery-overlay">
                            <div>
                                <h3 class="gallery-title"><?= $item['judul'] ?></h3>
                                <?php if ($item['deskripsi']): ?>
                                    <p class="gallery-description"><?= $item['deskripsi'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($gallery)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-images" style="font-size: 4rem; color: var(--gray-300); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">Belum ada galeri</h3>
                    <p style="color: var(--gray-600);">Galeri akan segera ditambahkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="../index.php" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-mountain"></i>
                        </div>
                        <span><?= $settings['site_name'] ?></span>
                    </a>
                    <p>
                        Partner terpercaya untuk petualangan outdoor Anda.
                        Sewa alat berkualitas dan join open trip ke berbagai destinasi menakjubkan.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Menu</h4>
                    <ul>
                        <li><a href="../index.php#home">Beranda</a></li>
                        <li><a href="../index.php#sewa">Sewa Alat</a></li>
                        <li><a href="about.php">Tentang</a></li>
                        <li><a href="gallery.php">Galeri</a></li>
                        <li><a href="contact.php">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Layanan</h4>
                    <ul>
                        <li><a href="#">Sewa Tenda</a></li>
                        <li><a href="#">Sewa Sleeping Bag</a></li>
                        <li><a href="#">Open Trip Gunung</a></li>
                        <li><a href="#">Camping Trip</a></li>
                        <li><a href="#">Private Trip</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Kontak</h4>
                    <ul>
                        <li><?= $settings['address'] ?></li>
                        <li><?= $settings['phone'] ?></li>
                        <li><?= $settings['email'] ?></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= $settings['site_name'] ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
    <script>
        // Gallery filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const galleryItems = document.querySelectorAll('.gallery-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active button
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;

                // Filter items
                galleryItems.forEach(item => {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>