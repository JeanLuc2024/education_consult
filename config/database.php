<?php
// Database configuration for Modern Education Consult Ltd

define('DB_HOST', 'localhost');
define('DB_NAME', 'modern_education_consult');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO connection options
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Create PDO connection
try {
    // First try to connect without database name to create it if needed
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
} catch (PDOException $e) {
    // If connection fails, try without database name
    try {
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdo_options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $pdo->exec("USE " . DB_NAME);
    } catch (PDOException $e2) {
        die("Database connection failed. Please check your MySQL server and credentials. Error: " . $e2->getMessage());
    }
}

// Helper function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
?>
