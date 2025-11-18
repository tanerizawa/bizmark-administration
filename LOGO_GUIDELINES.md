# BizMark Logo Guidelines

## 📋 Logo Files

### 1. **Logo Icon Only** (Circular)
**File:** `public/images/logo-bizmark.svg`
- **Ukuran:** 80x80px (scalable)
- **Format:** SVG
- **Penggunaan:** 
  - Favicon
  - App icon
  - Social media profile
  - Small spaces di UI

### 2. **Logo Full** (Icon + Text)
**File:** `public/images/logo-bizmark-full.svg`
- **Ukuran:** 300x80px (scalable)
- **Format:** SVG
- **Penggunaan:**
  - Header website
  - Email signature
  - Document headers
  - Print materials

### 3. **Favicon**
**File:** `public/images/favicon.svg`
- **Ukuran:** 32x32px (optimized)
- **Format:** SVG
- **Penggunaan:**
  - Browser tab icon
  - PWA icon
  - Mobile bookmark

---

## 🎨 Logo Design Concept

### Visual Elements:
1. **Leaf Symbol** 🍃
   - Represents **growth** and **sustainability**
   - Symbolizes **natural business development**
   - Modern, organic shape

2. **Growth Dots** (Green circles)
   - Symbolize **milestones** and **achievements**
   - Represent **business expansion**
   - Color: #28a745 (success green)

3. **Letter "B" Integration**
   - Subtle branding element
   - Integrated into leaf design
   - Low opacity for elegance

4. **Gradient Background**
   - Professional blue gradient
   - Primary: #0a66c2 (LinkedIn blue)
   - Secondary: #004182 (darker blue)
   - Conveys **trust** and **professionalism**

---

## 🎨 Color Palette

### Primary Colors:
- **BizMark Blue:** `#0a66c2`
- **Dark Blue:** `#004182`
- **Success Green:** `#28a745`
- **White:** `#ffffff`
- **Light Blue:** `#e3f2fd`

### Usage:
- Background: Blue gradient
- Leaf: White with 95% opacity
- Veins: Light blue with opacity
- Growth dots: Success green
- Text: BizMark blue or white

---

## 📐 Logo Spacing & Clearspace

### Minimum Clearspace:
- All sides: **10% of logo width**
- Example: For 80px logo → 8px clearspace

### Minimum Size:
- Digital: **24px** (height)
- Print: **0.5 inch** (height)

### Safe Sizes:
- **Small:** 32px - 64px (favicon, icons)
- **Medium:** 80px - 150px (buttons, cards)
- **Large:** 200px+ (headers, banners)

---

## ✅ Do's and Don'ts

### ✅ DO:
- Use official SVG files
- Maintain aspect ratio
- Keep minimum clearspace
- Use on white or dark backgrounds
- Scale proportionally

### ❌ DON'T:
- Rotate or skew
- Change colors (except approved variations)
- Add effects (shadow, glow, etc.)
- Stretch or compress
- Place on busy backgrounds

---

## 🖼️ Logo Variations

### 1. **Primary (Full Color)**
```html
<img src="/images/logo-bizmark.svg" alt="BizMark Logo" width="80">
```

### 2. **With Text**
```html
<img src="/images/logo-bizmark-full.svg" alt="BizMark Indonesia" width="300">
```

### 3. **Favicon**
```html
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
```

---

## 💻 Implementation Examples

### HTML:
```html
<!-- Header -->
<header>
    <img src="/images/logo-bizmark-full.svg" alt="BizMark Indonesia" height="60">
</header>

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
```

### CSS:
```css
.logo {
    width: 80px;
    height: auto;
    transition: transform 0.3s ease;
}

.logo:hover {
    transform: scale(1.05);
}
```

### PDF (Blade Template):
```php
<svg viewBox="0 0 80 80" width="75" height="75">
    <!-- Include SVG code from logo-bizmark.svg -->
</svg>
```

---

## 📱 Responsive Sizes

| Breakpoint | Logo Size | Usage |
|------------|-----------|-------|
| Mobile (< 640px) | 50px | Compact header |
| Tablet (640-1024px) | 70px | Standard header |
| Desktop (> 1024px) | 80-100px | Full header |
| Print | 1.5 inch | Documents |

---

## 🎯 Use Cases

### 1. **Website**
- Header/Navigation: Full logo (300x80)
- Footer: Icon only (50x50)
- Favicon: favicon.svg (32x32)

### 2. **Documents (PDF)**
- Letterhead: Full logo (80px height)
- Footer: Mini icon (16x16)
- Digital signature: Icon with text (40x40)

### 3. **Social Media**
- Profile picture: Icon only (400x400)
- Cover image: Full logo on branded background
- Posts: Watermark icon (bottom right)

### 4. **Print Materials**
- Business cards: Full logo (2 inch width)
- Letterhead: Full logo (1.5 inch)
- Banners: Large full logo (8-12 inch)

---

## 🔄 File Formats Available

| Format | Use Case | File |
|--------|----------|------|
| SVG | Web, scalable graphics | logo-bizmark.svg |
| SVG Full | Headers, branding | logo-bizmark-full.svg |
| SVG Favicon | Browser icon | favicon.svg |

**Note:** SVG is preferred for all digital use as it's infinitely scalable without quality loss.

---

## 📞 Contact

For logo modifications or new variations, contact:
**Design Team:** design@bizmark.id

---

**Last Updated:** November 17, 2025  
**Version:** 1.0  
**Created by:** BizMark Design Team
