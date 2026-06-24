@echo off
REM ========================================
REM KING RANGEMENT - QUICK START
REM ========================================

echo.
echo ðŸŽ¯ KING RANGEMENT - E-Commerce Platform
echo ========================================
echo.

cd c:\Users\TSU\Desktop\dressingue

echo [1/4] Installing dependencies...
composer install

echo.
echo [2/4] Configuring environment...
if not exist .env (
    copy .env.example .env
    echo .env created from .env.example
) else (
    echo .env already exists
)

echo.
echo [3/4] Generating app key...
php artisan key:generate

echo.
echo [4/4] Setting up database...
php artisan migrate
echo Running seeders...
php artisan db:seed

echo.
echo âœ… SETUP COMPLETE!
echo.
echo ðŸš€ To start the server, run:
echo    php artisan serve
echo.
echo ðŸ“ Then access: http://localhost:8000
echo.
echo ðŸ“„ Documentation:
echo    - ECOMMERCE_README.md
echo    - DEPLOYMENT_GUIDE.md
echo    - EXECUTION_REPORT.md
echo.
echo ðŸ˜Š Happy coding!
echo.

pause
