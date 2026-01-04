#!/bin/bash

echo "=== Testing Indonesian Service Pages ==="
echo ""

BASE_URL="http://localhost"

# Test service list page
echo "Testing /layanan (service index)..."
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/layanan"
echo ""

# Test each service detail page
services=(
    "perizinan-lb3"
    "amdal"
    "ukl-upl"
    "oss-nib"
    "pbg-slf"
    "izin-operasional"
    "konsultan-lingkungan"
    "monitoring-digital"
    "izin-k3"
)

for service in "${services[@]}"; do
    echo "Testing /layanan/$service..."
    curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/layanan/$service"
done

echo ""
echo "=== Test completed! ==="
echo "All 200 status codes = Success"
echo "Any 404 status codes = Service not found"
echo "Any 500 status codes = Server error"
