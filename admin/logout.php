<?php
require_once '../config/functions.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy session
$_SESSION = array();
session_destroy();

// Redirect to login
redirect('login.php');

