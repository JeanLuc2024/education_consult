<?php
require_once 'config/database.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test basic connection
    echo "✅ Database connection successful<br>";
    
    // Test if database exists
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "✅ Database name: " . $result['db_name'] . "<br>";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "✅ Tables found: " . count($tables) . "<br>";
    echo "Tables: " . implode(', ', $tables) . "<br>";
    
    // Test users table
    if (in_array('users', $tables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "✅ Users table has " . $result['count'] . " records<br>";
        
        // Check for admin user
        $stmt = $pdo->query("SELECT email, user_type FROM users WHERE user_type = 'admin'");
        $admin = $stmt->fetch();
        if ($admin) {
            echo "✅ Admin user found: " . $admin['email'] . "<br>";
        } else {
            echo "❌ No admin user found. Run create-admin-user.php<br>";
        }
    }
    
    echo "<br><a href='admin-login.php'>Test Admin Login</a><br>";
    echo "<a href='create-admin-user.php'>Create Admin User</a><br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "<a href='setup-database.php'>Run Database Setup</a><br>";
}
?>
