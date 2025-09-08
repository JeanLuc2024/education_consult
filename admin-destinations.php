<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['name']);
                $slug = strtolower(str_replace(' ', '-', $name));
                $description = trim($_POST['description']);
                $image_path = trim($_POST['image_path']);
                $features = json_encode(explode("\n", trim($_POST['features'])));
                $sort_order = (int)$_POST['sort_order'];
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO destinations (name, slug, description, image_path, features, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $slug, $description, $image_path, $features, $sort_order]);
                    $success = "Destination added successfully!";
                } catch (Exception $e) {
                    $error = "Error adding destination: " . $e->getMessage();
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $name = trim($_POST['name']);
                $slug = strtolower(str_replace(' ', '-', $name));
                $description = trim($_POST['description']);
                $image_path = trim($_POST['image_path']);
                $features = json_encode(explode("\n", trim($_POST['features'])));
                $sort_order = (int)$_POST['sort_order'];
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                try {
                    $stmt = $pdo->prepare("UPDATE destinations SET name=?, slug=?, description=?, image_path=?, features=?, sort_order=?, is_active=? WHERE id=?");
                    $stmt->execute([$name, $slug, $description, $image_path, $features, $sort_order, $is_active, $id]);
                    $success = "Destination updated successfully!";
                } catch (Exception $e) {
                    $error = "Error updating destination: " . $e->getMessage();
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = "Destination deleted successfully!";
                } catch (Exception $e) {
                    $error = "Error deleting destination: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get all destinations
try {
    $stmt = $pdo->query("SELECT * FROM destinations ORDER BY sort_order ASC, name ASC");
    $destinations = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Error fetching destinations: " . $e->getMessage();
    $destinations = [];
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
                        <a href="admin-students.php" class="sidebar-link">
                            <i class="bi bi-people me-2"></i>
                            Students
                        </a>
                        <a href="admin-destinations.php" class="sidebar-link active">
                            <i class="bi bi-globe me-2"></i>
                            Destinations
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
                        <h2>Manage Destinations</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="index.html" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-house me-1"></i>
                                View Website
                            </a>
                        </div>
                    </div>

                    <!-- Alerts -->
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

                    <!-- Add Destination Button -->
                    <div class="mb-4">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                            <i class="bi bi-plus-circle me-2"></i>
                            Add New Destination
                        </button>
                    </div>

                    <!-- Destinations Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Sort Order</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($destinations as $destination): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($destination['image_path']); ?>" 
                                                         alt="<?php echo htmlspecialchars($destination['name']); ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                </td>
                                                <td><?php echo htmlspecialchars($destination['name']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($destination['description'], 0, 100)) . '...'; ?></td>
                                                <td><?php echo $destination['sort_order']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $destination['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $destination['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editDestination(<?php echo htmlspecialchars(json_encode($destination)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteDestination(<?php echo $destination['id']; ?>, '<?php echo htmlspecialchars($destination['name']); ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Destination Modal -->
    <div class="modal fade" id="addDestinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image Path</label>
                            <input type="text" class="form-control" name="image_path" placeholder="assets/img/country.png" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features (one per line)</label>
                            <textarea class="form-control" name="features" rows="4" placeholder="Feature 1&#10;Feature 2&#10;Feature 3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="0">
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
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image Path</label>
                            <input type="text" class="form-control" name="image_path" id="edit_image_path" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features (one per line)</label>
                            <textarea class="form-control" name="features" id="edit_features" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_sort_order">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">
                                    Active
                                </label>
                            </div>
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
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the destination "<span id="delete_name"></span>"?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Destination</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editDestination(destination) {
            document.getElementById('edit_id').value = destination.id;
            document.getElementById('edit_name').value = destination.name;
            document.getElementById('edit_description').value = destination.description;
            document.getElementById('edit_image_path').value = destination.image_path;
            document.getElementById('edit_sort_order').value = destination.sort_order;
            document.getElementById('edit_is_active').checked = destination.is_active == 1;
            
            // Parse features from JSON
            const features = JSON.parse(destination.features);
            document.getElementById('edit_features').value = features.join('\n');
            
            new bootstrap.Modal(document.getElementById('editDestinationModal')).show();
        }
        
        function deleteDestination(id, name) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
