#!/bin/bash

# PWA Icon Generator Script
# Generates all required icon sizes from favicon.ico

FAVICON="public/favicon.ico"
ICONS_DIR="public/icons"

# Create icons directory
mkdir -p "$ICONS_DIR"

echo "🎨 Generating PWA Icons..."
echo "Source: $FAVICON"
echo "Output: $ICONS_DIR"
echo ""

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null; then
    echo "⚠️  ImageMagick not found. Trying alternative methods..."
    
    # Check if we have the favicon
    if [ ! -f "$FAVICON" ]; then
        echo "❌ favicon.ico not found at $FAVICON"
        echo ""
        echo "Please provide a source image and run:"
        echo "  convert source.png -resize 192x192 public/icons/icon-192x192.png"
        exit 1
    fi
    
    echo ""
    echo "📦 Creating placeholder SVG icon as fallback..."
    
    # Create a simple SVG icon as fallback
    cat > "$ICONS_DIR/icon.svg" << 'EOF'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#007AFF;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#0051D5;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="512" height="512" rx="100" fill="url(#grad)"/>
  <text x="256" y="340" font-family="Arial, sans-serif" font-size="280" font-weight="bold" fill="white" text-anchor="middle">B</text>
</svg>
EOF
    
    echo "✅ Created icon.svg"
    echo ""
    echo "🔧 To generate PNG icons, install ImageMagick and run:"
    echo "   sudo apt-get install imagemagick"
    echo "   ./generate-pwa-icons.sh"
    echo ""
    echo "Or manually create these sizes:"
    echo "   - 72x72, 96x96, 128x128, 144x144"
    echo "   - 152x152, 192x192, 384x384, 512x512"
    echo ""
    echo "Temporarily, you can use the SVG icon in manifest.json"
    
    exit 0
fi

# Required icon sizes for PWA
SIZES=(72 96 128 144 152 192 384 512)

echo "Generating PNG icons from favicon..."
for SIZE in "${SIZES[@]}"; do
    OUTPUT="$ICONS_DIR/icon-${SIZE}x${SIZE}.png"
    convert "$FAVICON" -resize "${SIZE}x${SIZE}" "$OUTPUT"
    
    if [ -f "$OUTPUT" ]; then
        echo "✅ Created icon-${SIZE}x${SIZE}.png"
    else
        echo "❌ Failed to create icon-${SIZE}x${SIZE}.png"
    fi
done

# Generate maskable icon (with padding for safe area)
echo ""
echo "Generating maskable icon..."
MASKABLE="$ICONS_DIR/icon-maskable-512x512.png"
convert "$FAVICON" -resize 340x340 -gravity center -background "#007AFF" -extent 512x512 "$MASKABLE"

if [ -f "$MASKABLE" ]; then
    echo "✅ Created icon-maskable-512x512.png"
fi

# Generate apple-touch-icon
echo ""
echo "Generating Apple Touch Icon..."
APPLE_ICON="$ICONS_DIR/apple-touch-icon.png"
convert "$FAVICON" -resize 180x180 "$APPLE_ICON"

if [ -f "$APPLE_ICON" ]; then
    echo "✅ Created apple-touch-icon.png"
fi

echo ""
echo "🎉 Icon generation complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Update manifest.json with new icon paths"
echo "   2. Add <link> tags to HTML for Apple icons"
echo "   3. Test PWA installation on mobile devices"
echo ""
echo "Generated files:"
ls -lh "$ICONS_DIR"
