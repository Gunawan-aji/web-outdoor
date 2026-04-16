-- =====================================================
-- Database: outdoor_adventure
-- For: Outdoor Equipment Rental & Open Trip Website
-- =====================================================

-- CREATE DATABASE IF NOT EXISTS outdoor_adventure;
-- USE outdoor_adventure;

-- =====================================================
-- Table: users (Admin & Karyawan)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir', 'pengelola') DEFAULT 'kasir',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: categories (Equipment Categories)
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: products (Equipment for Rent)
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga_sewa_harian DECIMAL(10,2) NOT NULL,
    harga_sewa_mingguan DECIMAL(10,2),
    harga_sewa_bulanan DECIMAL(10,2),
    stok INT NOT NULL DEFAULT 0,
    kategori_id INT,
    gambar VARCHAR(255),
    status ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- =====================================================
-- Table: trips (Open Trip Packages)
-- =====================================================
CREATE TABLE IF NOT EXISTS trips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_trip VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    deskripsi TEXT,
    itinerary TEXT,
    harga DECIMAL(10,2) NOT NULL,
    kapasitas INT NOT NULL,
    terisi INT DEFAULT 0,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    tingkat_kesulitan ENUM('easy', 'moderate', 'hard', 'extreme') DEFAULT 'moderate',
    lokasi VARCHAR(200) NOT NULL,
    gambar VARCHAR(255),
    include TEXT,
    exclude TEXT,
    status ENUM('open', 'full', 'cancelled', 'completed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: orders (Equipment Rental Orders)
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_order VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    total_harga DECIMAL(12,2) NOT NULL,
    tanggal_sewa DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    metode_pembayaran ENUM('tunai', 'qris', 'transfer') DEFAULT 'tunai',
    status_pembayaran ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    status_order ENUM('pending', 'confirmed', 'rented', 'returned', 'cancelled') DEFAULT 'pending',
    tipe_order ENUM('online', 'offline') DEFAULT 'offline',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================
-- Table: order_items (Rental Equipment Details)
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_sewa DECIMAL(10,2) NOT NULL,
    hari INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES products(id)
);

-- =====================================================
-- Table: trip_bookings (Open Trip Registrations)
-- =====================================================
CREATE TABLE IF NOT EXISTS trip_bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_booking VARCHAR(20) UNIQUE NOT NULL,
    trip_id INT NOT NULL,
    user_id INT,
    nama_peserta VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    jumlah_orang INT NOT NULL DEFAULT 1,
    total_harga DECIMAL(12,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'qris', 'transfer') DEFAULT 'tunai',
    status_pembayaran ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    status_booking ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================
-- Table: gallery
-- =====================================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    kategori ENUM('trip', 'camping', 'equipment', 'team') DEFAULT 'trip',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: messages
-- =====================================================
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    pesan TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Insert Default Admin (password: admin123)
-- =====================================================
INSERT INTO users (username, password, nama_lengkap, email, role, status) VALUES 
('admin', '$2y$12$0E3aQkWL4a0uC3LsWeOvbucZKn1yVNNINqvzK1K4W7Y1EOSs/J4gW', 'Administrator', 'admin@outdooradventure.com', 'admin', 'active');

-- =====================================================
-- Insert Sample Karyawan (password: kasir123)
-- =====================================================
INSERT INTO users (username, password, nama_lengkap, email, role, status) VALUES 
('kasir1', '$2y$12$0E3aQkWL4a0uC3LsWeOvbucZKn1yVNNINqvzK1K4W7Y1EOSs/J4gW', 'Budi Santoso', 'kasir@outdooradventure.com', 'kasir', 'active'),
('pengelola1', '$2y$12$0E3aQkWL4a0uC3LsWeOvbucZKn1yVNNINqvzK1K4W7Y1EOSs/J4gW', 'Siti Aminah', 'pengelola@outdooradventure.com', 'pengelola', 'active');

