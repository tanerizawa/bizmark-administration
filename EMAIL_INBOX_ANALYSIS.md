# 📧 ANALISIS: Email Inbox Display Issues

**Tanggal:** 23 November 2025  
**Status:** 🔴 CRITICAL - Email tidak tampil dengan benar

---

## 🔍 MASALAH YANG DITEMUKAN

### 1. **Raw MIME Content Displayed**
Email dari Jobstreet menampilkan:
```
--2ac1aebac543ce8e1e7f7735ef66fa1d95076145cf4efa440a359e891878
Mime-Version: 1.0
Content-Type: multipart/alternative...
=20 =20 =A0 =20 =A0
```

**Root Cause:**
- Email webhook/ingestion menyimpan raw MIME multipart message
- `body_html` dan `body_text` berisi MIME boundaries dan headers
- Quoted-printable encoding (=20, =A0, =3D) tidak di-decode
- Multipart boundaries tidak di-strip

### 2. **Parsing Method Tidak Cukup Robust**

**Existing Code di `EmailInbox.php`:**
```php
public function getCleanBodyTextAttribute() {
    // Regex hanya menghapus sebagian MIME headers
    // Tidak handle:
    // - Multipart boundaries
    // - Quoted-printable decoding
    // - Base64 content
    // - Nested MIME parts
}
```

**Limitasi:**
- Regex pattern tidak comprehensive
- Tidak decode quoted-printable (=XX)
- Tidak handle multipart/alternative
- Tidak extract specific MIME part (text/html vs text/plain)

---

## 📊 STRUKTUR EMAIL YANG MASUK

### Anatomy Email Jobstreet:
```
--[BOUNDARY]
Content-Type: text/plain; charset="UTF-8"
Content-Transfer-Encoding: quoted-printable

[Plain text dengan =20, =A0, etc]

--[BOUNDARY]
Content-Type: text/html; charset="UTF-8"
Content-Transfer-Encoding: quoted-printable

[HTML dengan =3D untuk =, dll]

--[BOUNDARY]--
```

### Encoding Issues:
- `=20` → Space
- `=A0` → Non-breaking space  
- `=3D` → `=`
- `=\r\n` → Soft line break (should be removed)

---

## 🎯 SOLUSI YANG DIPERLUKAN

### Phase 1: Improve MIME Parsing ✅

**1. Add Proper MIME Parser**
- Gunakan PHP's built-in `imap_mime_header_decode()`
- Or install `php-mime-mail-parser` library
- Or create robust parser dengan regex yang proper

**2. Decode Quoted-Printable**
- Use `quoted_printable_decode()` function
- Strip MIME boundaries
- Extract only relevant content parts

**3. Extract Correct MIME Part**
```php
// Detect multipart/alternative
// Extract text/html part (priority)
// Fallback to text/plain
// Decode based on Content-Transfer-Encoding
```

### Phase 2: Fix Webhook/Ingestion ✅

**Update `EmailWebhookController.php`:**
- Parse email sebelum save ke database
- Extract clean body_html dan body_text
- Store original raw content di field terpisah (jika diperlukan)

### Phase 3: Enhanced Display ✅

**Update View (`show.blade.php`):**
- Add error handling jika parsing gagal
- Show warning untuk malformed emails
- Add "View Raw" option untuk debugging

---

## 🛠️ IMPLEMENTATION PLAN

### Task 1: Create Robust MIME Parser Helper
**File:** `app/Services/EmailMimeParser.php`

**Features:**
- Parse multipart messages
- Decode quoted-printable
- Decode base64
- Extract specific MIME parts
- Handle nested multipart
- Sanitize HTML untuk XSS protection

### Task 2: Update EmailInbox Model
**File:** `app/Models/EmailInbox.php`

**Changes:**
- Refactor `getCleanBodyTextAttribute()`
- Refactor `getCleanBodyHtmlAttribute()`
- Use new MimeParser service
- Add `getRawBodyAttribute()` for debugging

### Task 3: Fix Email Webhook
**File:** `app/Http/Controllers/EmailWebhookController.php`

**Changes:**
- Parse email dengan MimeParser sebelum save
- Store clean content di body_html/body_text
- Optionally store raw content di new field

### Task 4: Update Views
**Files:** 
- `resources/views/admin/email/inbox/show.blade.php`
- `resources/views/admin/email/inbox/index.blade.php`

