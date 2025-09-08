<?php
// Admin Sidebar Component
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin-login.php');
    exit;
}
?>
<div class="col-md-3 col-lg-2 admin-sidebar p-0">
    <div class="p-4">
        <h4 class="text-white mb-4">
            <i class="bi bi-gear-fill me-2"></i>
            Admin Panel
        </h4>
        <nav class="nav flex-column">
            <a href="admin-dashboard.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
            <a href="admin-students.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-students.php' ? 'active' : ''; ?>">
                <i class="bi bi-people me-2"></i>
                Students
            </a>
            <a href="admin-inquiries.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-inquiries.php' ? 'active' : ''; ?>">
                <i class="bi bi-envelope me-2"></i>
                Inquiries
            </a>
            <a href="admin-documents.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-documents.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark me-2"></i>
                Documents
            </a>
            <a href="admin-profile.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-profile.php' ? 'active' : ''; ?>">
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

<style>
.admin-sidebar {
    min-height: 100vh;
    background: linear-gradient(135deg, #2d465e, #0d83fd);
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
    <script>
        function showAdminProfile() {
            window.location.href = 'admin-profile.php';
        }
    </script>
