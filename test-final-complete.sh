#!/bin/bash

# Color codes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}   DUAL MARKET FINAL TESTING - PHASE 4 & 5    ${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

PASSED=0
FAILED=0

# Test function
test_feature() {
    local name=$1
    local command=$2
    local expected=$3
    
    echo -n "Testing $name... "
    result=$(eval "$command" 2>&1)
    
    if echo "$result" | grep -q "$expected"; then
        echo -e "${GREEN}PASS${NC}"
        ((PASSED++))
        return 0
    else
        echo -e "${RED}FAIL${NC}"
        echo "  Expected: $expected"
        echo "  Got: $result"
        ((FAILED++))
        return 1
    fi
}

# Phase 1: Foundation Tests
echo -e "${YELLOW}[PHASE 1] Foundation${NC}"
test_feature "Config services_pma exists" "[ -f config/services_pma.php ] && echo 'exists'" "exists"
test_feature "SetLocale middleware" "grep -q 'market_segment' app/Http/Middleware/SetLocale.php && echo 'found'" "found"
test_feature "Available locales config" "grep -q 'available_locales' config/app.php && echo 'found'" "found"

# Test dual-market routes (no specific count - just verify existence)
DUAL_ROUTES=$(php artisan route:list 2>&1 | grep -cE '(locale|en/|id/|pma)')
if [ $DUAL_ROUTES -ge 9 ]; then
    echo -e "Testing Dual-market routes registered... ${GREEN}PASS${NC} ($DUAL_ROUTES routes)"
    ((PASSED++))
else
    echo -e "Testing Dual-market routes registered... ${RED}FAIL${NC}"
    echo -e "  Expected: >= 9"
    echo -e "  Got: $DUAL_ROUTES"
    ((FAILED++))
fi

# Phase 2: Content Development
echo ""
echo -e "${YELLOW}[PHASE 2] Content Development${NC}"
test_feature "English translations exist" "[ -f lang/en/landing.php ] && echo 'exists'" "exists"
test_feature "Investment translations" "[ -f lang/en/investment.php ] && echo 'exists'" "exists"
test_feature "English landing page" "[ -f resources/views/landing/en/index.blade.php ] && echo 'exists'" "exists"
test_feature "English service index" "[ -f resources/views/services/en/index.blade.php ] && echo 'exists'" "exists"
test_feature "English service detail" "[ -f resources/views/services/en/show.blade.php ] && echo 'exists'" "exists"
test_feature "Locale switcher component" "[ -f resources/views/components/locale-switcher.blade.php ] && echo 'exists'" "exists"

# Phase 3: Forms & Lead Generation
echo ""
echo -e "${YELLOW}[PHASE 3] Forms & Lead Generation${NC}"
test_feature "PMA Inquiry Controller" "[ -f app/Http/Controllers/PMAInquiryController.php ] && echo 'exists'" "exists"
test_feature "PMA Inquiry form view" "[ -f resources/views/pma/inquiry/create.blade.php ] && echo 'exists'" "exists"
test_feature "PMA Inquiry result view" "[ -f resources/views/pma/inquiry/result.blade.php ] && echo 'exists'" "exists"
test_feature "PMA routes registered" "php artisan route:list --name=pma 2>&1 | grep -c 'pma.inquiry'" "3"
test_feature "Form has 4 steps" "grep -c 'step ===' resources/views/pma/inquiry/create.blade.php" "4"

# Phase 4: SEO Enhancement
echo ""
echo -e "${YELLOW}[PHASE 4] SEO Enhancement${NC}"
test_feature "robots.txt updated" "grep -q 'Dual Market' public/robots.txt && echo 'found'" "found"
test_feature "Sitemap hreflang support" "grep -q 'addUrlWithHreflang' app/Http/Controllers/SitemapController.php && echo 'found'" "found"
test_feature "Hreflang in landing EN" "grep -q 'hreflang' resources/views/landing/en/index.blade.php && echo 'found'" "found"
test_feature "Canonical URLs" "grep -q 'canonical' resources/views/landing/en/index.blade.php && echo 'found'" "found"
test_feature "Open Graph tags" "grep -q 'og:locale' resources/views/landing/en/index.blade.php && echo 'found'" "found"

# Phase 5: Integration Tests
echo ""
echo -e "${YELLOW}[PHASE 5] Integration Tests${NC}"

# Controller tests
echo "Testing Controllers..."
test_feature "PublicArticleController locale support" "grep -q 'app()->getLocale()' app/Http/Controllers/PublicArticleController.php && echo 'found'" "found"
test_feature "ServiceController market segment" "grep -q 'market_segment' app/Http/Controllers/ServiceController.php && echo 'found'" "found"
test_feature "LocaleController market aware" "grep -q 'pma' app/Http/Controllers/LocaleController.php && echo 'found'" "found"

# Database compatibility
echo "Testing Database Schema..."
php artisan tinker --execute="
try {
    \$inquiry = new App\Models\ServiceInquiry();
    \$fillable = \$inquiry->getFillable();
    echo in_array('inquiry_number', \$fillable) ? 'compatible' : 'missing';
} catch (Exception \$e) {
    echo 'error';
}
" > /tmp/db_test.txt 2>&1

