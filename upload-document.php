<?php
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$student_id = $_SESSION['user_id'];
$document_type = $_POST['document_type'] ?? '';
$application_id = $_POST['application_id'] ?? null;

// Validate document type
if (empty($document_type)) {
    echo json_encode(['status' => 'error', 'message' => 'Please select document type']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a file to upload']);
    exit;
}

$file = $_FILES['document'];
$allowed_types = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
$max_size = 5 * 1024 * 1024; // 5MB

// Validate file type and size
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Only PDF, JPG, and PNG files are allowed']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['status' => 'error', 'message' => 'File size must be less than 5MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/documents/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '_' . time() . '.' . $file_extension;
$file_path = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    try {
        // Save to database
        $stmt = $pdo->prepare("
            INSERT INTO documents (student_id, application_id, filename, original_filename, file_type, file_size, document_type) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $student_id,
            $application_id,
            $filename,
            $file['name'],
            $file['type'],
            $file['size'],
            $document_type
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'Document uploaded successfully']);
    } catch (Exception $e) {
        // Delete uploaded file if database save fails
        unlink($file_path);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
}
?>
