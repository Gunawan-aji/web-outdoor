<?php
require_once '../config/functions.php';

$settings = getSettings();

// Get trip ID from URL
$trip_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$trip = getTripById($trip_id);

// If trip not found, redirect to trips
if (!$trip) {
    redirect('trips.php');
}

// Get related trips
$all_trips = getAllTrips(4);
$related_trips = array_filter($all_trips, function ($t) use ($trip_id) {
    return $t['id'] != $trip_id;
});
$related_trips = array_slice($related_trips, 0, 3);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $trip['nama_trip'] ?> - <?= $settings['site_name'] ?></title>
    <meta name="description" content="<?= substr($trip['deskripsi'], 0, 160) ?>">

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
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .trip-detail-section {
            padding: 80px 0;
            background: var(--light);
        }

        .trip-detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .trip-detail-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .trip-detail-image-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-accent));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6rem;
            color: var(--white);
            margin-bottom: 30px;
        }

        .trip-detail-title {
            font-size: 2rem;
            margin-bottom: 16px;
            color: var(--dark);
        }

        .trip-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 24px;
        }

        .trip-detail-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-600);
        }

        .trip-detail-meta-item i {
            color: var(--primary);
        }

        .trip-detail-description {
            color: var(--gray-700);
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .trip-detail-section-title {
            font-size: 1.3rem;
            margin-bottom: 16px;
            color: var(--dark);
        }

        .trip-itinerary {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .trip-itinerary h4 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .itinerary-item {
            display: flex;
            gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .itinerary-item:last-child {
            border-bottom: none;
        }

        .itinerary-time {
            min-width: 80px;
            font-weight: 600;
            color: var(--primary);
        }

        .itinerary-text {
            color: var(--gray-700);
        }

        .trip-include-exclude {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .trip-include,
        .trip-exclude {
            background: var(--white);
            padding: 24px;
            border-radius: 16px;
        }

        .trip-include h4,
        .trip-exclude h4 {
            font-size: 1.1rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trip-include h4 {
            color: var(--success);
        }

        .trip-exclude h4 {
            color: var(--danger);
        }

        .trip-include ul,
        .trip-exclude ul {
            list-style: none;
        }

        .trip-include li,
        .trip-exclude li {
            padding: 8px 0;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .trip-include li i {
            color: var(--success);
        }

        .trip-exclude li i {
            color: var(--danger);
        }

        /* Booking Card */
        .booking-card {
            background: var(--white);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 100px;
        }

        .booking-price {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .booking-price-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 20px;
        }

        .booking-availability {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px;
            background: var(--light-secondary);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .booking-availability i {
            color: var(--primary);
        }

        .booking-availability span {
            color: var(--gray-700);
            font-weight: 500;
        }

        .booking-form .form-group {
            margin-bottom: 16px;
        }

        .booking-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .booking-form label .required {
            color: var(--danger);
        }

        .booking-form input,
        .booking-form select,
        .booking-form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .booking-form input:focus,
        .booking-form select:focus,
        .booking-form textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .booking-form textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-book {
            width: 100%;
            padding: 16px;
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
            transition: var(--transition);
        }

        .btn-book:hover {
            background: var(--primary-dark);
        }

        .booking-note {
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-top: 16px;
            text-align: center;
        }

        /* Related Trips */
        .related-trips {
            padding: 80px 0;
            background: var(--white);
        }

        /* Success Modal */
        .booking-success-modal {
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

        .booking-success-modal.active {
            display: flex;
        }

        .booking-success-content {
            background: var(--white);
            padding: 48px;
            border-radius: 16px;
            text-align: center;
            max-width: 440px;
        }

        .booking-success-content i {
            font-size: 4.5rem;
            color: var(--success);
            margin-bottom: 24px;
        }

        .booking-success-content h3 {
            font-size: 1.75rem;
            margin-bottom: 12px;
        }

        .booking-success-content .kode-booking {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            margin: 20px 0;
            padding: 14px;
            background: var(--light-secondary);
            border-radius: 8px;
        }

        @media (max-width: 992px) {
            .trip-detail-grid {
                grid-template-columns: 1fr;
            }

            .booking-card {
                position: static;
            }

            .trip-include-exclude {
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
                    <span>
                        <?= $settings['site_name'] ?>
                    </span>
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
            <h1 class="page-title">
                <?= $trip['nama_trip'] ?>
            </h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <a href="trips.php">Open Trip</a>
                <span>/</span>
                <span>
                    <?= $trip['nama_trip'] ?>
                </span>
            </div>
        </div>
    </section>

    <!-- Trip Detail Section -->
    <section class="trip-detail-section">
        <div class="container">
            <div class="trip-detail-grid">
                <!-- Left Column -->
                <div class="trip-detail-content">
                    <div class="trip-detail-image-placeholder">
                        <i class="fas fa-mountain"></i>
                    </div>

                    <span class="trip-difficulty <?= $trip['tingkat_kesulitan'] ?>"
                        style="display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 16px;">
                        <?= ucfirst($trip['tingkat_kesulitan']) ?>
                    </span>

                    <h1 class="trip-detail-title">
                        <?= $trip['nama_trip'] ?>
                    </h1>

                    <div class="trip-detail-meta">
                        <div class="trip-detail-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= $trip['lokasi'] ?>
                        </div>
                        <div class="trip-detail-meta-item">
                            <i class="fas fa-calendar"></i>
                            <?= date('d M Y', strtotime($trip['tanggal_mulai'])) ?> -
                            <?= date('d M Y', strtotime($trip['tanggal_selesai'])) ?>
                        </div>
                        <div class="trip-detail-meta-item">
                            <i class="fas fa-users"></i>
                            <?= $trip['terisi'] ?>/
                            <?= $trip['kapasitas'] ?> peserta
                        </div>
                    </div>

                    <div class="trip-detail-description">
                        <p>
                            <?= $trip['deskripsi'] ?>
                        </p>
                    </div>

                    <!-- Itinerary -->
                    <div class="trip-itinerary">
                        <h4><i class="fas fa-list-ol"></i> Itinerary</h4>
                        <?php if ($trip['itinerary']): ?>
                            <?php
                            $itinerary_lines = explode("\n", $trip['itinerary']);
                            foreach ($itinerary_lines as $line):
                                if (trim($line)):
                                    ?>
                                    <div class="itinerary-item">
                                        <span class="itinerary-text">
                                            <?= trim($line) ?>
                                        </span>
                                    </div>
                                <?php
                                endif;
                            endforeach;
                            ?>
                        <?php else: ?>
                            <p style="color: var(--gray-600);">Itinerary akan diberikan setelah konfirmasi.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Include & Exclude -->
                    <div class="trip-include-exclude">
                        <div class="trip-include">
                            <h4><i class="fas fa-check-circle"></i> Sudah Include</h4>
                            <ul>
                                <?php if ($trip['include']): ?>
                                    <?php
                                    $includes = explode(',', $trip['include']);
                                    foreach ($includes as $include):
                                        ?>
                                        <li><i class="fas fa-check"></i>
                                            <?= trim($include) ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><i class="fas fa-check"></i> Peralatan camping</li>
                                    <li><i class="fas fa-check"></i> Guide profesional</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="trip-exclude">
                            <h4><i class="fas fa-times-circle"></i> Exclude</h4>
                            <ul>
                                <?php if ($trip['exclude']): ?>
                                    <?php
                                    $excludes = explode(',', $trip['exclude']);
                                    foreach ($excludes as $exclude):
                                        ?>
                                        <li><i class="fas fa-times"></i>
                                            <?= trim($exclude) ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><i class="fas fa-times"></i> Pengeluaran pribadi</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Card -->
                <div class="trip-detail-sidebar">
                    <div class="booking-card">
                        <div class="booking-price">
                            <?= formatCurrency($trip['harga']) ?>
                        </div>
                        <div class="booking-price-label">per orang</div>

                        <div class="booking-availability">
                            <i class="fas fa-users"></i>
                            <span>
                                <?= $trip['kapasitas'] - $trip['terisi'] ?> slot tersedia
                            </span>
                        </div>

                        <form class="booking-form" id="bookingForm">
                            <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">

                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama_peserta" required placeholder="Nama Anda">
                            </div>

                            <div class="form-group">
                                <label>Nomor WhatsApp <span class="required">*</span></label>
                                <input type="tel" name="no_hp" required placeholder="081234567890">
                            </div>

                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" required placeholder="email@anda.com">
                            </div>

                            <div class="form-group">
                                <label>Jumlah Orang <span class="required">*</span></label>
                                <select name="jumlah_orang" id="jumlah_orang" required onchange="updateTotalPrice()">
                                    <?php for ($i = 1; $i <= ($trip['kapasitas'] - $trip['terisi']); $i++): ?>
                                        <option value="<?= $i ?>">
                                            <?= $i ?> orang
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea name="catatan" placeholder="Catatan khusus (opsional)"></textarea>
                            </div>

                            <button type="submit" class="btn-book" id="btnBook">
                                <i class="fas fa-paper-plane"></i> Booking Sekarang
                            </button>

                            <p class="booking-note">
                                <i class="fas fa-info-circle"></i> Pembayaran dilakukan setelah konfirmasi dari tim kami
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Trips -->
    <?php if (!empty($related_trips)): ?>
        <section class="related-trips">
            <div class="container">
                <div class="section-header">
                    <span class="section-tag">Trip Lainnya</span>
                    <h2 class="section-title">Open Trip Serupa</h2>
                </div>

                <div class="trips-grid">
                    <?php foreach ($related_trips as $related): ?>
                        <div class="trip-card">
                            <div class="trip-image-wrapper">
                                <div class="trip-image-placeholder">
                                    <i class="fas fa-mountain"></i>
                                </div>
                                <div class="trip-overlay"></div>
                                <span class="trip-difficulty <?= $related['tingkat_kesulitan'] ?>">
                                    <?= ucfirst($related['tingkat_kesulitan']) ?>
                                </span>
                                <div class="trip-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= $related['lokasi'] ?>
                                </div>
                            </div>
                            <div class="trip-content">
                                <h3 class="trip-title">
                                    <?= $related['nama_trip'] ?>
                                </h3>
                                <div class="trip-meta">
                                    <div class="trip-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('d M', strtotime($related['tanggal_mulai'])) ?> -
                                        <?= date('d M Y', strtotime($related['tanggal_selesai'])) ?>
                                    </div>
                                </div>
                                <div class="trip-footer">
                                    <div class="trip-price">
                                        <span class="trip-price-value">
                                            <?= formatCurrency($related['harga']) ?>
                                        </span>
                                    </div>
                                    <a href="trip-detail.php?id=<?= $related['id'] ?>" class="btn btn-primary">Lihat</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="../index.php" class="logo">
                        <div class="logo-icon">
                            <i class="fas fa-mountain"></i>
                        </div>
                        <span>
                            <?= $settings['site_name'] ?>
                        </span>
                    </a>
                    <p>
                        Partner terpercaya untuk petualangan outdoor Anda.
                        Sewa alat berkualitas dan join open trip ke berbagai destinasi menakjubkan.
                    </p>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Menu</h4>
                    <ul>
                        <li><a href="../index.php#home">Beranda</a></li>
                        <li><a href="../index.php#sewa">Sewa Alat</a></li>
                        <li><a href="trips.php">Open Trip</a></li>
                        <li><a href="about.php">Tentang</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4 class="footer-title">Kontak</h4>
                    <ul>
                        <li>
                            <?= $settings['address'] ?>
                        </li>
                        <li>
                            <?= $settings['phone'] ?>
                        </li>
                        <li>
                            <?= $settings['email'] ?>
                        </li>
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

    <!-- Booking Success Modal -->
    <div class="booking-success-modal" id="bookingSuccessModal">
        <div class="booking-success-content">
            <i class="fas fa-check-circle"></i>
            <h3>Booking Berhasil!</h3>
            <div class="kode-booking" id="successKodeBooking"></div>
            <p>Tim kami akan menghubungi Anda untuk konfirmasi lebih lanjut.</p>
            <button class="btn btn-primary" onclick="closeBookingSuccess()">Tutup</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/main.js"></script>
    <script>
        // Calculate total price
        const tripPrice = <?= $trip['harga'] ?>;

        function updateTotalPrice() {
            const jumlah = document.getElementById('jumlah_orang').value;
            const total = tripPrice * jumlah;
        }

        // Booking form submission
        document.getElementById('bookingForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnBook');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            const formData = new FormData(this);

            try {
                const response = await fetch('process-trip-booking.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('successKodeBooking').textContent = result.kode_booking;
                    document.getElementById('bookingSuccessModal').classList.add('active');
                    this.reset();
                } else {
                    alert('Gagal: ' + result.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        function closeBookingSuccess() {
            document.getElementById('bookingSuccessModal').classList.remove('active');
        }
    </script>
</body>

</html>