-- =====================================================
-- Insert Equipment Categories
-- =====================================================
INSERT INTO categories (nama_kategori, deskripsi, gambar) VALUES 
('Tenda', 'Berbagai jenis tenda untuk camping dan hiking', 'category-tent.jpg'),
('Sleeping Bag', 'Sleeping bag untuk kenyamanan tidur di outdoor', 'category-sleepingbag.jpg'),
('Carrier & Backpack', 'Tas carrier dan backpack berkualitas', 'category-carrier.jpg'),
('Cooking Set', 'Peralatan masak untuk outdoor', 'category-cooking.jpg'),
('Lighting', 'Lampu dan senter untuk outdoor', 'category-lighting.jpg'),
('Navigation', 'Kompas, GPS dan peralatan navigasi', 'category-navigation.jpg'),
('Safety & First Aid', 'Peralatan keamanan dan P3K', 'category-safety.jpg'),
('Footwear', 'Sepatu boot dan sandals untuk hiking', 'category-footwear.jpg'),
('Clothing', 'Jaket, celana dan pakaian outdoor', 'category-clothing.jpg'),
('Accessories', 'Topi, kacamata, sarung tangan dll', 'category-accessories.jpg');

-- =====================================================
-- Insert Sample Equipment
-- =====================================================
INSERT INTO products (nama_produk, deskripsi, harga_sewa_harian, harga_sewa_mingguan, harga_sewa_bulanan, stok, kategori_id, gambar, status) VALUES 
-- Tenda
('Tenda Dome 2 Orang', 'Tenda dome kapasitas 2 orang, mudah pasang, waterproof', 75000, 450000, 1500000, 10, 1, 'tent-dome-2.jpg', 'available'),
('Tenda Dome 4 Orang', 'Tenda dome kapasitas 4 orang, ruang lebih luas', 120000, 700000, 2400000, 8, 1, 'tent-dome-4.jpg', 'available'),
('Tenda Family 6 Orang', 'Tenda family ukuran besar untuk 6 orang', 200000, 1200000, 4000000, 5, 1, 'tent-family-6.jpg', 'available'),
('Flysheet / T darurat', 'Flysheet multifungsi untuk shelter darurat', 35000, 200000, 700000, 15, 1, 'tent-flysheet.jpg', 'available'),

-- Sleeping Bag
('Sleeping Bag Winter', 'Sleeping bag untuk cuaca dingin (-10°C)', 50000, 300000, 1000000, 20, 2, 'sb-winter.jpg', 'available'),
('Sleeping Bag Regular', 'Sleeping bag untuk cuaca normal (5°C)', 35000, 200000, 700000, 25, 2, 'sb-regular.jpg', 'available'),
('Sleeping Bag Compact', 'Sleeping bag ringan dan compact', 30000, 175000, 600000, 15, 2, 'sb-compact.jpg', 'available'),
('Sleeping Pad', 'Matras insulasi untuk tambahan kenyamanan', 25000, 150000, 500000, 20, 2, 'sleeping-pad.jpg', 'available'),

-- Carrier & Backpack
('Carrier 50L', 'Tas carrier kapasitas 50 liter untuk hiking', 40000, 250000, 800000, 12, 3, 'carrier-50l.jpg', 'available'),
('Carrier 65L', 'Tas carrier kapasitas 65 liter untuk trekking', 50000, 300000, 1000000, 10, 3, 'carrier-65l.jpg', 'available'),
('Daypack 25L', 'Tas kecil untuk hiking harian', 20000, 120000, 400000, 20, 3, 'daypack-25l.jpg', 'available'),
('Waterproof Drybag', 'Tas kedap air untuk melindungi barang', 15000, 80000, 280000, 30, 3, 'drybag.jpg', 'available'),

