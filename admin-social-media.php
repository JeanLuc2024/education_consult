<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        $facebook = trim($_POST['facebook'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        try {
            // Update Facebook
            $stmt = $pdo->prepare("UPDATE social_media_settings SET url = ? WHERE platform = 'facebook'");
            $stmt->execute([$facebook]);
            
            // Update Instagram
            $stmt = $pdo->prepare("UPDATE social_media_settings SET url = ? WHERE platform = 'instagram'");
            $stmt->execute([$instagram]);
            
            // Update Email
            $stmt = $pdo->prepare("UPDATE social_media_settings SET url = ? WHERE platform = 'email'");
            $stmt->execute([$email]);
            
            $success = "Social media settings updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating social media settings: " . $e->getMessage();
        }
    }
}

// Get current social media settings
try {
    $stmt = $pdo->query("SELECT * FROM social_media_settings ORDER BY platform ASC");
    $settings = $stmt->fetchAll();
    $socialData = [];
    foreach ($settings as $setting) {
        $socialData[$setting['platform']] = $setting['url'];
    }
} catch (Exception $e) {
    $socialData = [
        'facebook' => 'https://facebook.com/moderneducationconsult',
        'instagram' => 'https://instagram.com/moderneducationconsult',
        'email' => 'moderneducationconsult2025@gmail.com'
    ];
    $error = "Error fetching social media settings: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Social Media - Admin Panel</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2d465e, #0d83fd);
        }
        .admin-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            border-radius: 10px;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-gear-fill me-2"></i>
                        Admin Panel
                    </h4>
                    <nav class="nav flex-column">
                        <a href="admin-dashboard.php" class="sidebar-link">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        <a href="admin-universities-loans.php" class="sidebar-link">
                            <i class="bi bi-bank me-2"></i>
                            Universities with Loans
                        </a>
                        <a href="admin-online-courses.php" class="sidebar-link">
                            <i class="bi bi-laptop me-2"></i>
                            Online Courses
                        </a>
                        <a href="admin-social-media.php" class="sidebar-link active">
                            <i class="bi bi-share me-2"></i>
                            Social Media
                        </a>
                        <a href="admin-services.php" class="sidebar-link">
                            <i class="bi bi-gear me-2"></i>
                            Services
                        </a>
                        <a href="admin-inquiries.php" class="sidebar-link">
                            <i class="bi bi-envelope me-2"></i>
                            Inquiries
                        </a>
                        <a href="logout.php" class="sidebar-link">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Social Media Settings</h2>
                        <a href="index.html" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="bi bi-eye me-1"></i>
                            View Website
                        </a>
                    </div>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Social Media Links</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-facebook text-primary me-2"></i>
                                                Facebook URL
                                            </label>
                                            <input type="url" class="form-control" name="facebook" 
                                                   value="<?php echo htmlspecialchars($socialData['facebook'] ?? ''); ?>" 
                                                   placeholder="https://facebook.com/yourpage">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-instagram text-danger me-2"></i>
                                                Instagram URL
                                            </label>
                                            <input type="url" class="form-control" name="instagram" 
                                                   value="<?php echo htmlspecialchars($socialData['instagram'] ?? ''); ?>" 
                                                   placeholder="https://instagram.com/yourpage">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="bi bi-envelope text-success me-2"></i>
                                                Email Address
                                            </label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?php echo htmlspecialchars($socialData['email'] ?? ''); ?>" 
                                                   placeholder="your-email@example.com">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i>
                                            Update Social Media Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Preview</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">How your social media links will appear on the website:</p>
                                    
                                    <div class="d-flex flex-column gap-2">
                                        <a href="<?php echo htmlspecialchars($socialData['facebook'] ?? '#'); ?>" 
                                           target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-facebook me-1"></i>
                                            Facebook
                                        </a>
                                        
                                        <a href="<?php echo htmlspecialchars($socialData['instagram'] ?? '#'); ?>" 
                                           target="_blank" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-instagram me-1"></i>
                                            Instagram
                                        </a>
                                        
                                        <a href="mailto:<?php echo htmlspecialchars($socialData['email'] ?? ''); ?>" 
                                           class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-envelope me-1"></i>
                                            Email
                                        </a>
                                    </div>
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
