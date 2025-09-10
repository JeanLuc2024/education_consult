<?php
// Contact form handler for Modern Education Consult Ltd
require_once '../config/database.php';
require_once '../config/email.php';

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
    $age = trim($_POST['age'] ?? '');
    $country_interest = trim($_POST['country_interest'] ?? '');
    $education_level = trim($_POST['education_level'] ?? '');
    $message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($age) || !is_numeric($age) || $age < 16 || $age > 65) {
    $errors[] = 'Valid age (16-65) is required';
}

if (empty($education_level)) {
    $errors[] = 'Education level is required';
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
        INSERT INTO inquiries (name, email, phone, age, country_interest, education_level, subject, message) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $subject = "Consultation Request from " . $name;
    $stmt->execute([$name, $email, $phone, $age, $country_interest, $education_level, $subject, $message]);
    
    // Send email notification
    $to = ADMIN_EMAIL;
    $subject = "New Consultation Request - " . $name;
    $body = "
    New consultation request received:
    
    Name: $name
    Email: $email
    Phone: $phone
    Age: $age
    Country of Interest: $country_interest
    Education Level: $education_level
    Message: $message
    
    Submitted on: " . date('Y-m-d H:i:s');
    
    // Send email using our email function
    sendEmail($to, $subject, $body);
    
    echo 'OK';
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred. Please try again later.']);
}
?>