-- Cooking Set
('Kompor Portable', 'Kompor portable ringan untuk memasak', 30000, 175000, 600000, 15, 4, 'stove-portable.jpg', 'available'),
('Fuel Bottle', 'Botol bahan bakar untuk kompor', 10000, 50000, 180000, 40, 4, 'fuel-bottle.jpg', 'available'),
('Cookset 4 in 1', 'Panci dan penggorengan lengkap untuk 4 orang', 25000, 150000, 500000, 20, 4, 'cookset.jpg', 'available'),
('Water Filter', 'Filter air portable untuk air bersih', 35000, 200000, 700000, 12, 4, 'water-filter.jpg', 'available'),

-- Lighting
('Headlamp', 'Senter kepala dengan cahaya terang', 15000, 80000, 280000, 30, 5, 'headlamp.jpg', 'available'),
('Tent Light LED', 'Lampu LED untuk dalam tenda', 10000, 50000, 180000, 40, 5, 'tent-light.jpg', 'available'),
('Flashlight', 'Senter tangan kuat dengan baterai tahan lama', 10000, 50000, 180000, 25, 5, 'flashlight.jpg', 'available'),

-- Navigation
('Compass', 'Kompas profesional untuk navigasi', 10000, 50000, 180000, 30, 6, 'compass.jpg', 'available'),
('Whistle', 'Peluit darurat untuk kondisi darurat', 5000, 25000, 90000, 50, 6, 'whistle.jpg', 'available'),

-- Safety & First Aid
('First Aid Kit', 'Paket P3K lengkap untuk pertolongan pertama', 20000, 120000, 400000, 25, 7, 'firstaid-kit.jpg', 'available'),
('Trekking Poles', 'Tongkat trekking untuk kestabilan', 25000, 150000, 500000, 20, 7, 'trekking-poles.jpg', 'available'),
('Raincoat', 'Jas hujan otomatis untuk hiking', 15000, 80000, 280000, 30, 7, 'raincoat.jpg', 'available'),
('Emergency Blanket', 'Selimut darurat untuk kondisi darurat', 8000, 40000, 150000, 50, 7, 'emergency-blanket.jpg', 'available'),

-- Footwear
('Sepatu Hiking Pro', 'Sepatu hiking profesional anti slip', 50000, 300000, 1000000, 15, 8, 'shoes-hiking-pro.jpg', 'available'),
('Sepatu Hiking Basic', 'Sepatu hiking untuk pemula', 35000, 200000, 700000, 20, 8, 'shoes-hiking-basic.jpg', 'available'),
('Sandals Outdoor', 'Sandal outdoor yang nyaman', 20000, 120000, 400000, 25, 8, 'sandals-outdoor.jpg', 'available'),
('Gaiters', 'Pelindung kaki dari lumpur dan batu', 15000, 80000, 280000, 20, 8, 'gaiters.jpg', 'available'),

-- Clothing
('Jaket Windbreaker', 'Jaket windbreaker ringan', 30000, 175000, 600000, 20, 9, 'jacket-windbreaker.jpg', 'available'),
('Jaket Fleece', 'Jaket fleece hangat untuk dingin', 35000, 200000, 700000, 18, 9, 'jacket-fleece.jpg', 'available'),
('Celana Hiking', 'Celana hiking quick dry', 25000, 150000, 500000, 25, 9, 'pants-hiking.jpg', 'available'),
('Buff / Neck Gaiter', 'Buff multifungsi untuk leher dan wajah', 10000, 50000, 180000, 40, 9, 'buff.jpg', 'available'),

-- Accessories
('Tas Ransel Daypack', 'Tas ransel kecil untuk daily use', 15000, 80000, 280000, 30, 10, 'daypack-small.jpg', 'available'),
('Kacamata Sunglasses', 'Kacamata matahari UV protection', 15000, 80000, 280000, 25, 10, 'sunglasses.jpg', 'available'),
('Topi Outdoor', 'Topi dengan pelindung leher', 10000, 50000, 180000, 35, 10, 'hat-outdoor.jpg', 'available'),
('Sarung Tangan Hiking', 'Sarung tangan untuk hiking', 10000, 50000, 180000, 30, 10, 'gloves-hiking.jpg', 'available');

