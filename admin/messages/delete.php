<?php
require_once '../../config/functions.php';
requireLogin();

// Handle delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $delete_id = (int) $_GET['id'];

    if (deleteMessage($delete_id)) {
        echo "<script>alert('Pesan berhasil dihapus!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pesan!'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('ID pesan tidak valid!'); window.location.href='index.php';</script>";
}

