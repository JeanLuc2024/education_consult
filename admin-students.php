<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Get all students with their application count
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(a.id) as application_count,
           MAX(a.created_at) as last_application
    FROM users u 
    LEFT JOIN applications a ON u.id = a.student_id 
    WHERE u.user_type = 'student'
    GROUP BY u.id 
    ORDER BY u.created_at DESC
");
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Panel</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Students</h2>
                    <a href="admin-dashboard.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Country</th>
                                        <th>Applications</th>
                                        <th>Last Application</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo $student['id']; ?></td>
                                            <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td><?php echo htmlspecialchars($student['phone'] ?? 'Not provided'); ?></td>
                                            <td>
                                                <?php 
                                                // Get latest application for this student
                                                $app = $pdo->prepare("SELECT country FROM applications WHERE student_id = ? ORDER BY created_at DESC LIMIT 1");
                                                $app->execute([$student['id']]);
                                                $country = $app->fetchColumn();
                                                echo htmlspecialchars($country ?: 'Not specified'); 
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $student['application_count']; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($student['last_application']): ?>
                                                    <?php echo date('M d, Y', strtotime($student['last_application'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                            <td>
                                                <a href="admin-applications.php?student_id=<?php echo $student['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View Applications
                                                </a>
                                                <button class="btn btn-sm btn-outline-info ms-1" 
                                                        onclick="viewStudentDocuments(<?php echo $student['id']; ?>)">
                                                    <i class="bi bi-file-earmark me-1"></i>Documents
                                                </button>
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
    </div>

    <!-- Student Documents Modal -->
    <div class="modal fade" id="studentDocumentsModal" tabindex="-1" aria-labelledby="studentDocumentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDocumentsModalLabel">Student Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="studentDocumentsBody">
                    <!-- Documents will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewStudentDocuments(studentId) {
            fetch(`get-student-documents.php?student_id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showStudentDocumentsModal(data.documents, data.student_name);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error loading documents: ' + error.message);
                });
        }

        function showStudentDocumentsModal(documents, studentName) {
            const modal = new bootstrap.Modal(document.getElementById('studentDocumentsModal'));
            document.getElementById('studentDocumentsModalLabel').textContent = `Documents - ${studentName}`;
            
            let html = '';
            if (documents.length === 0) {
                html = '<div class="alert alert-info">No documents uploaded yet.</div>';
            } else {
                html = '<div class="table-responsive"><table class="table table-striped">';
                html += '<thead><tr><th>Document Type</th><th>Filename</th><th>Upload Date</th><th>Size</th><th>Actions</th></tr></thead>';
                html += '<tbody>';
                documents.forEach(doc => {
                    html += `<tr>
                        <td>${doc.document_type.replace('_', ' ').toUpperCase()}</td>
                        <td>${doc.original_filename}</td>
                        <td>${new Date(doc.uploaded_at).toLocaleDateString()}</td>
                        <td>${Math.round(doc.file_size / 1024)} KB</td>
                        <td>
                            <a href="uploads/documents/${doc.filename}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
            }
            
            document.getElementById('studentDocumentsBody').innerHTML = html;
            modal.show();
        }
    </script>
</body>
</html>
