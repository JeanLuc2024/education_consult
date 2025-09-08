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
                        <a href="#replies" class="sidebar-link" onclick="showSection('replies', this)">
                            <i class="bi bi-chat-dots me-2"></i>
                            Admin Replies
                        </a>
                        <a href="#profile" class="sidebar-link" onclick="showSection('profile', this)">
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
                                                <option value="Malaysia">Malaysia</option>
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
                            <!-- Applications Table -->
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
                                            <td>
                                                <span class="badge bg-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="viewApplication(<?php echo $app['id']; ?>)">
                                                    <i class="bi bi-eye me-1"></i>View Details
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" onclick="editApplication(<?php echo $app['id']; ?>)">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteApplication(<?php echo $app['id']; ?>)">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDocument(<?php echo $doc['id']; ?>)">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Replies Section -->
                    <div id="replies-section" class="student-section" style="display: none;">
                        <h3>Admin Messages</h3>
                        <?php
                        // Get admin messages for this student
                        $stmt = $pdo->prepare("
                            SELECT am.*, u.first_name, u.last_name 
                            FROM admin_messages am 
                            JOIN users u ON am.admin_id = u.id 
                            WHERE am.student_id = ? 
                            ORDER BY am.created_at DESC
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        $messages = $stmt->fetchAll();
                        
                        if (empty($messages)) {
                            echo '<div class="alert alert-info">No messages from admin yet.</div>';
                        } else {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-striped">';
                            echo '<thead><tr><th>Subject</th><th>Message</th><th>From</th><th>Date</th><th>Actions</th></tr></thead>';
                            echo '<tbody>';
                            foreach ($messages as $message) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($message['subject']) . '</td>';
                                echo '<td>' . htmlspecialchars($message['message']) . '</td>';
                                echo '<td>' . htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) . '</td>';
                                echo '<td>' . date('M d, Y H:i', strtotime($message['created_at'])) . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-sm btn-outline-danger" onclick="deleteMessage(' . $message['id'] . ')">';
                                echo '<i class="bi bi-trash"></i> Delete';
                                echo '</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table></div>';
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
                                            
                                            <hr>
                                            <h6>Change Password (Optional)</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Current Password</label>
                                                <input type="password" class="form-control" name="current_password" placeholder="Enter current password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">New Password</label>
                                                <input type="password" class="form-control" name="new_password" placeholder="Enter new password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm new password">
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

    <!-- Application Details Modal -->
    <div class="modal fade" id="applicationDetailsModal" tabindex="-1" aria-labelledby="applicationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applicationDetailsModalLabel">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="applicationDetailsBody">
                    <!-- Application details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                'replies': 'Admin Replies',
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
            // Fetch application details via AJAX
            fetch(`get-application-details.php?id=${appId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showApplicationModal(data.application);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error loading application details');
                });
        }

        function showApplicationModal(app) {
            const modal = new bootstrap.Modal(document.getElementById('applicationDetailsModal'));
            document.getElementById('applicationDetailsBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>University Information</h6>
                        <p><strong>University:</strong> ${app.university_name}</p>
                        <p><strong>Program:</strong> ${app.program_name}</p>
                        <p><strong>Country:</strong> ${app.country}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Application Details</h6>
                        <p><strong>Degree:</strong> ${app.academic_degree || 'Not specified'}</p>
                        <p><strong>Start Year:</strong> ${app.start_year || 'Not specified'}</p>
                        <p><strong>Semester:</strong> ${app.start_semester || 'Not specified'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Status & Timeline</h6>
                        <p><strong>Status:</strong> <span class="badge bg-${app.status === 'approved' ? 'success' : (app.status === 'rejected' ? 'danger' : 'warning')}">${app.status.replace('_', ' ').toUpperCase()}</span></p>
                        <p><strong>Applied:</strong> ${new Date(app.created_at).toLocaleDateString()}</p>
                        <p><strong>Last Updated:</strong> ${new Date(app.updated_at).toLocaleDateString()}</p>
                    </div>
                </div>
                ${app.notes ? `<div class="row mt-3"><div class="col-12"><h6>Notes</h6><p>${app.notes}</p></div></div>` : ''}
            `;
            modal.show();
        }

        function editApplication(appId) {
            // Fetch application details and populate form
            fetch(`get-application-details.php?id=${appId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const app = data.application;
                        // Populate form fields
                        document.querySelector('select[name="country"]').value = app.country;
                        document.querySelector('input[name="university_name"]').value = app.university_name;
                        document.querySelector('input[name="program_name"]').value = app.program_name;
                        document.querySelector('select[name="academic_degree"]').value = app.academic_degree;
                        document.querySelector('select[name="start_year"]').value = app.start_year;
                        document.querySelector('select[name="start_semester"]').value = app.start_semester;
                        document.querySelector('textarea[name="notes"]').value = app.notes || '';
                        
                        // Show form and add hidden field for update
                        const form = document.getElementById('newApplicationForm');
                        form.innerHTML += `<input type="hidden" name="application_id" value="${appId}">`;
                        form.querySelector('button[type="submit"]').innerHTML = '<i class="bi bi-check-circle me-1"></i>Update Application';
                        
                        document.getElementById('application-form').style.display = 'block';
                        document.getElementById('application-form').scrollIntoView({ behavior: 'smooth' });
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error loading application details');
                });
        }

        function deleteApplication(appId) {
            if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
                fetch('delete-application.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: appId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Application deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting application');
                });
            }
        }

        function deleteDocument(docId) {
            if (confirm('Are you sure you want to delete this document?')) {
                fetch('delete-document.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: docId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Document deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting document');
                });
            }
        }

        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message?')) {
                fetch('delete-message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({id: messageId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Message deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting message');
                });
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
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error uploading document: ' + error.message);
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