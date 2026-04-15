<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$settings = getSettings();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$nama_pelanggan = sanitize($input['nama_pelanggan'] ?? '');
$no_hp = sanitize($input['no_hp'] ?? '');
$tanggal_sewa = sanitize($input['tanggal_sewa'] ?? '');
$tanggal_kembali = sanitize($input['tanggal_kembali'] ?? '');
$metode_pembayaran = sanitize($input['metode_pembayaran'] ?? 'tunai');
$catatan = sanitize($input['catatan'] ?? '');
$cart_items = $input['cart_items'] ?? [];

// Validation
if (empty($nama_pelanggan) || empty($no_hp) || empty($tanggal_sewa) || empty($tanggal_kembali) || empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Mohon lengkapi semua data']);
    exit;
}

// Calculate total with days
$start = new DateTime($tanggal_sewa);
$end = new DateTime($tanggal_kembali);
$days = $end->diff($start)->days + 1;
if ($days < 1)
    $days = 1;

$total_harga = 0;
foreach ($cart_items as $item) {
    $total_harga += $item['price'] * $item['quantity'] * $days;
}

// Generate kode order
$kode_order = generateKodeOrder();

// Create order
$user_id = $_SESSION['admin_id'] ?? null;
$order_id = createOrder(
    $kode_order,
    $user_id,
    $nama_pelanggan,
    $no_hp,
    $total_harga,
    $tanggal_sewa,
    $tanggal_kembali,
    $metode_pembayaran,
    $catatan,
    $cart_items
);

if ($order_id) {
    echo json_encode([
        'success' => true,
        'message' => 'Pesanan berhasil dibuat',
        'kode_order' => $kode_order,
        'order_id' => $order_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal membuat pesanan']);
}

