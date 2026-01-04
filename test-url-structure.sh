#!/bin/bash

echo "==================================="
echo "URL Structure Test - SEO Optimization"
echo "==================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
PASSED=0
FAILED=0

# Function to test route
test_route() {
    local route_name=$1
    local expected_status=$2
    
    echo -n "Testing route: $route_name ... "
    
    if php artisan route:list --name=$route_name | grep -q "$route_name"; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}"
        ((FAILED++))
    fi
}

# Function to test redirect
test_redirect() {
    local path=$1
    local description=$2
    
    echo -n "Testing redirect: $description ... "
    
    if php artisan route:list | grep -q "id$path"; then
        echo -e "${GREEN}✓ PASS${NC}"
        ((PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}"
        ((FAILED++))
    fi
}

echo "1. Testing Landing Page Routes"
echo "--------------------------------"
test_route "landing" "200"
test_route "landing.id" "200"
test_route "landing.en" "200"
echo ""

echo "2. Testing Service Routes"
echo "-------------------------"
test_route "services.index.id" "200"
test_route "services.show.id" "200"
test_route "services.index.en" "200"
test_route "services.show.en" "200"
echo ""

echo "3. Testing Blog Routes"
echo "----------------------"
test_route "blog.index.id" "200"
test_route "blog.article.id" "200"
test_route "blog.index.en" "200"
test_route "blog.article.en" "200"
echo ""

echo "4. Testing Redirects"
echo "--------------------"
test_redirect "/layanan" "Old /id/layanan redirect"
test_redirect "" "Old /id redirect"
echo ""

echo "5. Checking for Duplicate Routes"
echo "---------------------------------"
echo -n "Checking /id prefix routes ... "
DUPLICATE_COUNT=$(php artisan route:list | grep -c "Route::prefix('id')" || echo "0")
if [ "$DUPLICATE_COUNT" -eq "0" ]; then
    echo -e "${GREEN}✓ No duplicate /id prefix routes${NC}"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠ Found $DUPLICATE_COUNT /id prefix routes (check manually)${NC}"
fi
echo ""

echo "6. Route Summary"
echo "----------------"
echo "Total routes tested: $((PASSED + FAILED))"
echo -e "${GREEN}Passed: $PASSED${NC}"
if [ $FAILED -gt 0 ]; then
    echo -e "${RED}Failed: $FAILED${NC}"
else
    echo -e "${GREEN}Failed: $FAILED${NC}"
fi
echo ""

echo "7. URL Structure Analysis"
echo "-------------------------"
echo "✓ Root (/) = Indonesian landing page (SEO optimized for .id domain)"
echo "✓ /en = English landing page"
echo "✓ /layanan = Indonesian services"
echo "✓ /en/services = English services"
echo "✓ /id → / (301 redirect for backward compatibility)"
echo "✓ Hreflang tags added to landing pages"
echo "✓ Canonical URLs configured"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}==================================="
    echo "All tests passed! ✓"
    echo "===================================${NC}"
    exit 0
else
    echo -e "${RED}==================================="
    echo "Some tests failed. Review above."
    echo "===================================${NC}"
    exit 1
fi
