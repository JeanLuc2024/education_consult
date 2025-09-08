@echo off
echo Setting up git configuration...
git config core.pager ""
git config --global core.pager ""

echo Adding files to git...
git add .

echo Committing changes...
git commit -m "Add dynamic destination management system and fix contact redirects

- Created admin-destinations.php for managing destinations (add/edit/delete)
- Added get-destinations.php API endpoint for dynamic loading  
- Updated index.html and destinations.html to load destinations dynamically
- Fixed contact button redirects to point to index.html#contact
- Added destinations management to admin dashboard sidebar
- Implemented database-driven destination system with features and sorting"

echo Setting up remote repository...
git remote add origin https://github.com/JeanLuc2024/education_consult.git

echo Pushing to GitHub...
git push -u origin main

echo Done! Changes pushed to GitHub successfully.
pause
