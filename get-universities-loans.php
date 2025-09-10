<?php
header('Content-Type: application/json');
require_once 'config/database.php';

try {
    $stmt = $pdo->query("
        SELECT * FROM universities_with_loans 
        WHERE is_active = 1 
        ORDER BY university_name ASC
    ");
    $universities = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'universities' => $universities
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching universities: ' . $e->getMessage()
    ]);
}
?>
