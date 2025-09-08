<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
require_once 'config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
    $destinations = $stmt->fetchAll();
    
    // Parse features from JSON for each destination and clean up
    foreach ($destinations as &$destination) {
        $features = json_decode($destination['features'], true);
        // Clean up any carriage returns or newlines in features
        if (is_array($features)) {
            $features = array_map(function($feature) {
                return trim(str_replace(["\r", "\n"], '', $feature));
            }, $features);
            // Remove empty features
            $features = array_filter($features, function($feature) {
                return !empty(trim($feature));
            });
        }
        $destination['features'] = $features;
    }
    
    echo json_encode([
        'success' => true,
        'destinations' => $destinations
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch destinations: ' . $e->getMessage()
    ]);
}
?>