**Changes:**
- Better error handling
- Add "View Raw" button
- Improved HTML sanitization
- Better preview generation

---

## 📝 DETAILED TECHNICAL SPECS

### EmailMimeParser Service

```php
class EmailMimeParser
{
    /**
     * Parse raw email content
     * 
     * @param string $rawContent
     * @return array ['html' => string, 'text' => string, 'parts' => array]
     */
    public function parse(string $rawContent): array
    {
        // 1. Detect if multipart
        // 2. Extract boundary
        // 3. Split by boundary
        // 4. Parse each part
        // 5. Decode based on encoding
        // 6. Return structured array
    }
    
    /**
     * Decode quoted-printable
     */
    public function decodeQuotedPrintable(string $content): string
    {
        return quoted_printable_decode($content);
    }
    
    /**
     * Extract HTML part from multipart
     */
    public function extractHtmlPart(string $rawContent): ?string
    {
        // Find Content-Type: text/html part
        // Decode it
        // Return clean HTML
    }
    
    /**
     * Extract text part from multipart
     */
    public function extractTextPart(string $rawContent): ?string
    {
        // Find Content-Type: text/plain part
        // Decode it
        // Return clean text
    }
    
    /**
     * Sanitize HTML for display
     */
    public function sanitizeHtml(string $html): string
    {
        // Remove script tags
        // Remove dangerous attributes
        // Keep safe HTML
    }
}
```

### Updated EmailInbox Model Methods

```php
public function getCleanBodyTextAttribute()
{
    if (!$this->body_text) {
        return null;
    }
    
    $parser = new EmailMimeParser();
    $parsed = $parser->extractTextPart($this->body_text);
    
    return $parsed ?? $this->body_text;
}

public function getCleanBodyHtmlAttribute()
{
    if (!$this->body_html) {
        return null;
    }
    
    $parser = new EmailMimeParser();
    $parsed = $parser->extractHtmlPart($this->body_html);
    $sanitized = $parser->sanitizeHtml($parsed ?? $this->body_html);
    
    return $sanitized;
}

public function getPreviewAttribute()
{
    $text = $this->clean_body_text;
    
    if (!$text) {
        // Fallback to strip_tags dari HTML
        $text = strip_tags($this->clean_body_html ?? '');
    }
    
    // Extract meaningful preview (first 150 chars)
    $preview = Str::limit($text, 150);
    
    return $preview;
}
```

---

## ⚠️ CONSIDERATIONS

### 1. **Performance**
- MIME parsing bisa heavy untuk email besar
- Consider caching parsed results
- Parse on save, bukan on display

### 2. **Security**
- HTML emails bisa contain XSS
- Always sanitize HTML sebelum display
- Use HTMLPurifier atau DOMPurify

### 3. **Backwards Compatibility**
- Existing emails di database mungkin sudah corrupted
- Perlu migration untuk re-parse?
- Or handle gracefully dengan fallback

### 4. **External Libraries**
**Option A: Pure PHP**
- Pros: No dependencies
- Cons: More complex code

**Option B: Use Library**
```bash
composer require php-mime-mail-parser/php-mime-mail-parser
```
- Pros: Battle-tested, comprehensive
- Cons: External dependency

---

## 🎯 RECOMMENDED APPROACH

### Immediate Fix (Quick):
1. Add `quoted_printable_decode()` to existing methods
2. Strip MIME boundaries dengan regex yang lebih baik
3. Handle multipart/alternative detection

### Long-term Solution (Robust):
1. Install `php-mime-mail-parser` library
2. Create EmailMimeParser service wrapper
3. Update webhook to parse on ingestion
4. Add migration to re-parse existing emails

---

## 📈 PRIORITY

- **P0 (Critical):** Fix display untuk email baru (webhook parsing)
- **P1 (High):** Improve model methods untuk existing emails
- **P2 (Medium):** Add "View Raw" debugging option
- **P3 (Low):** Re-parse historical emails

---

## 🔗 REFERENCES

- PHP quoted_printable_decode: https://www.php.net/manual/en/function.quoted-printable-decode.php
- RFC 2045 (MIME): https://tools.ietf.org/html/rfc2045
- php-mime-mail-parser: https://github.com/php-mime-mail-parser/php-mime-mail-parser
- HTML Purifier: http://htmlpurifier.org/

---

**Next Steps:** Implement EmailMimeParser service dan update model methods.
