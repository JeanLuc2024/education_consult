@echo off
git config core.pager ""
git add .
git commit -m "Add dynamic destination management system and fix contact redirects"
git push origin main
echo Changes pushed successfully!
pause
