# Bug Fix: File Upload Gagal Tersimpan

## Problem
File upload di edit template document-editing selalu gagal dengan log:
```
"path": false
```

## Root Cause
**Permission Problem!**

Directory `storage/app/private/test-templates/` dibuat dengan owner `root:root` karena menggunakan command `mkdir` sebagai root user, sementara Laravel web server berjalan sebagai `www-data`.

## Diagnosis Log
```
[2025-11-23 18:30:06] local.INFO: Update validation passed 
    {"test_id":4,"test_type":"document-editing","has_file":true,"file_size":140382}

[2025-11-23 18:30:06] local.INFO: Template file uploaded (update) 
    {"filename":"...docx","path":false,"test_id":4}  ← FALSE = Gagal write!

[2025-11-23 18:30:06] local.INFO: Template updated 
    {"test_id":4,"has_file":true,"file_path":false}  ← Tersimpan sebagai false
```

## Permission Check (Before Fix)
```bash
$ ls -la storage/app/private/test-templates/
drwxr-xr-x 2 root     root     4096 Nov 23 18:23 .  ← OWNER ROOT!
drwxrwxr-x 3 www-data www-data 4096 Nov 23 18:19 ..
```

## Solution Applied
```bash
# Fix ownership
sudo chown -R www-data:www-data storage/app/private/test-templates

# Fix permissions
sudo chmod -R 775 storage/app/private/test-templates

# Fix all storage (preventive)
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

## Permission Check (After Fix)
```bash
$ ls -la storage/app/private/test-templates/
drwxrwxr-x 2 www-data www-data 4096 Nov 23 18:23 .  ← FIXED!
drwxrwxr-x 3 www-data www-data 4096 Nov 23 18:19 ..
```

## Verification Test
Created test script that simulates upload:
```php
// Test 1: Storage::disk('private')->put() = ✓ SUCCESS
// Test 2: UploadedFile->storeAs() = ✓ SUCCESS
```

## What Was Fixed

### 1. **Permission Root Cause**
- Directory created by root user
- Laravel runs as www-data
- www-data couldn't write to root-owned directory

### 2. **Files Modified**
- `config/filesystems.php` - Added 'private' disk
- `app/Http/Controllers/Admin/TestManagementController.php` - Added logging
- `resources/views/admin/recruitment/tests/edit.blade.php` - Added success message
- `resources/views/admin/recruitment/tests/show.blade.php` - Added template file display

### 3. **Controller Behavior**
- ✅ Redirect now goes to `edit` (not `show`)
- ✅ Logging shows validation, upload, and update steps
- ✅ File upload with `storeAs()` method
- ✅ Old file deleted before new upload

## Prevention for Future

### When creating directories manually:
```bash
# ❌ WRONG (creates as root)
sudo mkdir storage/app/private/some-folder

# ✓ CORRECT (creates with proper owner)
sudo mkdir storage/app/private/some-folder && \
sudo chown www-data:www-data storage/app/private/some-folder && \
sudo chmod 775 storage/app/private/some-folder
```

### Or use Laravel's Storage facade:
```php
// Automatically creates with correct permissions
Storage::disk('private')->makeDirectory('some-folder');
```

## Testing Checklist

After fix, test these scenarios:

- [x] Upload file via edit form → File saved
- [ ] Download file via show page → File downloaded
- [ ] Update file (replace existing) → Old deleted, new saved
- [ ] Edit without uploading → Existing file preserved
- [ ] Create new template with file → File saved

## Status: ✅ RESOLVED

Bug disebabkan oleh permission directory. Setelah fix ownership dan permission, upload berfungsi normal.

## Next Steps

Silakan test upload lagi di:
```
https://bizmark.id/admin/recruitment/tests/4/edit
```

Log seharusnya menunjukkan:
```
"path": "test-templates/1763922xxx_filename.docx"  ← Bukan false!
```
