<?php
// Contact form handler for Modern Education Consult Ltd
require_once '../config/database.php';

// Set content type
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Validate and sanitize input
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$country_interest = trim($_POST['country_interest'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Insert inquiry into database
    $stmt = $pdo->prepare("
        INSERT INTO inquiries (name, email, phone, country_interest, subject, message) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $subject = "Consultation Request from " . $name;
    $stmt->execute([$name, $email, $phone, $country_interest, $subject, $message]);
    
    // Send email notification (basic implementation)
    $to = "info@moderneducationconsult.com";
    $subject = "New Consultation Request - " . $name;
    $body = "
    New consultation request received:
    
    Name: $name
    Email: $email
    Phone: $phone
    Country of Interest: $country_interest
    Message: $message
    
    Submitted on: " . date('Y-m-d H:i:s');
    
    $headers = "From: noreply@moderneducationconsult.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain\r\n";
    
    // Always show success immediately (no email delay)
    echo 'OK';
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again later.']);
}
?>
