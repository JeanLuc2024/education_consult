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
    
    // Add default countries if no destinations exist
    $defaultCountries = [
        ['country' => 'Canada', 'slug' => 'canada'],
        ['country' => 'United Kingdom', 'slug' => 'uk'],
        ['country' => 'United States', 'slug' => 'usa'],
        ['country' => 'Australia', 'slug' => 'australia'],
        ['country' => 'Germany', 'slug' => 'germany'],
        ['country' => 'Malaysia', 'slug' => 'malaysia'],
        ['country' => 'Turkey', 'slug' => 'turkey']
    ];
    
    $countries = !empty($destinations) ? $destinations : $defaultCountries;
    
    echo json_encode([
        'success' => true,
        'countries' => $countries
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching countries: ' . $e->getMessage()
    ]);
}
?>
