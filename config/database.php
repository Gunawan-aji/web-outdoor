<?php
/**
 * Database Connection Configuration
 * For Outdoor Adventure Website
 */

$host = 'sjwlmxs0zym63146l8zhvuif';
$user = 'root';
$password = 'gunawan471';
$database = 'outdoor-db';

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
define('BASE_URL', 'http://localhost/proyek-rekayasa-web');
define('ADMIN_URL', BASE_URL . '/admin');
define('ASSETS_URL', BASE_URL . '/assets');

