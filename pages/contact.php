<?php
require_once '../config/functions.php';

$settings = getSettings();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - <?= $settings['site_name'] ?></title>
    <meta name="description" content="Hubungi kami untuk pertanyaan tentang sewa alat outdoor dan open trip">

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

        .contact-section {
            padding: 80px 0;
            background: var(--white);
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 60px;
            align-items: start;
        }

        .contact-info h3 {
            font-size: 1.75rem;
            margin-bottom: 24px;
            color: var(--dark);
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 28px;
        }

        .contact-icon {
            width: 56px;
            height: 56px;
            background: var(--light-secondary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.25rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .contact-item:hover .contact-icon {
            background: var(--primary);
            color: var(--white);
            transform: scale(1.1);
        }

        .contact-text h4 {
            font-size: 1.1rem;
            margin-bottom: 6px;
            color: var(--dark);
        }

        .contact-text p {
            color: var(--gray-600);
            margin: 0;
        }

        .contact-form {
            background: var(--light);
            padding: 48px;
            border-radius: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        .form-success {
            background: #d4edda;
            color: #155724;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: none;
        }

        .map-section {
            padding: 0 0 80px 0;
        }

        .map-container {
            border-radius: 16px;
            overflow: hidden;
            height: 400px;
            background: var(--gray-200);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .contact-form {
                padding: 32px 24px;
            }

            .form-row {
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
                    <li><a href="about.php" class="nav-link">Tentang</a></li>
                    <li><a href="gallery.php" class="nav-link">Galeri</a></li>
                    <li><a href="contact.php" class="nav-link active">Kontak</a></li>
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
            <h1 class="page-title">Hubungi Kami</h1>
            <div class="page-breadcrumb">
                <a href="../index.php">Beranda</a>
                <span>/</span>
                <span>Kontak</span>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Informasi Kontak</h3>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Alamat</h4>
                            <p><?= $settings['address'] ?></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Telepon / WhatsApp</h4>
                            <p><?= $settings['phone'] ?></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p><?= $settings['email'] ?></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <h4>Jam Buka</h4>
                            <p><?= $settings['opening_hours'] ?> (Setiap Hari)</p>
                        </div>
                    </div>

                    <div style="margin-top: 30px;">
                        <h4 style="margin-bottom: 16px;">Follow Kami</h4>
                        <div class="social-links">
                            <a href="#" class="social-link" style="background: var(--primary);">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" style="background: var(--primary);">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" style="background: var(--primary);">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" style="background: var(--primary);">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <form class="contact-form" id="contactForm">
                        <div id="formSuccess" class="form-success">
                            Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="nama" name="nama" required placeholder="Masukkan nama Anda">
                            </div>

                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="email@anda.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subjek <span class="required">*</span></label>
                            <input type="text" id="subject" name="subject" required
                                placeholder="Contoh: Pertanyaan sewa tenda">
                        </div>

                        <div class="form-group">
                            <label for="jenis_pemesanan">Jenis Pemesanan</label>
                            <select id="jenis_pemesanan" name="jenis_pemesanan">
                                <option value="">-- Pilih --</option>
                                <option value="sewa_alat">Sewa Alat Outdoor</option>
                                <option value="open_trip">Open Trip</option>
                                <option value="private_trip">Private Trip</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pesan">Pesan <span class="required">*</span></label>
                            <textarea id="pesan" name="pesan" required
                                placeholder="Tulis pesan Anda di sini..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0!2d106.8!3d-6.2!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMDAuMCJTIDEwNsKwNDgnMDAuMCJF!5e0!3m2!1sid!2sid!4v1234567890"
                    allowfullscreen="" loading="lazy">
                </iframe>
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
        // Contact form submission
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('send-message.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('formSuccess').style.display = 'block';
                        this.reset();
                        setTimeout(() => {
                            document.getElementById('formSuccess').style.display = 'none';
                        }, 5000);
                    } else {
                        alert('Gagal mengirim pesan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        });
    </script>
</body>

</html>