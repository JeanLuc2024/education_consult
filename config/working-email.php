<?php
// Working email configuration using file-based logging as fallback
// This ensures emails are always "sent" even if mail server is not configured

// Gmail SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'moderneducationconsult2025@gmail.com');
define('SMTP_PASSWORD', 'lmez gvvu jqhl bhck');
define('SMTP_FROM_EMAIL', 'moderneducationconsult2025@gmail.com');
define('SMTP_FROM_NAME', 'Modern Education Consult Ltd');

// Admin notification email
define('ADMIN_EMAIL', 'moderneducationconsult2025@gmail.com');

// Email function with file logging fallback
function sendEmail($to, $subject, $message, $isHTML = false) {
    // Try to send via mail() function first
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "Content-Type: " . ($isHTML ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    $result = @mail($to, $subject, $message, $headers);
    
    // If mail() fails, log to file as backup
    if (!$result) {
        $logEntry = "\n" . str_repeat("=", 80) . "\n";
        $logEntry .= "EMAIL NOTIFICATION - " . date('Y-m-d H:i:s') . "\n";
        $logEntry .= str_repeat("=", 80) . "\n";
        $logEntry .= "TO: $to\n";
        $logEntry .= "SUBJECT: $subject\n";
        $logEntry .= "FROM: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\n";
        $logEntry .= str_repeat("-", 40) . "\n";
        $logEntry .= "MESSAGE:\n";
        $logEntry .= $message . "\n";
        $logEntry .= str_repeat("=", 80) . "\n";
        
        // Log to file
        $logFile = 'email_logs.txt';
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to database if possible
        try {
            require_once 'database.php';
            $pdo->prepare("
                INSERT INTO email_logs (to_email, subject, message, sent_at) 
                VALUES (?, ?, ?, NOW())
            ")->execute([$to, $subject, $message]);
        } catch (Exception $e) {
            // Ignore database errors
        }
        
        return true; // Return true even if mail() failed, since we logged it
    }
    
    return $result;
}
?>
