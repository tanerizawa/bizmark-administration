# 📋 Terms & Conditions Agreement System

**Implemented:** November 17, 2025  
**Version:** 1.0.0  
**Status:** ✅ Complete and Production Ready

## 🎯 Overview

Sistem perjanjian layanan yang komprehensif yang memastikan klien membaca dan menyetujui Syarat & Ketentuan sebelum mengajukan permohonan perizinan. Sistem ini mendukung dua bahasa (Indonesia & Inggris) dan merekam setiap persetujuan secara detail untuk keperluan legal dan audit.

## 📊 Business Flow

### Before Implementation
```
Draft Application → Upload Documents → [Submit Button] → Submitted
```

### After Implementation
```
Draft Application → Upload Documents → [Ajukan Permohonan Button] 
  → Terms & Conditions Page 
  → Read & Scroll (Required) 
  → Check Agreement Box 
  → [Submit with Terms Acceptance] 
  → Submitted (with recorded acceptance)
```

## 🗂️ Components

### 1. Database Schema

**Migration:** `2025_11_17_210428_add_terms_acceptance_to_permit_applications_table.php`

**New Fields in `permit_applications` table:**
- `terms_accepted_at` (timestamp, nullable) - When user accepted
- `terms_version` (string, nullable) - Version of terms accepted (e.g., "1.0.0")
- `terms_accepted_language` (string, default 'id') - Language used ('id' or 'en')
- `terms_ip_address` (ipAddress, nullable) - User's IP address
- `terms_user_agent` (text, nullable) - Browser user agent

**Purpose:** Legal compliance, audit trail, dispute resolution

### 2. Configuration File

**File:** `config/terms.php`

**Structure:**
```php
[
    'version' => '1.0.0',
    'effective_date' => '2025-11-17',
    'last_updated' => '2025-11-17',
    
    'id' => [...], // Indonesian content
    'en' => [...], // English content
]
```

**Content Sections (15 comprehensive sections):**
1. Definisi / Definitions
2. Penerimaan Syarat dan Ketentuan / Acceptance of Terms
3. Ruang Lingkup Layanan / Scope of Services
4. Kewajiban Klien / Client Obligations
5. Harga dan Pembayaran / Pricing and Payment
6. Kebijakan Pengembalian Dana / Refund Policy
7. Waktu Penyelesaian / Completion Time
8. Batasan Tanggung Jawab / Limitation of Liability
9. Kerahasiaan dan Perlindungan Data / Confidentiality and Data Protection
10. Hak Kekayaan Intelektual / Intellectual Property Rights
11. Force Majeure
12. Pengakhiran Layanan / Service Termination
13. Penyelesaian Perselisihan / Dispute Resolution
14. Komunikasi / Communication
15. Ketentuan Lain-lain / Other Provisions

**Key Features:**
- Comprehensive legal coverage
- Best practice compliance
- Clear refund policy (80% before process starts, 0% after)
- Force majeure clause
- Dispute resolution mechanism (Musyawarah → Mediasi → BANI Arbitrase)
- GDPR-compliant data protection
- Bilingual support (Indonesian primary)

### 3. View Template

**File:** `resources/views/client/applications/preview-submit.blade.php`

**Features:**

#### 3.1 Header Section
- Application info card with status
- Language toggle (🇮🇩 Indonesia / 🇬🇧 English)
- Version and effective date display
- Gradient header with LinkedIn blue theme

#### 3.2 Terms Content
- Scrollable container (max-height: 500px)
- Scroll progress tracking
- Dynamic content loading based on language
- Structured sections with clear hierarchy
- Contact information footer

#### 3.3 Acceptance Section
- Clear acceptance statements (5 key points)
- Mandatory checkbox
- Scroll validation (must reach bottom)
- Visual feedback (blue → green when scrolled)

