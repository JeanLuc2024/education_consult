
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
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'First name, last name, and email are required']);
    exit;
}

// Check if password change is requested
$password_change = !empty($current_password) || !empty($new_password) || !empty($confirm_password);

if ($password_change) {
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo json_encode(['status' => 'error', 'message' => 'All password fields are required for password change']);
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
        exit;
    }
    
    if (strlen($new_password) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'New password must be at least 6 characters long']);
        exit;
    }
}

try {
    // Verify current password if changing password
    if ($password_change) {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
            exit;
        }
    }
    
    // Update user profile
    if ($password_change) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, phone = ?, password_hash = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, phone = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$first_name, $last_name, $email, $phone, $_SESSION['user_id']]);
    }
    
    // Update session name
    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
    
    $message = $password_change ? 'Profile and password updated successfully' : 'Profile updated successfully';
    echo json_encode(['status' => 'success', 'message' => $message]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
