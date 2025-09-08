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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['country', 'university_name', 'program_name', 'academic_degree', 'start_year'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}

try {
    $application_id = $input['application_id'] ?? null;
    
    if ($application_id) {
        // Update existing application
        $stmt = $pdo->prepare("
            UPDATE applications SET 
                country = ?, 
                university_name = ?, 
                program_name = ?, 
                academic_degree = ?, 
                start_year = ?, 
                start_semester = ?, 
                notes = ?, 
                updated_at = NOW()
            WHERE id = ? AND student_id = ?
        ");
        
        $stmt->execute([
            $input['country'],
            $input['university_name'],
            $input['program_name'],
            $input['academic_degree'],
            $input['start_year'],
            $input['start_semester'] ?? 'Fall',
            $input['notes'] ?? '',
            $application_id,
            $_SESSION['user_id']
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'Application updated successfully']);
    } else {
        // Insert new application
        $stmt = $pdo->prepare("
            INSERT INTO applications (
                student_id, 
                country, 
                university_name, 
                program_name, 
                academic_degree, 
                start_year, 
                start_semester, 
                notes, 
                status, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $input['country'],
            $input['university_name'],
            $input['program_name'],
            $input['academic_degree'],
            $input['start_year'],
            $input['start_semester'] ?? 'Fall',
            $input['notes'] ?? ''
        ]);
        
        echo json_encode(['status' => 'success', 'message' => 'Application submitted successfully']);
    }
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