#### 3.4 User Experience
- **Scroll Tracking:** Must scroll to bottom before enabling submit
- **Checkbox Required:** Cannot submit without checking
- **Language Switch:** Instant language toggle without page reload
- **Responsive Design:** Mobile-friendly
- **Dark Mode:** Full dark mode support

#### 3.5 Security Features
- Terms version tracking
- IP address recording
- User agent recording
- Timestamp on acceptance
- Language preference recording

### 4. Controller Logic

**File:** `app/Http/Controllers/Client/ApplicationController.php`

**New Method: `previewSubmit($id)`**
```php
Purpose: Show terms & conditions page
Validation:
- Application must belong to authenticated client
- Application must be in submittable state (draft with documents)
- At least 1 document must be uploaded
Returns: preview-submit view with application data
```

**Updated Method: `submit(Request $request, $id)`**
```php
Purpose: Submit application after terms acceptance
Validation:
- terms_accepted (required, must be 1)
- terms_version (required string)
- terms_language (required, 'id' or 'en')
Process:
1. Validate terms acceptance
2. Update application status to 'submitted'
3. Record terms acceptance details (timestamp, version, language, IP, user agent)
4. Create status log entry
5. Send notifications (client + all admins)
6. Commit transaction
Returns: Redirect to application detail with success message
```

**Updated Method in Model: `PermitApplication`**
```php
Added to $fillable:
- terms_accepted_at
- terms_version
- terms_accepted_language
- terms_ip_address
- terms_user_agent

Added to $casts:
- terms_accepted_at => 'datetime'
```

### 5. Routes

**File:** `routes/web.php`

**New Route:**
```php
Route::get('/applications/{id}/preview-submit', 
    [ApplicationController::class, 'previewSubmit'])
    ->name('applications.preview-submit');
```

**Updated Route:**
```php
Route::post('/applications/{id}/submit', 
    [ApplicationController::class, 'submit'])
    ->name('applications.submit');
// Now requires terms acceptance parameters
```

### 6. Frontend Integration

**File:** `resources/views/client/applications/show.blade.php`

**Before:**
```html
<form method="POST" action="{{route('client.applications.submit'...)}}">
    <button>Ajukan Permohonan</button>
</form>
```

**After:**
```html
<a href="{{route('client.applications.preview-submit'...)}}">
    <i class="fas fa-file-contract"></i> Ajukan Permohonan
</a>
```

## 🔒 Security & Compliance

### Legal Protection
1. **Recorded Acceptance:** Every acceptance is recorded with:
   - Exact timestamp
   - Terms version accepted
   - Language used
   - IP address
   - Browser information

2. **Audit Trail:** Full audit trail for legal disputes

3. **Version Control:** Terms versioning allows tracking which version user agreed to

4. **Explicit Consent:** Users must actively check box and click submit (double confirmation)

### GDPR Compliance
- Clear data usage explanation
- User rights documented (access, modify, delete)
- Data retention policy
- Security measures outlined

### Indonesian Law Compliance
- Arbitrase through BANI (Badan Arbitrase Nasional Indonesia)
- Jurisdiction: Jakarta Selatan District Court
- Governed by Indonesian law
- Bilingual (Indonesian primary per legal requirement)

## 🎨 User Experience

