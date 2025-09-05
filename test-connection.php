<?php
// Test database connection
require_once 'config/database.php';

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = getDB();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>✓ Database query successful! Found {$result['count']} users in database.</p>";
    
    // Test services table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
    $result = $stmt->fetch();
    echo "<p>✓ Services table found! {$result['count']} services available.</p>";
    
    echo "<h3>Database Status: ✅ Working</h3>";
    echo "<p><a href='index.html'>← Back to Homepage</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please run <a href='setup-database.php'>setup-database.php</a> to create the database.</p>";
}
?>
