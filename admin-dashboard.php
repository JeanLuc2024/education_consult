<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Get statistics
try {
    $stats = [];
    
    // Total students
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'student'");
    $stats['students'] = $stmt->fetch()['count'];
    
    // Total applications
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM applications");
    $stats['applications'] = $stmt->fetch()['count'];
    
    // Total inquiries
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM inquiries");
    $stats['inquiries'] = $stmt->fetch()['count'];
    
    // Recent applications
    $stmt = $pdo->query("
        SELECT a.*, u.first_name, u.last_name 
        FROM applications a 
        JOIN users u ON a.student_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ");
    $recent_applications = $stmt->fetchAll();
    
    // Recent inquiries
    $stmt = $pdo->query("
        SELECT * FROM inquiries 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_inquiries = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Modern Education Consult</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
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
                        <a href="#dashboard" class="sidebar-link active" onclick="showSection('dashboard')">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        <a href="admin-students.php" class="sidebar-link">
                            <i class="bi bi-people me-2"></i>
                            Students
                        </a>
                        <a href="admin-applications.php" class="sidebar-link">
                            <i class="bi bi-file-text me-2"></i>
                            Applications
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
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 id="section-title">Dashboard</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="index.html" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-house me-1"></i>
                                View Website
                            </a>
                        </div>
                    </div>

                    <!-- Dashboard Section -->
                    <div id="dashboard-section" class="admin-section">

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-primary me-3">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?php echo $stats['students'] ?? 0; ?></h3>
                                        <p class="text-muted mb-0">Total Students</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-success me-3">
                                        <i class="bi bi-file-text"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?php echo $stats['applications'] ?? 0; ?></h3>
                                        <p class="text-muted mb-0">Applications</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-warning me-3">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0"><?php echo $stats['inquiries'] ?? 0; ?></h3>
                                        <p class="text-muted mb-0">Inquiries</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-4">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon bg-info me-3">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">95%</h3>
                                        <p class="text-muted mb-0">Success Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Applications</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($recent_applications)): ?>
                                        <?php foreach ($recent_applications as $app): ?>
                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($app['university_name']); ?></small>
                                                </div>
                                                <span class="badge bg-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No applications yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Inquiries</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($recent_inquiries)): ?>
                                        <?php foreach ($recent_inquiries as $inquiry): ?>
                                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($inquiry['name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($inquiry['email']); ?></small>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No inquiries yet</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Students Section -->
                    <div id="students-section" class="admin-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Student Management</h5>
                            </div>
                            <div class="card-body">
                                <p>Student management functionality will be implemented here.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">No students found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div id="applications-section" class="admin-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Application Management</h5>
                            </div>
                            <div class="card-body">
                                <p>Application management functionality will be implemented here.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Application ID</th>
                                                <th>Student</th>
                                                <th>University</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">No applications found</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inquiries Section -->
                    <div id="inquiries-section" class="admin-section" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Inquiry Management</h5>
                            </div>
                            <div class="card-body">
                                <p>Inquiry management functionality will be implemented here.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Subject</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($recent_inquiries)): ?>
                                                <?php foreach ($recent_inquiries as $inquiry): ?>
                                                    <tr>
                                                        <td><?php echo $inquiry['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                                        <td><?php echo htmlspecialchars($inquiry['subject']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary">View</button>
                                                            <button class="btn btn-sm btn-success">Reply</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No inquiries found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div id="content-section" class="admin-section" style="display: none;">
                        <div class="row">
                            <!-- Manage Pages -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-text fs-1 text-primary mb-3"></i>
                                        <h5 class="card-title">Manage Pages</h5>
                                        <p class="card-text">Edit website pages and content</p>
                                        <div class="d-grid gap-2">
                                            <a href="index.html" target="_blank" class="btn btn-primary">Edit Homepage</a>
                                            <a href="destinations.html" target="_blank" class="btn btn-outline-primary">Edit Destinations</a>
                                            <a href="about.html" target="_blank" class="btn btn-outline-primary">Edit About</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manage Images -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-image fs-1 text-success mb-3"></i>
                                        <h5 class="card-title">Manage Images</h5>
                                        <p class="card-text">Upload and manage website images</p>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" onclick="showImageManager()">View Images</button>
                                            <button class="btn btn-outline-success" onclick="uploadImage()">Upload New</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-gear fs-1 text-warning mb-3"></i>
                                        <h5 class="card-title">Settings</h5>
                                        <p class="card-text">Website configuration and settings</p>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-warning" onclick="showSettings()">General Settings</button>
                                            <button class="btn btn-outline-warning" onclick="showEmailSettings()">Email Settings</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Manager Modal -->
                        <div id="imageManagerModal" class="modal fade" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Image Manager</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row" id="imageGrid">
                                            <!-- Images will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Modal -->
                        <div id="settingsModal" class="modal fade" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Website Settings</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form>
                                            <div class="mb-3">
                                                <label class="form-label">Website Name</label>
                                                <input type="text" class="form-control" value="Modern Education Consult Ltd">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Contact Email</label>
                                                <input type="email" class="form-control" value="info@moderneducationconsult.com">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text" class="form-control" value="+1 (555) 123-4567">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary">Save Changes</button>
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
    
    <script>
        function showSection(sectionName) {
            // Remove active class from all sidebar links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked link
            event.target.closest('.sidebar-link').classList.add('active');
            

            ];

            imageGrid.innerHTML = '';
            images.forEach(img => {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-3';
                col.innerHTML = `
                    <div class="card">
                        <img src="${img.path}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="${img.name}">
                        <div class="card-body p-2">
                            <small class="text-muted">${img.name}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-primary" onclick="copyImagePath('${img.path}')">Copy Path</button>
                            </div>
                        </div>
                    </div>
                `;
                imageGrid.appendChild(col);
            });
        }

        function copyImagePath(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('Image path copied to clipboard: ' + path);
            });
        }

        function uploadImage() {
            alert('Image upload functionality will be implemented here. For now, you can manually add images to the assets/img folder.');
        }

        function showSettings() {
            const modal = new bootstrap.Modal(document.getElementById('settingsModal'));
            modal.show();
        }

        function showEmailSettings() {
            alert('Email settings:\n\nSMTP Server: localhost\nPort: 25\nFrom: noreply@moderneducationconsult.com\n\nNote: Configure your mail server in php.ini for production use.');
        }
    </script>
</body>
</html>
