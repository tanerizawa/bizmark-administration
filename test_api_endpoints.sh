#!/bin/bash

# Test API Endpoints for KBLI & Consultation System
# Phase 2 - API Testing

BASE_URL="https://bizmark.id/api"
echo "=========================================="
echo "API Endpoints Testing"
echo "=========================================="
echo ""

# Test 1: KBLI Search (autocomplete)
echo "1. Testing KBLI Search (5-digit codes only)"
echo "   GET $BASE_URL/kbli/search?q=restoran&limit=5"
curl -s "$BASE_URL/kbli/search?q=restoran&limit=5" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 2: KBLI Details
echo "2. Testing KBLI Details by Code"
echo "   GET $BASE_URL/kbli/56101"
curl -s "$BASE_URL/kbli/56101" | python3 -m json.tool | head -80
echo ""
echo "---"
echo ""

# Test 3: Popular KBLI
echo "3. Testing Popular KBLI Codes"
echo "   GET $BASE_URL/kbli/popular?limit=10"
curl -s "$BASE_URL/kbli/popular?limit=10" | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 4: Quick Estimate (no AI)
echo "4. Testing Quick Estimate (no AI, fast preview)"
echo "   POST $BASE_URL/consultation/quick-estimate"
curl -s -X POST "$BASE_URL/consultation/quick-estimate" \
  -H "Content-Type: application/json" \
  -d '{
    "kbli_code": "56101",
    "business_size": "small",
    "location_type": "commercial",
    "investment_level": "100m_500m"
  }' | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 5: Full Consultation Submit (with AI - will take ~35 seconds)
echo "5. Testing Full Consultation Submit (WITH AI - expect 35s wait)"
echo "   POST $BASE_URL/consultation/submit"
echo "   NOTE: This will take approximately 35 seconds due to OpenRouter AI processing..."
echo ""

START_TIME=$(date +%s)
curl -s -X POST "$BASE_URL/consultation/submit" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "081234567890",
    "company_name": "Test Restaurant",
    "kbli_code": "56101",
    "business_size": "small",
    "location": "Jakarta",
    "location_type": "commercial",
    "investment_level": "100m_500m",
    "employee_count": 15,
    "project_description": "Opening a new restaurant in commercial area of Jakarta with 15 employees. Investment range 100-500 million. Need help with all permits and licensing."
  }' | python3 -m json.tool | head -120

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
echo ""
echo "Processing time: ${DURATION} seconds"
echo ""
echo "---"
echo ""

# Test 6: Validation - Invalid KBLI (should fail)
echo "6. Testing Validation: Invalid KBLI Code (3-digit)"
echo "   POST $BASE_URL/consultation/quick-estimate"
curl -s -X POST "$BASE_URL/consultation/quick-estimate" \
  -H "Content-Type: application/json" \
  -d '{
    "kbli_code": "561",
    "business_size": "small",
    "location_type": "commercial"
  }' | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 7: Validation - Non-existent KBLI (should fail)
echo "7. Testing Validation: Non-existent KBLI Code"
echo "   POST $BASE_URL/consultation/quick-estimate"
curl -s -X POST "$BASE_URL/consultation/quick-estimate" \
  -H "Content-Type: application/json" \
  -d '{
    "kbli_code": "99999",
    "business_size": "small",
    "location_type": "commercial"
  }' | python3 -m json.tool
echo ""
echo "---"
echo ""

echo "=========================================="
echo "Testing Complete!"
echo "=========================================="
