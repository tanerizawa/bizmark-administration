#!/bin/bash

# PWA Version Updater
# This script automatically updates version numbers in PWA files
# Run this before every deployment to ensure users get the latest version

echo "🔄 Updating PWA versions..."

# Get current timestamp
TIMESTAMP=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

# Read current version from manifest.json
CURRENT_VERSION=$(grep -oP '"version": "\K[^"]+' public/manifest.json)
echo "📌 Current version: $CURRENT_VERSION"

# Increment version (e.g., 2.2.0 -> 2.2.1)
IFS='.' read -r -a version_parts <<< "$CURRENT_VERSION"
major="${version_parts[0]}"
minor="${version_parts[1]}"
patch="${version_parts[2]}"

# Increment patch version
patch=$((patch + 1))
NEW_VERSION="$major.$minor.$patch"

echo "📌 New version: $NEW_VERSION"

# Update manifest.json
sed -i "s/\"version\": \"$CURRENT_VERSION\"/\"version\": \"$NEW_VERSION\"/g" public/manifest.json
sed -i "s/v=$CURRENT_VERSION/v=$NEW_VERSION/g" public/manifest.json

# Update service worker
sed -i "s/CACHE_VERSION = 'v$CURRENT_VERSION'/CACHE_VERSION = 'v$NEW_VERSION'/g" public/sw.js
sed -i "s/BUILD_TIMESTAMP = '[^']*'/BUILD_TIMESTAMP = '$TIMESTAMP'/g" public/sw.js

echo "✅ Updated manifest.json to version $NEW_VERSION"
echo "✅ Updated sw.js to version v$NEW_VERSION"
echo "✅ Updated build timestamp to $TIMESTAMP"

# Create version info file
cat > public/version.json << EOF
{
  "version": "$NEW_VERSION",
  "build_timestamp": "$TIMESTAMP",
  "build_number": "$(git rev-parse --short HEAD 2>/dev/null || echo 'unknown')"
}
EOF

echo "✅ Created version.json"
echo ""
echo "🎉 PWA version updated successfully!"
echo ""
echo "📝 Next steps:"
echo "   1. Test the changes locally"
echo "   2. Commit the version changes: git add public/"
echo "   3. Deploy to production"
echo ""
echo "ℹ️  Users will see an update prompt within 60 seconds of opening the app"

