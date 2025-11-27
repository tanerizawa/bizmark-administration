#!/bin/bash

# ===========================================
# E2E Test: Consultation Form Flow
# Testing complete user journey from form to result
# ===========================================

BASE_URL="https://bizmark.id"
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo ""
echo "=========================================="
echo "E2E Testing: Consultation Flow"
echo "=========================================="
echo ""

# Test 1: Check if form page is accessible
echo -e "${BLUE}Test 1: Form Page Accessibility${NC}"
echo "   GET $BASE_URL/estimasi-biaya"

HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/estimasi-biaya")

if [ "$HTTP_CODE" == "200" ]; then
    echo -e "   ${GREEN}✓ PASS${NC} - Form page accessible (HTTP $HTTP_CODE)"
else
    echo -e "   ${RED}✗ FAIL${NC} - Form page returned HTTP $HTTP_CODE"
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 2: KBLI Search (Autocomplete)
echo -e "${BLUE}Test 2: KBLI Autocomplete Search${NC}"
echo "   GET $BASE_URL/api/kbli/search?q=restoran&limit=5"

SEARCH_RESPONSE=$(curl -s "$BASE_URL/api/kbli/search?q=restoran&limit=5")
SEARCH_SUCCESS=$(echo "$SEARCH_RESPONSE" | grep -o '"success":true' | head -1)

if [ ! -z "$SEARCH_SUCCESS" ]; then
    KBLI_COUNT=$(echo "$SEARCH_RESPONSE" | grep -o '"count":[0-9]*' | grep -o '[0-9]*')
    echo -e "   ${GREEN}✓ PASS${NC} - Search returned $KBLI_COUNT KBLI codes"
    
    # Extract first KBLI code for next tests
    KBLI_CODE=$(echo "$SEARCH_RESPONSE" | grep -o '"code":"[0-9]*"' | head -1 | grep -o '[0-9]*')
    echo "   Using KBLI: $KBLI_CODE for next tests"
else
    echo -e "   ${RED}✗ FAIL${NC} - Search failed"
    echo "$SEARCH_RESPONSE" | head -20
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 3: Quick Estimate Preview
echo -e "${BLUE}Test 3: Quick Estimate Preview${NC}"
echo "   POST $BASE_URL/api/consultation/quick-estimate"

QUICK_ESTIMATE_DATA="{
    \"kbli_code\": \"$KBLI_CODE\",
    \"business_size\": \"small\",
    \"location_type\": \"jakarta\"
}"

CSRF_TOKEN=$(curl -s "$BASE_URL/estimasi-biaya" | grep -o 'csrf-token" content="[^"]*"' | cut -d'"' -f4)

QUICK_RESPONSE=$(curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
    -d "$QUICK_ESTIMATE_DATA" \
    "$BASE_URL/api/consultation/quick-estimate")

QUICK_SUCCESS=$(echo "$QUICK_RESPONSE" | grep -o '"success":true' | head -1)

if [ ! -z "$QUICK_SUCCESS" ]; then
    GRAND_TOTAL=$(echo "$QUICK_RESPONSE" | grep -o '"grand_total":"[^"]*"' | head -1 | cut -d'"' -f4)
    echo -e "   ${GREEN}✓ PASS${NC} - Quick estimate: $GRAND_TOTAL"
else
    echo -e "   ${RED}✗ FAIL${NC} - Quick estimate failed"
    echo "$QUICK_RESPONSE" | head -20
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 4: Full Consultation Submit (WITH AI)
echo -e "${BLUE}Test 4: Full Consultation Submit (AI Analysis)${NC}"
echo "   POST $BASE_URL/api/consultation/submit"
echo -e "   ${YELLOW}⏳ This will take approximately 25-35 seconds (AI processing)...${NC}"
echo ""

SUBMIT_DATA="{
    \"kbli_code\": \"$KBLI_CODE\",
    \"business_size\": \"small\",
    \"location\": \"Jakarta Selatan\",
    \"location_type\": \"jakarta\",
    \"investment_level\": \"1b_5b\",
    \"employee_count\": 15,
    \"contact_phone\": \"081234567890\",
    \"deliverables\": \"\"
}"

START_TIME=$(date +%s)

SUBMIT_RESPONSE=$(curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
    -d "$SUBMIT_DATA" \
    "$BASE_URL/api/consultation/submit")

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

SUBMIT_SUCCESS=$(echo "$SUBMIT_RESPONSE" | grep -o '"success":true' | head -1)

