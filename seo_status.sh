#!/bin/bash

# SEO Status Check for Bizmark.ID
# Quick command to check current SEO implementation status

echo "═══════════════════════════════════════════"
echo "🔍 Bizmark.ID SEO Status Check"
echo "═══════════════════════════════════════════"
echo ""

# 1. Check Sitemap
echo "📍 1. Sitemap Status"
echo "---"
SITEMAP_URLS=$(curl -s "https://bizmark.id/sitemap.xml" | grep -c "<loc>")
echo "   Total URLs in sitemap: $SITEMAP_URLS"
if [ "$SITEMAP_URLS" -ge 25 ]; then
    echo "   ✅ Status: Good (target: 25+)"
else
    echo "   ⚠️  Status: Warning (current: $SITEMAP_URLS, target: 25+)"
fi
echo ""

# 2. Check WebP Images
echo "🖼️  2. Image Optimization"
echo "---"
WEBP_COUNT=$(find /home/bizmark/bizmark.id/storage/app/public/articles/ -name "*.webp" | wc -l)
JPG_COUNT=$(find /home/bizmark/bizmark.id/storage/app/public/articles/ -name "*.jpg" -o -name "*.jpeg" | wc -l)
echo "   WebP images: $WEBP_COUNT"
echo "   JPG images: $JPG_COUNT"
if [ "$WEBP_COUNT" -ge 15 ]; then
    echo "   ✅ Status: Optimized"
else
    echo "   ⚠️  Status: Run optimize_images.sh"
fi
echo ""

# 3. Check Schema Markup
echo "📋 3. Schema Markup"
echo "---"
ARTICLE_SCHEMA=$(curl -s "https://bizmark.id/blog/panduan-lengkap-izin-lingkungan-untuk-usaha-anda" | grep -c '"@type": "Article"')
FAQ_SCHEMA=$(curl -s "https://bizmark.id/" | grep -c '"@type": "FAQPage"')

if [ "$ARTICLE_SCHEMA" -gt 0 ]; then
    echo "   ✅ Article Schema: Active"
else
    echo "   ❌ Article Schema: Not found"
fi

if [ "$FAQ_SCHEMA" -gt 0 ]; then
    echo "   ✅ FAQ Schema: Active"
else
    echo "   ❌ FAQ Schema: Not found"
fi
echo ""

# 4. Check Published Articles
echo "📝 4. Content Status"
echo "---"
cd /home/bizmark/bizmark.id
ARTICLE_COUNT=$(php artisan tinker --execute="echo Article::published()->count();" 2>/dev/null)
echo "   Published articles: $ARTICLE_COUNT"
if [ "$ARTICLE_COUNT" -ge 10 ]; then
    echo "   ✅ Status: Good content base"
else
    echo "   ⚠️  Status: Need more content (target: 10+)"
fi
echo ""

# 5. Check Storage Size
echo "💾 5. Storage Usage"
echo "---"
STORAGE_SIZE=$(du -sh /home/bizmark/bizmark.id/storage/app/public/articles/ | cut -f1)
echo "   Articles storage: $STORAGE_SIZE"
echo "   ℹ️  WebP saves ~48% vs JPG"
echo ""

# 6. Quick Performance Test
echo "⚡ 6. Performance Quick Test"
echo "---"
RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' https://bizmark.id/)
echo "   Homepage response time: ${RESPONSE_TIME}s"
if (( $(echo "$RESPONSE_TIME < 1.0" | bc -l) )); then
    echo "   ✅ Status: Fast (<1s)"
elif (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
    echo "   ⚠️  Status: Acceptable (<2s)"
else
    echo "   ❌ Status: Slow (>2s) - Optimize!"
fi
echo ""

# 7. Security Check
echo "🔒 7. Security Status"
echo "---"
HTTPS_CHECK=$(curl -s -o /dev/null -w "%{http_code}" https://bizmark.id/)
if [ "$HTTPS_CHECK" -eq 200 ]; then
    echo "   ✅ HTTPS: Active"
else
    echo "   ❌ HTTPS: Issue detected (code: $HTTPS_CHECK)"
fi

ROBOTS_CHECK=$(curl -s -o /dev/null -w "%{http_code}" https://bizmark.id/robots.txt)
if [ "$ROBOTS_CHECK" -eq 200 ]; then
    echo "   ✅ Robots.txt: Active"
else
    echo "   ❌ Robots.txt: Not found"
fi
echo ""

# Summary
echo "═══════════════════════════════════════════"
echo "📊 SUMMARY"
echo "═══════════════════════════════════════════"
echo ""

# Calculate score
SCORE=0
[ "$SITEMAP_URLS" -ge 25 ] && SCORE=$((SCORE + 20))
[ "$WEBP_COUNT" -ge 15 ] && SCORE=$((SCORE + 20))
[ "$ARTICLE_SCHEMA" -gt 0 ] && SCORE=$((SCORE + 15))
[ "$FAQ_SCHEMA" -gt 0 ] && SCORE=$((SCORE + 15))
[ "$ARTICLE_COUNT" -ge 10 ] && SCORE=$((SCORE + 15))
[ "$HTTPS_CHECK" -eq 200 ] && SCORE=$((SCORE + 10))
[ "$ROBOTS_CHECK" -eq 200 ] && SCORE=$((SCORE + 5))

echo "   Overall SEO Score: $SCORE/100"
echo ""

if [ "$SCORE" -ge 90 ]; then
    echo "   🏆 Excellent! Your SEO is in great shape!"
elif [ "$SCORE" -ge 70 ]; then
    echo "   ✅ Good! Minor improvements possible."
elif [ "$SCORE" -ge 50 ]; then
    echo "   ⚠️  Fair. Need some improvements."
else
    echo "   ❌ Poor. Significant work needed."
fi
echo ""

echo "═══════════════════════════════════════════"
echo "📋 ACTION ITEMS"
echo "═══════════════════════════════════════════"
echo ""

# Action items based on checks
[ "$SITEMAP_URLS" -lt 25 ] && echo "   • Submit sitemap to Google Search Console"
[ "$WEBP_COUNT" -lt 15 ] && echo "   • Run: bash optimize_images.sh"
[ "$ARTICLE_SCHEMA" -eq 0 ] && echo "   • Fix Article schema in article.blade.php"
[ "$FAQ_SCHEMA" -eq 0 ] && echo "   • Add FAQ schema to landing page"
[ "$ARTICLE_COUNT" -lt 10 ] && echo "   • Create more articles (target: 10+)"
(( $(echo "$RESPONSE_TIME > 2.0" | bc -l) )) && echo "   • Optimize performance (Core Web Vitals)"

echo ""
echo "   For detailed info, see: SEO_IMPLEMENTATION_COMPLETE.md"
echo ""
echo "═══════════════════════════════════════════"
echo "✨ Check complete!"
echo "Last checked: $(date '+%Y-%m-%d %H:%M:%S')"
echo "═══════════════════════════════════════════"
