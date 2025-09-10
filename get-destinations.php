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
    
    // Get destinations from database
    $stmt = $pdo->query("
        SELECT name, slug, country 
        FROM destinations 
        WHERE is_active = 1 
        ORDER BY name ASC
    ");
    $destinations = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'destinations' => $destinations
    ]);
    
} catch (Exception $e) {
    // Return default destinations if database error
    $defaultDestinations = [
        ['name' => 'Canada', 'slug' => 'canada', 'country' => 'Canada'],
        ['name' => 'United Kingdom', 'slug' => 'uk', 'country' => 'United Kingdom'],
        ['name' => 'United States', 'slug' => 'usa', 'country' => 'United States'],
        ['name' => 'Australia', 'slug' => 'australia', 'country' => 'Australia'],
        ['name' => 'Germany', 'slug' => 'germany', 'country' => 'Germany'],
        ['name' => 'Malaysia', 'slug' => 'malaysia', 'country' => 'Malaysia'],
        ['name' => 'Turkey', 'slug' => 'turkey', 'country' => 'Turkey']
    ];
    
    echo json_encode([
        'success' => true,
        'destinations' => $defaultDestinations,
        'error' => 'Using default destinations due to database error: ' . $e->getMessage()
    ]);
}
?>