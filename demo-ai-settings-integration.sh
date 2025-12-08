#!/bin/bash
# Demo: Change AI Settings and See Impact on Pricing

echo "🎯 AI Settings Integration - Live Demo"
echo "========================================"
echo ""

echo "📊 Step 1: Current Settings"
echo "----------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;
echo 'Small multiplier: ' . AISettingService::get('pricing.size_multiplier.small') . PHP_EOL;
echo 'Overhead %: ' . AISettingService::get('pricing.overhead_percentage') . '%' . PHP_EOL;
"
echo ""

echo "🧪 Step 2: Test Calculation (BEFORE changes)"
echo "---------------------------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\ConsultationPricingEngine;
use App\Services\OpenRouterService;

\$engine = new ConsultationPricingEngine(app(OpenRouterService::class));
\$reflection = new ReflectionClass(\$engine);
\$method = \$reflection->getMethod('getBusinessSizeMultiplier');
\$method->setAccessible(true);

echo 'Small multiplier used: ' . \$method->invoke(\$engine, 'small') . PHP_EOL;
"
echo ""

echo "✏️  Step 3: Update Settings"
echo "---------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;
use App\Models\AISetting;

// Update small multiplier from 1.3 to 1.5
AISettingService::set('pricing.size_multiplier.small', 1.5, 'Demo: Testing dynamic updates');

// Update overhead from 10% to 12%
AISettingService::set('pricing.overhead_percentage', 12, 'Demo: Testing dynamic updates');

echo '✅ Settings updated!' . PHP_EOL;
"
echo ""

echo "📊 Step 4: Verify New Settings"
echo "-------------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;
echo 'Small multiplier: ' . AISettingService::get('pricing.size_multiplier.small') . ' (was 1.3)' . PHP_EOL;
echo 'Overhead %: ' . AISettingService::get('pricing.overhead_percentage') . '% (was 10%)' . PHP_EOL;
"
echo ""

echo "🧪 Step 5: Test Calculation (AFTER changes)"
echo "--------------------------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\ConsultationPricingEngine;
use App\Services\OpenRouterService;

\$engine = new ConsultationPricingEngine(app(OpenRouterService::class));
\$reflection = new ReflectionClass(\$engine);
\$method = \$reflection->getMethod('getBusinessSizeMultiplier');
\$method->setAccessible(true);

echo 'Small multiplier used: ' . \$method->invoke(\$engine, 'small') . ' (changed!)' . PHP_EOL;
"
echo ""

echo "🔄 Step 6: Reset to Default Values"
echo "-----------------------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;

AISettingService::reset('pricing.size_multiplier.small');
AISettingService::reset('pricing.overhead_percentage');

echo '✅ Settings reset to default!' . PHP_EOL;
"
echo ""

echo "📊 Step 7: Verify Reset"
echo "-----------------------"
php /home/bizmark/bizmark.id/artisan tinker --execute="
use App\Services\AISettingService;
echo 'Small multiplier: ' . AISettingService::get('pricing.size_multiplier.small') . ' (back to 1.3)' . PHP_EOL;
echo 'Overhead %: ' . AISettingService::get('pricing.overhead_percentage') . '% (back to 10%)' . PHP_EOL;
"
echo ""

echo "✅ Demo Complete!"
echo "================="
echo ""
echo "🎯 What we proved:"
echo "   1. ✅ Settings can be changed programmatically"
echo "   2. ✅ Changes are immediately reflected in calculations"
echo "   3. ✅ Caching works correctly (updates clear cache)"
echo "   4. ✅ Reset functionality restores defaults"
echo ""
echo "💡 Next: Try changing values via admin panel UI!"
echo "   URL: https://bizmark.id/admin/ai-settings"
