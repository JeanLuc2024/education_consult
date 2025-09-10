<?php
header('Content-Type: application/json');
require_once 'config/database.php';

try {
    // Get destinations from database
    $stmt = $pdo->query("
        SELECT name, slug, country 
        FROM destinations 
        WHERE is_active = 1 
        ORDER BY country ASC
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
        'destinations' => $defaultDestinations
    ]);
}
?>