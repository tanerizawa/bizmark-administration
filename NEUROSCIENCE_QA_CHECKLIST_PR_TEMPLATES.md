# 📋 NEUROSCIENCE DESIGN REFACTOR - QA CHECKLIST & PR TEMPLATES  
## Quality Assurance Framework & Development Workflow

**Created:** 12 Januari 2026  
**Purpose:** Standardize QA process untuk neuroscience design implementation  
**Scope:** Admin panel refactor, client portal unification, performance optimization  
**Team:** Development, QA, Design validation

---

## ✅ QA CHECKLIST PER FILE REFACTOR

### **Pre-Development Checklist**

#### **File Analysis** 
- [ ] **Hardcoded Value Count:** Document total instances to replace
- [ ] **Pattern Assessment:** Identify which established patterns apply
- [ ] **Complexity Rating:** Simple/Medium/Complex (affects time estimate)
- [ ] **Dependencies:** Check if file imports/affects other components
- [ ] **Visual Reference:** Take before screenshot for comparison

#### **Impact Assessment**
- [ ] **User Impact:** High/Medium/Low (critical user flows?)
- [ ] **Business Logic:** Any payment/authentication/security concerns?
- [ ] **Mobile Responsive:** Does file have mobile-specific styling?
- [ ] **Cross-Browser:** Any browser-specific CSS that needs attention?
- [ ] **Performance:** Large file size or complex animations?

### **Development Phase Checklist**

#### **Token Application** 
- [ ] **Color Replacement:** All hardcoded colors → neuroscience variables
```css
/* ❌ OLD */ color: #FFFFFF; background: rgba(255,255,255,0.1);
/* ✅ NEW */ color: var(--text-dark-primary); background: var(--dark-bg-elevated);
```

- [ ] **Typography Migration:** Font sizes/weights → typography tokens  
```css
/* ❌ OLD */ font-size: 14px; font-weight: 600;
/* ✅ NEW */ font-size: var(--text-sm); font-weight: var(--font-weight-semibold);
```

- [ ] **Spacing Standardization:** Margins/padding → 8px grid tokens
```css
/* ❌ OLD */ padding: 16px 24px; margin: 20px 0;
/* ✅ NEW */ padding: var(--space-4) var(--space-6); margin: var(--space-5) 0;
```

- [ ] **Shadow & Effects:** Box shadows → soft elevation tokens
```css
/* ❌ OLD */ box-shadow: 0 4px 12px rgba(0,0,0,0.15);
/* ✅ NEW */ box-shadow: var(--shadow-soft-lg);
```

#### **Code Quality**
- [ ] **CSS Validation:** W3C CSS validator passes
- [ ] **No Inline Styles:** Remove style="" attributes where possible
- [ ] **Class Naming:** BEM methodology or utility classes only
- [ ] **Comments:** Remove outdated comments, add clarification where needed
- [ ] **Indentation:** Consistent 4-space indentation

### **Post-Development Checklist**

#### **Visual Validation**
- [ ] **Pixel Perfect:** Compare before/after screenshots
- [ ] **Color Accuracy:** Use color picker to verify neuroscience palette
- [ ] **Typography:** Confirm font sizes, weights, line heights
- [ ] **Spacing:** Verify 8px grid compliance with measurement tools
- [ ] **Responsive:** Test at 320px, 768px, 1024px, 1440px+ breakpoints

#### **Functional Testing**
- [ ] **Interactive Elements:** Buttons, links, form inputs work correctly
- [ ] **Hover States:** All interactive elements have proper hover feedback
- [ ] **Focus States:** Keyboard navigation works, focus indicators visible
- [ ] **Error States:** Error messages display correctly with proper colors
- [ ] **Loading States:** Loading spinners/skeletons work if applicable

#### **Cross-Browser Validation**
- [ ] **Chrome 120+:** Full functionality
- [ ] **Firefox 121+:** Full functionality  
- [ ] **Safari 17+:** Full functionality
- [ ] **Edge 120+:** Full functionality
- [ ] **Mobile Safari:** Touch interactions work
- [ ] **Chrome Mobile:** Responsive layout correct

#### **Performance Check**
- [ ] **File Size:** Check for any significant increase in file size
- [ ] **Render Performance:** No new layout thrashing or performance issues
- [ ] **Network Panel:** Verify no additional HTTP requests introduced
- [ ] **Lighthouse:** No regression in performance score

