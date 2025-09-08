<?php
require_once 'config/database.php';

try {
    // Update documents with empty document_type to 'other'
    $stmt = $pdo->prepare("UPDATE documents SET document_type = 'other' WHERE document_type IS NULL OR document_type = ''");
    $stmt->execute();
    
    $affected = $stmt->rowCount();
    echo "Updated $affected documents with document type 'other'";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
