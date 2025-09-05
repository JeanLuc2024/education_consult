<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: student-portal.php');
    exit;
}

// Get user applications
$stmt = $pdo->prepare("
    SELECT a.*, u.first_name, u.last_name 
    FROM applications a 
    JOIN users u ON a.student_id = u.id 
    WHERE a.student_id = ? 
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();

// Get uploaded documents
$stmt = $pdo->prepare("
    SELECT * FROM documents 
    WHERE student_id = ? 
    ORDER BY uploaded_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$documents = $stmt->fetchAll();

// Get user profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Modern Education Consult</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        .student-sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2d465e, #0d83fd);
        }
        .student-content {
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
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .application-card {
            border-left: 4px solid #0d83fd;
        }
        .document-upload {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .document-upload:hover {
            border-color: #0d83fd;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 student-sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="bi bi-person-circle me-2"></i>
                        Student Portal
                    </h4>
                    <nav class="nav flex-column">
                        <a href="#applications" class="sidebar-link active" onclick="showSection('applications', this)">
                            <i class="bi bi-file-text me-2"></i>
                            My Applications
                        </a>
                        <a href="#documents" class="sidebar-link" onclick="showSection('documents', this)">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Documents
                        </a>
                        <a href="#profile" class="sidebar-link" onclick="showSection('profile', this)">
                        <a href="#replies" class="sidebar-link" onclick="showSection('replies', this)">
                            <i class="bi bi-chat-dots me-2"></i>
                            Admin Replies
                        </a>
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
            <div class="col-md-9 col-lg-10 student-content">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 id="section-title">My Applications</h2>
                        <div class="d-flex align-items-center">
                            <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="index.html" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-house me-1"></i>
                                View Website
                            </a>
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div id="applications-section" class="student-section">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3>My Applications</h3>
                            <button class="btn btn-primary" onclick="startNewApplication()">
                                <i class="bi bi-plus-circle me-1"></i>
                                Start New Application
                            </button>
                        </div>
                        
                        <!-- Application Form (Hidden by default) -->
                        <div id="application-form" class="card mb-4" style="display: none;">
                            <div class="card-header">
                                <h5>New Application</h5>
                            </div>
                            <div class="card-body">
                                <form id="newApplicationForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Desired Country *</label>
                                            <select class="form-select" name="country" required>
                                                <option value="">Select Country</option>
                                                <option value="Canada">Canada</option>
                                                <option value="United Kingdom">United Kingdom</option>
                                                <option value="United States">United States</option>
                                                <option value="Australia">Australia</option>
                                                <option value="Germany">Germany</option>
                                                <option value="France">France</option>
                                                <option value="Netherlands">Netherlands</option>
                                                <option value="Sweden">Sweden</option>
                                                <option value="Ireland">Ireland</option>
                                                <option value="China">China</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">University Name *</label>
                                            <input type="text" class="form-control" name="university_name" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Program Name *</label>
                                            <input type="text" class="form-control" name="program_name" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Academic Degree Completed *</label>
                                            <select class="form-select" name="academic_degree" required>
                                                <option value="">Select Degree</option>
                                                <option value="High School">High School</option>
                                                <option value="Bachelor's">Bachelor's</option>
                                                <option value="Master's">Master's</option>
                                                <option value="PhD">PhD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Intended Start Year *</label>
                                            <select class="form-select" name="start_year" required>
                                                <option value="">Select Year</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Intended Start Semester</label>
                                            <select class="form-select" name="start_semester">
                                                <option value="Fall">Fall</option>
                                                <option value="Spring">Spring</option>
                                                <option value="Summer">Summer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Additional Notes</label>
                                        <textarea class="form-control" name="notes" rows="3" placeholder="Any additional information about your application..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Submit Application
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="hideApplicationForm()">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle fs-1 text-primary mb-3"></i>
                                <h4>No Applications Yet</h4>
                                <p>Start your study abroad journey by creating your first application.</p>
                                <button class="btn btn-primary btn-lg" onclick="showApplicationForm()">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Start Application
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($applications as $app): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card application-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($app['university_name']); ?></h5>
                                                    <span class="badge bg-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                    </span>
                                                </div>
                                                <p class="card-text"><strong>Program:</strong> <?php echo htmlspecialchars($app['program_name']); ?></p>
                                                <p class="card-text"><strong>Country:</strong> <?php echo htmlspecialchars($app['country']); ?></p>
                                                <p class="card-text"><small class="text-muted">Applied: <?php echo date('M d, Y', strtotime($app['created_at'])); ?></small></p>
                                                <div class="mt-3">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewApplication(<?php echo $app['id']; ?>)">
                                                        <i class="bi bi-eye me-1"></i>
                                                        View Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Documents Section -->
                    <div id="documents-section" class="student-section" style="display: none;">
                        <h3>Document Management</h3>
                        
                        <!-- Upload Area -->
                        <div class="document-upload mb-4">
                            <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                            <h5>Upload Documents</h5>
                            <p class="text-muted">Upload your academic documents, passport, and other required files</p>
                            <form id="documentUploadForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <select class="form-select" name="document_type" required>
                                        <option value="">Select Document Type</option>
                                        <option value="passport">Passport</option>
                                        <option value="academic_transcript">Academic Transcript</option>
                                        <option value="degree_certificate">Degree Certificate</option>
                                        <option value="english_proficiency">English Proficiency Test</option>
                                        <option value="recommendation_letter">Recommendation Letter</option>
                                        <option value="statement_of_purpose">Statement of Purpose</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">Accepted formats: PDF, JPG, PNG (Max 5MB)</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-1"></i>
                                    Upload Document
                                </button>
                            </form>
                        </div>

                        <!-- Uploaded Documents -->
                        <h5>Uploaded Documents</h5>
                        <?php if (empty($documents)): ?>
                            <div class="alert alert-info">No documents uploaded yet.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Filename</th>
                                            <th>Upload Date</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $doc): ?>
                                            <tr>
                                                <td><?php echo ucfirst(str_replace('_', ' ', $doc['document_type'])); ?></td>
                                                <td><?php echo htmlspecialchars($doc['original_filename']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($doc['uploaded_at'])); ?></td>
                                                <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadDocument(<?php echo $doc['id']; ?>)">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDocument(<?php echo $doc['id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                        <!-- Applications Table (add View Details) -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>University</th>
                                        <th>Program</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['university_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['program_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['country']); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" onclick="showAppDetails(<?php echo htmlspecialchars(json_encode($app), ENT_QUOTES, 'UTF-8'); ?>)">View Details</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
        <!-- Application Details Modal -->
        <div class="modal fade" id="appDetailsModal" tabindex="-1" aria-labelledby="appDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="appDetailsModalLabel">Application Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="appDetailsBody">
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showAppDetails(app) {
                let html = '';
                html += `<strong>University:</strong> ${app.university_name}<br>`;
                html += `<strong>Program:</strong> ${app.program_name}<br>`;
                html += `<strong>Country:</strong> ${app.country}<br>`;
                html += `<strong>Degree:</strong> ${app.academic_degree}<br>`;
                html += `<strong>Status:</strong> ${app.status}<br>`;
                html += `<strong>Applied:</strong> ${app.created_at}<br>`;
                if (app.notes) html += `<strong>Notes:</strong> ${app.notes}<br>`;
                document.getElementById('appDetailsBody').innerHTML = html;
                var modal = new bootstrap.Modal(document.getElementById('appDetailsModal'));
                modal.show();
            }
        </script>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Replies Section -->
                    <div id="replies-section" class="student-section" style="display: none;">
                        <h3>Admin Replies to Your Inquiries</h3>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM inquiries WHERE email = ? AND status = 'contacted' ORDER BY created_at DESC");
                        $stmt->execute([$user['email']]);
                        $replies = $stmt->fetchAll();
                        if (empty($replies)) {
                            echo '<div class="alert alert-info">No replies from admin yet.</div>';
                        } else {
                            echo '<div class="list-group">';
                            foreach ($replies as $reply) {
                                $adminReply = explode('| Admin Reply: ', $reply['subject'])[1] ?? '';
                                echo '<div class="list-group-item">';
                                echo '<strong>Inquiry:</strong> ' . htmlspecialchars($reply['message']) . '<br>';
                                echo '<strong>Admin Reply:</strong> ' . htmlspecialchars($adminReply) . '<br>';
                                echo '<small class="text-muted">Replied on: ' . date('M d, Y', strtotime($reply['created_at'])) . '</small>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>

                    <!-- Profile Section -->
                    <div id="profile-section" class="student-section" style="display: none;">
                        <h3>My Profile</h3>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="profileForm">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">First Name</label>
                                                    <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Last Name</label>
                                                    <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
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
                                        <h5><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                        <hr>
                                        <h6>Account Statistics</h6>
                                        <p><strong>Applications:</strong> <?php echo count($applications); ?></p>
                                        <p><strong>Documents:</strong> <?php echo count($documents); ?></p>
                                        <p><strong>Member Since:</strong> <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
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
        function showSection(sectionName, el) {
            // Remove active class from all sidebar links
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
            });
            // Add active class to clicked link
            if (el) {
                el.classList.add('active');
            }
            // Hide all sections
            document.querySelectorAll('.student-section').forEach(section => {
                section.style.display = 'none';
            });
            // Show selected section
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            // Update section title
            const titles = {
                'applications': 'My Applications',
                'documents': 'Document Management',
                'profile': 'My Profile'
            };
            const titleElement = document.getElementById('section-title');
            if (titleElement && titles[sectionName]) {
                titleElement.textContent = titles[sectionName];
            }
        }

        function startNewApplication() {
            showApplicationForm();
        }

        function showApplicationForm() {
            document.getElementById('application-form').style.display = 'block';
            document.getElementById('application-form').scrollIntoView({ behavior: 'smooth' });
        }

        function hideApplicationForm() {
            document.getElementById('application-form').style.display = 'none';
        }

        function viewApplication(appId) {
            alert('Application details for ID: ' + appId + '\n\nThis feature will show detailed application information.');
        }

        function downloadDocument(docId) {
            alert('Download document ID: ' + docId + '\n\nThis feature will download the document.');
        }

        function deleteDocument(docId) {
            if (confirm('Are you sure you want to delete this document?')) {
                alert('Delete document ID: ' + docId + '\n\nThis feature will delete the document.');
            }
        }

        // Document upload form
        document.getElementById('documentUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('upload-document.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Document uploaded successfully!');
                    this.reset();
                    loadDocuments(); // Reload documents list
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error uploading document. Please try again.');
            });
        });

        // Application form
        document.getElementById('newApplicationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Send to server
            fetch('submit-application.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Application submitted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error submitting application. Please try again.');
            });
        });

        // Profile form
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('update-profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                alert('Error updating profile. Please try again.');
            });
        });

        // Initialize applications section on load
        document.addEventListener('DOMContentLoaded', function() {
            showSection('applications');
        });
    </script>
</body>
</html>