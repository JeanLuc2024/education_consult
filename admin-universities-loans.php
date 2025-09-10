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
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $university_name = trim($_POST['university_name'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $loan_provider = trim($_POST['loan_provider'] ?? '');
        $loan_type = trim($_POST['loan_type'] ?? '');
        $interest_rate = floatval($_POST['interest_rate'] ?? 0);
        $max_amount = floatval($_POST['max_amount'] ?? 0);
        $repayment_period = trim($_POST['repayment_period'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $contact_info = trim($_POST['contact_info'] ?? '');
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO universities_with_loans 
                (university_name, country, loan_provider, loan_type, interest_rate, max_amount, repayment_period, requirements, contact_info) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$university_name, $country, $loan_provider, $loan_type, $interest_rate, $max_amount, $repayment_period, $requirements, $contact_info]);
            $success = "University with study loan added successfully!";
        } catch (Exception $e) {
            $error = "Error adding university: " . $e->getMessage();
        }
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $university_name = trim($_POST['university_name'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $loan_provider = trim($_POST['loan_provider'] ?? '');
        $loan_type = trim($_POST['loan_type'] ?? '');
        $interest_rate = floatval($_POST['interest_rate'] ?? 0);
        $max_amount = floatval($_POST['max_amount'] ?? 0);
        $repayment_period = trim($_POST['repayment_period'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $contact_info = trim($_POST['contact_info'] ?? '');
        
        try {
            $stmt = $pdo->prepare("
                UPDATE universities_with_loans 
                SET university_name=?, country=?, loan_provider=?, loan_type=?, interest_rate=?, max_amount=?, repayment_period=?, requirements=?, contact_info=?
                WHERE id=?
            ");
            $stmt->execute([$university_name, $country, $loan_provider, $loan_type, $interest_rate, $max_amount, $repayment_period, $requirements, $contact_info, $id]);
            $success = "University updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating university: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM universities_with_loans WHERE id=?");
            $stmt->execute([$id]);
            $success = "University deleted successfully!";
        } catch (Exception $e) {
            $error = "Error deleting university: " . $e->getMessage();
        }
    } elseif ($action === 'toggle_status') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE universities_with_loans SET is_active = NOT is_active WHERE id=?");
            $stmt->execute([$id]);
            $success = "University status updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating university status: " . $e->getMessage();
        }
    }
}

// Get all universities with loans
try {
    $stmt = $pdo->query("SELECT * FROM universities_with_loans ORDER BY university_name ASC");
    $universities = $stmt->fetchAll();
} catch (Exception $e) {
    $universities = [];
    $error = "Error fetching universities: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Universities with Study Loans - Admin Panel</title>
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
            <?php include 'admin-sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Universities with Study Loans</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUniversityModal">
                            <i class="bi bi-plus-circle me-1"></i>
                            Add New University
                        </button>
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

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>University</th>
                                            <th>Country</th>
                                            <th>Loan Provider</th>
                                            <th>Max Amount</th>
                                            <th>Interest Rate</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($universities as $uni): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($uni['university_name']); ?></td>
                                                <td><?php echo htmlspecialchars($uni['country']); ?></td>
                                                <td><?php echo htmlspecialchars($uni['loan_provider']); ?></td>
                                                <td>$<?php echo number_format($uni['max_amount']); ?></td>
                                                <td><?php echo $uni['interest_rate']; ?>%</td>
                                                <td>
                                                    <span class="badge bg-<?php echo $uni['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $uni['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editUniversity(<?php echo htmlspecialchars(json_encode($uni)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="id" value="<?php echo $uni['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-<?php echo $uni['is_active'] ? 'warning' : 'success'; ?>">
                                                            <i class="bi bi-<?php echo $uni['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this university?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $uni['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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

    <!-- Add/Edit University Modal -->
    <div class="modal fade" id="addUniversityModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New University</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="universityForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add" id="formAction">
                        <input type="hidden" name="id" value="" id="universityId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">University Name *</label>
                                <input type="text" class="form-control" name="university_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" class="form-control" name="country" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Provider *</label>
                                <input type="text" class="form-control" name="loan_provider" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loan Type *</label>
                                <input type="text" class="form-control" name="loan_type" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interest Rate (%) *</label>
                                <input type="number" step="0.01" class="form-control" name="interest_rate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Amount ($) *</label>
                                <input type="number" step="0.01" class="form-control" name="max_amount" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Repayment Period *</label>
                                <input type="text" class="form-control" name="repayment_period" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Info (URL)</label>
                                <input type="url" class="form-control" name="contact_info">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea class="form-control" name="requirements" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save University</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUniversity(uni) {
            document.getElementById('modalTitle').textContent = 'Edit University';
            document.getElementById('formAction').value = 'update';
            document.getElementById('universityId').value = uni.id;
            document.querySelector('input[name="university_name"]').value = uni.university_name;
            document.querySelector('input[name="country"]').value = uni.country;
            document.querySelector('input[name="loan_provider"]').value = uni.loan_provider;
            document.querySelector('input[name="loan_type"]').value = uni.loan_type;
            document.querySelector('input[name="interest_rate"]').value = uni.interest_rate;
            document.querySelector('input[name="max_amount"]').value = uni.max_amount;
            document.querySelector('input[name="repayment_period"]').value = uni.repayment_period;
            document.querySelector('input[name="contact_info"]').value = uni.contact_info;
            document.querySelector('textarea[name="requirements"]').value = uni.requirements;
            
            const modal = new bootstrap.Modal(document.getElementById('addUniversityModal'));
            modal.show();
        }

        // Reset form when modal is hidden
        document.getElementById('addUniversityModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Add New University';
            document.getElementById('formAction').value = 'add';
            document.getElementById('universityId').value = '';
            document.getElementById('universityForm').reset();
        });
    </script>
</body>
</html>
