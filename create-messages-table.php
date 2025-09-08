<?php
require_once 'config/database.php';

try {
    // Create admin_messages table
    $sql = "
    CREATE TABLE IF NOT EXISTS admin_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        admin_id INT NOT NULL,
        application_id INT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE SET NULL
    )";
    
    $pdo->exec($sql);
    echo "Messages table created successfully!";
    
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
