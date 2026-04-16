<?php
/**
 * Database Connection Configuration
 * For Outdoor Adventure Website
 */

$host = 'a7ohtf5qe2v993jo93k3htjb';
$user = 'root';
$password = 'gunawan471';
$database = 'outdoordb';

try {
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

// Define base URL
define('BASE_URL', 'http://outdoor.gnwn.web.id');
define('ADMIN_URL', BASE_URL . '/admin');
define('ASSETS_URL', BASE_URL . '/assets');

