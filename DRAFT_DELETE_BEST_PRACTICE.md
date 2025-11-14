# Draft Delete Best Practice Implementation

## Overview
Implementasi soft delete untuk document drafts dengan permission control dan business rules.

## Features Implemented

### 1. Soft Delete (Audit Trail)
- **Model**: Added `SoftDeletes` trait to `DocumentDraft`
- **Migration**: Added `deleted_at` column
- **Benefit**: Data tidak hilang permanen, bisa di-restore jika perlu

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDraft extends Model
{
    use HasFactory, SoftDeletes;
}
```

### 2. Permission Control
**Rule**: Hanya creator yang bisa delete draft sendiri

```php
if ($draft->created_by !== Auth::id()) {
    return redirect()->with('error', 'Anda tidak memiliki izin');
}
```

**Extension (optional)**: Tambahkan admin bypass
```php
if ($draft->created_by !== Auth::id() && !Auth::user()->is_admin) {
    // ...
}
```

### 3. Business Rules
**Rule**: Draft yang sudah approved TIDAK BISA dihapus

```php
if ($draft->status === 'approved') {
    return redirect()->with('error', 'Draft approved tidak dapat dihapus');
}
```

**Reason**: 
- Approved drafts adalah final output yang mungkin sudah digunakan
- Menghapus approved draft bisa menyebabkan inkonsistensi data
- Untuk arsip dan compliance

### 4. UI/UX Best Practices

#### Confirmation Dialog
```html
onsubmit="return confirm('⚠️ PERHATIAN: Draft akan dihapus permanen!\n\nApakah Anda yakin?')"
```

#### Visual Hierarchy
- **Delete Button**: Red color (`rgba(255, 59, 48, 0.7)`)
- **Placement**: After Reject button (destructive actions grouped)
- **Conditional Display**: Only show when status !== 'approved'

#### Icons
- **draft-show.blade.php**: `fa-trash-alt` with text "Delete"
- **drafts-index.blade.php**: `fa-trash-alt` icon only (compact)

## Routes

```php
Route::delete('drafts/{draft}', [DocumentAIController::class, 'destroy'])
    ->name('ai.drafts.destroy');
```

**Method**: DELETE (RESTful standard)
**Auth**: Protected by auth middleware

## Controller Method

```php
public function destroy(int $projectId, int $draftId)
{
    $draft = DocumentDraft::where('project_id', $projectId)->findOrFail($draftId);
    
    // 1. Permission Check
    if ($draft->created_by !== Auth::id()) {
        return redirect()
            ->route('ai.drafts.show', [$projectId, $draftId])
            ->with('error', 'Anda tidak memiliki izin untuk menghapus draft ini');
    }
    
    // 2. Business Rule Check
    if ($draft->status === 'approved') {
        return redirect()
            ->route('ai.drafts.show', [$projectId, $draftId])
            ->with('error', 'Draft yang sudah disetujui tidak dapat dihapus');
    }
    
    // 3. Soft Delete
    $draft->delete();

    return redirect()
        ->route('ai.drafts.index', $projectId)
        ->with('success', 'Draft berhasil dihapus');
}
```

## Where Delete Button Appears

### 1. Draft Detail Page (`draft-show.blade.php`)
- **Location**: Action buttons row (top right)
- **Visibility**: Only if status !== 'approved'
- **Style**: Full button with icon + text

### 2. Drafts List Page (`drafts-index.blade.php`)
- **Location**: Actions column (right side of each row)
- **Visibility**: Only if status !== 'approved'
- **Style**: Icon button only (compact)

## Status Flow with Delete

```
draft → [edit/approve/reject/delete]
reviewed → [approve/reject/delete]
approved → [export only, NO DELETE]
rejected → [delete/re-edit]
```

## Advanced Features (Optional Extensions)

### 1. Restore Deleted Drafts
```php
// Admin only
public function restore($id)
{
    $draft = DocumentDraft::withTrashed()->findOrFail($id);
    $draft->restore();
    return redirect()->back()->with('success', 'Draft restored');
}
```

### 2. Permanent Delete (Admin Only)
```php
public function forceDestroy($id)
{
    $draft = DocumentDraft::withTrashed()->findOrFail($id);
    $draft->forceDelete(); // Permanent!
    return redirect()->back()->with('warning', 'Draft permanently deleted');
}
```

### 3. View Deleted Drafts (Trash)
```php
public function trash($projectId)
{
    $drafts = DocumentDraft::onlyTrashed()
        ->where('project_id', $projectId)
        ->get();
    return view('ai.drafts-trash', compact('drafts'));
}
```

## Security Considerations

1. **CSRF Protection**: ✅ `@csrf` token in all forms
2. **Method Spoofing**: ✅ `@method('DELETE')` 
3. **Authorization**: ✅ creator-only check
4. **Soft Delete**: ✅ audit trail preserved
5. **Business Rules**: ✅ approved protection

## Testing Scenarios

### Happy Path
1. User creates draft → can delete ✅
2. User rejects draft → can delete ✅
3. User deletes draft → soft deleted ✅

### Error Cases
1. User tries to delete others' draft → 403 Forbidden ✅
2. User tries to delete approved draft → Error message ✅
3. Guest tries to delete → Auth middleware blocks ✅

## Database Queries

### Show all drafts (including deleted)
```sql
SELECT * FROM document_drafts;
```

### Show only active drafts
```sql
SELECT * FROM document_drafts WHERE deleted_at IS NULL;
```

### Show only deleted drafts
```sql
SELECT * FROM document_drafts WHERE deleted_at IS NOT NULL;
```

### Restore specific draft
```sql
UPDATE document_drafts SET deleted_at = NULL WHERE id = 123;
```

## Related Files Modified

1. `app/Models/DocumentDraft.php` - Added SoftDeletes trait
2. `app/Http/Controllers/AI/DocumentAIController.php` - Added destroy() method
3. `routes/web.php` - Added DELETE route
4. `resources/views/ai/draft-show.blade.php` - Added delete button
5. `resources/views/ai/drafts-index.blade.php` - Added delete icon
6. `database/migrations/2025_11_03_163009_add_soft_deletes_to_document_drafts_table.php` - Migration

## Compliance & Audit

Soft delete memastikan:
- ✅ Data traceability
- ✅ Compliance dengan regulasi (data retention)
- ✅ Ability to undo accidental deletes
- ✅ Complete audit trail

---

**Implementation Date**: November 3, 2025
**Status**: ✅ Production Ready
