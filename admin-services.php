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
        $service_name = trim($_POST['service_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            // Update service in database (you'll need to create a services table)
            $stmt = $pdo->prepare("
                UPDATE services 
                SET service_name=?, description=?, icon=?, is_active=?
                WHERE id=?
            ");
            $stmt->execute([$service_name, $description, $icon, $is_active, $_POST['service_id']]);
            $success = "Service updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating service: " . $e->getMessage();
        }
    }
}

// Get services from database
try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $stmt->fetchAll();
    
    // If no services found, provide default services
    if (empty($services)) {
        $services = [
            ['id' => 1, 'service_name' => 'Study and Work Abroad', 'description' => 'Complete assistance in university selection, application preparation, and program matching based on your academic profile and career goals.', 'icon' => 'bi-mortarboard', 'is_active' => 1],
            ['id' => 2, 'service_name' => 'Scholarship Assistance', 'description' => 'Expert help in identifying and applying for scholarships, grants, and financial aid opportunities to make your education affordable.', 'icon' => 'bi-currency-dollar', 'is_active' => 1],
            ['id' => 3, 'service_name' => 'Student Visa Support', 'description' => 'Comprehensive visa application support including document preparation, interview guidance, and immigration compliance assistance.', 'icon' => 'bi-passport', 'is_active' => 1],
            ['id' => 4, 'service_name' => 'After Visa Services', 'description' => 'Comprehensive support after visa approval including airport pickup, accommodation assistance, and settling-in support for a smooth transition.', 'icon' => 'bi-check-circle', 'is_active' => 1],
            ['id' => 5, 'service_name' => 'Study Loan Assistance', 'description' => 'Help you find and apply for study loans from various financial institutions and universities to fund your education abroad.', 'icon' => 'bi-bank', 'is_active' => 1],
            ['id' => 6, 'service_name' => 'Tuition Fee Discounts', 'description' => 'Access to exclusive tuition fee discounts and early bird offers from partner universities to make your education more affordable.', 'icon' => 'bi-percent', 'is_active' => 1]
        ];
    }
} catch (Exception $e) {
    $services = [
        ['id' => 1, 'service_name' => 'Study and Work Abroad', 'description' => 'Complete assistance in university selection, application preparation, and program matching based on your academic profile and career goals.', 'icon' => 'bi-mortarboard', 'is_active' => 1],
        ['id' => 2, 'service_name' => 'Scholarship Assistance', 'description' => 'Expert help in identifying and applying for scholarships, grants, and financial aid opportunities to make your education affordable.', 'icon' => 'bi-currency-dollar', 'is_active' => 1],
        ['id' => 3, 'service_name' => 'Student Visa Support', 'description' => 'Comprehensive visa application support including document preparation, interview guidance, and immigration compliance assistance.', 'icon' => 'bi-passport', 'is_active' => 1],
        ['id' => 4, 'service_name' => 'After Visa Services', 'description' => 'Comprehensive support after visa approval including airport pickup, accommodation assistance, and settling-in support for a smooth transition.', 'icon' => 'bi-check-circle', 'is_active' => 1],
        ['id' => 5, 'service_name' => 'Study Loan Assistance', 'description' => 'Help you find and apply for study loans from various financial institutions and universities to fund your education abroad.', 'icon' => 'bi-bank', 'is_active' => 1],
        ['id' => 6, 'service_name' => 'Tuition Fee Discounts', 'description' => 'Access to exclusive tuition fee discounts and early bird offers from partner universities to make your education more affordable.', 'icon' => 'bi-percent', 'is_active' => 1]
    ];
    $error = "Error fetching services: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin Panel</title>
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
                        <a href="admin-social-media.php" class="sidebar-link">
                            <i class="bi bi-share me-2"></i>
                            Social Media
                        </a>
                        <a href="admin-services.php" class="sidebar-link active">
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
                        <h2>Manage Services</h2>
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

                    <div class="row g-4">
                        <?php foreach ($services as $service): ?>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="service-icon me-3">
                                                <i class="<?php echo $service['icon'] ?? 'bi-gear'; ?> fs-2 text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="card-title"><?php echo htmlspecialchars($service['service_name'] ?? 'Unnamed Service'); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars($service['description'] ?? 'No description available'); ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-<?php echo ($service['is_active'] ?? 0) ? 'success' : 'secondary'; ?>">
                                                        <?php echo ($service['is_active'] ?? 0) ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                    <button class="btn btn-sm btn-primary" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="serviceForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="service_id" id="serviceId">
                        
                        <div class="mb-3">
                            <label class="form-label">Service Name *</label>
                            <input type="text" class="form-control" name="service_name" id="serviceName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" id="serviceDescription" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Icon Class (Bootstrap Icons)</label>
                            <input type="text" class="form-control" name="icon" id="serviceIcon" placeholder="bi-mortarboard">
                            <small class="form-text text-muted">Use Bootstrap Icons class names (e.g., bi-mortarboard, bi-passport)</small>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="serviceActive">
                            <label class="form-check-label" for="serviceActive">
                                Active Service
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editService(service) {
            document.getElementById('serviceId').value = service.id || '';
            document.getElementById('serviceName').value = service.service_name || '';
            document.getElementById('serviceDescription').value = service.description || '';
            document.getElementById('serviceIcon').value = service.icon || '';
            document.getElementById('serviceActive').checked = (service.is_active == 1) || false;
            
            const modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
            modal.show();
        }
    </script>
</body>
</html>
