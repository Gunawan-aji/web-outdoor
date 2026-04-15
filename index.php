<?php
require_once 'config/functions.php';

$settings = getSettings();
$featured_products = getFeaturedProducts(6);
$categories = getAllCategories();
$gallery = getAllGallery();
$trips = getAllTrips(4);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $settings['site_name'] ?> -
        <?= $settings['tagline'] ?>
    </title>
    <meta name="description"
        content="Sewa alat outdoor berkualitas danjoin open trip hiking/camping bersama Outdoor Adventure. Pengalaman tak terlupakan menunggumu!">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Page Header Style */
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

        /* Cart Button in Header */
        .cart-header-btn {
            position: relative;
            padding: 10px 15px;
            background: var(--primary);
            color: var(--white);
            border-radius: 50px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .cart-header-btn:hover {
            background: var(--primary-dark);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent);
            color: white;
            font-size: 0.75rem;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Menu Card Actions */
        .menu-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-add-cart {
            flex: 1;
            padding: 10px 15px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-add-cart:hover {
            background: var(--primary-dark);
        }

        /* Cart Modal */
        .cart-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: flex-end;
        }

        .cart-modal.active {
            display: flex;
        }

        .cart-modal-content {
            width: 100%;
            max-width: 500px;
            height: 100%;
            background: var(--white);
            box-shadow: -5px 0 25px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .cart-modal-header {
            padding: 20px;
            background: var(--dark-secondary);
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-modal-header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
        }

        .close-cart {
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }

        .cart-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-600);
        }

        .cart-empty i {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 20px;
        }

        .cart-empty p {
            margin-bottom: 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--gray-100);
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .cart-item-price {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cart-item-quantity .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .cart-item-quantity .qty-btn:hover {
            background: var(--primary-dark);
        }

        .cart-item-quantity span {
            min-width: 25px;
            text-align: center;
            font-weight: 600;
        }

        .cart-item-total {
            min-width: 70px;
            text-align: right;
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            padding: 5px;
            font-size: 1rem;
        }

        .cart-modal-footer {
            padding: 20px;
            border-top: 2px solid var(--gray-200);
            background: var(--white);
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .cart-total-label {
            font-size: 1rem;
            color: var(--gray-600);
        }

        .cart-total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Checkout Button */
        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 90, 39, 0.3);
        }

        /* Checkout Form */
        .checkout-form {
            display: none;
        }

        .checkout-form.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .payment-option {
            display: none;
        }

        .payment-option+label {
            display: block;
            padding: 12px 8px;
            text-align: center;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }

        .payment-option:checked+label {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--white);
        }

        .payment-option+label i {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .btn-submit-order {
            width: 100%;
            padding: 15px;
            background: var(--accent);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-submit-order:hover {
            background: var(--accent-light);
        }

        .btn-submit-order:disabled {
            background: var(--gray-400);
            cursor: not-allowed;
        }

        .btn-back-to-cart {
            width: 100%;
            padding: 12px;
            background: var(--gray-200);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .btn-back-to-cart:hover {
            background: var(--gray-300);
        }

        /* Order Success Modal */
        .order-success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .order-success-modal.active {
            display: flex;
        }

        .order-success-content {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            animation: scaleIn 0.3s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .order-success-content i {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
        }

        .order-success-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .order-success-content .kode-order {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin: 15px 0;
            padding: 10px;
            background: var(--light-secondary);
            border-radius: 8px;
        }

        .order-success-content p {
            color: var(--gray-600);
            margin-bottom: 20px;
        }

        .order-success-content .btn-primary {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 80px 0;
            text-align: center;
            color: var(--white);
        }

        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 16px;
            color: var(--white);
        }

        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Quick Info Cards */
        .quick-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .quick-info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quick-info-card i {
            font-size: 2rem;
            color: var(--primary-accent);
            margin-bottom: 12px;
        }

        .quick-info-card h4 {
            color: var(--white);
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .quick-info-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <span>
                        <?= $settings['site_name'] ?>
                    </span>
                </a>

                <ul class="nav-menu">
                    <li><a href="#home" class="nav-link active">Beranda</a></li>
                    <li><a href="#sewa" class="nav-link">Sewa Alat</a></li>
                    <li><a href="#trip" class="nav-link">Open Trip</a></li>
                    <li><a href="#about" class="nav-link">Tentang</a></li>
                    <li><a href="#gallery" class="nav-link">Galeri</a></li>
                    <li><a href="#contact" class="nav-link">Kontak</a></li>
                    <li>
                        <button class="cart-header-btn" id="cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Keranjang</span>
                            <span class="cart-count" id="cart-count" style="display: none;">0</span>
                        </button>
                    </li>
                </ul>

                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-compass"></i>
                    <span>Petualangan Menunggumu</span>
                </div>
                <h1 class="hero-title">
                    Jelajahi Alam,<br>
                    Temukan <span>Petualangan</span>
                </h1>
                <p class="hero-subtitle">
                    Sewa alat outdoor berkualitas tinggi dan bergabung dengan open trip menarik ke berbagai destinasi
                    gunung dan camping terpopuler Indonesia.
                </p>
                <div class="btn-group">
                    <a href="#sewa" class="btn btn-primary">
                        <i class="fas fa-campground"></i> Sewa Alat
                    </a>
                    <a href="#trip" class="btn btn-outline">
                        <i class="fas fa-hiking"></i> Open Trip
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-number">50+</div>
                        <div class="hero-stat-label">Alat Outdoor</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">20+</div>
                        <div class="hero-stat-label">Open Trip</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">5000+</div>
                        <div class="hero-stat-label">Pelanggan Puas</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number">99%</div>
                        <div class="hero-stat-label">Kepuasan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Info Section -->
    <div style="background: var(--dark-secondary); padding: 30px 0; margin-top: -1px;">
        <div class="container">
            <div class="quick-info-grid">
                <div class="quick-info-card">
                    <i class="fas fa-shipping-fast"></i>
                    <h4>Pengiriman Cepat</h4>
                    <p>Pengiriman ke seluruh Indonesia</p>
                </div>
                <div class="quick-info-card">
                    <i class="fas fa-medal"></i>
                    <h4>Alat Berkualitas</h4>
                    <p>Perawatan rutin & terjamin bersih</p>
                </div>
                <div class="quick-info-card">
                    <i class="fas fa-user-tie"></i>
                    <h4>Guide Profesional</h4>
                    <p>Tim berpengalaman & tersertifikasi</p>
                </div>
                <div class="quick-info-card">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Asuransi Tertanggung</h4>
                    <p>Perlindungan keamanan penuh</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section class="section features">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Layanan Kami</span>
                <h2 class="section-title">Apa yang Kami Tawarkan</h2>
                <p class="section-subtitle">
                    Dua layanan utama untuk memenuhi kebutuhan petualangan outdoormu
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-campground"></i>
                    </div>
                    <h3 class="feature-title">Sewa Alat Outdoor</h3>
                    <p class="feature-text">
                        Tersedia berbagai peralatan outdoor berkualitas: tenda, sleeping bag, carrier, kompor, dan masih
                        banyak lagi dengan harga terjangkau.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3 class="feature-title">Open Trip Hiking</h3>
                    <p class="feature-text">
                        Bergabung dengan open trip ke gunung-gunung populer: Semeru, Arjuno, Bromo, Rinjani, dan masih
                        banyak lagi dengan guide profesional.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-campground"></i>
                    </div>
                    <h3 class="feature-title">Camping Trip</h3>
                    <p class="feature-text">
                        Nikmati pengalaman camping di lokasi-lokasi eksotis dengan tim yang berpengalaman dan peralatan
                        lengkap siap pakai.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Private Trip</h3>
                    <p class="feature-text">
                        Ingin private? Kami juga melayani private trip dengan itinerary customize sesuai keinginanmu
                        bersama keluarga atau komunitas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipment Rental Section -->
    <section id="sewa" class="section equipment-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Sewa Alat</span>
                <h2 class="section-title">Perlengkapan Outdoor</h2>
                <p class="section-subtitle">
                    Pilih peralatan outdoor berkualitas untuk petualanganmu
                </p>
            </div>

            <!-- Category Filter -->
            <div class="equipment-categories">
                <button class="category-btn active" data-category="all">Semua</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="category-btn" data-category="<?= $cat['id'] ?>">
                        <?= $cat['nama_kategori'] ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Equipment Grid -->
            <div class="equipment-grid">
                <?php foreach ($featured_products as $product): ?>
                    <div class="equipment-card" data-category="<?= $product['kategori_id'] ?>">
                        <div class="equipment-image-placeholder">
                            <i class="fas fa-campground"></i>
                        </div>
                        <div class="equipment-badge available">Tersedia</div>
                        <div class="equipment-content">
                            <span class="equipment-category">
                                <?= $product['nama_kategori'] ?>
                            </span>
                            <h3 class="equipment-title">
                                <?= $product['nama_produk'] ?>
                            </h3>
                            <p class="equipment-description">
                                <?= $product['deskripsi'] ?>
                            </p>
                            <div class="equipment-pricing">
                                <span class="price-label">Mulai dari</span>
                                <span class="price-value"><?= formatCurrency($product['harga_sewa_harian']) ?></span>
                                <span class="price-period">/hari</span>
                            </div>
                            <div class="equipment-footer">
                                <div class="equipment-stock">
                                    <i class="fas fa-box"></i>
                                    <span>Stok: <?= $product['stok'] ?></span>
                                </div>
                                <div class="equipment-actions">
                                    <button class="btn-rent"
                                        onclick="quickAddToCart(<?= $product['id'] ?>, '<?= addslashes($product['nama_produk']) ?>', <?= $product['harga_sewa_harian'] ?>)">
                                        <i class="fas fa-cart-plus"></i> Sewa
                                    </button>
                                    <a href="pages/product-detail.php?id=<?= $product['id'] ?>" class="btn-detail">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 50px;">
                <a href="pages/menu.php" class="btn btn-primary">Lihat Semua Alat</a>
            </div>
        </div>
    </section>

    <!-- Open Trips Section -->
    <section id="trip" class="section section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Open Trip</span>
                <h2 class="section-title">Jadwal Open Trip</h2>
                <p class="section-subtitle">
                    Bergabung dengan open trip ke destinasi favorit para pendaki
                </p>
            </div>

            <div class="trips-grid">
                <?php foreach ($trips as $trip): ?>
                    <div class="trip-card">
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
                                <a href="pages/trip-detail.php?id=<?= $trip['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Booking
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 50px;">
                <a href="pages/trips.php" class="btn btn-primary">Lihat Semua Trip</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-images">
                    <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600" alt="Tentang Kami"
                        class="about-img-main">
                    <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=300"
                        alt="Outdoor Adventure" class="about-img-accent">
                    <div class="about-img-stats">
                        <div class="about-img-stats-number">5+</div>
                        <div class="about-img-stats-label">Tahun Pengalaman</div>
                    </div>
                </div>

                <div class="about-content">
                    <span class="section-tag">Tentang Kami</span>
                    <h2>Outdoor Adventure</h2>
                    <p>
                        Didirikan dengan semangat cinta terhadap alam dan petualangan, Outdoor Adventure hadir untuk
                        memberikan pengalaman outdoor yang aman, menyenangkan, dan tak terlupakan bagi seluruh pecinta
                        alam Indonesia.
                    </p>
                    <p>
                        Kami percaya bahwa setiap orang berhak merasakan keindahan alam Indonesia yang luar biasa.
                        Dengan peralatan berkualitas dan tim guide profesional, kami siap menjembatani mimpi-mimpi
                        petualanganmu menjadi kenyataan.
                    </p>

                    <div class="about-features">
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Alat Terawat & Steril</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Guide Bersertifikat</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Itinerary Fleksibel</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Harga Transparan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Gunung Ditinjau</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Pelanggan Puas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number">200+</div>
                    <div class="stat-label">Trip Diselesaikan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Penghargaan Diterima</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="section gallery-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Galeri</span>
                <h2 class="section-title">Dokumentasi Petualangan</h2>
                <p class="section-subtitle">
                    Momen-momen tak terlupakan dari petualangan kami
                </p>
            </div>

            <div class="gallery-grid">
                <?php foreach (array_slice($gallery, 0, 6) as $item): ?>
                    <div class="gallery-item">
                        <div class="gallery-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                        <div class="gallery-overlay">
                            <h3 class="gallery-title">
                                <?= $item['judul'] ?>
                            </h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 50px;">
                <a href="pages/gallery.php" class="btn btn-primary">Lihat Semua Foto</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Siap Memulai Petualangan?</h2>
            <p>
                Jangan tunggu lagi! Segera rencanakan trip outdoor-mu bersama Outdoor Adventure.
                Tim kami siap membantu Anda dari perencanaan hingga execução.
            </p>
            <div class="btn-group" style="justify-content: center;">
                <a href="#sewa" class="btn" style="background: var(--white); color: var(--primary);">
                    <i class="fas fa-campground"></i> Sewa Alat
                </a>
                <a href="#contact" class="btn" style="background: var(--accent); color: var(--white);">
                    <i class="fas fa-phone"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Hubungi Kami</span>
                <h2 class="section-title">Kontak & Lokasi</h2>
                <p class="section-subtitle">
                    Kami siap membantu Anda. Jangan ragu untuk menghubungi kami!
                </p>
            </div>

            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Informasi Kontak</h3>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Alamat</h4>
                            <p>
                                <?= $settings['address'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Telepon / WhatsApp</h4>
                            <p>
                                <?= $settings['phone'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p>
                                <?= $settings['email'] ?>
                            </p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Jam Buka</h4>
                            <p>
                                <?= $settings['opening_hours'] ?> (Setiap Hari)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <form class="contact-form" id="contactForm">
                        <div id="formSuccess" class="form-success">
                            Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.
                        </div>

                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subjek</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label for="pesan">Pesan</label>
                            <textarea id="pesan" name="pesan" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-mountain"></i>
                        </div>
                        <span>
                            <?= $settings['site_name'] ?>
                        </span>
                    </a>
                    <p>
                        Partner terpercaya untuk petualangan outdoor Anda.
                        Sewa alat berkualitas danjoin open trip ke berbagai destinasi menakjubkan.
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
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#sewa">Sewa Alat</a></li>
                        <li><a href="#trip">Open Trip</a></li>
                        <li><a href="#about">Tentang</a></li>
                        <li><a href="#gallery">Galeri</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Layanan</h4>
                    <ul>
                        <li><a href="#">Sewa Tenda</a></li>
                        <li><a href="#">Sewa Sleeping Bag</a></li>
                        <li><a href="#">Sewa Carrier</a></li>
                        <li><a href="#">Open Trip Gunung</a></li>
                        <li><a href="#">Camping Trip</a></li>
                        <li><a href="#">Private Trip</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Destinasi Populer</h4>
                    <ul>
                        <li><a href="#">Gunung Semeru</a></li>
                        <li><a href="#">Gunung Arjuno</a></li>
                        <li><a href="#">Mount Bromo</a></li>
                        <li><a href="#">Gunung Rinjani</a></li>
                        <li><a href="#">Kawah Ijen</a></li>
                        <li><a href="#">Papandayan</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy;
                    <?= date('Y') ?>
                    <?= $settings['site_name'] ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Keranjang Sewa</h3>
                <button class="close-cart" id="close-cart">&times;</button>
            </div>
            <div class="cart-modal-body" id="cart-items">
                <!-- Cart items will be loaded here -->
            </div>
            <div class="cart-modal-footer" id="cart-total">
                <!-- Cart total and checkout button will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div class="order-success-modal" id="order-success-modal">
        <div class="order-success-content">
            <i class="fas fa-check-circle"></i>
            <h3>Pemesanan Berhasil!</h3>
            <div class="kode-order" id="success-kode-order"></div>
            <p>Silakan simpan kode order Anda. Tim kami akan menghubungi Anda untuk konfirmasi.</p>
            <a href="#" class="btn-primary" onclick="Cart.clearCart(); closeSuccessModal();">Pesan Lagi</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
    <script>
        // Override cart update to include checkout button
        const originalUpdateCartModal = Cart.updateCartModal;
        Cart.updateCartModal = function () {
            const cartItemsContainer = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');

            if (!cartItemsContainer) return;

            const cart = this.getCart();

            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Keranjang Anda kosong</p>
                    </div>
                `;
                if (cartTotal) cartTotal.innerHTML = '';
                return;
            }

            // Show cart items
            let itemsHTML = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                itemsHTML += `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4>${item.productName}</h4>
                            <p class="cart-item-price">Rp ${item.price.toLocaleString('id-ID')} / hari</p>
                        </div>
                        <div class="cart-item-quantity">
                            <button class="qty-btn" onclick="Cart.updateQuantity(${item.productId}, ${item.quantity - 1})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span>${item.quantity}</span>
                            <button class="qty-btn" onclick="Cart.updateQuantity(${item.productId}, ${item.quantity + 1})">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="cart-item-total">
                            Rp ${itemTotal.toLocaleString('id-ID')}
                        </div>
                        <button class="cart-item-remove" onclick="Cart.removeItem(${item.productId})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            cartItemsContainer.innerHTML = itemsHTML;

            // Show total and checkout button
            if (cartTotal) {
                const total = this.getTotalPrice();
                cartTotal.innerHTML = `
                    <div class="cart-total-row">
                        <span class="cart-total-label">Total Pembayaran</span>
                        <span class="cart-total-amount">Rp ${total.toLocaleString('id-ID')}</span>
                    </div>
                    <button class="btn-checkout" onclick="showCheckoutForm()">
                        <i class="fas fa-credit-card"></i> Checkout
                    </button>
                `;
            }
        };

        // Show checkout form
        function showCheckoutForm() {
            const cartTotal = document.getElementById('cart-total');
            const cartItems = document.getElementById('cart-items');

            if (cartTotal && cartItems) {
                const total = Cart.getTotalPrice();

                // Hide cart items, show checkout form
                cartItems.innerHTML = '';

                cartTotal.innerHTML = `
                    <div class="checkout-form active">
                        <button type="button" class="btn-back-to-cart" onclick="showCartItems()">
                            <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                        </button>
                        <div class="cart-total-row">
                            <span class="cart-total-label">Total Pembayaran</span>
                            <span class="cart-total-amount">Rp ${total.toLocaleString('id-ID')}</span>
                        </div>
                        <form id="checkout-form" onsubmit="submitOrder(event)">
                            <div class="form-group">
                                <label>Nama Pelanggan <span class="required">*</span></label>
                                <input type="text" name="nama_pelanggan" id="nama_pelanggan" required placeholder="Masukkan nama Anda">
                            </div>
                            <div class="form-group">
                                <label>Nomor WhatsApp <span class="required">*</span></label>
                                <input type="text" name="no_hp" id="no_hp" required placeholder="Contoh: 081234567890">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Sewa <span class="required">*</span></label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Kembali <span class="required">*</span></label>
                                <input type="date" name="tanggal_kembali" id="tanggal_kembali" required>
                            </div>
                            <div class="form-group">
                                <label>Metode Pembayaran <span class="required">*</span></label>
                                <div class="payment-methods">
                                    <input type="radio" name="metode_pembayaran" id="payment-tunai" value="tunai" class="payment-option" checked>
                                    <label for="payment-tunai">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Tunai
                                    </label>
                                    
                                    <input type="radio" name="metode_pembayaran" id="payment-qris" value="qris" class="payment-option">
                                    <label for="payment-qris">
                                        <i class="fas fa-qrcode"></i>
                                        QRIS
                                    </label>
                                    
                                    <input type="radio" name="metode_pembayaran" id="payment-transfer" value="transfer" class="payment-option">
                                    <label for="payment-transfer">
                                        <i class="fas fa-university"></i>
                                        Transfer
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Catatan (Opsional)</label>
                                <textarea name="catatan" id="catatan" placeholder="Contoh: Saya butuh pengiriman ke hotel"></textarea>
                            </div>
                            <button type="submit" class="btn-submit-order" id="btn-submit">
                                <i class="fas fa-paper-plane"></i> Pesan
                            </button>
                        </form>
                    </div>
                `;

                // Set minimum date to today
                const today = new Date().toISOString().split('T')[0];
                document.getElementById('tanggal_sewa').setAttribute('min', today);
                document.getElementById('tanggal_kembali').setAttribute('min', today);
            }
        }

        // Show cart items view
        function showCartItems() {
            Cart.updateCartModal();
        }

        // Submit Order
        async function submitOrder(event) {
            event.preventDefault();

            const btn = document.getElementById('btn-submit');
            const originalText = btn.innerHTML;
            btn.classList.add('btn-loading');
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';

            try {
                const formData = new FormData(event.target);
                const cart = Cart.getCart();

                const cartItems = cart.map(item => ({
                    productId: item.productId,
                    productName: item.productName,
                    price: item.price,
                    quantity: item.quantity
                }));

                const requestData = {
                    nama_pelanggan: formData.get('nama_pelanggan'),
                    no_hp: formData.get('no_hp'),
                    tanggal_sewa: formData.get('tanggal_sewa'),
                    tanggal_kembali: formData.get('tanggal_kembali'),
                    metode_pembayaran: formData.get('metode_pembayaran'),
                    catatan: formData.get('catatan'),
                    cart_items: cartItems
                };

                const response = await fetch('pages/process-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('success-kode-order').textContent = result.kode_order;
                    document.getElementById('order-success-modal').classList.add('active');
                    Cart.clearCart();
                    Cart.closeCartModal();
                } else {
                    alert('Gagal: ' + result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('order-success-modal').classList.remove('active');
        }
    </script>
</body>

</html>