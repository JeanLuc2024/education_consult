<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid application ID']);
    exit;
}

$application_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Get application details
    $stmt = $pdo->prepare("
        SELECT * FROM applications 
        WHERE id = ? AND student_id = ?
    ");
    $stmt->execute([$application_id, $user_id]);
    $application = $stmt->fetch();
    
    if (!$application) {
        echo json_encode(['status' => 'error', 'message' => 'Application not found']);
        exit;
    }
    
    echo json_encode(['status' => 'success', 'application' => $application]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
