<?php
/**
 * Send Message Handler (AJAX)
 */

require_once '../config/functions.php';

// Set header for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit;
}

// Check if form fields are present
if (!isset($_POST['nama']) || !isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['pesan'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

global $conn;

$nama = sanitize($_POST['nama']);
$email = sanitize($_POST['email']);
$subject = sanitize($_POST['subject']);
$pesan = sanitize($_POST['pesan']);

// Validate
if (empty($nama) || empty($email) || empty($subject) || empty($pesan)) {
    echo json_encode([
        'success' => false,
        'message' => 'Semua field wajib diisi!'
    ]);
    exit;
}

// Check if table exists
$table_check = $conn->query("SHOW TABLES LIKE 'messages'");
if ($table_check->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Tabel pesan belum ada. Silakan hubungi administrator.'
    ]);
    exit;
}

// Insert message
$stmt = $conn->prepare("INSERT INTO messages (nama, email, subject, pesan) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $email, $subject, $pesan);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Pesan berhasil dikirim!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengirim pesan: ' . $conn->error
    ]);
}