-- =====================================================
-- Insert Sample Open Trips
-- =====================================================
INSERT INTO trips (nama_trip, slug, deskripsi, itinerary, harga, kapasitas, terisi, tanggal_mulai, tanggal_selesai, tingkat_kesulitan, lokasi, gambar, include, exclude, status) VALUES
('Gunung Semeru Open Trip 3D2N', 'gunung-semeru-3d2n', 'Nikmati keindahan sunrise dari puncak tertinggi Jawa, Gunung Semeru dengan panduan profesional kami.', 
'Hari 1: Briefing & Basecamp Arrival
Hari 2: Basecamp - Ranu Kumbolo - Oro-oro Ombo - Puncak Mahameru
Hari 3: Puncak Mahameru - Ranu Kumbolo - Basecamp - Pulang',
1500000, 20, 12, '2025-02-15', '2025-02-17', 'hard', 'Jawa Timur, Lumajang',
'trip-semeru.jpg',
'Tent, Sleeping Bag, Porter, Meals, Guide, Transport PP, RT',
'Personal Equipment, Flight tickets, Personal expenses',
'open'),

('Gunung Arjuno Open Trip 2D1N', 'gunung-arjuno-2d1n', 'Pendakian Gunung Arjuno dengan pemandangan sunrise yang menakjubkan dari puncak tertinggi ke-2 Jawa.',
'Hari 1: Basecamp - Pos 1 - Pos 2 - Puncak
Hari 2: Puncak - Basecamp - Pulang',
850000, 25, 18, '2025-02-22', '2025-02-23', 'moderate', 'Jawa Timur, Batu',
'trip-arjuno.jpg',
'Sleeping Bag, Porter, Meals, Guide, Transport Lokal',
'Personal Equipment, Flight tickets',
'open'),

('Mount Bromo Adventure 1D', 'mount-bromo-1d', 'Petualangan eksotis ke Gunung Bromo dengan pemandangan laut pasir dan sunrise yang luar biasa.',
'04:00 - Pen Jeep ke Penanjakan
05:00 - Sunrise di Bukit Penanjakan
06:30 - Berangkat ke Laut Pasir
08:00 - Mendaki Bukit Teletubies
10:00 - Kembali ke Hotel',
350000, 50, 35, '2025-02-20', '2025-02-20', 'easy', 'Jawa Timur, Probolinggo',
'trip-bromo.jpg',
'Jeep 4WD, Sunrise Point Ticket, Breakfast, Guide',
'Personal expenses, Accommodation',
'open'),

('Kawah Ijen Blue Fire Trip', 'kawah-ijen-blue-fire', 'Saksikan fenomena api biru langka di Kawah Ijen dengan keindahan danau berwarna turquoise.',
'Hari 1: Malam - Basecamp - Puncak
02:00 - Blue Fire Exploration
04:30 - Sunrise di Puncak
07:00 - Turun ke Basecamp',
750000, 30, 22, '2025-03-01', '2025-03-01', 'moderate', 'Jawa Timur, Bondowoso',
'trip-ijen.jpg',
'Entrance Ticket, Guide, Mineral Water, Mask',
'Personal Equipment, Meals',
'open'),

('Papandayan Hill Camping 2D1N', 'papandayan-camping-2d1n', 'Camping di padang edelweis Papua ndutan dengan pemandangan gunung yang indah.',
'Hari 1: Basecamp -pos 1 - Pos 2 - Camping Ground
Hari 2: Sunrise - exploration - Turun',
600000, 30, 15, '2025-03-08', '2025-03-09', 'easy', 'Jawa Barat, Bandung',
'trip-papandayan.jpg',
'Tent, Sleeping Bag, Meals, Guide, Transport Lokal',
'Personal Equipment, Flight tickets',
'open'),

