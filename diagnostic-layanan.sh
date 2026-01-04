#!/bin/bash

echo "======================================"
echo "DIAGNOSTIC: Layanan Service Pages"
echo "======================================"
echo ""

echo "1. Checking Config..."
php artisan tinker --execute="
\$services = config('services_data');
echo 'Total services: ' . count(\$services) . PHP_EOL;
foreach (\$services as \$slug => \$service) {
    \$hasLongDesc = isset(\$service['long_description']) ? '✓' : '✗';
    echo \"  [\$hasLongDesc] \$slug\" . PHP_EOL;
}
"

echo ""
echo "2. Checking Routes..."
php artisan route:list | grep -E "(layanan|services)" | grep -v "client"

echo ""
echo "3. Testing ServiceController with different URLs..."

echo "  Testing: /layanan/amdal (no prefix)"
php artisan tinker --execute="
app()->setLocale('id');
\$controller = new App\Http\Controllers\ServiceController();
\$request = Illuminate\Http\Request::create('/layanan/amdal');
try {
    \$result = \$controller->show(\$request, 'amdal');
    echo 'SUCCESS: View = ' . \$result->name() . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "  Testing: /id/layanan/ukl-upl (with /id prefix)"
php artisan tinker --execute="
app()->setLocale('id');
\$controller = new App\Http\Controllers\ServiceController();
\$request = Illuminate\Http\Request::create('/id/layanan/ukl-upl');
try {
    \$result = \$controller->show(\$request, 'ukl-upl');
    echo 'SUCCESS: View = ' . \$result->name() . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "  Testing: /layanan/oss-nib"
php artisan tinker --execute="
app()->setLocale('id');
\$controller = new App\Http\Controllers\ServiceController();
\$request = Illuminate\Http\Request::create('/layanan/oss-nib');
try {
    \$result = \$controller->show(\$request, 'oss-nib');
    \$data = \$result->getData();
    echo 'SUCCESS: View = ' . \$result->name() . PHP_EOL;
    echo 'Service has long_description: ' . (isset(\$data['service']['long_description']) ? 'YES' : 'NO') . PHP_EOL;
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "======================================"
echo "Diagnostic Complete!"
echo "======================================"
