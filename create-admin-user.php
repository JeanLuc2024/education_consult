<?php
require_once 'config/database.php';

try {
    // Create admin user
    $admin_email = 'admin@moderneducationconsult.com';
    $admin_password = 'admin123';
    $admin_name = 'Admin';
    $admin_lastname = 'User';
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND user_type = 'admin'");
    $stmt->execute([$admin_email]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists!<br>";
    } else {
        // Create admin user
        $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, first_name, last_name, user_type, is_active, created_at) 
            VALUES (?, ?, ?, ?, 'admin', 1, NOW())
        ");
        
        $stmt->execute([$admin_email, $password_hash, $admin_name, $admin_lastname]);
        
        echo "Admin user created successfully!<br>";
    }
    
    echo "Admin Login Details:<br>";
    echo "Email: " . $admin_email . "<br>";
    echo "Password: " . $admin_password . "<br>";
    echo "<br><a href='admin-login.php'>Go to Admin Login</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
