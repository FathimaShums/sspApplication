<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'FathimaShums');
define('DB_PASS', 'Pattyboi1');
define('DB_NAME', 'restaurantSystem');
define('DB_PORT', '3308');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>