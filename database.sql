-- =====================================================
-- Database: kopi_kenangan
-- For: Coffee Shop Website
-- =====================================================

CREATE DATABASE IF NOT EXISTS kopi_kenangan;
USE kopi_kenangan;

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
-- Table: categories
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: products
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    kategori_id INT,
    gambar VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- =====================================================
-- Table: orders (Pemesanan)
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_order VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    nama_pelanggan VARCHAR(100),
    nomor_meja VARCHAR(20),
    total_harga DECIMAL(12,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'qris', 'transfer') DEFAULT 'tunai',
    catatan TEXT,
    tipe_order ENUM('online', 'offline') DEFAULT 'offline',
    status_order ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================
-- Table: order_items (Detail Pemesanan)
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_saat_pesan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES products(id)
);

-- =====================================================
-- Table: gallery
-- =====================================================
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    deskripsi TEXT,
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
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@kopikenangan.com', 'admin');

-- =====================================================
-- Insert Sample Karyawan (password: karyawan123)
-- =====================================================
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES 
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'kasir@kopikenangan.com', 'kasir'),
('pengelola1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Aminah', 'pengelola@kopikenangan.com', 'pengelola');

-- =====================================================
-- Insert Sample Categories
-- =====================================================
INSERT INTO categories (nama_kategori, deskripsi, gambar) VALUES 
('Kopi Hitam', 'Kopi murni tanpa gula dan susu', 'category-coffee-black.jpg'),
('Kopi Susu', 'Kopi dengan campuran susu segar', 'category-coffee-milk.jpg'),
('Espresso', 'Kopi pekat dengan crema', 'category-espresso.jpg'),
('Non-Kopi', 'Minuman tanpa kopi', 'category-non-coffee.jpg'),
('Makanan', 'Snack dan makanan ringan', 'category-food.jpg');

-- =====================================================
-- Insert Sample Products
-- =====================================================
INSERT INTO products (nama_produk, deskripsi, harga, kategori_id, gambar, status) VALUES 
('Americano', 'Kopi espresso dengan air panas, rasa lebih ringan', 25000, 3, 'productamericano.jpg', 'active'),
('Latte', 'Kopi espresso dengan susu steamed, rasa creamy', 30000, 2, 'productlatte.jpg', 'active'),
('Cappuccino', 'Kopi dengan susu foam, perbandingan 1:1:1', 28000, 2, 'productcappuccino.jpg', 'active'),
('Mocha', 'Kopi dengan cokelat dan susu', 32000, 2, 'productmocha.jpg', 'active'),
('Kopi Hitam', 'Kopi murni tanpa gula, bold and rich', 20000, 1, 'productblack-coffee.jpg', 'active'),
('Kopi Susu Gula Aren', 'Kopi dengan susu dan gula aren khas', 25000, 2, 'productkopiaren.jpg', 'active'),
('Cold Brew', 'Kopi seduh dingin 12 jam, smooth', 28000, 1, 'productcoldbrew.jpg', 'active'),
('Matcha Latte', 'Teh hijau Jepang dengan susu', 30000, 4, 'productmatcha.jpg', 'active'),
('Chocolate Ice', 'Minuman cokelat dingin dengan cream', 25000, 4, 'productchocolate.jpg', 'active'),
('Croissant', 'Roti pastry khas Prancis', 18000, 5, 'productcroissant.jpg', 'active'),
('Churros', 'Roti goreng dengan taburan gula', 15000, 5, 'productchurros.jpg', 'active'),
('Cheesecake', 'Kue cheesecake lembut', 22000, 5, 'productcheesecake.jpg', 'active');

-- =====================================================
-- Insert Sample Gallery
-- =====================================================
INSERT INTO gallery (judul, gambar, deskripsi) VALUES 
('Interior Cozy', 'gallery-1.jpg', 'Suasana nyaman dan cozy'),
('Barista Professional', 'gallery-2.jpg', 'Tim barista profesional'),
('Outdoor Seating', 'gallery-3.jpg', 'Tempat duduk outdoor'),
('Coffee Beans', 'gallery-4.jpg', 'Biji kopi berkualitas tinggi'),
('Signature Drinks', 'gallery-5.jpg', 'Minuman khas kami'),
('Sweet Treats', 'gallery-6.jpg', 'Pemberian manis');

-- =====================================================
-- Insert Sample Messages
-- =====================================================
INSERT INTO messages (nama, email, subject, pesan, status) VALUES 
('Ahmad Fauzi', 'ahmad@example.com', 'Pertanyaan Franchise', 'Halo, saya tertarik untuk membuka franchise. Apa persyaratannya?', 'unread'),
('Siti Rahayu', 'siti@example.com', 'Pemesanan Grosir', 'Mau tanya, apakah ada diskon untuk pemesanan grosir?', 'read');

-- =====================================================
-- Insert Sample Orders (untuk testing laporan)
-- =====================================================
INSERT INTO orders (kode_order, user_id, nama_pelanggan, total_harga, metode_pembayaran, status_order, created_at) VALUES 
('ORD-001', 2, 'Pelanggan 1', 55000, 'tunai', 'selesai', NOW() - INTERVAL 1 DAY),
('ORD-002', 2, 'Pelanggan 2', 80000, 'qris', 'selesai', NOW() - INTERVAL 2 DAY),
('ORD-003', 2, 'Pelanggan 3', 45000, 'tunai', 'selesai', NOW() - INTERVAL 5 DAY),
('ORD-004', 2, 'Pelanggan 4', 120000, 'transfer', 'selesai', NOW() - INTERVAL 10 DAY),
('ORD-005', 2, 'Pelanggan 5', 30000, 'tunai', 'selesai', NOW() - INTERVAL 15 DAY),
('ORD-006', 2, 'Pelanggan 6', 75000, 'qris', 'diproses', NOW() - INTERVAL 1 HOUR),
('ORD-007', 3, 'Pelanggan 7', 60000, 'tunai', 'selesai', NOW() - INTERVAL 3 DAY);

-- =====================================================
-- Insert Sample Order Items
-- =====================================================
INSERT INTO order_items (order_id, produk_id, jumlah, harga_saat_pesan, subtotal) VALUES 
(1, 1, 2, 25000, 50000),
(1, 9, 1, 25000, 25000),
(2, 2, 2, 30000, 60000),
(2, 10, 1, 18000, 18000),
(3, 5, 1, 20000, 20000),
(3, 6, 1, 25000, 25000),
(4, 3, 2, 28000, 56000),
(4, 4, 2, 32000, 64000),
(5, 7, 1, 28000, 28000),
(5, 11, 1, 15000, 15000),
(6, 2, 1, 30000, 30000),
(6, 8, 1, 30000, 30000),
(6, 12, 1, 22000, 22000),
(7, 1, 1, 25000, 25000),
(7, 9, 1, 25000, 25000),
(7, 10, 1, 18000, 18000);

