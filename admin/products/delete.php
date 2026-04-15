<?php
require_once '../../config/functions.php';
requireLogin();

// Handle delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $delete_id = (int) $_GET['id'];

    if (deleteProduct($delete_id)) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus produk!'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('ID produk tidak valid!'); window.location.href='index.php';</script>";
}

