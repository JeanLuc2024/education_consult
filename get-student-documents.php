<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid student ID']);
    exit;
}

$student_id = $_GET['student_id'];

try {
    // Get student name
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ? AND user_type = 'student'");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        exit;
    }
    
    // Get student documents
    $stmt = $pdo->prepare("
        SELECT * FROM documents 
        WHERE student_id = ? 
        ORDER BY uploaded_at DESC
    ");
    $stmt->execute([$student_id]);
    $documents = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success', 
        'documents' => $documents,
        'student_name' => $student['first_name'] . ' ' . $student['last_name']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
