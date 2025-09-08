<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Handle delete inquiry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_inquiry') {
        $inquiry_id = $_POST['inquiry_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
            $stmt->execute([$inquiry_id]);
            $success = 'Inquiry deleted successfully';
        } catch (Exception $e) {
            $error = 'Error deleting inquiry: ' . $e->getMessage();
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
                                    
                                    <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>" class="text-primary"><?php echo htmlspecialchars($inquiry['email']); ?></a></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($inquiry['phone'] ?? 'Not provided'); ?></p>
                                    <p><strong>Country Interest:</strong> <?php echo htmlspecialchars($inquiry['country_interest'] ?? 'Not specified'); ?></p>
                                    
                                    <div class="mb-3">
                                        <strong>Message:</strong>
                                        <p class="mt-1"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-sm" onclick="openEmailClient('<?php echo htmlspecialchars($inquiry['email']); ?>', '<?php echo urlencode($inquiry['subject']); ?>', '<?php echo urlencode($inquiry['name']); ?>')">
                                            <i class="bi bi-envelope me-1"></i>
                                            Reply via Email
                                        </button>
                                        <button class="btn btn-danger btn-sm ms-2" onclick="deleteInquiry(<?php echo $inquiry['id']; ?>)">
                                            <i class="bi bi-trash me-1"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteInquiry(inquiryId) {
            if (confirm('Are you sure you want to delete this inquiry?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_inquiry">
                    <input type="hidden" name="inquiry_id" value="${inquiryId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function openEmailClient(email, subject, name) {
            const subjectLine = 'Re: ' + decodeURIComponent(subject);
            const body = `Dear ${decodeURIComponent(name)},\n\n\n\nBest regards,\nModern Education Consult Team`;
            
            // Try to open Gmail first
            const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${email}&su=${encodeURIComponent(subjectLine)}&body=${encodeURIComponent(body)}`;
            
            // Try to open default email client
            const mailtoUrl = `mailto:${email}?subject=${encodeURIComponent(subjectLine)}&body=${encodeURIComponent(body)}`;
            
            // Open Gmail in new tab
            window.open(gmailUrl, '_blank');
            
            // Also try to open default email client as fallback
            setTimeout(() => {
                window.location.href = mailtoUrl;
            }, 1000);
        }
    </script>
</body>
</html>