### **Accessibility Verification**

#### **WCAG 2.1 AA Compliance**
- [ ] **Color Contrast:** 4.5:1 ratio minimum for all text
- [ ] **Focus Indicators:** Visible focus outline on all interactive elements
- [ ] **Semantic HTML:** Proper heading hierarchy, form labels, ARIA attributes
- [ ] **Keyboard Navigation:** All functionality accessible via keyboard
- [ ] **Screen Reader:** Test with NVDA/VoiceOver for basic functionality

#### **Automated Testing**
- [ ] **axe-core:** Automated accessibility scan passes
- [ ] **WAVE:** Web accessibility evaluation tool passes  
- [ ] **Lighthouse:** Accessibility score 95+
- [ ] **Color Contrast Analyzer:** Manual spot-checking of key elements

---

## 📝 PR TEMPLATE - NEUROSCIENCE DESIGN REFACTOR

### **Pull Request Title Format**
```
refactor: [COMPONENT] neuroscience design tokens - [FILE_COUNT] files
```

**Examples:**
- `refactor: email management neuroscience design tokens - 6 files`
- `refactor: client portal color unification - 15 files`
- `refactor: admin forms typography & spacing tokens - 8 files`

### **PR Description Template**

```markdown
## 🧠 Neuroscience Design Refactor

### Summary
Brief description of what this PR accomplishes in the context of our neuroscience design system implementation.

### Files Modified
- `path/to/file1.blade.php` - [Brief description of changes]
- `path/to/file2.blade.php` - [Brief description of changes]  
- `path/to/file3.blade.php` - [Brief description of changes]

### Changes Made

#### ✅ Tokens Applied
- **Colors:** [X] hardcoded values → neuroscience color variables
- **Typography:** [X] font sizes/weights → typography tokens
- **Spacing:** [X] margins/padding → 8px grid tokens  
- **Shadows:** [X] box shadows → elevation tokens
- **Other:** [Any other token types applied]

#### 🔧 Code Quality Improvements
- [ ] Removed inline styles
- [ ] Consistent CSS formatting
- [ ] Updated class naming
- [ ] Removed unused CSS

#### 📱 Responsive & Accessibility  
- [ ] Mobile responsive verified
- [ ] Keyboard navigation tested
- [ ] Color contrast verified
- [ ] Screen reader compatible

### Before/After Screenshots

#### Before
![Before Screenshot](link-to-before-screenshot)

#### After  
![After Screenshot](link-to-after-screenshot)

### Testing Completed

#### ✅ Visual Testing
- [ ] Chrome desktop/mobile
- [ ] Firefox desktop/mobile
- [ ] Safari desktop/mobile
- [ ] Edge desktop

#### ✅ Functional Testing
- [ ] All interactive elements working
- [ ] Form submissions working  
- [ ] Navigation functioning
- [ ] Responsive layout verified

#### ✅ Accessibility Testing
- [ ] axe-core scan passed
- [ ] Keyboard navigation verified
- [ ] Color contrast checked
- [ ] Screen reader tested

#### ✅ Performance Testing
- [ ] No Lighthouse regression
- [ ] No render performance issues
- [ ] File size increase acceptable

### Deployment Notes

#### Configuration Changes
- [ ] No environment variables changed
- [ ] No database migrations required
- [ ] No cache clearing required
- [ ] No third-party service changes

#### Rollback Plan
If issues arise after deployment:
1. Revert this PR immediately
2. Check [specific potential issues based on changes]
3. Follow standard rollback procedure

### Related Issues/PRs
- Closes #[issue-number]
- Related to PR #[pr-number]
- Part of Sprint [number]: [sprint-name]

### Next Steps
- [ ] Monitor for any user reports after deployment
- [ ] Continue with next set of files in sprint backlog
- [ ] Update progress tracking documentation

---

### Review Checklist (for reviewer)

#### Code Review
- [ ] Token usage follows established patterns
- [ ] No hardcoded values remain  
- [ ] CSS quality meets standards
- [ ] No functionality regressions

#### Visual Review
- [ ] Screenshots verify visual consistency
- [ ] Neuroscience color palette correctly applied
- [ ] Typography hierarchy maintained
- [ ] Spacing follows 8px grid

#### Testing Review
- [ ] All testing checkboxes completed
- [ ] Edge cases considered
- [ ] Mobile responsiveness verified
- [ ] Accessibility standards met

/cc @[team-lead] @[designer] @[qa-engineer]
```

