# Modern Education Consult Ltd Website

A complete education consultancy website built on the iLanding Bootstrap template, customized for Modern Education Consult Ltd - a company specializing in study abroad guidance, scholarship assistance, and student visa support.

## Features

### Frontend
- **Responsive Design**: Mobile-first approach with Bootstrap 5.3.3
- **Modern UI**: Clean, professional design with smooth animations
- **Education-Focused Content**: Tailored for international education consultancy
- **Student Portal**: Login system for students to track applications
- **Admin Dashboard**: Management interface for administrators
- **Blog System**: Educational content and resources
- **Destinations Page**: Information about study destinations
- **Contact Forms**: Working contact and consultation forms

### Backend
- **PHP/MySQL**: Server-side functionality with PDO
- **Secure Authentication**: Password hashing and session management
- **File Upload System**: Secure document upload for students
- **Database Management**: Complete schema for all features
- **Form Validation**: Server-side validation and sanitization

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional, for dependencies)

### Setup Instructions

1. **Clone/Download the project**
   ```bash
   # If using git
   git clone [repository-url]
   cd modern-education-consult
   ```

2. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE modern_education_consult;
   exit
   
   # Import schema
   mysql -u root -p modern_education_consult < database/schema.sql
   
   # Import seed data
   mysql -u root -p modern_education_consult < database/seed.sql
   ```

3. **Configure Database Connection**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'modern_education_consult');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **Set File Permissions**
   ```bash
   # Make uploads directory writable
   mkdir uploads
   chmod 755 uploads
   
   # Ensure PHP can write to uploads
   chown www-data:www-data uploads
   ```

5. **Configure Web Server**
   - Point document root to project directory
   - Ensure PHP is enabled
   - Set up URL rewriting if needed

6. **Test Installation**
   - Visit `http://your-domain.com`
   - Check contact form functionality
   - Test student portal login (admin@moderneducationconsult.com / admin123)

## Default Login Credentials

### Admin Account
- **Email**: admin@moderneducationconsult.com
- **Password**: admin123

### Student Account
- **Email**: student@example.com
- **Password**: admin123

## File Structure

```
modern-education-consult/
├── assets/
│   ├── css/
│   │   └── main.css (Custom styles)
│   ├── js/
│   │   └── main.js (JavaScript functionality)
│   ├── img/ (Images and media)
│   └── vendor/ (Third-party libraries)
├── config/
│   └── database.php (Database configuration)
├── database/
│   ├── schema.sql (Database structure)
│   └── seed.sql (Sample data)
├── forms/
│   └── contact.php (Contact form handler)
├── uploads/ (Document uploads - create this)
├── index.html (Homepage)
├── destinations.html (Study destinations)
├── blog.html (Blog listing)
├── student-portal.php (Student login)
├── student-dashboard.php (Student dashboard)
├── logout.php (Logout script)
└── README.md
```

## Key Pages

- **Homepage** (`index.html`): Main landing page with services and testimonials
- **Destinations** (`destinations.html`): Study destination information
- **Blog** (`blog.html`): Educational content and resources
- **Student Portal** (`student-portal.php`): Student login and dashboard
- **Contact Forms**: Working contact and consultation forms

## Database Tables

- `users`: Student and admin accounts
- `student_profiles`: Student information
- `applications`: Student applications
- `documents`: Uploaded files
- `inquiries`: Contact form submissions
- `services`: Service offerings
- `destinations`: Study destinations
- `universities`: University information
- `testimonials`: Student testimonials
- `posts`: Blog posts
- `settings`: Site configuration

## Security Features

- Password hashing with `password_hash()`
- SQL injection prevention with PDO prepared statements
- Input validation and sanitization
- Secure file upload restrictions
- Session management
- CSRF protection (can be added)

## Customization

### Colors and Branding
Edit CSS variables in `assets/css/main.css`:
```css
:root {
  --accent-color: #0d83fd; /* Primary blue */
  --heading-color: #2d465e; /* Dark blue */
  --default-color: #212529; /* Dark gray */
}
```

### Content Updates
- Edit HTML files for content changes
- Update database for dynamic content
- Modify PHP files for functionality changes

## Support

For technical support or customization requests, contact:
- **Email**: info@moderneducationconsult.com
- **Phone**: +1 (555) 123-4567

## License

This project is based on the iLanding Bootstrap template by BootstrapMade.com and is customized for Modern Education Consult Ltd.

---

**Modern Education Consult Ltd** - Your trusted partner in international education since 2014.
