
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

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'First name, last name, and email are required']);
    exit;
}

try {
    // Update user profile
    $stmt = $pdo->prepare("
        UPDATE users 
        SET first_name = ?, last_name = ?, email = ?, phone = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$first_name, $last_name, $email, $phone, $_SESSION['user_id']]);
    // Update session name
    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
