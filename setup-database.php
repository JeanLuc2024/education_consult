<?php
// Database setup script for Modern Education Consult Ltd
// Run this file once to create the database and tables

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'modern_education_consult';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup for Modern Education Consult Ltd</h2>";
    echo "<p>Setting up database and tables...</p>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database '$database' created successfully</p>";
    
    // Use the database
    $pdo->exec("USE `$database`");
    
    // Read and execute schema.sql
    $schema = file_get_contents('database/schema.sql');
    if ($schema) {
        // Split by semicolon and execute each statement
        $statements = explode(';', $schema);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        echo "<p>✓ Database tables created successfully</p>";
    } else {
        echo "<p>⚠ Warning: Could not read schema.sql file</p>";
    }
    
    // Read and execute seed.sql
    $seed = file_get_contents('database/seed.sql');
    if ($seed) {
        // Split by semicolon and execute each statement
        $statements = explode(';', $seed);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        echo "<p>✓ Sample data inserted successfully</p>";
    } else {
        echo "<p>⚠ Warning: Could not read seed.sql file</p>";
    }
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Your database is now ready. You can:</p>";
    echo "<ul>";
    echo "<li>Test the contact form</li>";
    echo "<li>Access the student portal (admin@moderneducationconsult.com / admin123)</li>";
    echo "<li>View the website at <a href='index.html'>index.html</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Default Login Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: admin@moderneducationconsult.com / admin123</li>";
    echo "<li>Student: student@example.com / admin123</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h2>Database Setup Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your MySQL server is running and credentials are correct.</p>";
}
?>
