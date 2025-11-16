#!/bin/bash

# PWA Deployment Verification Script
# Verifies all PWA components are working correctly

echo "🚀 PWA Deployment Verification"
echo "================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

PASSED=0
FAILED=0
TOTAL=0

# Function to test URL
test_url() {
    local name=$1
    local url=$2
    local expected=$3
    
    TOTAL=$((TOTAL + 1))
    
    echo -n "Testing ${name}... "
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null)
    
    if [ "$response" = "$expected" ]; then
        echo -e "${GREEN}✓ PASS${NC} (${response})"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}✗ FAIL${NC} (Expected: ${expected}, Got: ${response})"
        FAILED=$((FAILED + 1))
    fi
}

# Function to validate JSON
validate_json() {
    local name=$1
    local url=$2
    
    TOTAL=$((TOTAL + 1))
    
    echo -n "Validating ${name}... "
    
    content=$(curl -s "$url" 2>/dev/null)
    
    if echo "$content" | python3 -m json.tool >/dev/null 2>&1; then
        echo -e "${GREEN}✓ PASS${NC} (Valid JSON)"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}✗ FAIL${NC} (Invalid JSON)"
        FAILED=$((FAILED + 1))
    fi
}

# Function to check file content
check_content() {
    local name=$1
    local url=$2
    local search=$3
    
    TOTAL=$((TOTAL + 1))
    
    echo -n "Checking ${name}... "
    
    content=$(curl -s "$url" 2>/dev/null)
    
    if echo "$content" | grep -q "$search"; then
        echo -e "${GREEN}✓ PASS${NC}"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}✗ FAIL${NC} (Content not found: ${search})"
        FAILED=$((FAILED + 1))
    fi
}

echo "📦 Core PWA Components"
echo "----------------------"
test_url "Landing Page" "https://bizmark.id/" "200"
test_url "Manifest" "https://bizmark.id/manifest.json" "200"
test_url "Service Worker" "https://bizmark.id/sw.js" "200"
test_url "Offline Page" "https://bizmark.id/offline.html" "200"
test_url "SVG Icon" "https://bizmark.id/icons/icon.svg" "200"
echo ""

echo "🛠️ PWA Tools"
echo "------------"
test_url "Health Check Tool" "https://bizmark.id/pwa-health-check.html" "200"
test_url "Icon Generator" "https://bizmark.id/generate-icons.html" "200"
test_url "Cache Clearer" "https://bizmark.id/clear-sw.html" "200"
echo ""

echo "🔍 JSON Validation"
echo "------------------"
validate_json "Manifest JSON" "https://bizmark.id/manifest.json"
echo ""

echo "📝 Content Verification"
echo "----------------------"
check_content "Manifest Name" "https://bizmark.id/manifest.json" "Bizmark.ID"
check_content "Service Worker Version" "https://bizmark.id/sw.js" "CACHE_VERSION"
check_content "Offline Page Branding" "https://bizmark.id/offline.html" "Bizmark"
check_content "SVG Icon Content" "https://bizmark.id/icons/icon.svg" "<svg"
echo ""

echo "🔐 Security Headers"
echo "------------------"
TOTAL=$((TOTAL + 1))
echo -n "Checking HTTPS... "
if curl -s -I "https://bizmark.id" | grep -q "HTTP.*200"; then
    echo -e "${GREEN}✓ PASS${NC} (HTTPS enabled)"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}✗ FAIL${NC} (HTTPS not working)"
    FAILED=$((FAILED + 1))
fi
echo ""

echo "📱 Client Portal"
echo "---------------"
test_url "Client Login" "https://bizmark.id/client/login" "200"
echo ""

echo "================================"
echo "📊 Test Summary"
echo "================================"
echo -e "Total Tests: ${TOTAL}"
echo -e "Passed: ${GREEN}${PASSED}${NC}"
echo -e "Failed: ${RED}${FAILED}${NC}"
echo ""

PERCENTAGE=$((PASSED * 100 / TOTAL))

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✅ ALL TESTS PASSED (${PERCENTAGE}%)${NC}"
    echo ""
    echo "🎉 PWA is production ready!"
    echo ""
    echo "Next steps:"
    echo "1. Test PWA install on mobile device"
    echo "2. Test offline mode"
    echo "3. Monitor service worker registration"
    echo "4. Check analytics for install rate"
    exit 0
elif [ $PERCENTAGE -ge 80 ]; then
    echo -e "${YELLOW}⚠️  MOSTLY PASSING (${PERCENTAGE}%)${NC}"
    echo ""
    echo "Some non-critical tests failed."
    echo "Review failed tests above."
    exit 0
else
    echo -e "${RED}❌ TESTS FAILED (${PERCENTAGE}%)${NC}"
    echo ""
    echo "Critical issues detected!"
    echo "Please fix failed tests before deploying."
    exit 1
fi
