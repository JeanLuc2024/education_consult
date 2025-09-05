<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $debug_info .= "Email: " . $email . "<br>";
    $debug_info .= "Password: " . $password . "<br>";
    
    if (!empty($email) && !empty($password)) {
        try {
            // First check if user exists
            $stmt = $pdo->prepare("SELECT id, email, password_hash, first_name, last_name, user_type, is_active FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            $debug_info .= "User found: " . ($user ? "Yes" : "No") . "<br>";
            
            if ($user) {
                $debug_info .= "User type: " . $user['user_type'] . "<br>";
                $debug_info .= "Is active: " . $user['is_active'] . "<br>";
                $debug_info .= "Password hash: " . substr($user['password_hash'], 0, 20) . "...<br>";
                
                if ($user['user_type'] === 'admin' && $user['is_active'] == 1) {
                    $password_check = password_verify($password, $user['password_hash']);
                    $debug_info .= "Password verify: " . ($password_check ? "Success" : "Failed") . "<br>";
                    
                    if ($password_check) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        $_SESSION['user_type'] = $user['user_type'];
                        
                        $debug_info .= "Login successful!<br>";
                        header('Location: admin-dashboard.php');
                        exit;
                    } else {
                        $error = 'Invalid password';
                    }
                } else {
                    $error = 'User is not an admin or not active';
                }
            } else {
                $error = 'User not found';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
            $debug_info .= "Exception: " . $e->getMessage() . "<br>";
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Debug - Modern Education Consult</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        .debug-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; font-family: monospace; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Admin Login Debug</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($debug_info): ?>
                            <div class="debug-info">
                                <strong>Debug Info:</strong><br>
                                <?php echo $debug_info; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="admin@moderneducationconsult.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="admin123" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                        
                        <div class="mt-3">
                            <a href="create-admin-user.php" class="btn btn-secondary">Create Admin User</a>
                            <a href="test-database.php" class="btn btn-info">Test Database</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
