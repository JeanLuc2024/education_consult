<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$application_id = $input['id'] ?? null;

if (!$application_id || !is_numeric($application_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid application ID']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check if application belongs to user
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND student_id = ?");
    $stmt->execute([$application_id, $user_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Application not found or access denied']);
        exit;
    }
    
    // Delete application
    $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ? AND student_id = ?");
    $stmt->execute([$application_id, $user_id]);
    
    echo json_encode(['status' => 'success', 'message' => 'Application deleted successfully']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
