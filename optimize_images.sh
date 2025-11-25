#!/bin/bash

# Image Optimization Script for Bizmark.ID
# Converts JPG/PNG to WebP and optimizes images
# Run: bash optimize_images.sh

echo "🖼️  BizMark Image Optimization Script"
echo "===================================="
echo ""

# Check if webp is available
if ! command -v cwebp &> /dev/null; then
    echo "⚠️  cwebp not found. Installing webp..."
    apt-get update && apt-get install -y webp
fi

# Directories to optimize
DIRS=(
    "/home/bizmark/bizmark.id/storage/app/public/articles/featured"
    "/home/bizmark/bizmark.id/storage/app/public/articles/content"
    "/home/bizmark/bizmark.id/storage/app/public/articles"
)

TOTAL_BEFORE=0
TOTAL_AFTER=0
COUNT=0

# Function to get file size in KB
get_size() {
    du -k "$1" | cut -f1
}

echo "📊 Starting optimization..."
echo ""

for DIR in "${DIRS[@]}"; do
    if [ ! -d "$DIR" ]; then
        echo "⏭️  Skipping $DIR (not found)"
        continue
    fi
    
    echo "📁 Processing: $DIR"
    echo "---"
    
    # Find all jpg, jpeg, png files
    find "$DIR" -maxdepth 1 -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) | while read -r file; do
        # Skip if webp already exists
        webp_file="${file%.*}.webp"
        
        if [ -f "$webp_file" ]; then
            echo "  ⏭️  Skipping $(basename "$file") (WebP exists)"
            continue
        fi
        
        # Get original size
        SIZE_BEFORE=$(get_size "$file")
        
        # Convert to WebP with quality 85
        cwebp -q 85 "$file" -o "$webp_file" > /dev/null 2>&1
        
        if [ $? -eq 0 ]; then
            SIZE_AFTER=$(get_size "$webp_file")
            SAVING=$((SIZE_BEFORE - SIZE_AFTER))
            PERCENT=$((100 - (SIZE_AFTER * 100 / SIZE_BEFORE)))
            
            echo "  ✅ $(basename "$file")"
            echo "     Original: ${SIZE_BEFORE}KB → WebP: ${SIZE_AFTER}KB (${PERCENT}% smaller)"
            
            TOTAL_BEFORE=$((TOTAL_BEFORE + SIZE_BEFORE))
            TOTAL_AFTER=$((TOTAL_AFTER + SIZE_AFTER))
            COUNT=$((COUNT + 1))
        else
            echo "  ❌ Failed: $(basename "$file")"
        fi
    done
    
    echo ""
done

echo "===================================="
echo "🎉 Optimization Complete!"
echo ""
echo "📊 Summary:"
echo "  • Images processed: $COUNT"
echo "  • Total size before: ${TOTAL_BEFORE}KB ($(echo "scale=2; $TOTAL_BEFORE/1024" | bc)MB)"
echo "  • Total size after: ${TOTAL_AFTER}KB ($(echo "scale=2; $TOTAL_AFTER/1024" | bc)MB)"

if [ $COUNT -gt 0 ]; then
    TOTAL_SAVING=$((TOTAL_BEFORE - TOTAL_AFTER))
    TOTAL_PERCENT=$((100 - (TOTAL_AFTER * 100 / TOTAL_BEFORE)))
    echo "  • Total savings: ${TOTAL_SAVING}KB (${TOTAL_PERCENT}%)"
    echo ""
    echo "💡 Next Steps:"
    echo "  1. Update blade templates to use <picture> tags with WebP"
    echo "  2. Add loading='lazy' to all images"
    echo "  3. Test with PageSpeed Insights"
fi

echo ""
echo "✨ Done!"
