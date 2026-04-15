<?php
require_once '../config/functions.php';

$settings = getSettings();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - <?= $settings['site_name'] ?></title>
    <meta name="description" content="Tentang Outdoor Adventure - Partner terpercaya untuk petualangan outdoor Anda">

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

        .about-section {
            padding: 80px 0;
            background: var(--white);
        }

        .about-story {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .about-story h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .about-story p {
            color: var(--gray-600);
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 1.05rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 60px;
        }

        .stat-item {
            text-align: center;
            padding: 30px;
            background: var(--light-secondary);
            border-radius: var(--border-radius);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 500;
        }

        .team-section {
            padding: 80px 0;
            background: var(--light);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary), var(--primary-accent));
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--white);
        }

        .team-name {
            font-size: 1.25rem;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .team-role {
            color: var(--primary);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .values-section {
            padding: 80px 0;
            background: var(--white);
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .value-card {
            text-align: center;
            padding: 40px 30px;
        }

        .value-icon {
            width: 80px;
            height: 80px;
            background: var(--light-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary);
        }

        .value-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .value-text {
            color: var(--gray-600);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .values-grid {
                grid-template-columns: 1fr;
            }
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
                    <li><a href="about.php" class="nav-link active">Tentang</a></li>
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
            <h1 class="page-title">Tentang Kami</h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <span>Tentang Kami</span>
            </div>
        </div>
    </section>

    <!-- About Story -->
    <section class="about-section">
        <div class="container">
            <div class="about-story">
                <span class="section-tag">Cerita Kami</span>
                <h2>Outdoor Adventure</h2>
                <p>
                    Didirikan pada tahun 2020, Outdoor Adventure bermula dari kecintaan kami terhadap
                    alam dan petualangan. Kami percaya bahwa setiap orang berhak merasakan keindahan
                    alam Indonesia yang luar biasa - dari puncak-puncak gunung yang menjulang tinggi
                    hingga hamparan savana yang memesona.
                </p>
                <p>
                    Dengan semangat tersebut, kami hadir sebagai partner terpercaya untuk memenuhi
                    kebutuhan peralatan outdoor Anda. Kami menyediakan berbagai perlengkapan hiking,
                    camping, dan mountaineering berkualitas tinggi dengan harga terjangkau.
                </p>
                <p>
                    Tak hanya itu, kami juga membuka kesempatan bagi Anda untuk bergabung dalam
                    open trip ke berbagai destinasi populer seperti Gunung Semeru, Arjuno, Bromo,
                    Rinjani, dan masih banyak lagi. Dengan dipandu oleh tim profesional yang
                    berpengalaman, keamanan dan kenyamanan Anda menjadi prioritas utama kami.
                </p>
            </div>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">5+</div>
                    <div class="stat-label">Tahun Pengalaman</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Alat Outdoor</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Trip Diselesaikan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Nilai Kami</span>
                <h2 class="section-title">Mengapa Memilih Kami</h2>
                <p class="section-subtitle">
                    Komitmen kami untuk memberikan pengalaman terbaik
                </p>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3 class="value-title">Kualitas Terjamin</h3>
                    <p class="value-text">
                        Semua peralatan kami melalui perawatan rutin dan inspeksi ketat sebelum disewakan.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3 class="value-title">Keamanan Utama</h3>
                    <p class="value-text">
                        Guide kami tersertifikasi dan memiliki pengalaman bertahun-tahun di dunia mountaineering.
                    </p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="value-title">Pelayanan Prima</h3>
                    <p class="value-text">
                        Tim kami siap membantu Anda 24/7 untuk memastikan perjalanan Anda berjalan lancar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Tim Kami</span>
                <h2 class="section-title">Kenali Tim Kami</h2>
                <p class="section-subtitle">
                    Tim profesional yang siap menemani petualangan Anda
                </p>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3 class="team-name">Ahmad Fauzi</h3>
                    <p class="team-role">Head Guide</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-hiking"></i>
                    </div>
                    <h3 class="team-name">Budi Santoso</h3>
                    <p class="team-role">Senior Climber</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-campground"></i>
                    </div>
                    <h3 class="team-name">Siti Rahayu</h3>
                    <p class="team-role">Equipment Specialist</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-map"></i>
                    </div>
                    <h3 class="team-name">Dewi Lestari</h3>
                    <p class="team-role">Trip Coordinator</p>
                </div>
            </div>
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
                        <li><a href="#">Sewa Carrier</a></li>
                        <li><a href="#">Open Trip Gunung</a></li>
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
</body>

</html>