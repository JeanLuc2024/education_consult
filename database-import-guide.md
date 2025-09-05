# Database Import Guide for phpMyAdmin

## Step 1: Access phpMyAdmin
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Login with your MySQL credentials (usually root with no password)

## Step 2: Create Database
1. Click on "New" in the left sidebar
2. Enter database name: `modern_education_consult`
3. Click "Create"

## Step 3: Import Database Schema
1. Click on the `modern_education_consult` database
2. Click on "Import" tab
3. Click "Choose File" and select `database/schema.sql`
4. Click "Go" to import

## Step 4: Import Sample Data
1. Stay in the same database
2. Click on "Import" tab again
3. Click "Choose File" and select `database/seed.sql`
4. Click "Go" to import

## Step 5: Verify Tables
You should see these tables:
- users
- student_profiles
- applications
- application_status_history
- documents
- inquiries
- services
- destinations
- universities
- testimonials
- posts
- settings

## Step 6: Test Admin Login
- Email: admin@moderneducationconsult.com
- Password: admin123

## Alternative: Use Setup Script
You can also run: `http://localhost/iLanding/setup-database.php`
