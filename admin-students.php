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
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
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
</body>
</html>
