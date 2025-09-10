<?php
// Check destinations table structure
require_once 'config/database.php';

echo "<h2>Checking Destinations Table Structure</h2>";

try {
    // Check if destinations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'destinations'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p>❌ Destinations table does not exist</p>";
        
        // Create destinations table
        $pdo->exec("
            CREATE TABLE destinations (
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
        echo "<p>✅ Destinations table created</p>";
        
        // Insert sample data
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
        echo "<p>✅ Destinations table exists</p>";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE destinations");
        $columns = $stmt->fetchAll();
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show sample data
        $stmt = $pdo->query("SELECT * FROM destinations LIMIT 5");
        $destinations = $stmt->fetchAll();
        
        echo "<h3>Sample Data:</h3>";
        if ($destinations) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Country</th><th>Active</th></tr>";
            foreach ($destinations as $dest) {
                echo "<tr>";
                echo "<td>" . $dest['id'] . "</td>";
                echo "<td>" . $dest['name'] . "</td>";
                echo "<td>" . $dest['slug'] . "</td>";
                echo "<td>" . $dest['country'] . "</td>";
                echo "<td>" . ($dest['is_active'] ? 'Yes' : 'No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data found in destinations table</p>";
        }
    }
    
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
table { margin: 20px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f8f9fa; }
</style>
