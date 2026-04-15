<?php
require_once '../../config/functions.php';
requireLogin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    deleteUser($id);
}

redirect('index.php');

