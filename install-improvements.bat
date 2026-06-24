@echo off
REM Installation script pour les améliorations du dashboard

setlocal enabledelayedexpansion

cd /d C:\Users\TSU\Desktop\dressingue

echo.
echo ======================================
echo  DASHBOARD IMPROVEMENTS INSTALLATION
echo ======================================
echo.

echo [1] Running database migrations...
php artisan migrate --force
if errorlevel 1 (
    echo ❌ Migration failed
    exit /b 1
)
echo ✅ Migrations complete

echo.
echo [2] Running tests...
php artisan test tests/Feature/DashboardTest.php tests/Unit/AuditLogTest.php --no-coverage
if errorlevel 1 (
    echo ⚠️  Some tests failed (check output above)
) else (
    echo ✅ All tests passed
)

echo.
echo [3] Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo ✅ Caches cleared

echo.
echo [4] Running preflight check...
php artisan catalog:preflight
echo ✅ Preflight complete

echo.
echo ======================================
echo  ✅ INSTALLATION COMPLETE!
echo ======================================
echo.
echo 📊 New features available:
echo   • Audit Trail (automatic logging)
echo   • CSV Exports (products, orders, clients, audit)
echo   • Analytics Service (chart data)
echo   • Advanced Dashboard (with exports section)
echo.
echo 🔗 New routes:
echo   • GET /dashboard/audit-logs
echo   • GET /dashboard/export/products
echo   • GET /dashboard/export/orders
echo   • GET /dashboard/export/clients
echo   • GET /dashboard/export/audit-logs
echo.
echo 📖 Documentation: .copilot/session-state/*/DASHBOARD_IMPROVEMENTS.md
echo.
pause
