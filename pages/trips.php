<?php
require_once '../config/functions.php';

$settings = getSettings();
$trips = getAllTrips();

// Filter by difficulty
$difficulty = isset($_GET['difficulty']) ? $_GET['difficulty'] : 'all';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Trip - <?= $settings['site_name'] ?></title>
    <meta name="description" content="Bergabung dengan open trip ke gunung-gunung populer Indonesia">

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

        .trips-section {
            padding: 80px 0;
            background: var(--light);
        }

        .trips-filters {
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
                    <li><a href="trips.php" class="nav-link active">Open Trip</a></li>
                    <li><a href="about.php" class="nav-link">Tentang</a></li>
                    <li><a href="gallery.php" class="nav-link">Galeri</a></li>
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
            <h1 class="page-title">Open Trip</h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <span>Open Trip</span>
            </div>
        </div>
    </section>

    <!-- Trips Section -->
    <section class="trips-section">
        <div class="container">
            <!-- Filters -->
            <div class="trips-filters">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="easy">Mudah</button>
                <button class="filter-btn" data-filter="moderate">Sedang</button>
                <button class="filter-btn" data-filter="hard">Sulit</button>
            </div>

            <!-- Trips Grid -->
            <div class="trips-grid">
                <?php foreach ($trips as $trip): ?>
                    <div class="trip-card" data-difficulty="<?= $trip['tingkat_kesulitan'] ?>">
                        <div class="trip-image-wrapper">
                            <div class="trip-image-placeholder">
                                <i class="fas fa-mountain"></i>
                            </div>
                            <div class="trip-overlay"></div>
                            <span
                                class="trip-difficulty <?= $trip['tingkat_kesulitan'] ?>"><?= ucfirst($trip['tingkat_kesulitan']) ?></span>
                            <div class="trip-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= $trip['lokasi'] ?>
                            </div>
                        </div>
                        <div class="trip-content">
                            <h3 class="trip-title"><?= $trip['nama_trip'] ?></h3>
                            <p class="trip-description"><?= $trip['deskripsi'] ?></p>
                            <div class="trip-meta">
                                <div class="trip-meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d M', strtotime($trip['tanggal_mulai'])) ?> -
                                    <?= date('d M Y', strtotime($trip['tanggal_selesai'])) ?>
                                </div>
                                <div class="trip-meta-item">
                                    <i class="fas fa-users"></i>
                                    <?= $trip['terisi'] ?>/<?= $trip['kapasitas'] ?> peserta
                                </div>
                            </div>
                            <div class="trip-footer">
                                <div class="trip-price">
                                    <span class="trip-price-label">Mulai dari</span>
                                    <span class="trip-price-value"><?= formatCurrency($trip['harga']) ?></span>
                                    <span class="trip-availablity">Tersedia
                                        <span><?= $trip['kapasitas'] - $trip['terisi'] ?> slot</span></span>
                                </div>
                                <a href="trip-detail.php?id=<?= $trip['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Booking
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($trips)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-mountain" style="font-size: 4rem; color: var(--gray-300); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">Tidak ada trip tersedia</h3>
                    <p style="color: var(--gray-600);">Silakan cek kembali soon untuk jadwal trip terbaru.</p>
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
                        <li><a href="trips.php">Open Trip</a></li>
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
        // Filter functionality
        const filterBtns = document.querySelectorAll('.filter-btn');
        const tripCards = document.querySelectorAll('.trip-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;

                tripCards.forEach(card => {
                    if (filter === 'all' || card.dataset.difficulty === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>