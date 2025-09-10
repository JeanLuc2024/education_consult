<?php
header('Content-Type: application/json');
require_once 'config/database.php';

try {
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
        'countries' => $defaultCountries
    ]);
}
?>