---

## 🚀 AUTOMATED QA PIPELINE

### **GitHub Actions Workflow**

```yaml
name: Neuroscience Design QA Pipeline

on:
  pull_request:
    paths:
      - 'resources/views/**/*.blade.php'
      - 'resources/css/**/*.css'

jobs:
  neuroscience-qa:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v4
    
    - name: Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: '18'
        
    - name: Install dependencies
      run: npm ci
      
    - name: CSS Validation
      run: |
        # W3C CSS validator
        npm run css:validate
        
    - name: Check for hardcoded values  
      run: |
        # Custom script to detect hardcoded rgba, #colors, px values
        npm run lint:hardcoded
        
    - name: Accessibility scan
      run: |
        # axe-core automated testing
        npm run test:a11y
        
    - name: Performance audit
      run: |
        # Lighthouse CI for performance regression
        npm run test:lighthouse
        
    - name: Visual regression  
      run: |
        # Percy/Chromatic for visual changes
        npm run test:visual
```

### **Custom Linting Rules**

```javascript
// .eslintrc.js - Custom rules for neuroscience compliance
module.exports = {
  rules: {
    // Detect hardcoded colors
    'no-hardcoded-colors': 'error',
    
    // Require CSS variables for specific properties
    'require-css-variables': ['error', {
      properties: ['color', 'background-color', 'font-size', 'margin', 'padding']
    }],
    
    // Enforce 8px grid for spacing
    'enforce-8px-grid': 'error',
    
    // Require semantic class names
    'semantic-class-names': 'warn'
  }
};
```

---

## 📊 PROGRESS TRACKING TEMPLATES

### **Sprint Progress Report Template**

```markdown
# Sprint [X] Progress Report - Week [Y]

## 📊 Overview
- **Sprint Goal:** [Sprint objective]
- **Week:** [Date range]  
- **Team:** [Team member allocations]

## ✅ Completed This Week
- [ ] **File:** `path/to/file.blade.php` - [X] hardcoded values removed
- [ ] **File:** `path/to/file.blade.php` - [X] hardcoded values removed
- [ ] **Testing:** Cross-browser validation completed
- [ ] **Documentation:** Updated patterns guide

## 🚧 In Progress
- **File:** `path/to/file.blade.php` - 60% complete, [blocker if any]
- **Testing:** Performance optimization - starting tomorrow

## 📋 Next Week Plan
- [ ] Complete remaining 3 files in email management
- [ ] Start permit dashboard refactor
- [ ] Update visual regression test suite

## 🚨 Blockers & Risks
- **Blocker:** [Description and owner]
- **Risk:** [Description and mitigation plan]

## 📈 Metrics
- **Files Completed:** 8/18 sprint target
- **Hardcoded Values Removed:** 156 this week
- **Test Coverage:** 95% passing
- **Performance:** No regression detected

## 🎯 Sprint Burn Down
Week 1: 40% complete ✅  
Week 2: 85% target  

---
**Next Review:** [Date]
**Report By:** [Name]
```

### **File Refactor Tracking Sheet**

```markdown
| File Path | Size | Hardcoded Values | Priority | Status | Assignee | Est Hours | Actual Hours | Notes |
|-----------|------|------------------|----------|--------|----------|-----------|--------------|-------|
| `admin/email/index.blade.php` | 450 lines | 23 | High | ✅ Complete | John | 2.5 | 3.0 | Complex table layout |
| `admin/email/tabs/inbox.blade.php` | 320 lines | 18 | High | 🚧 In Progress | Sarah | 2.0 | - | Modal refactor needed |
| `admin/permits/dashboard.blade.php` | 520 lines | 31 | Medium | 📋 Planned | Mike | 3.0 | - | Dependencies on charts |
```

---

## 🎯 QUALITY GATES

### **Definition of Done - File Level**

Before any file can be marked as "Complete":

#### **Code Quality Gates**  
- ✅ Zero hardcoded `rgba()` values
- ✅ Zero hardcoded hex colors (`#ffffff`)
- ✅ Zero hardcoded pixel values for spacing
- ✅ All typography using size/weight tokens
- ✅ CSS validation passes
- ✅ No inline `style=""` attributes

