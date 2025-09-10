<?php
// Simple email test with working email configuration
require_once 'config/working-email.php';

echo "<h2>Testing Simple Email Functionality</h2>";

// Test data
$testData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'phone' => '+250798979720',
    'age' => '25',
    'country_interest' => 'canada',
    'education_level' => 'bachelor',
    'message' => 'This is a test consultation request to verify email functionality.'
];

echo "<h3>Test Data:</h3>";
echo "<pre>" . print_r($testData, true) . "</pre>";

// Test email sending
$to = ADMIN_EMAIL;
$subject = "Test Consultation Request - " . $testData['name'];
$body = "
New consultation request received:

Name: {$testData['name']}
Email: {$testData['email']}
Phone: {$testData['phone']}
Age: {$testData['age']}
Country of Interest: {$testData['country_interest']}
Education Level: {$testData['education_level']}
Message: {$testData['message']}

Submitted on: " . date('Y-m-d H:i:s');

echo "<h3>Email Details:</h3>";
echo "<p><strong>To:</strong> $to</p>";
echo "<p><strong>Subject:</strong> $subject</p>";
echo "<p><strong>Body:</strong></p>";
echo "<pre>$body</pre>";

// Send test email
$result = sendEmail($to, $subject, $body);

if ($result) {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px;'>";
    echo "<h4>✅ Email Sent Successfully!</h4>";
    echo "<p>Test email has been sent to: <strong>$to</strong></p>";
    echo "<p>Please check your email inbox to confirm receipt.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;'>";
    echo "<h4>❌ Email Failed!</h4>";
    echo "<p>There was an error sending the test email. This might be due to:</p>";
    echo "<ul>";
    echo "<li>Local mail server not configured</li>";
    echo "<li>SMTP settings not properly configured</li>";
    echo "<li>Firewall blocking email ports</li>";
    echo "</ul>";
    echo "<p>For production use, consider using a proper SMTP service or hosting provider with email support.</p>";
    echo "</div>";
}

echo "<h3>Email Configuration:</h3>";
echo "<p><strong>From Email:</strong> " . SMTP_FROM_EMAIL . "</p>";
echo "<p><strong>From Name:</strong> " . SMTP_FROM_NAME . "</p>";
echo "<p><strong>Admin Email:</strong> " . ADMIN_EMAIL . "</p>";

echo "<hr>";
echo "<p><a href='index.html'>← Back to Website</a> | <a href='admin-login.php'>Admin Login</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2, h3 { color: #2d465e; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6; }
</style>
