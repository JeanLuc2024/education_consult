<?php
// Email configuration for Modern Education Consult Ltd

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'moderneducationconsult2025@gmail.com');
define('SMTP_PASSWORD', 'lmez gvvu jqhl bhck');
define('SMTP_FROM_EMAIL', 'moderneducationconsult2025@gmail.com');
define('SMTP_FROM_NAME', 'Modern Education Consult Ltd');

// Admin notification email
define('ADMIN_EMAIL', 'moderneducationconsult2025@gmail.com');

// Email function
function sendEmail($to, $subject, $message, $headers = '') {
    $defaultHeaders = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $defaultHeaders .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $defaultHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $allHeaders = $headers ? $defaultHeaders . $headers : $defaultHeaders;
    
    return mail($to, $subject, $message, $allHeaders);
}
?>
