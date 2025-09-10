<?php
// Install PHPMailer via Composer
echo "<h2>Installing PHPMailer</h2>";

// Check if composer is available
if (function_exists('shell_exec')) {
    echo "<p>Installing PHPMailer via Composer...</p>";
    
    // Create composer.json if it doesn't exist
    if (!file_exists('composer.json')) {
        $composerJson = '{
    "require": {
        "phpmailer/phpmailer": "^6.8"
    }
}';
        file_put_contents('composer.json', $composerJson);
        echo "<p>✅ Created composer.json</p>";
    }
    
    // Install PHPMailer
    $output = shell_exec('composer install 2>&1');
    echo "<pre>$output</pre>";
    
    if (file_exists('vendor/autoload.php')) {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px;'>";
        echo "<h3>✅ PHPMailer Installed Successfully!</h3>";
        echo "<p>You can now use the improved email functionality.</p>";
        echo "<p><a href='test-email.php'>Test Email</a> | <a href='index.html'>View Website</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px;'>";
        echo "<h3>❌ PHPMailer Installation Failed</h3>";
        echo "<p>Please install Composer and run: <code>composer install</code></p>";
        echo "</div>";
    }
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; color: #856404; border-radius: 5px;'>";
    echo "<h3>⚠️ Manual Installation Required</h3>";
    echo "<p>Please install Composer and run:</p>";
    echo "<pre>composer require phpmailer/phpmailer</pre>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
h2, h3 { color: #2d465e; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; }
</style>
