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
    
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO destinations (name, slug, country, description, is_active) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $slug, $country, $description, $is_active]);
            $success = "Destination added successfully!";
        } catch (Exception $e) {
            $error = "Error adding destination: " . $e->getMessage();
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("
                UPDATE destinations 
                SET name=?, slug=?, country=?, description=?, is_active=?
                WHERE id=?
            ");
            $stmt->execute([$name, $slug, $country, $description, $is_active, $id]);
            $success = "Destination updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating destination: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Destination deleted successfully!";
        } catch (Exception $e) {
            $error = "Error deleting destination: " . $e->getMessage();
        }
    }
}

// Get all destinations
try {
    $stmt = $pdo->query("SELECT * FROM destinations ORDER BY country ASC");
    $destinations = $stmt->fetchAll();
} catch (Exception $e) {
    $destinations = [];
    $error = "Error fetching destinations: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin Panel</title>
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
                        <a href="admin-destinations.php" class="sidebar-link active">
                            <i class="bi bi-globe me-2"></i>
                            Destinations
                        </a>
                        <a href="admin-social-media.php" class="sidebar-link">
                            <i class="bi bi-share me-2"></i>
                            Social Media
                        </a>
                        <a href="admin-inquiries.php" class="sidebar-link">
                            <i class="bi bi-envelope me-2"></i>
                            Inquiries
                        </a>
                        <a href="admin-profile.php" class="sidebar-link">
                            <i class="bi bi-person me-2"></i>
                            Profile
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
                        <h2>Manage Destinations</h2>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add Destination
                            </button>
                            <a href="index.html" class="btn btn-outline-primary btn-sm ms-2" target="_blank">
                                <i class="bi bi-eye me-1"></i>
                                View Website
                            </a>
                        </div>
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
                        <?php foreach ($destinations as $destination): ?>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title"><?php echo htmlspecialchars($destination['name']); ?></h5>
                                            <span class="badge bg-<?php echo $destination['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $destination['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                        
                                        <p><strong>Country:</strong> <?php echo htmlspecialchars($destination['country']); ?></p>
                                        <p><strong>Slug:</strong> <?php echo htmlspecialchars($destination['slug']); ?></p>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($destination['description']); ?></p>
                                        
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-primary" onclick="editDestination(<?php echo htmlspecialchars(json_encode($destination)); ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger ms-2" onclick="deleteDestination(<?php echo $destination['id']; ?>)">
                                                <i class="bi bi-trash"></i> Delete
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
    </div>

    <!-- Add Destination Modal -->
    <div class="modal fade" id="addDestinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Destination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="destinationForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label class="form-label">Destination Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Slug *</label>
                            <input type="text" class="form-control" name="slug" required placeholder="e.g., canada, uk, usa">
                            <small class="form-text text-muted">URL-friendly version of the name (lowercase, no spaces)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Country *</label>
                            <input type="text" class="form-control" name="country" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label" for="isActive">
                                Active Destination
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Destination</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Destination Modal -->
    <div class="modal fade" id="editDestinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Destination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editDestinationForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="editId">
                        
                        <div class="mb-3">
                            <label class="form-label">Destination Name *</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Slug *</label>
                            <input type="text" class="form-control" name="slug" id="editSlug" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Country *</label>
                            <input type="text" class="form-control" name="country" id="editCountry" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive">
                            <label class="form-check-label" for="editIsActive">
                                Active Destination
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Destination</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this destination? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editDestination(destination) {
            document.getElementById('editId').value = destination.id;
            document.getElementById('editName').value = destination.name;
            document.getElementById('editSlug').value = destination.slug;
            document.getElementById('editCountry').value = destination.country;
            document.getElementById('editDescription').value = destination.description || '';
            document.getElementById('editIsActive').checked = destination.is_active == 1;
            
            const modal = new bootstrap.Modal(document.getElementById('editDestinationModal'));
            modal.show();
        }
        
        function deleteDestination(id) {
            document.getElementById('deleteId').value = id;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>
</html>