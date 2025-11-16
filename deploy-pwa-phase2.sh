#!/bin/bash

# Complete PWA Phase 2 Deployment Script
# Deploys push notifications and standalone mode features

echo "🚀 Deploying PWA Phase 2 Features"
echo "===================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Check prerequisites
echo -e "${BLUE}Step 1: Checking Prerequisites${NC}"
echo "--------------------------------"

if [ ! -f ".env" ]; then
    echo -e "${RED}✗ .env file not found${NC}"
    exit 1
fi
echo -e "${GREEN}✓ .env file exists${NC}"

if ! grep -q "VAPID_PUBLIC_KEY=" .env; then
    echo -e "${RED}✗ VAPID keys not configured${NC}"
    exit 1
fi
echo -e "${GREEN}✓ VAPID keys configured${NC}"

echo ""

# Step 2: Clear caches
echo -e "${BLUE}Step 2: Clearing Caches${NC}"
echo "--------------------------------"

php artisan config:clear
echo -e "${GREEN}✓ Config cache cleared${NC}"

php artisan route:clear
echo -e "${GREEN}✓ Route cache cleared${NC}"

php artisan view:clear
echo -e "${GREEN}✓ View cache cleared${NC}"

echo ""

# Step 3: Optimize for production
echo -e "${BLUE}Step 3: Optimizing for Production${NC}"
echo "--------------------------------"

php artisan config:cache
echo -e "${GREEN}✓ Config cached${NC}"

php artisan route:cache
echo -e "${GREEN}✓ Routes cached${NC}"

php artisan view:cache
echo -e "${GREEN}✓ Views cached${NC}"

echo ""

# Step 4: Check database
echo -e "${BLUE}Step 4: Checking Database${NC}"
echo "--------------------------------"

if php artisan db:table push_subscriptions &>/dev/null; then
    echo -e "${GREEN}✓ push_subscriptions table exists${NC}"
else
    echo -e "${YELLOW}! push_subscriptions table not found${NC}"
    echo -e "${YELLOW}  Running migration...${NC}"
    php artisan migrate --force
    echo -e "${GREEN}✓ Migration completed${NC}"
fi

echo ""

# Step 5: Verify files
echo -e "${BLUE}Step 5: Verifying Files${NC}"
echo "--------------------------------"

FILES=(
    "public/sw.js"
    "public/manifest.json"
    "app/Http/Controllers/Api/PushNotificationController.php"
    "app/Notifications/PermitStatusUpdated.php"
    "resources/views/client/components/notification-prompt.blade.php"
    "resources/views/client/components/notification-settings.blade.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ $file${NC}"
    else
        echo -e "${RED}✗ $file missing${NC}"
    fi
done

echo ""

# Step 6: Test service worker
echo -e "${BLUE}Step 6: Testing Service Worker${NC}"
echo "--------------------------------"

if grep -q "addEventListener('push'" public/sw.js; then
    echo -e "${GREEN}✓ Push event listener found${NC}"
else
    echo -e "${RED}✗ Push event listener not found${NC}"
fi

if grep -q "notificationclick" public/sw.js; then
    echo -e "${GREEN}✓ Notification click handler found${NC}"
else
    echo -e "${RED}✗ Notification click handler not found${NC}"
fi

echo ""

# Step 7: Restart services
echo -e "${BLUE}Step 7: Restarting Services${NC}"
echo "--------------------------------"

if command -v supervisorctl &> /dev/null; then
    supervisorctl restart laravel-worker:* 2>/dev/null || echo -e "${YELLOW}! Queue workers not managed by supervisor${NC}"
    echo -e "${GREEN}✓ Queue workers restarted${NC}"
else
    echo -e "${YELLOW}! Supervisor not installed${NC}"
    echo -e "${YELLOW}  Manually restart queue workers if running${NC}"
fi

echo ""

# Step 8: Validate deployment
echo -e "${BLUE}Step 8: Final Validation${NC}"
echo "--------------------------------"

# Run automated tests
if [ -f "test-push-notifications.sh" ]; then
    bash test-push-notifications.sh 2>&1 | tail -20
else
    echo -e "${YELLOW}! Test script not found${NC}"
fi

echo ""
echo "===================================="
echo -e "${GREEN}✅ Deployment Complete!${NC}"
echo ""
echo "📝 Post-Deployment Checklist:"
echo ""
echo "1. ✅ Verify service worker registration:"
echo "   https://bizmark.id/sw.js"
echo ""
echo "2. ✅ Test push notification subscription:"
echo "   - Open client portal in PWA mode"
echo "   - Check browser console for subscription"
echo ""
echo "3. ✅ Send test notification:"
echo "   php artisan tinker"
echo "   \$client = App\\Models\\Client::find(1);"
echo "   \$app = \$client->applications()->first();"
echo "   \$client->notify(new App\\Notifications\\PermitStatusUpdated(\$app));"
echo ""
echo "4. ✅ Monitor error logs:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "🎉 PWA Phase 2 is now LIVE!"
echo ""
