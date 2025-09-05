# Quick Installation Guide

## Database Setup

### Option 1: Automatic Setup (Recommended)
1. Open your web browser
2. Navigate to: `http://localhost/iLanding/setup-database.php`
3. The script will automatically create the database and tables
4. You'll see a success message when complete

### Option 2: Manual Setup
1. Open phpMyAdmin or MySQL command line
2. Create database: `modern_education_consult`
3. Import `database/schema.sql`
4. Import `database/seed.sql`

## Default Login Credentials

### Admin Account
- **Email**: admin@moderneducationconsult.com
- **Password**: admin123

### Student Account  
- **Email**: student@example.com
- **Password**: admin123

## Testing the Website

1. **Homepage**: Visit `index.html`
2. **Contact Form**: Test the contact form at the bottom of the homepage
3. **Student Portal**: Visit `student-portal.php`
4. **Destinations**: Visit `destinations.html`
5. **Blog**: Visit `blog.html`

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running
- Check database credentials in `config/database.php`
- Run the setup script: `setup-database.php`

### Contact Form Not Working
- Check if database is created
- Verify PHP mail configuration
- Check error logs

## File Structure
```
iLanding/
├── index.html (Homepage)
├── destinations.html (Study destinations)
├── blog.html (Blog)
├── student-portal.php (Student login)
├── student-dashboard.php (Student dashboard)
├── setup-database.php (Database setup)
├── config/database.php (Database config)
├── database/
│   ├── schema.sql (Database structure)
│   └── seed.sql (Sample data)
└── forms/contact.php (Contact form handler)
```

## Support
If you encounter any issues, check:
1. MySQL server is running
2. PHP is enabled
3. File permissions are correct
4. Database credentials are correct
