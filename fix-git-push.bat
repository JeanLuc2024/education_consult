@echo off
echo Fixing Git push connection issues...

echo.
echo Solution 1: Increasing Git buffer size...
git config --global http.postBuffer 524288000

echo.
echo Solution 2: Setting HTTP version...
git config --global http.version HTTP/1.1

echo.
echo Solution 3: Disabling SSL verification temporarily...
git config --global http.sslVerify false

echo.
echo Attempting to push...
git push -u origin main

if %errorlevel% equ 0 (
    echo.
    echo SUCCESS! Changes pushed to GitHub.
    echo Re-enabling SSL verification...
    git config --global http.sslVerify true
) else (
    echo.
    echo Push failed. Trying SSH method...
    git remote set-url origin git@github.com:JeanLuc2024/education_consult.git
    git push -u origin main
)

echo.
echo Done!
pause
