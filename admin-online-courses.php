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
        $course_name = trim($_POST['course_name'] ?? '');
        $university_name = trim($_POST['university_name'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $tuition_fee = floatval($_POST['tuition_fee'] ?? 0);
        $discount_percentage = floatval($_POST['discount_percentage'] ?? 0);
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO online_courses 
                (course_name, university_name, country, duration, level, description, requirements, tuition_fee, discount_percentage) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$course_name, $university_name, $country, $duration, $level, $description, $requirements, $tuition_fee, $discount_percentage]);
            $success = "Online course added successfully!";
        } catch (Exception $e) {
            $error = "Error adding course: " . $e->getMessage();
        }
    } elseif ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $course_name = trim($_POST['course_name'] ?? '');
        $university_name = trim($_POST['university_name'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $tuition_fee = floatval($_POST['tuition_fee'] ?? 0);
        $discount_percentage = floatval($_POST['discount_percentage'] ?? 0);
        
        try {
            $stmt = $pdo->prepare("
                UPDATE online_courses 
                SET course_name=?, university_name=?, country=?, duration=?, level=?, description=?, requirements=?, tuition_fee=?, discount_percentage=?
                WHERE id=?
            ");
            $stmt->execute([$course_name, $university_name, $country, $duration, $level, $description, $requirements, $tuition_fee, $discount_percentage, $id]);
            $success = "Course updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating course: " . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM online_courses WHERE id=?");
            $stmt->execute([$id]);
            $success = "Course deleted successfully!";
        } catch (Exception $e) {
            $error = "Error deleting course: " . $e->getMessage();
        }
    } elseif ($action === 'toggle_status') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE online_courses SET is_active = NOT is_active WHERE id=?");
            $stmt->execute([$id]);
            $success = "Course status updated successfully!";
        } catch (Exception $e) {
            $error = "Error updating course status: " . $e->getMessage();
        }
    }
}

// Get all online courses
try {
    $stmt = $pdo->query("SELECT * FROM online_courses ORDER BY course_name ASC");
    $courses = $stmt->fetchAll();
} catch (Exception $e) {
    $courses = [];
    $error = "Error fetching courses: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Online Courses - Admin Panel</title>
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
                        <a href="admin-online-courses.php" class="sidebar-link active">
                            <i class="bi bi-laptop me-2"></i>
                            Online Courses
                        </a>
                        <a href="admin-social-media.php" class="sidebar-link">
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
                        <h2>Manage Online Courses</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                            <i class="bi bi-plus-circle me-1"></i>
                            Add New Course
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
                                            <th>Course Name</th>
                                            <th>University</th>
                                            <th>Country</th>
                                            <th>Duration</th>
                                            <th>Level</th>
                                            <th>Tuition Fee</th>
                                            <th>Discount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $course): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                                <td><?php echo htmlspecialchars($course['university_name']); ?></td>
                                                <td><?php echo htmlspecialchars($course['country']); ?></td>
                                                <td><?php echo htmlspecialchars($course['duration']); ?></td>
                                                <td><?php echo htmlspecialchars($course['level']); ?></td>
                                                <td>$<?php echo number_format($course['tuition_fee']); ?></td>
                                                <td><?php echo $course['discount_percentage']; ?>%</td>
                                                <td>
                                                    <span class="badge bg-<?php echo $course['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $course['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="editCourse(<?php echo htmlspecialchars(json_encode($course)); ?>)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-<?php echo $course['is_active'] ? 'warning' : 'success'; ?>">
                                                            <i class="bi bi-<?php echo $course['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
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

    <!-- Add/Edit Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="courseForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add" id="formAction">
                        <input type="hidden" name="id" value="" id="courseId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Name *</label>
                                <input type="text" class="form-control" name="course_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">University Name *</label>
                                <input type="text" class="form-control" name="university_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country *</label>
                                <input type="text" class="form-control" name="country" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration *</label>
                                <input type="text" class="form-control" name="duration" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level *</label>
                                <select class="form-control" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="Certificate">Certificate</option>
                                    <option value="Diploma">Diploma</option>
                                    <option value="Bachelor">Bachelor</option>
                                    <option value="Master">Master</option>
                                    <option value="PhD">PhD</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tuition Fee ($) *</label>
                                <input type="number" step="0.01" class="form-control" name="tuition_fee" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Percentage (%)</label>
                                <input type="number" step="0.01" class="form-control" name="discount_percentage" value="0">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Requirements</label>
                                <textarea class="form-control" name="requirements" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCourse(course) {
            document.getElementById('modalTitle').textContent = 'Edit Course';
            document.getElementById('formAction').value = 'update';
            document.getElementById('courseId').value = course.id;
            document.querySelector('input[name="course_name"]').value = course.course_name;
            document.querySelector('input[name="university_name"]').value = course.university_name;
            document.querySelector('input[name="country"]').value = course.country;
            document.querySelector('input[name="duration"]').value = course.duration;
            document.querySelector('select[name="level"]').value = course.level;
            document.querySelector('input[name="tuition_fee"]').value = course.tuition_fee;
            document.querySelector('input[name="discount_percentage"]').value = course.discount_percentage;
            document.querySelector('textarea[name="description"]').value = course.description;
            document.querySelector('textarea[name="requirements"]').value = course.requirements;
            
            const modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            modal.show();
        }

        // Reset form when modal is hidden
        document.getElementById('addCourseModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Add New Course';
            document.getElementById('formAction').value = 'add';
            document.getElementById('courseId').value = '';
            document.getElementById('courseForm').reset();
        });
    </script>
</body>
</html>