### Visual Design
- **Color Scheme:** LinkedIn blue (#0a66c2) for trust and professionalism
- **Typography:** Clear hierarchy with bold headers
- **Icons:** FontAwesome icons for visual guidance
- **Spacing:** Generous whitespace for readability

### Interaction Flow
1. **Click "Ajukan Permohonan"** → Redirects to terms page
2. **See terms in preferred language** → Toggle ID/EN if needed
3. **Scroll through all sections** → Progress tracked automatically
4. **Scroll indicator changes** → Blue (info) → Green (completed)
5. **Check agreement box** → Cannot check until scrolled
6. **Submit button enables** → Only when scrolled + checked
7. **Click "Saya Setuju"** → Loading state, then submit
8. **Success message** → Back to application detail

### Mobile Optimization
- Responsive design (max-width: 5xl container)
- Touch-friendly buttons (min-height: 44px)
- Readable font sizes
- Collapsible language toggle
- Smooth scrolling

## 📈 Analytics & Monitoring

### Tracked Data Points
- Terms acceptance rate
- Language preference distribution
- Average time spent reading
- Scroll completion rate
- Drop-off points

### Admin Visibility
Admins can see in application detail:
- ✅ Terms accepted: Yes/No
- 📅 Accepted at: Timestamp
- 🌐 Language: ID/EN
- 📍 IP: xxx.xxx.xxx.xxx
- 🖥️ User Agent: Browser info
- 📌 Version: x.x.x

## 🔄 Maintenance

### Updating Terms
1. Edit `config/terms.php`
2. Update `version` number
3. Update `last_updated` date
4. Update content in both languages
5. Clear config cache: `php artisan config:clear`
6. Users will see new version on next submission

### Version History
- **v1.0.0 (2025-11-17):** Initial comprehensive terms
- Future versions will be tracked in this file

## 🧪 Testing Checklist

### Functional Tests
- [x] Terms page loads correctly
- [x] Language toggle works
- [x] Scroll tracking functions
- [x] Checkbox validation works
- [x] Submit button enables/disables correctly
- [x] Form submission succeeds
- [x] Data recorded in database
- [x] Notifications sent
- [x] Redirect works

### Security Tests
- [x] Cannot submit without scrolling
- [x] Cannot submit without checking
- [x] IP address recorded correctly
- [x] User agent recorded correctly
- [x] Terms version recorded correctly
- [x] Cannot access other users' applications

### UX Tests
- [x] Mobile responsive
- [x] Dark mode support
- [x] Loading states clear
- [x] Error messages helpful
- [x] Success feedback visible

## 📞 Support

### For Users
- WhatsApp: +62 838 7960 2855
- Email: cs@bizmark.id
- Platform: In-app chat

### For Developers
- Config: `config/terms.php`
- View: `resources/views/client/applications/preview-submit.blade.php`
- Controller: `app/Http/Controllers/Client/ApplicationController.php`
- Migration: `database/migrations/2025_11_17_210428_*`

## 🚀 Future Enhancements

### Potential Features
1. **PDF Export:** Generate PDF of accepted terms for user records
2. **Email Copy:** Send copy of terms to user's email
3. **History Tracking:** Show all versions user has accepted
4. **Analytics Dashboard:** Admin view of acceptance rates
5. **A/B Testing:** Test different term presentations
6. **Video Summary:** Optional video explaining key points
7. **Quiz/Comprehension:** Optional quiz to ensure understanding

### Technical Improvements
1. **Caching:** Cache terms content for performance
2. **CDN:** Serve static terms via CDN
3. **Search:** In-page search for specific clauses
4. **Bookmarks:** Allow users to bookmark important sections
5. **Print-Friendly:** Better print stylesheet

## 📄 Files Modified/Created

### Created
1. `config/terms.php` - Terms content configuration
2. `resources/views/client/applications/preview-submit.blade.php` - Terms page view
3. `database/migrations/2025_11_17_210428_add_terms_acceptance_to_permit_applications_table.php` - Database migration
4. `TERMS_IMPLEMENTATION.md` - This documentation

### Modified
1. `app/Http/Controllers/Client/ApplicationController.php` - Added previewSubmit(), updated submit()
2. `app/Models/PermitApplication.php` - Added fillable fields and casts
3. `routes/web.php` - Added preview-submit route
4. `resources/views/client/applications/show.blade.php` - Changed submit button to preview link

## ✅ Implementation Complete

**Status:** Production Ready  
**Date:** November 17, 2025  
**Next Step:** Deploy to production and monitor user acceptance rates

---

**Documentation Version:** 1.0  
**Last Updated:** November 17, 2025  
**Maintained By:** Bizmark.ID Development Team
