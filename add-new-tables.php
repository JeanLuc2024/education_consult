<?php
// Add new tables for Modern Education Consult Ltd
// Run this file to add new tables for online courses, universities with loans, and social media

require_once 'config/database.php';

try {
    echo "<h2>Adding New Tables for Modern Education Consult Ltd</h2>";
    echo "<p>Adding new tables for enhanced features...</p>";
    
    // Create online courses table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS online_courses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            course_name VARCHAR(255) NOT NULL,
            university_name VARCHAR(255) NOT NULL,
            country VARCHAR(100) NOT NULL,
            duration VARCHAR(100) NOT NULL,
            level VARCHAR(50) NOT NULL,
            description TEXT,
            requirements TEXT,
            tuition_fee DECIMAL(10,2),
            discount_percentage DECIMAL(5,2) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✓ Online courses table created successfully</p>";
    
    // Create universities with study loans table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS universities_with_loans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            university_name VARCHAR(255) NOT NULL,
            country VARCHAR(100) NOT NULL,
            loan_provider VARCHAR(255) NOT NULL,
            loan_type VARCHAR(100) NOT NULL,
            interest_rate DECIMAL(5,2),
            max_amount DECIMAL(12,2),
            repayment_period VARCHAR(100),
            requirements TEXT,
            contact_info TEXT,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✓ Universities with study loans table created successfully</p>";
    
    // Create social media settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS social_media_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50) NOT NULL UNIQUE,
            url VARCHAR(500) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✓ Social media settings table created successfully</p>";
    
    // Insert default social media settings
    $pdo->exec("
        INSERT IGNORE INTO social_media_settings (platform, url) VALUES
        ('facebook', 'https://facebook.com/moderneducationconsult'),
        ('instagram', 'https://instagram.com/moderneducationconsult'),
        ('email', 'moderneducationconsult2025@gmail.com')
    ");
    echo "<p>✓ Default social media settings inserted</p>";
    
    // Add age column to inquiries table if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE inquiries ADD COLUMN age INT AFTER phone");
        echo "<p>✓ Age column added to inquiries table</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p>✓ Age column already exists in inquiries table</p>";
        } else {
            throw $e;
        }
    }
    
    // Insert sample online courses
    $pdo->exec("
        INSERT IGNORE INTO online_courses (course_name, university_name, country, duration, level, description, requirements, tuition_fee, discount_percentage) VALUES
        ('Computer Science Online', 'University of London', 'UK', '3 years', 'Bachelor', 'Complete online computer science degree program', 'High school diploma, English proficiency', 15000.00, 10.00),
        ('MBA Online', 'University of Toronto', 'Canada', '2 years', 'Master', 'Flexible online MBA program', 'Bachelor degree, 2 years work experience', 25000.00, 15.00),
        ('Data Science Online', 'MIT', 'USA', '1 year', 'Certificate', 'Intensive data science certification', 'Basic programming knowledge', 8000.00, 5.00),
        ('Business Administration', 'University of Melbourne', 'Australia', '3 years', 'Bachelor', 'Online business administration degree', 'High school diploma', 18000.00, 12.00)
    ");
    echo "<p>✓ Sample online courses inserted</p>";
    
    // Insert sample universities with study loans
    $pdo->exec("
        INSERT IGNORE INTO universities_with_loans (university_name, country, loan_provider, loan_type, interest_rate, max_amount, repayment_period, requirements, contact_info) VALUES
        ('University of Toronto', 'Canada', 'TD Bank', 'Student Loan', 4.5, 50000.00, '10 years after graduation', 'Canadian citizen or PR, good credit score', 'tdbank.com/student-loans'),
        ('University of London', 'UK', 'Student Finance England', 'Government Loan', 6.3, 40000.00, '30 years or until paid off', 'UK resident, course eligibility', 'gov.uk/student-finance'),
        ('University of Melbourne', 'Australia', 'Commonwealth Bank', 'Education Loan', 5.2, 60000.00, '15 years after graduation', 'Australian citizen, course approval', 'commbank.com.au/education-loans'),
        ('MIT', 'USA', 'Sallie Mae', 'Private Student Loan', 7.8, 100000.00, '20 years after graduation', 'US citizen or co-signer, credit check', 'salliemae.com'),
        ('Bogazici University', 'Turkey', 'Turkiye Is Bankasi', 'Education Loan', 3.5, 25000.00, '8 years after graduation', 'Turkish citizen or resident, good academic record', 'isbank.com.tr/education-loans')
    ");
    echo "<p>✓ Sample universities with study loans inserted</p>";
    
    // Insert Turkey destination
    $pdo->exec("
        INSERT IGNORE INTO destinations (name, slug, country, description, is_active) VALUES
        ('Turkey', 'turkey', 'Turkey', 'Study in Turkey with world-class universities and affordable education', 1)
    ");
    echo "<p>✓ Turkey destination added</p>";
    
    // Create services table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            icon VARCHAR(100) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p>✓ Services table created successfully</p>";
    
    // Insert default services
    $pdo->exec("
        INSERT IGNORE INTO services (service_name, description, icon, is_active) VALUES
        ('Study and Work Abroad', 'Complete assistance in university selection, application preparation, and program matching based on your academic profile and career goals.', 'bi-mortarboard', 1),
        ('Scholarship Assistance', 'Expert help in identifying and applying for scholarships, grants, and financial aid opportunities to make your education affordable.', 'bi-currency-dollar', 1),
        ('Student Visa Support', 'Comprehensive visa application support including document preparation, interview guidance, and immigration compliance assistance.', 'bi-passport', 1),
        ('After Visa Services', 'Comprehensive support after visa approval including airport pickup, accommodation assistance, and settling-in support for a smooth transition.', 'bi-check-circle', 1),
        ('Study Loan Assistance', 'Help you find and apply for study loans from various financial institutions and universities to fund your education abroad.', 'bi-bank', 1),
        ('Tuition Fee Discounts', 'Access to exclusive tuition fee discounts and early bird offers from partner universities to make your education more affordable.', 'bi-percent', 1)
    ");
    echo "<p>✓ Default services inserted</p>";
    
    echo "<h3>New Tables Setup Complete!</h3>";
    echo "<p>New features are now available:</p>";
    echo "<ul>";
    echo "<li>Online courses management</li>";
    echo "<li>Universities with study loans</li>";
    echo "<li>Social media settings management</li>";
    echo "<li>Age field in contact form</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h2>Database Setup Error</h2>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>
