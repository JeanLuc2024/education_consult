<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Get admin profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'First name, last name, and email are required';
    } else {
        // Check if password change is requested
        $password_change = !empty($current_password) || !empty($new_password) || !empty($confirm_password);

        if ($password_change) {
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error = 'All password fields are required for password change';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New passwords do not match';
            } elseif (strlen($new_password) < 6) {
                $error = 'New password must be at least 6 characters long';
            }
        }

        if (!isset($error)) {
            try {
                // Check if email is being changed and if it already exists
                if ($email !== $admin['email']) {
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $_SESSION['user_id']]);
                    if ($stmt->fetch()) {
                        $error = 'Email address is already in use by another account';
                    }
                }

                if (!isset($error)) {
                    // Verify current password if changing password
                    if ($password_change) {
                        if (!password_verify($current_password, $admin['password_hash'])) {
                            $error = 'Current password is incorrect';
                        }
                    }

                    if (!isset($error)) {
                        // Update admin profile
                        if ($password_change) {
                            $stmt = $pdo->prepare("
                                UPDATE users 
                                SET first_name = ?, last_name = ?, email = ?, password_hash = ?, updated_at = NOW()
                                WHERE id = ?
                            ");
                            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt->execute([$first_name, $last_name, $email, $password_hash, $_SESSION['user_id']]);
                        } else {
                            $stmt = $pdo->prepare("
                                UPDATE users 
                                SET first_name = ?, last_name = ?, email = ?, updated_at = NOW()
                                WHERE id = ?
                            ");
                            $stmt->execute([$first_name, $last_name, $email, $_SESSION['user_id']]);
                        }

                        // Update session name
                        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                        $success = $password_change ? 'Profile and password updated successfully' : 'Profile updated successfully';
                    }
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Modern Education Consult</title>
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
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Admin Profile</h2>
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
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">First Name</label>
                                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Last Name</label>
                                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                        </div>
                                        
                                        <hr>
                                        <h6>Change Password (Optional)</h6>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" placeholder="Enter current password">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="new_password" placeholder="Enter new password">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Update Profile
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
                                    <h5><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($admin['email']); ?></p>
                                    <hr>
                                    <h6>Account Information</h6>
                                    <p><strong>Role:</strong> Administrator</p>
                                    <p><strong>Member Since:</strong> <?php echo date('M Y', strtotime($admin['created_at'])); ?></p>
                                    <p><strong>Last Updated:</strong> <?php echo date('M d, Y', strtotime($admin['updated_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
