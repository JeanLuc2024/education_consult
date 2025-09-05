<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}


// Filter by student_id if provided
$where = '';
$params = [];
if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $where = 'WHERE a.student_id = ?';
    $params[] = $_GET['student_id'];
}

$sql = "
    SELECT a.*, u.first_name, u.last_name, u.email, u.phone 
    FROM applications a 
    JOIN users u ON a.student_id = u.id 
    $where
    ORDER BY a.created_at DESC
";
if ($params) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll();
} else {
    $stmt = $pdo->query($sql);
    $applications = $stmt->fetchAll();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $app_id = $_POST['application_id'];
        $new_status = $_POST['status'];
        $admin_message = $_POST['admin_message'] ?? '';
        
        try {
            // Update application status
            $stmt = $pdo->prepare("UPDATE applications SET status = ?, notes = ? WHERE id = ?");
            $stmt->execute([$new_status, $admin_message, $app_id]);
            
            // Add to status history
            $stmt = $pdo->prepare("
                INSERT INTO application_status_history (application_id, new_status, changed_by, notes) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$app_id, $new_status, $_SESSION['user_id'], $admin_message]);
            
            $success = 'Application status updated successfully';
        } catch (Exception $e) {
            $error = 'Error updating status: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications - Admin Panel</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        .application-card {
            border-left: 4px solid #0d83fd;
        }
        .status-new { border-left-color: #ffc107; }
        .status-under_review { border-left-color: #17a2b8; }
        .status-submitted { border-left-color: #6f42c1; }
        .status-approved { border-left-color: #28a745; }
        .status-rejected { border-left-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Applications</h2>
                    <a href="admin-dashboard.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($applications as $app): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card application-card status-<?php echo $app['status']; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title"><?php echo htmlspecialchars($app['university_name']); ?></h5>
                                        <span class="badge bg-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                        </span>
                                    </div>
                                    
                                    <p><strong>Student:</strong> <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($app['email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($app['phone'] ?? 'Not provided'); ?></p>
                                    <p><strong>Program:</strong> <?php echo htmlspecialchars($app['program_name']); ?></p>
                                    <p><strong>Country:</strong> <?php echo htmlspecialchars($app['country']); ?></p>
                                    <p><strong>Degree:</strong> <?php echo htmlspecialchars($app['academic_degree'] ?? 'Not specified'); ?></p>
                                    <p><strong>Start Year:</strong> <?php echo htmlspecialchars($app['start_year'] ?? 'Not specified'); ?></p>
                                    <p><strong>Applied:</strong> <?php echo date('M d, Y', strtotime($app['created_at'])); ?></p>
                                    
                                    <?php if ($app['notes']): ?>
                                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($app['notes']); ?></p>
                                    <?php endif; ?>

                                    <hr>
                                    
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <select class="form-select form-select-sm" name="status" required>
                                                    <option value="new" <?php echo $app['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                    <option value="under_review" <?php echo $app['status'] === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                                    <option value="submitted" <?php echo $app['status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                                    <option value="approved" <?php echo $app['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <textarea class="form-control form-control-sm" name="admin_message" rows="2" placeholder="Message to student (optional)"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
