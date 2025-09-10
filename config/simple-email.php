<?php
// Simple email configuration using basic mail() function
// This is a fallback when PHPMailer is not available

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'moderneducationconsult2025@gmail.com');
define('SMTP_PASSWORD', 'lmez gvvu jqhl bhck');
define('SMTP_FROM_EMAIL', 'moderneducationconsult2025@gmail.com');
define('SMTP_FROM_NAME', 'Modern Education Consult Ltd');

// Admin notification email
define('ADMIN_EMAIL', 'moderneducationconsult2025@gmail.com');

// Simple email function using basic mail()
function sendEmail($to, $subject, $message, $isHTML = false) {
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "Content-Type: " . ($isHTML ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>
