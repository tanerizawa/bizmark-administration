#!/bin/bash
# Test AI Settings Menu and Access

echo "🧪 Testing AI Settings Implementation..."
echo ""

# Test 1: Check routes
echo "1️⃣ Checking routes registration..."
php /home/bizmark/bizmark.id/artisan route:list --path=ai-settings --columns=method,uri,name
echo ""

# Test 2: Check user role
echo "2️⃣ Checking admin user..."
php /home/bizmark/bizmark.id/artisan tinker --execute="
\$user = App\Models\User::where('email', 'pujia@bizmark.id')->first();
if (\$user) {
    echo 'User: ' . \$user->name . PHP_EOL;
    echo 'Email: ' . \$user->email . PHP_EOL;
    echo 'Role: ' . (\$user->role ? \$user->role->name : 'No role') . PHP_EOL;
    echo 'Has admin role: ' . (\$user->hasRole('admin') ? '✅ YES' : '❌ NO') . PHP_EOL;
} else {
    echo '❌ User not found' . PHP_EOL;
}
"
echo ""

# Test 3: Check settings count
echo "3️⃣ Checking AI settings in database..."
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Models\AISetting;
echo 'Total settings: ' . AISetting::count() . PHP_EOL;
echo 'Pricing settings: ' . AISetting::where('category', 'pricing')->count() . PHP_EOL;
echo 'Global settings: ' . AISetting::where('category', 'global')->count() . PHP_EOL;
"
echo ""

# Test 4: Check service
echo "4️⃣ Testing AISettingService..."
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;
echo 'Sample values:' . PHP_EOL;
echo '  - Small multiplier: ' . AISettingService::get('pricing.size_multiplier.small') . PHP_EOL;
echo '  - Overhead %: ' . AISettingService::get('pricing.overhead_percentage') . '%' . PHP_EOL;
echo '  - AI Enabled: ' . (AISettingService::get('global.ai_enabled') ? 'Yes' : 'No') . PHP_EOL;
"
echo ""

# Test 5: Generate URL
echo "5️⃣ Access URL..."
php /home/bizmark/bizmark.id/artisan tinker --execute="
echo route('admin.ai-settings.index');
"
echo ""

echo "✅ All tests completed!"
echo ""
echo "📋 Next steps:"
echo "   1. Login as admin user (pujia@bizmark.id)"
echo "   2. Look for 'AI Settings' menu in sidebar (below 'Pengaturan')"
echo "   3. Click to access the settings page"
echo "   4. Test editing values and saving"