#### **Visual Quality Gates**
- ✅ Neuroscience color palette verified
- ✅ 8px grid spacing compliance  
- ✅ Typography hierarchy maintained
- ✅ Mobile responsive at all breakpoints
- ✅ Visual regression test passes

#### **Functional Quality Gates**
- ✅ All interactive elements working
- ✅ Form submissions functioning  
- ✅ No JavaScript errors in console
- ✅ Cross-browser compatibility verified
- ✅ Performance within acceptable range

#### **Accessibility Quality Gates**
- ✅ axe-core automated scan passes
- ✅ Color contrast 4.5:1 minimum
- ✅ Keyboard navigation functional
- ✅ Screen reader basic compatibility
- ✅ Focus indicators visible

### **Definition of Done - Sprint Level**

Before any sprint can be marked as "Complete":

#### **Sprint Completion Gates**
- ✅ All targeted files meet individual DoD
- ✅ Sprint goal achieved (functionality delivered)
- ✅ No critical bugs introduced
- ✅ Documentation updated
- ✅ Team retrospective completed

#### **Production Readiness Gates**
- ✅ Staging environment testing passed
- ✅ Performance benchmarks met
- ✅ Security review completed (if applicable)
- ✅ Rollback plan prepared
- ✅ Monitoring alerts configured

---

## 🛠️ TROUBLESHOOTING GUIDE

### **Common Issues & Solutions**

#### **Issue: Color Not Displaying Correctly**
**Symptoms:** Color looks different than expected  
**Diagnostic Steps:**
1. Check browser dev tools for CSS variable resolution
2. Verify CSS variable is defined in `:root`  
3. Check for CSS specificity conflicts
4. Verify browser supports CSS custom properties

**Solutions:**
- Add `!important` temporarily to test
- Check CSS load order in `app.css`
- Clear browser cache and hard refresh
- Test in incognito mode

#### **Issue: Spacing Not Following 8px Grid**  
**Symptoms:** Elements don't align properly  
**Diagnostic Steps:**
1. Use browser dev tools ruler/measurement
2. Check computed styles for margin/padding values
3. Look for conflicting CSS rules
4. Verify correct spacing token usage

**Solutions:**
- Replace hardcoded values with tokens
- Check for inherited spacing from parent elements
- Use CSS reset to eliminate browser defaults
- Apply spacing tokens to correct elements

#### **Issue: Mobile Layout Broken**
**Symptoms:** Layout doesn't work on mobile devices  
**Diagnostic Steps:**
1. Test in responsive design mode (Chrome DevTools)  
2. Check for fixed widths or overflow issues
3. Verify responsive breakpoints
4. Test on actual devices

**Solutions:**
- Use flexible units (rem, %, vw/vh) instead of fixed px
- Add appropriate media queries
- Test mobile-first approach
- Check touch target sizes (minimum 44px)

#### **Issue: Performance Regression**
**Symptoms:** Page loads slower after changes  
**Diagnostic Steps:**
1. Run Lighthouse performance audit
2. Check Network panel for additional requests
3. Analyze CSS file size increase
4. Check for render-blocking resources

**Solutions:**
- Optimize CSS delivery (critical CSS)
- Remove unused CSS rules
- Compress images and assets
- Implement lazy loading where appropriate

---

## 📈 SUCCESS METRICS DASHBOARD

### **Key Performance Indicators**

#### **Development Velocity**
- **Files per Sprint:** Target 15-20, Track actual completion
- **Hours per File:** Target 2-3 hours average, Track actual time
- **Pattern Compliance:** Target 100% token usage, Track violations
- **Rework Rate:** Target <10%, Track files needing revision

#### **Quality Metrics**  
- **Bug Introduction Rate:** Target 0 critical bugs per sprint
- **CSS Validation:** Target 100% W3C compliance
- **Accessibility Score:** Target 95+ Lighthouse accessibility
- **Performance Score:** Target 90+ Lighthouse performance

#### **User Experience Impact**
- **Task Completion Rate:** Baseline vs improved (target +20%)
- **User Satisfaction:** NPS score tracking
- **Support Tickets:** UI/UX related tickets (target -15%)
- **Page Load Time:** Core Web Vitals improvement

---

**Document Status:** ✅ **QA FRAMEWORK COMPLETE**  
**Ready for Sprint 1:** January 13, 2026  
**Next Review:** End of Sprint 1 (January 26, 2026)  
**Version:** 1.0.0 (Complete QA & PR Framework)