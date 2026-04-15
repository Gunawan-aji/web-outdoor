<?php
require_once '../../config/functions.php';
requireLogin();

// Handle delete
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if (deleteTrip($id)) {
        echo "<script>alert('Trip berhasil dihapus'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus trip'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid'); window.location.href='index.php';</script>";
}

