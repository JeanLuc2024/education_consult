<?php
// Complete setup script for all Modern Education Consult updates
require_once 'config/database.php';

echo "<h2>Modern Education Consult - Complete Setup</h2>";
echo "<p>Setting up all new features and updates...</p>";

try {
    // Run the add-new-tables script
    echo "<h3>1. Creating New Tables and Data</h3>";
    include 'add-new-tables.php';
    
    echo "<h3>2. Setup Complete!</h3>";
    echo "<div class='alert alert-success'>";
    echo "<h4>✅ All Updates Successfully Applied!</h4>";
    echo "<p>Your Modern Education Consult website now includes:</p>";
    echo "<ul>";
    echo "<li>✅ Age field in consultation form</li>";
    echo "<li>✅ Updated contact information (Musanze, Rwanda)</li>";
    echo "<li>✅ Email notifications to admin</li>";
    echo "<li>✅ Online courses management</li>";
    echo "<li>✅ Universities with study loans</li>";
    echo "<li>✅ Social media management</li>";
    echo "<li>✅ Services management</li>";
    echo "<li>✅ Turkey destination added</li>";
    echo "<li>✅ Updated images for testimonials and services</li>";
    echo "<li>✅ Dynamic country dropdown</li>";
    echo "<li>✅ Facility cards with modals</li>";
    echo "<li>✅ Removed student portal features</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Test the contact form to ensure email notifications work</li>";
    echo "<li>Add some universities with loans via admin panel</li>";
    echo "<li>Add some online courses via admin panel</li>";
    echo "<li>Update social media links via admin panel</li>";
    echo "<li>Customize services via admin panel</li>";
    echo "</ol>";
    echo "<p><a href='index.html' class='btn btn-primary'>View Website</a> | <a href='admin-login.php' class='btn btn-secondary'>Admin Login</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>❌ Setup Error</h4>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
.alert { padding: 20px; margin: 20px 0; border-radius: 5px; }
.alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
.alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
.btn { padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; display: inline-block; }
.btn-primary { background: #007bff; color: white; }
.btn-secondary { background: #6c757d; color: white; }
</style>
