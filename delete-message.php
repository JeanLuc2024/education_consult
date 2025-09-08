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
$message_id = $input['id'] ?? null;

if (!$message_id || !is_numeric($message_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check if message belongs to user
    $stmt = $pdo->prepare("SELECT id FROM admin_messages WHERE id = ? AND student_id = ?");
    $stmt->execute([$message_id, $user_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Message not found or access denied']);
        exit;
    }
    
    // Delete message
    $stmt = $pdo->prepare("DELETE FROM admin_messages WHERE id = ? AND student_id = ?");
    $stmt->execute([$message_id, $user_id]);
    
    echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
