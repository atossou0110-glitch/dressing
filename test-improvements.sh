#!/bin/bash
cd C:\Users\TSU\Desktop\dressingue

echo "📦 Running database migrations..."
php artisan migrate --force

echo ""
echo "🧪 Running tests..."
php artisan test tests/Feature/DashboardTest.php tests/Unit/AuditLogTest.php

echo ""
echo "✅ All done!"
