# 🎨 BizMark Logo Assets

Professional SVG logo suite for BizMark Indonesia - Konsultan Perizinan Usaha Terpercaya

## 📦 Available Files

```
public/images/
├── logo-bizmark.svg           # Primary logo (circular, gradient)
├── logo-bizmark-full.svg      # Full logo with text
├── logo-bizmark-white.svg     # White version (dark backgrounds)
├── favicon.svg                # Optimized favicon
└── [logo assets here]
```

## 🚀 Quick Start

### 1. Website Header
```html
<img src="/images/logo-bizmark-full.svg" alt="BizMark Indonesia" height="60">
```

### 2. Favicon
```html
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
```

### 3. Icon Only
```html
<img src="/images/logo-bizmark.svg" alt="BizMark" width="80">
```

### 4. Dark Mode / Dark Background
```html
<img src="/images/logo-bizmark-white.svg" alt="BizMark" width="80">
```

## 🎨 Logo Concept

### Design Philosophy:
- **Leaf** 🍃: Growth, sustainability, natural business development
- **Green Dots** 🟢: Business milestones and achievements
- **Blue Gradient** 🔵: Trust, professionalism, stability
- **Letter "B"**: Subtle brand integration

### Color Codes:
- Primary Blue: `#0a66c2`
- Dark Blue: `#004182`
- Success Green: `#28a745`
- White: `#ffffff`
- Light Blue: `#e3f2fd`

## 📐 Usage Guidelines

### Sizing:
- **Favicon:** 16x16, 32x32
- **Small:** 50-80px (mobile headers)
- **Medium:** 100-150px (desktop headers)
- **Large:** 200px+ (banners, print)

### Clearspace:
- Minimum 10% of logo width on all sides

### Backgrounds:
- ✅ White backgrounds (use logo-bizmark.svg)
- ✅ Dark backgrounds (use logo-bizmark-white.svg)
- ✅ Gradient backgrounds (ensure contrast)
- ❌ Busy patterns or photos

## 🛠️ Implementation Examples

### Blade Template (Laravel):
```blade
<!-- Header -->
<div class="header">
    <img src="{{ asset('images/logo-bizmark-full.svg') }}" 
         alt="BizMark Indonesia" 
         class="h-16">
</div>

<!-- Footer -->
<footer>
    <img src="{{ asset('images/logo-bizmark.svg') }}" 
         alt="BizMark" 
         class="w-12 h-12">
</footer>
```

### Tailwind CSS:
```html
<!-- Responsive logo -->
<img src="/images/logo-bizmark-full.svg" 
     alt="BizMark" 
     class="h-12 md:h-16 lg:h-20">

<!-- With hover effect -->
<img src="/images/logo-bizmark.svg" 
     class="w-16 h-16 transition-transform hover:scale-110">
```

### Alpine.js Dark Mode:
```html
<img :src="darkMode ? '/images/logo-bizmark-white.svg' : '/images/logo-bizmark.svg'" 
     alt="BizMark" 
     class="w-20 h-20">
```

## 📄 PDF Implementation

Already implemented in:
- `resources/views/client/services/summary-pdf.blade.php`

Features:
- ✅ Gradient logo in header
- ✅ Mini logo in footer
- ✅ Logo in digital signature
- ✅ Watermark with company name

## 🎯 Use Cases

| Context | Logo File | Size |
|---------|-----------|------|
| Website Header | logo-bizmark-full.svg | 200-300px wide |
| Mobile Header | logo-bizmark.svg | 50-60px |
| Footer | logo-bizmark.svg | 40-50px |
| Favicon | favicon.svg | 32x32 |
| PDF Header | Inline SVG | 75x75 |
| PDF Footer | Inline SVG | 16x16 |
| Email Signature | logo-bizmark-full.svg | 150-200px |
| Social Media | logo-bizmark.svg | 400x400 |
| Print Materials | logo-bizmark-full.svg | 300dpi |

## 🔧 Customization

### Changing Colors:
Edit SVG files and update:
```svg
<!-- Primary gradient -->
<stop offset="0%" style="stop-color:#0a66c2"/>
<stop offset="100%" style="stop-color:#004182"/>

<!-- Leaf color -->
<path fill="#ffffff"/>

<!-- Growth dots -->
<circle fill="#28a745"/>
```

### Adding Text:
Use `logo-bizmark-full.svg` as template:
```svg
<text x="95" y="50" font-size="32" fill="#0a66c2">
    Your Text
</text>
```

## ✨ Features

- ✅ **Scalable:** Vector format, infinite resolution
- ✅ **Lightweight:** < 5KB per file
- ✅ **Responsive:** Adapts to any size
- ✅ **Professional:** Modern design with meaning
- ✅ **Accessible:** Alt text support
- ✅ **Print-ready:** High quality at any size

## 🚫 Don'ts

- ❌ Don't rotate or skew
- ❌ Don't change proportions
- ❌ Don't add effects (shadow, glow)
- ❌ Don't use low-quality formats (JPG with compression)
- ❌ Don't place on busy backgrounds

## 📱 Responsive Examples

```html
<!-- Mobile: Icon only -->
<img src="/images/logo-bizmark.svg" 
     class="block sm:hidden w-12 h-12">

<!-- Desktop: Full logo -->
<img src="/images/logo-bizmark-full.svg" 
     class="hidden sm:block h-16">
```

## 🎨 Brand Assets

For complete brand guidelines, see: `LOGO_GUIDELINES.md`

---

**Version:** 1.0  
**Last Updated:** November 17, 2025  
**Maintained by:** BizMark Design Team  
**Contact:** design@bizmark.id
