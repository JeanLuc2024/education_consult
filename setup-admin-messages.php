<?php
require_once 'config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS admin_messages (
        id int(11) NOT NULL AUTO_INCREMENT,
        student_id int(11) NOT NULL,
        admin_id int(11) NOT NULL,
        application_id int(11) DEFAULT NULL,
        subject varchar(255) NOT NULL,
        message text NOT NULL,
        is_read tinyint(1) DEFAULT 0,
        created_at timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id),
        KEY student_id (student_id),
        KEY admin_id (admin_id),
        KEY application_id (application_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $pdo->exec($sql);
    echo "Admin messages table created successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
