<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Handle reply to inquiry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'reply') {
        $inquiry_id = $_POST['inquiry_id'];
        $reply_message = trim($_POST['reply_message']);
        
        if (!empty($reply_message)) {
            try {
                // Store reply in a new table or as a status update (for now, just mark as contacted and store reply in a notes field)
                $stmt = $pdo->prepare("UPDATE inquiries SET status = 'contacted', subject = CONCAT(subject, ' | Admin Reply: ', ?) WHERE id = ?");
                $stmt->execute([$reply_message, $inquiry_id]);
                $success = 'Reply sent successfully';
            } catch (Exception $e) {
                $error = 'Error sending reply: ' . $e->getMessage();
            }
        } else {
            $error = 'Please enter a reply message';
        }
    }
}

// Get all inquiries
$stmt = $pdo->query("
    SELECT * FROM inquiries 
    ORDER BY created_at DESC
");
$inquiries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin Panel</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Inquiries</h2>
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
                    <?php foreach ($inquiries as $inquiry): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title"><?php echo htmlspecialchars($inquiry['name']); ?></h5>
                                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($inquiry['created_at'])); ?></small>
                                    </div>
                                    
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($inquiry['email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['phone'] ?? 'Not provided'); ?></p>
                                    <p><strong>Country Interest:</strong> <?php echo htmlspecialchars($inquiry['country_interest'] ?? 'Not specified'); ?></p>
                                    
                                    <div class="mb-3">
                                        <strong>Message:</strong>
                                        <p class="mt-1"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                    </div>

                                    <?php if ($inquiry['status'] === 'contacted'): ?>
                                        <div class="alert alert-info">
                                            <strong>Your Reply:</strong>
                                            <p class="mt-1 mb-0"><?php echo htmlspecialchars(explode('| Admin Reply: ', $inquiry['subject'])[1] ?? ''); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <form method="POST" class="mt-3">
                                            <input type="hidden" name="action" value="reply">
                                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                            <div class="mb-2">
                                                <label class="form-label">Reply Message:</label>
                                                <textarea class="form-control" name="reply_message" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Send Reply</button>
                                        </form>
                                    <?php endif; ?>
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
