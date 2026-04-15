<?php
require_once '../config/functions.php';

header('Content-Type: application/json');

$settings = getSettings();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_id = (int) ($_POST['trip_id'] ?? 0);
    $nama_peserta = sanitize($_POST['nama_peserta'] ?? '');
    $no_hp = sanitize($_POST['no_hp'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $jumlah_orang = (int) ($_POST['jumlah_orang'] ?? 1);
    $catatan = sanitize($_POST['catatan'] ?? '');
    $metode_pembayaran = sanitize($_POST['metode_pembayaran'] ?? 'transfer');

    // Validation
    if (empty($trip_id) || empty($nama_peserta) || empty($no_hp) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Mohon lengkapi semua data']);
        exit;
    }

    // Get trip details
    $trip = getTripById($trip_id);

    if (!$trip) {
        echo json_encode(['success' => false, 'message' => 'Trip tidak ditemukan']);
        exit;
    }

    // Check availability
    $available = $trip['kapasitas'] - $trip['terisi'];
    if ($jumlah_orang > $available) {
        echo json_encode(['success' => false, 'message' => 'Maaf, slot yang tersedia tidak cukup']);
        exit;
    }

    // Calculate total price
    $total_harga = $trip['harga'] * $jumlah_orang;

    // Generate kode booking
    $kode_booking = generateKodeBooking();

    // For online bookings, user_id is null
    $user_id = null;

    // Create booking using centralized function
    $booking_id = createTripBooking(
        $kode_booking,
        $trip_id,
        $user_id,
        $nama_peserta,
        $no_hp,
        $email,
        $jumlah_orang,
        $total_harga,
        $metode_pembayaran,
        $catatan,
        'pending',  // status_booking
        'pending'   // status_pembayaran
    );

    if ($booking_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Booking berhasil dibuat',
            'kode_booking' => $kode_booking,
            'booking_id' => $booking_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat booking']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

