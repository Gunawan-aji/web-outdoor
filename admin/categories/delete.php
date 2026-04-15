<?php
require_once '../../config/functions.php';
requireLogin();

// Handle delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $delete_id = (int) $_GET['id'];

    if (deleteCategory($delete_id)) {
        echo "<script>alert('Kategori berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus kategori!'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('ID kategori tidak valid!'); window.location.href='index.php';</script>";
}

