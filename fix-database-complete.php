<?php
require_once 'config/database.php';

try {
    // Add missing columns to users table
    $alter_queries = [
        "ALTER TABLE users ADD COLUMN date_of_birth DATE AFTER phone",
        "ALTER TABLE applications ADD COLUMN academic_degree VARCHAR(100) AFTER program_name",
        "ALTER TABLE applications ADD COLUMN start_year INT AFTER academic_degree", 
        "ALTER TABLE applications ADD COLUMN start_semester VARCHAR(20) AFTER start_year"
    ];
    
    foreach ($alter_queries as $query) {
        try {
            $pdo->exec($query);
            echo "✓ " . $query . "\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                echo "✗ " . $query . " - " . $e->getMessage() . "\n";
            } else {
                echo "✓ Column already exists\n";
            }
        }
    }
    
    echo "\nDatabase schema updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>