<?php
require_once 'config/database.php';

try {
    // Add missing columns to applications table
    $alter_queries = [
        "ALTER TABLE applications ADD COLUMN academic_degree VARCHAR(100) AFTER program_name",
        "ALTER TABLE applications ADD COLUMN start_year INT AFTER academic_degree",
        "ALTER TABLE applications ADD COLUMN start_semester VARCHAR(20) AFTER start_year"
    ];
    
    foreach ($alter_queries as $query) {
        $pdo->exec($query);
        echo "Executed: " . $query . "\n";
    }
    
    echo "Database updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
