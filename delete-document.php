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
$document_id = $input['id'] ?? null;

if (!$document_id || !is_numeric($document_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid document ID']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get document info
    $stmt = $pdo->prepare("SELECT filename FROM documents WHERE id = ? AND student_id = ?");
    $stmt->execute([$document_id, $user_id]);
    $document = $stmt->fetch();
    
    if (!$document) {
        echo json_encode(['status' => 'error', 'message' => 'Document not found or access denied']);
        exit;
    }
    
    // Delete file from filesystem
    $file_path = 'uploads/documents/' . $document['filename'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ? AND student_id = ?");
    $stmt->execute([$document_id, $user_id]);
    
    echo json_encode(['status' => 'success', 'message' => 'Document deleted successfully']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
