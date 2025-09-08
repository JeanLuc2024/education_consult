<?php
session_start();
require_once 'config/database.php';

// This is a test script to insert a sample admin message
// You can run this to test if messages are being displayed correctly

try {
    // Insert a test message
    $stmt = $pdo->prepare("
        INSERT INTO admin_messages (student_id, admin_id, application_id, subject, message) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    // Use student_id = 5 (Byiringiro) and admin_id = 3 (admini@gmail.com)
    $stmt->execute([
        5, // student_id
        3, // admin_id  
        1, // application_id
        'Test Message - Application Update',
        'This is a test message to verify that admin messages are working correctly. Your application is under review.'
    ]);
    
    echo "Test message inserted successfully!";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
