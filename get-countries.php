<?php
header('Content-Type: application/json');
require_once 'config/database.php';

try {
    // First check if destinations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'destinations'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        // Create destinations table if it doesn't exist
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
        
        // Insert sample destinations
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
    }
    
    // Get countries from destinations table
    $stmt = $pdo->query("
        SELECT DISTINCT country, slug 
        FROM destinations 
        WHERE is_active = 1 
        ORDER BY country ASC
    ");
    $destinations = $stmt->fetchAll();
    
    // If no destinations exist, return empty array
    $countries = $destinations ?: [];
    
    echo json_encode([
        'success' => true,
        'countries' => $countries
    ]);
    
} catch (Exception $e) {
    // Return default countries if database error
    $defaultCountries = [
        ['country' => 'Canada', 'slug' => 'canada'],
        ['country' => 'United Kingdom', 'slug' => 'uk'],
        ['country' => 'United States', 'slug' => 'usa'],
        ['country' => 'Australia', 'slug' => 'australia'],
        ['country' => 'Germany', 'slug' => 'germany'],
        ['country' => 'Malaysia', 'slug' => 'malaysia'],
        ['country' => 'Turkey', 'slug' => 'turkey']
    ];
    
    echo json_encode([
        'success' => true,
        'countries' => $defaultCountries,
        'error' => 'Using default countries due to database error: ' . $e->getMessage()
    ]);
}
?>