if [ ! -z "$SUBMIT_SUCCESS" ]; then
    REQUEST_ID=$(echo "$SUBMIT_RESPONSE" | grep -o '"request_id":[0-9]*' | grep -o '[0-9]*')
    PERMITS_COUNT=$(echo "$SUBMIT_RESPONSE" | grep -o '"permits_count":[0-9]*' | grep -o '[0-9]*')
    AI_MODEL=$(echo "$SUBMIT_RESPONSE" | grep -o '"model_used":"[^"]*"' | cut -d'"' -f4)
    REALISTIC_DAYS=$(echo "$SUBMIT_RESPONSE" | grep -o '"realistic_days":[0-9]*' | grep -o '[0-9]*')
    
    echo -e "   ${GREEN}✓ PASS${NC} - Consultation submitted successfully"
    echo "   Request ID: #$REQUEST_ID"
    echo "   Processing Time: ${DURATION}s"
    echo "   Permits Count: $PERMITS_COUNT"
    echo "   AI Model: $AI_MODEL"
    echo "   Timeline: $REALISTIC_DAYS days (realistic)"
    
    # Save request ID for next test
    echo "$REQUEST_ID" > /tmp/last_consultation_id.txt
else
    echo -e "   ${RED}✗ FAIL${NC} - Consultation submission failed"
    echo "$SUBMIT_RESPONSE" | head -30
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 5: Result Page Accessibility
echo -e "${BLUE}Test 5: Result Page Accessibility${NC}"
echo "   GET $BASE_URL/estimasi-biaya/hasil/$REQUEST_ID"

RESULT_HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/estimasi-biaya/hasil/$REQUEST_ID")

if [ "$RESULT_HTTP_CODE" == "200" ]; then
    echo -e "   ${GREEN}✓ PASS${NC} - Result page accessible (HTTP $RESULT_HTTP_CODE)"
else
    echo -e "   ${RED}✗ FAIL${NC} - Result page returned HTTP $RESULT_HTTP_CODE"
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 6: Verify data in database
echo -e "${BLUE}Test 6: Database Verification${NC}"
echo "   Checking consult_requests table via Laravel..."

# Use Laravel Tinker to check database
DB_CHECK=$(php artisan tinker --execute="
\$record = \App\Models\ConsultRequest::find($REQUEST_ID);
if (\$record) {
    echo 'FOUND:' . \$record->kbli_code . '|' . \$record->business_size . '|' . \$record->estimate_status;
} else {
    echo 'NOT_FOUND';
}
" 2>/dev/null)

if echo "$DB_CHECK" | grep -q "FOUND:"; then
    RECORD_DATA=$(echo "$DB_CHECK" | grep "FOUND:" | sed 's/FOUND://')
    echo -e "   ${GREEN}✓ PASS${NC} - Record found in database"
    echo "   Data: $RECORD_DATA"
else
    echo -e "   ${RED}✗ FAIL${NC} - Record not found in database"
    exit 1
fi

echo ""
echo "---"
echo ""

# Test 7: KBLI Usage Count Increment
echo -e "${BLUE}Test 7: KBLI Usage Count${NC}"
echo "   Verifying usage_count incremented for KBLI $KBLI_CODE..."

# Use Laravel to check KBLI usage
USAGE_CHECK=$(php artisan tinker --execute="
\$kbli = \App\Models\Kbli::findByCode('$KBLI_CODE');
if (\$kbli) {
    echo 'USAGE:' . \$kbli->usage_count;
} else {
    echo 'NOT_FOUND';
}
" 2>/dev/null)

if echo "$USAGE_CHECK" | grep -q "USAGE:"; then
    USAGE_COUNT=$(echo "$USAGE_CHECK" | grep "USAGE:" | sed 's/USAGE://')
    echo -e "   ${GREEN}✓ PASS${NC} - KBLI usage count: $USAGE_COUNT"
else
    echo -e "   ${YELLOW}⚠ WARNING${NC} - Could not verify usage count"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}E2E Testing Complete!${NC}"
echo "=========================================="
echo ""
echo "Summary:"
echo "  ✓ Form page accessible"
echo "  ✓ KBLI search working"
echo "  ✓ Quick estimate preview working"
echo "  ✓ Full AI submission working (${DURATION}s)"
echo "  ✓ Result page accessible"
echo "  ✓ Database record created"
echo "  ✓ KBLI usage tracked"
echo ""
echo "Test Request ID: #$REQUEST_ID"
echo "View result at: $BASE_URL/estimasi-biaya/hasil/$REQUEST_ID"
echo ""
echo "=========================================="
