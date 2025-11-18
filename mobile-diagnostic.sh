#!/bin/bash

echo "🔍 MOBILE ADMIN DIAGNOSTIC TOOL"
echo "================================"
echo ""

echo "📊 1. Checking Routes..."
php artisan route:list | grep "mobile.dashboard" | head -n 1
if [ $? -eq 0 ]; then
    echo "   ✅ Mobile routes registered"
else
    echo "   ❌ Mobile routes NOT found"
fi
echo ""

echo "📁 2. Checking Files..."
if [ -f "app/Http/Controllers/Mobile/DashboardController.php" ]; then
    echo "   ✅ DashboardController exists"
else
    echo "   ❌ DashboardController missing"
fi

if [ -f "resources/views/mobile/dashboard/index.blade.php" ]; then
    echo "   ✅ Dashboard view exists"
else
    echo "   ❌ Dashboard view missing"
fi

if [ -f "resources/views/mobile/layouts/app.blade.php" ]; then
    echo "   ✅ Layout exists"
else
    echo "   ❌ Layout missing"
fi
echo ""

echo "📜 3. Checking Error Log..."
if [ -f "storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(grep -c "ERROR" storage/logs/laravel.log 2>/dev/null || echo "0")
    if [ "$ERROR_COUNT" -gt 0 ]; then
        echo "   ⚠️  Found $ERROR_COUNT errors in log"
        echo "   Last 3 errors:"
        grep "production.ERROR" storage/logs/laravel.log | tail -n 3 | sed 's/^/      /'
    else
        echo "   ✅ No errors in log"
    fi
else
    echo "   ✅ No log file (clean)"
fi
echo ""

echo "🌐 4. Testing Endpoint..."
STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://bizmark.id/m)
echo "   HTTP Status: $STATUS"
if [ "$STATUS" = "302" ]; then
    echo "   ℹ️  Redirecting (probably not logged in)"
elif [ "$STATUS" = "200" ]; then
    echo "   ✅ Success!"
elif [ "$STATUS" = "500" ]; then
    echo "   ❌ Internal Server Error"
else
    echo "   ⚠️  Unexpected status: $STATUS"
fi
echo ""

echo "🔧 5. Testing with Auth..."
php artisan tinker --execute="
\$user = App\Models\User::first();
if (\$user) {
    echo '   ✅ Test user found (ID: ' . \$user->id . ')' . PHP_EOL;
    auth()->login(\$user);
    try {
        \$controller = app(\App\Http\Controllers\Mobile\DashboardController::class);
        \$result = \$controller->index();
        if (\$result) {
            echo '   ✅ Controller executed successfully' . PHP_EOL;
        }
    } catch (\Exception \$e) {
        echo '   ❌ Controller error: ' . \$e->getMessage() . PHP_EOL;
    }
} else {
    echo '   ❌ No users in database' . PHP_EOL;
}
"
echo ""

echo "📈 6. Cache Status..."
php artisan config:show app.debug 2>/dev/null | grep -q "true" && echo "   Debug Mode: ON" || echo "   Debug Mode: OFF"
echo ""

echo "================================"
echo "✅ Diagnostic Complete!"
echo ""
echo "💡 Next Steps:"
echo "   1. If status is 302: You need to LOGIN first"
echo "   2. If status is 500: Check the error details above"
echo "   3. If no errors shown: Clear browser cache (Ctrl+Shift+R)"
echo ""
