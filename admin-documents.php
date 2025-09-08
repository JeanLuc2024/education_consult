<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Get all documents with student info
try {
    $stmt = $pdo->prepare("
        SELECT d.*, u.first_name, u.last_name, u.email 
        FROM documents d 
        JOIN users u ON d.student_id = u.id 
        ORDER BY d.uploaded_at DESC
    ");
    $stmt->execute();
    $documents = $stmt->fetchAll();
} catch (Exception $e) {
    $documents = [];
    error_log('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents - Admin Panel</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .admin-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin-sidebar.php'; ?>
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Documents</h2>
                        <a href="admin-dashboard.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Back to Dashboard
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student</th>
                                    <th>Document Type</th>
                                    <th>Filename</th>
                                    <th>Upload Date</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($doc['email']); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php 
                                                $docType = $doc['document_type'] ?? 'other';
                                                if (empty($docType) || $docType === '') {
                                                    $docType = 'other';
                                                }
                                                echo ucfirst(str_replace('_', ' ', $docType)); 
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($doc['original_filename']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($doc['uploaded_at'])); ?></td>
                                        <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                                        <td>
                                            <a href="uploads/documents/<?php echo htmlspecialchars($doc['filename']); ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
