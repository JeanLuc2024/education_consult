<?php
// Database fix script for Modern Education Consult
require_once 'config/database.php';

echo "<h2>Fixing Database Issues</h2>";

try {
    // Check if services table exists and has correct structure
    $stmt = $pdo->query("SHOW TABLES LIKE 'services'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p>Creating services table...</p>";
        
        // Create services table
        $pdo->exec("
            CREATE TABLE services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                service_name VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(100) NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "<p>✅ Services table created</p>";
        
        // Insert default services
        $pdo->exec("
            INSERT INTO services (service_name, description, icon, is_active) VALUES
            ('Study and Work Abroad', 'Complete assistance in university selection, application preparation, and program matching based on your academic profile and career goals.', 'bi-mortarboard', 1),
            ('Scholarship Assistance', 'Expert help in identifying and applying for scholarships, grants, and financial aid opportunities to make your education affordable.', 'bi-currency-dollar', 1),
            ('Student Visa Support', 'Comprehensive visa application support including document preparation, interview guidance, and immigration compliance assistance.', 'bi-passport', 1),
            ('After Visa Services', 'Comprehensive support after visa approval including airport pickup, accommodation assistance, and settling-in support for a smooth transition.', 'bi-check-circle', 1),
            ('Study Loan Assistance', 'Help you find and apply for study loans from various financial institutions and universities to fund your education abroad.', 'bi-bank', 1),
            ('Tuition Fee Discounts', 'Access to exclusive tuition fee discounts and early bird offers from partner universities to make your education more affordable.', 'bi-percent', 1)
        ");
        echo "<p>✅ Default services inserted</p>";
        
    } else {
        echo "<p>Services table exists, checking structure...</p>";
        
        // Check if service_name column exists
        $stmt = $pdo->query("DESCRIBE services");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('service_name', $columns)) {
            echo "<p>Adding missing columns to services table...</p>";
            $pdo->exec("ALTER TABLE services ADD COLUMN service_name VARCHAR(255) NOT NULL AFTER id");
            $pdo->exec("ALTER TABLE services ADD COLUMN description TEXT NOT NULL AFTER service_name");
            $pdo->exec("ALTER TABLE services ADD COLUMN icon VARCHAR(100) NOT NULL AFTER description");
            $pdo->exec("ALTER TABLE services ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER icon");
            echo "<p>✅ Missing columns added</p>";
        } else {
            echo "<p>✅ Services table structure is correct</p>";
        }
    }
    
    // Check other required tables
    $requiredTables = ['universities_with_loans', 'online_courses', 'social_media_settings'];
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if (!$stmt->fetch()) {
            echo "<p>Creating $table table...</p>";
            
            switch ($table) {
                case 'universities_with_loans':
                    $pdo->exec("
                        CREATE TABLE universities_with_loans (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            university_name VARCHAR(255) NOT NULL,
                            country VARCHAR(100) NOT NULL,
                            loan_provider VARCHAR(255) NOT NULL,
                            loan_type VARCHAR(100) NOT NULL,
                            interest_rate DECIMAL(5, 2) NOT NULL,
                            max_amount DECIMAL(10, 2) NOT NULL,
                            repayment_period VARCHAR(100) NOT NULL,
                            requirements TEXT,
                            contact_info VARCHAR(255),
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                    ");
                    break;
                    
                case 'online_courses':
                    $pdo->exec("
                        CREATE TABLE online_courses (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            course_name VARCHAR(255) NOT NULL,
                            university VARCHAR(255) NOT NULL,
                            country VARCHAR(100) NOT NULL,
                            duration VARCHAR(50) NOT NULL,
                            degree_type VARCHAR(100) NOT NULL,
                            description TEXT,
                            requirements TEXT,
                            tuition_fee DECIMAL(10, 2) NOT NULL,
                            discount_percentage DECIMAL(5, 2) DEFAULT 0,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                    ");
                    break;
                    
                case 'social_media_settings':
                    $pdo->exec("
                        CREATE TABLE social_media_settings (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            platform VARCHAR(50) NOT NULL,
                            url VARCHAR(500) NOT NULL,
                            is_active TINYINT(1) DEFAULT 1,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                    ");
                    break;
            }
            echo "<p>✅ $table table created</p>";
        }
    }
    
    echo "<div class='alert alert-success'>";
    echo "<h3>✅ Database Fix Complete!</h3>";
    echo "<p>All required tables and columns have been created/updated successfully.</p>";
    echo "<p><a href='admin-services.php' class='btn btn-primary'>Test Admin Services</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h3>❌ Database Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 40px; }
.alert { padding: 20px; margin: 20px 0; border-radius: 5px; }
.alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
.alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
.btn { padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; display: inline-block; background: #007bff; color: white; }
</style>