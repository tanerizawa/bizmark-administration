# 📧 Email Inbox - Quick Reference Guide

## 🎯 What Was Fixed

**Problem:** Email inbox menampilkan raw MIME content dengan encoding seperti `=20`, `=A0`, `--boundary`, dll.

**Solution:** Created EmailMimeParser service untuk parse dan decode MIME multipart messages dengan proper.

---

## 📁 Files Modified

1. **NEW:** `app/Services/EmailMimeParser.php` (330 lines)
2. **UPDATED:** `app/Models/EmailInbox.php` (accessor methods refactored)
3. **UPDATED:** `resources/views/admin/email/inbox/show.blade.php` (added Raw View)

---

## 🧪 Quick Test

### Step 1: Open Problem Email
```
URL: https://bizmark.id/admin/inbox/{id}
```

### Step 2: Check Views
- **HTML View:** Should show formatted HTML (if available)
- **Text View:** Should show clean text without MIME artifacts
- **Raw View:** Shows original MIME for debugging

### Step 3: Check Inbox List
```
URL: https://bizmark.id/admin/inbox
```
- Preview should show meaningful text (not MIME boundaries)

---

## 🔍 How Parser Works

### Input (Raw MIME):
```
--2ac1aebac543ce8e1e7f7735ef66fa1d95076145cf4efa440a359e891878
Content-Type: text/plain; charset="UTF-8"
Content-Transfer-Encoding: quoted-printable

003032 adalah kode=20Anda untuk Jobstreet=0A
```

### Processing:
1. Detect multipart boundary: `--2ac1aebac543...`
2. Split by boundary
3. Extract Content-Type: `text/plain`
4. Extract encoding: `quoted-printable`
5. Decode: `=20` → space, `=0A` → newline
6. Clean result

### Output:
```
003032 adalah kode Anda untuk Jobstreet
```

---

## 📊 EmailMimeParser API

### Main Methods:

```php
$parser = new EmailMimeParser();

// Parse full email
$result = $parser->parse($rawContent);
// Returns: ['html' => string, 'text' => string, 'boundary' => string]

// Extract specific parts
$html = $parser->extractHtmlPart($rawContent);
$text = $parser->extractTextPart($rawContent);

// Decode content
$decoded = $parser->decodeQuotedPrintable($content);

// Sanitize HTML (XSS protection)
$safe = $parser->sanitizeHtml($html);

// Extract preview
$preview = $parser->extractPreview($text, 150);

// Strip MIME artifacts
$clean = $parser->stripMimeBoundaries($content);
```

---

## 🛠️ Troubleshooting

### Email masih menampilkan raw content?

**Check 1:** Cache cleared?
```bash
php artisan optimize:clear
```

**Check 2:** Browser cache?
```
Ctrl+Shift+R (hard refresh)
```

**Check 3:** Parser working?
```bash
php artisan tinker

$parser = new \App\Services\EmailMimeParser();
$email = \App\Models\EmailInbox::find(1);
$parsed = $parser->parse($email->body_html);
print_r($parsed);
```

**Check 4:** View Raw
- Click "Raw View (Debug)" button
- See if original email content is corrupted

---

## 📝 Common Issues

### Issue: Preview masih kosong di inbox list
**Fix:** Check `getPreviewAttribute()` in EmailInbox model

### Issue: HTML tidak ter-render
**Fix:** Check jika HTML ter-sanitize terlalu agresif, adjust `sanitizeHtml()`

### Issue: Performance lambat
**Fix:** Parser berjalan on-the-fly. Consider parse saat email ingestion (future improvement)

---

## 🔐 Security Features

✅ **XSS Protection:**
- `<script>` tags removed
- `<iframe>` removed
- Event handlers (`onclick`, etc) stripped
- `javascript:` protocol blocked

✅ **Safe Display:**
- HTML sanitized before render
- Raw view only for admins
- Content treated as untrusted

---

## 📈 Next Steps (Optional)

### 1. Parse on Email Ingestion
Instead of parsing on display, parse when email arrives via webhook:

```php
// In EmailWebhookController
$parser = new EmailMimeParser();
$parsed = $parser->parse($incomingEmail);

EmailInbox::create([
    'body_html' => $parsed['html'],  // Already clean!
    'body_text' => $parsed['text'],  // Already clean!
    'body_raw' => $incomingEmail,    // Store original
]);
```

**Benefits:**
- Faster page load (no parsing overhead)
- Better error handling
- Can log parsing failures

### 2. Re-parse Existing Emails
Create artisan command to fix historical emails:

```bash
php artisan make:command ReparseEmails
```

```php
EmailInbox::where('created_at', '<', now()->subDays(30))
    ->chunk(100, function($emails) {
        foreach ($emails as $email) {
            $parser = new EmailMimeParser();
            $parsed = $parser->parse($email->body_raw ?? $email->body_html);
            
            $email->update([
                'body_html' => $parsed['html'],
                'body_text' => $parsed['text'],
            ]);
        }
    });
```

---

## 🎓 Learning Resources

- **Quoted-Printable:** https://en.wikipedia.org/wiki/Quoted-printable
- **MIME Multipart:** https://www.w3.org/Protocols/rfc1341/7_2_Multipart.html
- **RFC 2045 (MIME):** https://tools.ietf.org/html/rfc2045
- **PHP quoted_printable_decode:** https://www.php.net/manual/en/function.quoted-printable-decode.php

---

## ✅ Verification Checklist

Testing pada production:

- [ ] Buka email Jobstreet (OTP code)
  - [ ] HTML View menampilkan formatted content
  - [ ] Text View menampilkan clean text
  - [ ] Raw View menampilkan original MIME
  
- [ ] Check inbox list preview
  - [ ] Preview menampilkan meaningful text
  - [ ] Tidak ada `=20`, `=A0`, `--boundary`, dll
  
- [ ] Test dengan email lain
  - [ ] HTML email dari Gmail/Yahoo
  - [ ] Plain text email
  - [ ] Email dengan attachment
  
- [ ] Performance check
  - [ ] Page load < 1 detik
  - [ ] No PHP errors in log
  
- [ ] Security check
  - [ ] Try inject `<script>alert('XSS')</script>` in test email
  - [ ] Verify script tidak ter-execute

---

## 📞 Contact

**Issue tracking:** Create GitHub issue atau contact admin

**Logs location:**
```bash
storage/logs/laravel.log
```

**Debug mode:**
```bash
# Enable debug logging
LOG_LEVEL=debug
```

---

**Version:** 1.0.0  
**Last Updated:** 23 November 2025  
**Status:** ✅ Production Ready
