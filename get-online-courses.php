<?php
header('Content-Type: application/json');
require_once 'config/database.php';

try {
    $stmt = $pdo->query("
        SELECT * FROM online_courses 
        WHERE is_active = 1 
        ORDER BY course_name ASC
    ");
    $courses = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching online courses: ' . $e->getMessage()
    ]);
}
?>