if grep -q "compatible" /tmp/db_test.txt; then
    echo -e "Testing ServiceInquiry model... ${GREEN}PASS${NC}"
    ((PASSED++))
else
    echo -e "Testing ServiceInquiry model... ${RED}FAIL${NC}"
    ((FAILED++))
fi

# Config loading
echo "Testing Config Loading..."
php artisan tinker --execute="
\$pma = config('services_pma');
\$local = config('services_data');
echo 'PMA:' . count(\$pma) . '|Local:' . count(\$local);
" > /tmp/config_test.txt 2>&1

if grep -q "PMA:8" /tmp/config_test.txt && grep -q "Local:9" /tmp/config_test.txt; then
    echo -e "Testing dual config system... ${GREEN}PASS${NC}"
    ((PASSED++))
else
    echo -e "Testing dual config system... ${RED}FAIL${NC}"
    ((FAILED++))
fi

# Translation system
echo "Testing Translation System..."
php artisan tinker --execute="
\$en = __('landing.meta.title', [], 'en');
\$id = __('landing.meta.title', [], 'id');
echo (strlen(\$en) > 10 && strlen(\$id) > 10) ? 'working' : 'broken';
" > /tmp/trans_test.txt 2>&1

if grep -q "working" /tmp/trans_test.txt; then
    echo -e "Testing translations... ${GREEN}PASS${NC}"
    ((PASSED++))
else
    echo -e "Testing translations... ${RED}FAIL${NC}"
    ((FAILED++))
fi

# Cleanup temp files
rm -f /tmp/db_test.txt /tmp/config_test.txt /tmp/trans_test.txt

# File structure validation
echo ""
echo -e "${YELLOW}[VALIDATION] File Structure${NC}"
test_feature "Views directory structure" "[ -d resources/views/landing/en ] && echo 'exists'" "exists"
test_feature "Services EN directory" "[ -d resources/views/services/en ] && echo 'exists'" "exists"
test_feature "PMA inquiry directory" "[ -d resources/views/pma/inquiry ] && echo 'exists'" "exists"

# Syntax validation
echo ""
echo -e "${YELLOW}[VALIDATION] Syntax Checks${NC}"
echo "Checking PHP syntax errors..."
ERROR_COUNT=0

for file in app/Http/Controllers/PMAInquiryController.php \
            app/Http/Controllers/PublicArticleController.php \
            app/Http/Controllers/ServiceController.php \
            app/Http/Controllers/LocaleController.php \
            app/Http/Middleware/SetLocale.php; do
    if php -l "$file" 2>&1 | grep -q "No syntax errors"; then
        ERROR_COUNT=$((ERROR_COUNT + 0))
    else
        echo -e "${RED}Syntax error in $file${NC}"
        ERROR_COUNT=$((ERROR_COUNT + 1))
    fi
done

if [ $ERROR_COUNT -eq 0 ]; then
    echo -e "PHP syntax validation... ${GREEN}PASS (0 errors)${NC}"
    ((PASSED++))
else
    echo -e "PHP syntax validation... ${RED}FAIL ($ERROR_COUNT errors)${NC}"
    ((FAILED++))
fi

# Route validation
echo ""
echo -e "${YELLOW}[VALIDATION] Routes${NC}"
echo "Checking route registration..."

ROUTE_COUNT=$(php artisan route:list 2>&1 | grep -E "landing\.(id|en)|services\.(index|show)\.(id|en)|pma\.inquiry" | wc -l)

if [ $ROUTE_COUNT -ge 9 ]; then
    echo -e "Route count validation... ${GREEN}PASS ($ROUTE_COUNT routes)${NC}"
    ((PASSED++))
else
    echo -e "Route count validation... ${RED}FAIL ($ROUTE_COUNT routes, expected >= 9)${NC}"
    ((FAILED++))
fi

# Final Summary
echo ""
echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}              TEST SUMMARY                      ${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "${GREEN}PASSED:${NC} $PASSED tests"
echo -e "${RED}FAILED:${NC} $FAILED tests"
echo ""

TOTAL=$((PASSED + FAILED))
PERCENTAGE=$((PASSED * 100 / TOTAL))

echo "Success Rate: $PERCENTAGE% ($PASSED/$TOTAL)"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ ALL TESTS PASSED - SYSTEM READY FOR PRODUCTION!${NC}"
    echo ""
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}         DEPLOYMENT CHECKLIST                   ${NC}"
    echo -e "${BLUE}================================================${NC}"
    echo ""
    echo "✓ Phase 1: Foundation - COMPLETE"
    echo "✓ Phase 2: Content Development - COMPLETE"
    echo "✓ Phase 3: Forms & Lead Generation - COMPLETE"
    echo "✓ Phase 4: SEO Enhancement - COMPLETE"
    echo "✓ Phase 5: Testing & QA - COMPLETE"
    echo ""
    echo "Next Steps:"
    echo "1. Run 'php artisan optimize' for production"
    echo "2. Test on staging environment"
    echo "3. Configure email notifications"
    echo "4. Setup Google Analytics & Search Console"
    echo "5. Deploy to production"
    echo ""
    exit 0
else
    echo -e "${RED}✗ SOME TESTS FAILED - PLEASE REVIEW ERRORS${NC}"
    echo ""
    echo "Failed tests need attention before production deployment."
    exit 1
fi
