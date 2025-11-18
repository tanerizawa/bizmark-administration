<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PUSH NOTIFICATION DIAGNOSTIC ===\n\n";

// 1. Check VAPID Keys
echo "1. VAPID Configuration:\n";
echo "   - Public Key: " . (config('webpush.vapid.public_key') ? '✓ Set' : '✗ Missing') . "\n";
echo "   - Private Key: " . (config('webpush.vapid.private_key') ? '✓ Set' : '✗ Missing') . "\n";
echo "   - Subject: " . (config('webpush.vapid.subject') ?: '✗ Missing') . "\n";
echo "\n";

// 2. Check Database Table
echo "2. Database Table:\n";
try {
    $tableExists = Schema::hasTable('push_subscriptions');
    echo "   - Table exists: " . ($tableExists ? '✓ Yes' : '✗ No') . "\n";
    
    if ($tableExists) {
        $count = DB::table('push_subscriptions')->count();
        echo "   - Total subscriptions: $count\n";
        
        if ($count > 0) {
            $latest = DB::table('push_subscriptions')->latest('id')->first();
            echo "   - Latest endpoint: " . substr($latest->endpoint, 0, 50) . "...\n";
            echo "   - Subscribable type: {$latest->subscribable_type}\n";
            echo "   - Subscribable ID: {$latest->subscribable_id}\n";
        }
    }
} catch (\Exception $e) {
    echo "   - Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Check Client Model Traits
echo "3. Client Model Configuration:\n";
$client = new \App\Models\Client();
$traits = class_uses($client);
echo "   - Notifiable trait: " . (in_array('Illuminate\Notifications\Notifiable', $traits) ? '✓ Yes' : '✗ No') . "\n";
echo "   - HasPushSubscriptions trait: " . (in_array('NotificationChannels\WebPush\HasPushSubscriptions', $traits) ? '✓ Yes' : '✗ No') . "\n";
echo "\n";

// 4. Check WebPush Package
echo "4. WebPush Package:\n";
try {
    $reflection = new ReflectionClass('NotificationChannels\WebPush\WebPushChannel');
    echo "   - Package installed: ✓ Yes\n";
    echo "   - Package path: " . dirname($reflection->getFileName()) . "\n";
} catch (\Exception $e) {
    echo "   - Package installed: ✗ No - " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Check Routes
echo "5. API Routes:\n";
$routes = Route::getRoutes();
$pushRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/client/push') !== false) {
        $pushRoutes[] = $route->methods()[0] . ' ' . $route->uri();
    }
}
if (count($pushRoutes) > 0) {
    foreach ($pushRoutes as $r) {
        echo "   - $r ✓\n";
    }
} else {
    echo "   - No push routes found ✗\n";
}
echo "\n";

// 6. Check if we can create a test client and subscription
echo "6. Test Subscription Creation:\n";
try {
    $testClient = \App\Models\Client::first();
    if ($testClient) {
        echo "   - Test client: {$testClient->name} (ID: {$testClient->id})\n";
        echo "   - Existing subscriptions: " . $testClient->pushSubscriptions()->count() . "\n";
        
        // Try to check if methods exist
        if (method_exists($testClient, 'updatePushSubscription')) {
            echo "   - updatePushSubscription method: ✓ Exists\n";
        } else {
            echo "   - updatePushSubscription method: ✗ Missing\n";
        }
        
        if (method_exists($testClient, 'pushSubscriptions')) {
            echo "   - pushSubscriptions method: ✓ Exists\n";
        } else {
            echo "   - pushSubscriptions method: ✗ Missing\n";
        }
    } else {
        echo "   - No clients found in database\n";
    }
} catch (\Exception $e) {
    echo "   - Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Test Notification Class
echo "7. TestNotification Class:\n";
try {
    $notification = new \App\Notifications\TestNotification();
    echo "   - Class exists: ✓ Yes\n";
    echo "   - Channels: " . implode(', ', $notification->via(null)) . "\n";
    
    if (method_exists($notification, 'toWebPush')) {
        echo "   - toWebPush method: ✓ Exists\n";
    } else {
        echo "   - toWebPush method: ✗ Missing\n";
    }
} catch (\Exception $e) {
    echo "   - Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 8. Check Service Worker
echo "8. Service Worker File:\n";
$swPath = public_path('sw.js');
if (file_exists($swPath)) {
    echo "   - File exists: ✓ Yes\n";
    echo "   - File size: " . filesize($swPath) . " bytes\n";
    
    // Check for push event listener
    $content = file_get_contents($swPath);
    if (strpos($content, "addEventListener('push'") !== false) {
        echo "   - Push event listener: ✓ Found\n";
    } else {
        echo "   - Push event listener: ✗ Not found\n";
    }
} else {
    echo "   - File exists: ✗ No\n";
}
echo "\n";

echo "=== DIAGNOSTIC COMPLETE ===\n";
echo "\nNext Steps:\n";
echo "1. If subscriptions = 0, try to subscribe from browser console\n";
echo "2. Check browser console for errors during subscription\n";
echo "3. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "4. Test API endpoint: curl -X POST /api/client/push/test (with auth)\n";
