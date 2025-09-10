<?php
// Update database to add email_logs table
require_once 'config/database.php';

echo "<h2>Updating Database for Email Logs</h2>";

try {
    // Create email_logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS email_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            to_email VARCHAR(255) NOT NULL,
            subject VARCHAR(500) NOT NULL,
            message TEXT NOT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✅ Email logs table created successfully</p>";
    
    // Update destinations table to ensure it has the right structure
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS destinations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            country VARCHAR(100) NOT NULL,
            description TEXT,
            image VARCHAR(500),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✅ Destinations table structure verified</p>";
    
    // Insert sample destinations if none exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM destinations");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO destinations (name, slug, country, description, is_active) VALUES
            ('Canada', 'canada', 'Canada', 'Study in Canada with world-class universities and excellent quality of life', 1),
            ('United Kingdom', 'uk', 'United Kingdom', 'Experience British education excellence and rich cultural heritage', 1),
            ('United States', 'usa', 'United States', 'Pursue your dreams at top American universities and colleges', 1),
            ('Australia', 'australia', 'Australia', 'Study in Australia with its diverse culture and high-quality education', 1),
            ('Germany', 'germany', 'Germany', 'Access free or low-cost education in Germany with excellent programs', 1),
            ('Malaysia', 'malaysia', 'Malaysia', 'Affordable quality education in Malaysia with multicultural environment', 1),
            ('Turkey', 'turkey', 'Turkey', 'Study in Turkey with world-class universities and affordable education', 1)
        ");
        echo "<p>✅ Sample destinations inserted</p>";
    } else {
        echo "<p>✅ Destinations already exist ($count found)</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>✅ Database Update Complete!</h3>";
    echo "<p>All required tables and data are now in place.</p>";
    echo "<p><a href='test-simple-email.php'>Test Email Functionality</a> | <a href='index.html'>View Website</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2, h3 { color: #2d465e; }
</style>
