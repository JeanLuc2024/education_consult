<?php
// Test upload functionality
session_start();
require_once 'config/database.php';

// Check if uploads directory exists
$upload_dir = 'uploads/documents/';
if (!is_dir($upload_dir)) {
    echo "Creating uploads directory...\n";
    if (mkdir($upload_dir, 0755, true)) {
        echo "Uploads directory created successfully\n";
    } else {
        echo "Failed to create uploads directory\n";
    }
} else {
    echo "Uploads directory exists\n";
}

// Check permissions
if (is_writable($upload_dir)) {
    echo "Uploads directory is writable\n";
} else {
    echo "Uploads directory is NOT writable\n";
}

// Test database connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful\n";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}

// Check if documents table exists
try {
    $stmt = $pdo->query("DESCRIBE documents");
    echo "Documents table exists\n";
} catch (Exception $e) {
    echo "Documents table does not exist: " . $e->getMessage() . "\n";
}
?>
