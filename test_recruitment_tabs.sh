#!/bin/bash

echo "=========================================="
echo "RECRUITMENT TAB SYSTEM - VERIFICATION"
echo "=========================================="
echo ""

echo "1. Checking file structure..."
echo "   applications.blade.php lines:"
wc -l resources/views/admin/recruitment/tabs/applications.blade.php

echo ""
echo "2. Checking tab-pane structure..."
grep -c "tab-pane" resources/views/admin/recruitment/index.blade.php
echo "   Found tab-pane references"

echo ""
echo "3. Checking JavaScript TabManager..."
grep -c "TabManager" resources/views/admin/recruitment/index.blade.php
echo "   Found TabManager references"

echo ""
echo "4. Testing controller..."
php artisan tinker --execute="
try {
    \$controller = new App\Http\Controllers\Admin\RecruitmentController();
    \$request = new Illuminate\Http\Request(['tab' => 'jobs']);
    \$view = \$controller->index(\$request);
    echo '   ✓ Jobs tab: OK' . PHP_EOL;
    
    \$request2 = new Illuminate\Http\Request(['tab' => 'applications']);
    \$view2 = \$controller->index(\$request2);
    echo '   ✓ Applications tab: OK' . PHP_EOL;
    
    echo '   Data: ' . count(\$view2->getData()['applications']) . ' applications found' . PHP_EOL;
} catch (Exception \$e) {
    echo '   ✗ Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "5. Checking data-tab attributes..."
grep -o 'data-tab="[^"]*"' resources/views/admin/recruitment/index.blade.php

echo ""
echo "=========================================="
echo "✓ VERIFICATION COMPLETE"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Open browser: /admin/recruitment"
echo "2. Check console for debug logs"
echo "3. Test tab switching"
echo "4. Verify no refresh needed"
echo ""
