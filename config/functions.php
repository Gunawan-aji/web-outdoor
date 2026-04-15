<?php
/**
 * Reusable Functions
 */

require_once 'database.php';

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input
 */
function sanitize($data)
{
    global $conn;
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to URL
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

/**
 * Require login
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('../admin/login.php');
    }
}

/**
 * Require specific role
 */
function requireRole($roles)
{
    if (!isLoggedIn()) {
        redirect('../admin/login.php');
    }

    $user_role = $_SESSION['admin_role'] ?? '';
    if (!in_array($user_role, $roles)) {
        if ($user_role === 'admin') {
            redirect('../admin/index.php');
        } elseif ($user_role === 'kasir') {
            redirect('../kasir/index.php');
        } else {
            redirect('../admin/login.php');
        }
    }
}

/**
 * Get all categories
 */
function getAllCategories()
{
    global $conn;
    $result = $conn->query("SELECT * FROM categories ORDER BY nama_kategori ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get category by ID
 */
function getCategoryById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get all products
 */
function getAllProducts($limit = null)
{
    global $conn;
    $sql = "SELECT p.*, c.nama_kategori 
            FROM products p 
            LEFT JOIN categories c ON p.kategori_id = c.id 
            ORDER BY p.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get product by ID
 */
function getProductById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.nama_kategori 
                            FROM products p 
                            LEFT JOIN categories c ON p.kategori_id = c.id 
                            WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get products by category
 */
function getProductsByCategory($category_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.nama_kategori 
                            FROM products p 
                            LEFT JOIN categories c ON p.kategori_id = c.id 
                            WHERE p.kategori_id = ? AND (p.status = 'available' OR p.status = 'active')
                            ORDER BY p.created_at DESC");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get featured products
 */
function getFeaturedProducts($limit = 6)
{
    return getAllProducts($limit);
}

/**
 * Get all trips
 */
function getAllTrips($limit = null)
{
    global $conn;
    $sql = "SELECT * FROM trips ORDER BY tanggal_mulai DESC";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get trip by ID
 */
function getTripById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM trips WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Add new trip
 */
function addTrip($nama_trip, $slug, $deskripsi, $itinerary, $harga, $kapasitas, $tanggal_mulai, $tanggal_selesai, $tingkat_kesulitan, $lokasi, $gambar, $include, $exclude, $status = 'open')
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO trips (nama_trip, slug, deskripsi, itinerary, harga, kapasitas, tanggal_mulai, tanggal_selesai, tingkat_kesulitan, lokasi, gambar, include, exclude, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiissssssss", $nama_trip, $slug, $deskripsi, $itinerary, $harga, $kapasitas, $tanggal_mulai, $tanggal_selesai, $tingkat_kesulitan, $lokasi, $gambar, $include, $exclude, $status);
    return $stmt->execute();
}

/**
 * Update trip
 */
function updateTrip($id, $nama_trip, $slug, $deskripsi, $itinerary, $harga, $kapasitas, $tanggal_mulai, $tanggal_selesai, $tingkat_kesulitan, $lokasi, $gambar, $include, $exclude, $status)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE trips SET nama_trip = ?, slug = ?, deskripsi = ?, itinerary = ?, harga = ?, kapasitas = ?, tanggal_mulai = ?, tanggal_selesai = ?, tingkat_kesulitan = ?, lokasi = ?, gambar = ?, include = ?, exclude = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssiissssssssi", $nama_trip, $slug, $deskripsi, $itinerary, $harga, $kapasitas, $tanggal_mulai, $tanggal_selesai, $tingkat_kesulitan, $lokasi, $gambar, $include, $exclude, $status, $id);
    return $stmt->execute();
}

/**
 * Delete trip
 */
function deleteTrip($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// =====================================================
// TRIP BOOKING FUNCTIONS
// =====================================================

/**
 * Generate kode booking
 */
function generateKodeBooking()
{
    return 'TRIP-' . date('Ymd') . rand(100, 999);
}

/**
 * Create trip booking
 */
function createTripBooking($kode_booking, $trip_id, $user_id, $nama_peserta, $no_hp, $email, $jumlah_orang, $total_harga, $metode_pembayaran, $catatan, $status_booking = 'pending', $status_pembayaran = 'pending')
{
    global $conn;

    $stmt = $conn->prepare("INSERT INTO trip_bookings (kode_booking, trip_id, user_id, nama_peserta, no_hp, email, jumlah_orang, total_harga, metode_pembayaran, catatan, status_booking, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siisssisssss", $kode_booking, $trip_id, $user_id, $nama_peserta, $no_hp, $email, $jumlah_orang, $total_harga, $metode_pembayaran, $catatan, $status_booking, $status_pembayaran);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        // Update trip terisi count
        $conn->query("UPDATE trips SET terisi = terisi + $jumlah_orang WHERE id = $trip_id");

        return $booking_id;
    }

    return false;
}

/**
 * Get all trip bookings
 */
function getAllTripBookings($limit = null)
{
    global $conn;
    $sql = "SELECT tb.*, t.nama_trip, t.lokasi, t.tanggal_mulai, t.tanggal_selesai, u.nama_lengkap as nama_kasir
            FROM trip_bookings tb
            LEFT JOIN trips t ON tb.trip_id = t.id
            LEFT JOIN users u ON tb.user_id = u.id
            ORDER BY tb.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get trip booking by ID
 */
function getTripBookingById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT tb.*, t.nama_trip, t.lokasi, t.tanggal_mulai, t.tanggal_selesai, u.nama_lengkap as nama_kasir
                            FROM trip_bookings tb
                            LEFT JOIN trips t ON tb.trip_id = t.id
                            LEFT JOIN users u ON tb.user_id = u.id
                            WHERE tb.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get trip bookings by trip ID
 */
function getTripBookingsByTripId($trip_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM trip_bookings WHERE trip_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Update trip booking status
 */
function updateTripBookingStatus($id, $status_booking, $status_pembayaran = null)
{
    global $conn;

    if ($status_pembayaran) {
        $stmt = $conn->prepare("UPDATE trip_bookings SET status_booking = ?, status_pembayaran = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status_booking, $status_pembayaran, $id);
    } else {
        $stmt = $conn->prepare("UPDATE trip_bookings SET status_booking = ? WHERE id = ?");
        $stmt->bind_param("si", $status_booking, $id);
    }

    return $stmt->execute();
}

/**
 * Delete trip booking
 */
function deleteTripBooking($id)
{
    global $conn;

    // Get booking first to update trip terisi
    $booking = getTripBookingById($id);
    if ($booking) {
        $trip_id = $booking['trip_id'];
        $jumlah_orang = $booking['jumlah_orang'];

        $stmt = $conn->prepare("DELETE FROM trip_bookings WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Update trip terisi count
            $conn->query("UPDATE trips SET terisi = GREATEST(0, terisi - $jumlah_orang) WHERE id = $trip_id");
            return true;
        }
    }

    return false;
}

/**
 * Create rental order (for kasir POS)
 */
function createRentalOrder($kode_order, $user_id, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan, $items)
{
    global $conn;

    $stmt = $conn->prepare("INSERT INTO orders (kode_order, user_id, nama_pelanggan, no_hp, total_harga, tanggal_sewa, tanggal_kembali, metode_pembayaran, catatan, tipe_order, status_order, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'offline', 'confirmed', 'pending')");
    $stmt->bind_param("sisdsssss", $kode_order, $user_id, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // Calculate days
        $start = new DateTime($tanggal_sewa);
        $end = new DateTime($tanggal_kembali);
        $days = $end->diff($start)->days + 1;
        if ($days < 1)
            $days = 1;

        // Insert order items
        foreach ($items as $item) {
            $subtotal = $item['harga'] * $item['jumlah'] * $days;
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, produk_id, jumlah, harga_sewa, hari, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("iiiddi", $order_id, $item['produk_id'], $item['jumlah'], $item['harga'], $days, $subtotal);
            $stmt_item->execute();
        }

        return $order_id;
    }

    return false;
}

/**
 * Get all gallery images
 */
function getAllGallery()
{
    global $conn;
    $result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get all messages
 */
function getAllMessages()
{
    global $conn;
    $result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get unread messages count
 */
function getUnreadMessagesCount()
{
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM messages WHERE status = 'unread'");
    return $result->fetch_assoc()['total'];
}

/**
 * Mark message as read
 */
function markMessageAsRead($id)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Delete message
 */
function deleteMessage($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Delete product
 */
function deleteProduct($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Delete category
 */
function deleteCategory($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Upload image
 */
function uploadImage($file, $target_dir)
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        return null;
    }

    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_path = $target_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    }

    return null;
}

/**
 * Format currency
 */
function formatCurrency($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get site settings
 */
function getSettings()
{
    return [
        'site_name' => 'Outdoor Adventure',
        'tagline' => 'Jelajahi Alam, Temukan Petualangan',
        'address' => 'Jl. Puncak Jaya No. 123, Jakarta',
        'phone' => '0812-3456-7890',
        'email' => 'info@outdooradventure.com',
        'opening_hours' => '08:00 - 20:00'
    ];
}

// =====================================================
// USER/KARYAWAN FUNCTIONS
// =====================================================

/**
 * Get all users/karyawan
 */
function getAllUsers()
{
    global $conn;
    $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get user by ID
 */
function getUserById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Add new user/karyawan
 */
function addUser($username, $password, $nama_lengkap, $email, $role = 'kasir')
{
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, email, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $hashed_password, $nama_lengkap, $email, $role);
    return $stmt->execute();
}

/**
 * Update user
 */
function updateUser($id, $username, $nama_lengkap, $email, $role, $status)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET username = ?, nama_lengkap = ?, email = ?, role = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $username, $nama_lengkap, $email, $role, $status, $id);
    return $stmt->execute();
}

/**
 * Update user password
 */
function updateUserPassword($id, $new_password)
{
    global $conn;
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $id);
    return $stmt->execute();
}

/**
 * Delete user
 */
function deleteUser($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// =====================================================
// ORDER FUNCTIONS
// =====================================================

/**
 * Generate kode order
 */
function generateKodeOrder()
{
    $date = date('Ymd');
    $random = rand(100, 999);
    return 'ORD-' . $date . $random;
}

/**
 * Create new order (offline/kasir) - Updated for rental
 */
function createOrder($kode_order, $user_id, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan, $items, $tipe_order = 'offline')
{
    global $conn;

    $stmt = $conn->prepare("INSERT INTO orders (kode_order, user_id, nama_pelanggan, no_hp, total_harga, tanggal_sewa, tanggal_kembali, metode_pembayaran, catatan, tipe_order, status_order, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', 'pending')");
    $stmt->bind_param("sissssssss", $kode_order, $user_id, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan, $tipe_order);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // Calculate days
        $start = new DateTime($tanggal_sewa);
        $end = new DateTime($tanggal_kembali);
        $days = $end->diff($start)->days + 1; // Include both start and end day

        if ($days < 1)
            $days = 1;

        // Insert order items
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'] * $days;
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, produk_id, jumlah, harga_sewa, hari, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("iiiddi", $order_id, $item['productId'], $item['quantity'], $item['price'], $days, $subtotal);
            $stmt_item->execute();
        }

        return $order_id;
    }

    return false;
}

/**
 * Create online order (from website)
 */
function createOnlineOrder($kode_order, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan, $items)
{
    global $conn;

    $user_id = null;
    $tipe_order = 'online';

    $stmt = $conn->prepare("INSERT INTO orders (kode_order, user_id, nama_pelanggan, no_hp, total_harga, tanggal_sewa, tanggal_kembali, metode_pembayaran, catatan, tipe_order, status_order, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')");
    $stmt->bind_param("sissssssss", $kode_order, $user_id, $nama_pelanggan, $no_hp, $total_harga, $tanggal_sewa, $tanggal_kembali, $metode_pembayaran, $catatan, $tipe_order);

    if ($stmt->execute()) {
        $order_id = $conn->insert_id;

        // Calculate days
        $start = new DateTime($tanggal_sewa);
        $end = new DateTime($tanggal_kembali);
        $days = $end->diff($start)->days + 1;

        if ($days < 1)
            $days = 1;

        // Insert order items
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'] * $days;
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, produk_id, jumlah, harga_sewa, hari, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_item->bind_param("iiiddi", $order_id, $item['productId'], $item['quantity'], $item['price'], $days, $subtotal);
            $stmt_item->execute();
        }

        return $order_id;
    }

    return false;
}

/**
 * Get all orders
 */
function getAllOrders($limit = null)
{
    global $conn;
    $sql = "SELECT o.*, u.nama_lengkap as nama_kasir 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get order by ID
 */
function getOrderById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT o.*, u.nama_lengkap as nama_kasir 
                            FROM orders o 
                            LEFT JOIN users u ON o.user_id = u.id 
                            WHERE o.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get order items
 */
function getOrderItems($order_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT oi.*, p.nama_produk, p.gambar 
                            FROM order_items oi 
                            LEFT JOIN products p ON oi.produk_id = p.id 
                            WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Update order status
 */
function updateOrderStatus($id, $status)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE orders SET status_order = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

/**
 * Delete order
 */
function deleteOrder($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Get orders by date range
 */
function getOrdersByDateRange($start_date, $end_date)
{
    global $conn;
    $stmt = $conn->prepare("SELECT o.*, u.nama_lengkap as nama_kasir 
                            FROM orders o 
                            LEFT JOIN users u ON o.user_id = u.id 
                            WHERE DATE(o.created_at) BETWEEN ? AND ? 
                            ORDER BY o.created_at DESC");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// =====================================================
// REPORT FUNCTIONS
// =====================================================

/**
 * Get daily report
 */
function getDailyReport($date = null)
{
    $date = $date ?? date('Y-m-d');
    return getOrdersByDateRange($date, $date);
}

/**
 * Get weekly report
 */
function getWeeklyReport()
{
    $start_date = date('Y-m-d', strtotime('-7 days'));
    $end_date = date('Y-m-d');
    return getOrdersByDateRange($start_date, $end_date);
}

/**
 * Get monthly report
 */
function getMonthlyReport($year = null, $month = null)
{
    $year = $year ?? date('Y');
    $month = $month ?? date('m');
    $start_date = "$year-$month-01";
    $end_date = date('Y-m-t', strtotime($start_date));
    return getOrdersByDateRange($start_date, $end_date);
}

/**
 * Get yearly report
 */
function getYearlyReport($year = null)
{
    $year = $year ?? date('Y');
    $start_date = "$year-01-01";
    $end_date = "$year-12-31";
    return getOrdersByDateRange($start_date, $end_date);
}

/**
 * Get report summary
 */
function getReportSummary($start_date, $end_date)
{
    global $conn;
    $stmt = $conn->prepare("SELECT 
                                COUNT(*) as total_order,
                                SUM(total_harga) as total_pendapatan,
                                AVG(total_harga) as rata_rata,
                                SUM(CASE WHEN metode_pembayaran = 'tunai' THEN 1 ELSE 0 END) as jumlah_tunai,
                                SUM(CASE WHEN metode_pembayaran = 'qris' THEN 1 ELSE 0 END) as jumlah_qris,
                                SUM(CASE WHEN metode_pembayaran = 'transfer' THEN 1 ELSE 0 END) as jumlah_transfer
                            FROM orders 
                            WHERE DATE(created_at) BETWEEN ? AND ? AND status_order = 'selesai'");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Get daily sales data for chart (last 7 days)
 */
function getDailySalesData()
{
    global $conn;
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stmt = $conn->prepare("SELECT SUM(total_harga) as total FROM orders WHERE DATE(created_at) = ? AND status_order = 'selesai'");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $data[] = [
            'tanggal' => $date,
            'total' => $result['total'] ?? 0
        ];
    }
    return $data;
}

// =====================================================
// EXPORT EXCEL FUNCTION
// =====================================================

/**
 * Export data to Excel (CSV format)
 */
function exportToExcel($filename, $headers, $data)
{
    // Set headers for download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Open output
    $output = fopen('php://output', 'w');

    // Write BOM for UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Write headers
    fputcsv($output, $headers, ';', '"');

    // Write data
    foreach ($data as $row) {
        fputcsv($output, $row, ';', '"');
    }

    fclose($output);
    exit;
}

/**
 * Export orders to Excel
 */
function exportOrdersToExcel($orders, $title = 'Laporan Pesanan')
{
    $headers = ['No', 'Kode Order', 'Pelanggan', 'Kasir', 'Total', 'Metode Pembayaran', 'Status', 'Tanggal'];
    $data = [];

    foreach ($orders as $index => $order) {
        $data[] = [
            $index + 1,
            $order['kode_order'],
            $order['nama_pelanggan'],
            $order['nama_kasir'],
            $order['total_harga'],
            $order['metode_pembayaran'],
            $order['status_order'],
            date('d-m-Y H:i', strtotime($order['created_at']))
        ];
    }

    $filename = $title . '_' . date('YmdHis') . '.csv';
    exportToExcel($filename, $headers, $data);
}

/**
 * Export products to Excel
 */
function exportProductsToExcel($products, $title = 'Laporan Produk')
{
    $headers = ['No', 'Nama Produk', 'Kategori', 'Harga', 'Status', 'Tanggal Dibuat'];
    $data = [];

    foreach ($products as $index => $product) {
        $data[] = [
            $index + 1,
            $product['nama_produk'],
            $product['nama_kategori'],
            $product['harga'],
            $product['status'],
            date('d-m-Y', strtotime($product['created_at']))
        ];
    }

    $filename = $title . '_' . date('YmdHis') . '.csv';
    exportToExcel($filename, $headers, $data);
}

/**
 * Logout function
 */
function logout()
{
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear all session variables
    $_SESSION = array();

    // Destroy session
    session_destroy();

    // Redirect to login
    redirect('../admin/login.php');
}