('Merbabu Summit 2D1N', 'merbabu-summit-2d1n', 'Puncak Merbabu dengan view spektakuler ke Semeru dan Merapi.',
'Hari 1: Basecamp - Pos 1 - Pos 2 - Puncak
Hari 2: Sunrise - Turun - Basecamp',
750000, 25, 10, '2025-03-15', '2025-03-16', 'moderate', 'Jawa Tengah, Boyolali',
'trip-merbabu.jpg',
'Sleeping Bag, Porter, Meals, Guide',
'Personal Equipment, Transport to basecamp',
'open'),

('Rinjani Summit 3D2N', 'rinjani-summit-3d2n', 'Gunung Rinjani - trekking ke gunung berapi aktif tertinggi ke-2 di Indonesia dengan pemandangan Danau Segara Anak.',
'Hari 1: Basecamp - Pos 2 - Lake Camp
Hari 2: Lake Camp - Puncak - Lake Camp
Hari 3: Lake Camp - Basecamp',
2200000, 15, 8, '2025-04-10', '2025-04-12', 'hard', 'Nusa Tenggara Barat, Lombok',
'trip-rinjani.jpg',
'Tent, Sleeping Bag, Porter, Meals, Guide, Entrance Fee',
'Flight tickets, Personal expenses',
'open'),

('Kelimutu Lake Trip 2D1N', 'kelimutu-2d1n', 'Kunjungi tiga danau colored di puncak Gunung Kelimutu dengan warna air yang berbeda setiap saat.',
'Hari 1: Basecamp - Puncak Kelimutu (sunset)
Hari 2: Sunrise - Lake Exploration - Turun',
550000, 20, 5, '2025-04-20', '2025-04-21', 'easy', 'Nusa Tenggara Timur, Ende',
'trip-kelimutu.jpg',
'Entrance Ticket, Meals, Guide, Transport Lokal',
'Flight tickets, Personal expenses',
'open');

-- =====================================================
-- Insert Sample Gallery
-- =====================================================
INSERT INTO gallery (judul, gambar, deskripsi, kategori) VALUES 
('Semeru Sunrise', 'gallery-semeru-1.jpg', 'Sunrise di puncak Mahameru', 'trip'),
('Bromo Adventure', 'gallery-bromo-1.jpg', 'Keindahan Bromo di pagi hari', 'trip'),
('Camping Night', 'gallery-camping-1.jpg', 'Suasana camping di malam hari', 'camping'),
('Team Hiking', 'gallery-team-1.jpg', 'Tim hiking profesional kami', 'team'),
('Equipment Display', 'gallery-gear-1.jpg', 'Koleksi peralatan outdoor', 'equipment'),
('Ijen Blue Fire', 'gallery-ijen-1.jpg', 'Blue fire phenomenon at Ijen', 'trip'),
('Rinjani Lake', 'gallery-rinjani-1.jpg', 'Danau Segara Anak Rinjani', 'trip'),
('Mountain View', 'gallery-mountain-1.jpg', 'Pemandangan gunung yang menakjubkan', 'trip'),
('Camping Friends', 'gallery-camping-2.jpg', 'Momen camping bersama teman', 'camping'),
('Summit Success', 'gallery-summit-1.jpg', 'Momen puncak pendakian', 'trip');

-- =====================================================
-- Insert Sample Messages
-- =====================================================
INSERT INTO messages (nama, email, subject, pesan, status) VALUES 
('Ahmad Fauzi', 'ahmad@example.com', 'Pertanyaan Penyewaan', 'Halo, saya ingin menyewa tenda untuk weekend ini. Apa saja persyaratannya?', 'unread'),
('Siti Rahayu', 'siti@example.com', 'Booking Open Trip', 'Mau tanya, apakah ada slot untuk Semeru bulan depan?', 'read');

