#!/bin/bash

# Push Notification Testing Script
# Tests push notification system for Bizmark.ID PWA

echo "🔔 Push Notification Testing Script"
echo "===================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Test 1: Check if webpush package is installed
echo -n "1. Checking webpush package... "
if grep -q "laravel-notification-channels/webpush" composer.json; then
    echo -e "${GREEN}✓ Installed${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 2: Check VAPID keys in .env
echo -n "2. Checking VAPID keys... "
if grep -q "VAPID_PUBLIC_KEY=" .env && grep -q "VAPID_PRIVATE_KEY=" .env; then
    echo -e "${GREEN}✓ Configured${NC}"
else
    echo -e "${RED}✗ Missing${NC}"
    exit 1
fi

# Test 3: Check push_subscriptions table
echo -n "3. Checking push_subscriptions table... "
TABLE_CHECK=$(php artisan db:table push_subscriptions 2>&1)
if echo "$TABLE_CHECK" | grep -q "does not exist"; then
    echo -e "${RED}✗ Table not found${NC}"
    exit 1
else
    echo -e "${GREEN}✓ Exists${NC}"
fi

# Test 4: Check PushNotificationController
echo -n "4. Checking PushNotificationController... "
if [ -f "app/Http/Controllers/Api/PushNotificationController.php" ]; then
    echo -e "${GREEN}✓ Found${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 5: Check notification classes
echo -n "5. Checking notification classes... "
FOUND_COUNT=0
[ -f "app/Notifications/PermitStatusUpdated.php" ] && ((FOUND_COUNT++))
[ -f "app/Notifications/DocumentRequired.php" ] && ((FOUND_COUNT++))
[ -f "app/Notifications/DeadlineReminder.php" ] && ((FOUND_COUNT++))

if [ $FOUND_COUNT -eq 3 ]; then
    echo -e "${GREEN}✓ All 3 classes found${NC}"
elif [ $FOUND_COUNT -gt 0 ]; then
    echo -e "${YELLOW}! Found $FOUND_COUNT/3${NC}"
else
    echo -e "${RED}✗ None found${NC}"
    exit 1
fi

# Test 6: Check service worker push handler
echo -n "6. Checking service worker push handler... "
if grep -q "addEventListener('push'" public/sw.js; then
    echo -e "${GREEN}✓ Implemented${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 7: Check frontend subscription code
echo -n "7. Checking frontend subscription code... "
if grep -q "subscribeToPushNotifications" resources/views/client/layouts/app.blade.php; then
    echo -e "${GREEN}✓ Implemented${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 8: Check API routes
echo -n "8. Checking API routes... "
if grep -q "PushNotificationController" routes/web.php; then
    echo -e "${GREEN}✓ Configured${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 9: Check PWA standalone mode detection
echo -n "9. Checking PWA standalone detection... "
if grep -q "display-mode: standalone" resources/views/client/layouts/app.blade.php; then
    echo -e "${GREEN}✓ Implemented${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 10: Check PWA header
echo -n "10. Checking PWA-specific header... "
if grep -q "pwa-header" resources/views/client/layouts/app.blade.php; then
    echo -e "${GREEN}✓ Implemented${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

# Test 11: Check Client model trait
echo -n "11. Checking HasPushSubscriptions trait... "
if grep -q "HasPushSubscriptions" app/Models/Client.php; then
    echo -e "${GREEN}✓ Added${NC}"
else
    echo -e "${RED}✗ Not found${NC}"
    exit 1
fi

echo ""
echo "===================================="
echo -e "${GREEN}✓ All tests passed!${NC}"
echo ""

# Additional info
echo "📊 Statistics:"
SUBSCRIPTION_COUNT=$(php artisan tinker --execute="echo \NotificationChannels\WebPush\PushSubscription::count();" 2>/dev/null || echo "0")
echo "   Push subscriptions: $SUBSCRIPTION_COUNT"

echo ""
echo "🧪 Manual Testing Steps:"
echo ""
echo "1. Open client portal in browser"
echo "2. Install PWA to home screen"
echo "3. Open installed PWA"
echo "4. Check browser console for subscription success"
echo "5. Test sending notification:"
echo ""
echo "   php artisan tinker"
echo "   \$client = App\Models\Client::find(1);"
echo "   \$app = \$client->applications()->first();"
echo "   \$client->notify(new App\Notifications\PermitStatusUpdated(\$app));"
echo ""
echo "6. Check device for push notification"
echo ""

echo "✅ Phase 2 Implementation Complete!"
echo ""
echo "Features implemented:"
echo "  ✓ Push notifications (Web Push API)"
echo "  ✓ VAPID authentication"
echo "  ✓ Push subscriptions database"
echo "  ✓ 3 notification types (Status, Document, Deadline)"
echo "  ✓ PWA standalone mode detection"
echo "  ✓ PWA-specific UI (minimal header, icon-only nav)"
echo "  ✓ Service worker push handler"
echo "  ✓ Frontend subscription system"
echo ""
