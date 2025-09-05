<?php
session_start();
require_once 'config/database.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, email, password_hash, first_name, last_name, user_type FROM users WHERE email = ? AND user_type = 'admin' AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                header('Location: admin-dashboard.php');
                exit;
            } else {
                $error = 'Invalid admin credentials';
            }
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
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
    <title>Admin Login - Modern Education Consult</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .admin-login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .admin-header {
            background: linear-gradient(135deg, #0d83fd, #2d465e);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .admin-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="admin-card">
                        <div class="admin-header">
                            <div class="admin-icon">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <h3>Admin Login</h3>
                            <p class="mb-0">Access Management Dashboard</p>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Admin Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Login to Dashboard
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-4">
                                <p class="mb-2">
                                    <a href="index.html" class="text-decoration-none">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Back to Website
                                    </a>
                                </p>
                                <small class="text-muted">
                                    Default: admin@moderneducationconsult.com / admin123
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
