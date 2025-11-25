# Submit Popup & Page Leave Bug Fix

## Masalah
Setelah klik submit, muncul popup "Leave this page?" (beforeunload warning). Ketika user klik "Stay/Kembali", halaman reload dan kembali ke pertanyaan pertama dengan timer reset.

## Root Cause Analysis

### 1. beforeunload Event Always Triggering
```javascript
// OLD CODE - WRONG ❌
window.addEventListener('beforeunload', function(e) {
    saveAnswers();
    e.preventDefault();
    e.returnValue = ''; // ALWAYS shows warning
});
```
**Masalah:**
- Event selalu trigger warning, bahkan saat legitimate submit
- User klik "Stay" → page reload → timer reset
- Tidak ada flag untuk detect submit vs accidental leave

### 2. Controller Redirect Back to Test Page
```php
// OLD CODE - WRONG ❌
public function complete(...) {
    $session->completeWithoutScore();
    
    return redirect()
        ->route('candidate.test.show', $token) // ← Back to test!
        ->with('success', 'Test berhasil diselesaikan!');
}
```
**Masalah:**
- Redirect ke `candidate.test.show` = halaman test
- Jika user klik "Stay", akan reload test page
- Tidak ada loading indicator during submit

### 3. Status Check Mismatch
```php
// OLD CODE - WRONG ❌
if ($session->status !== 'started') {
    return redirect()->back(); // Status is 'in-progress', not 'started'
}
```
**Masalah:**
- Status setelah start adalah `'in-progress'`
- Check `=== 'started'` always fail
- Submit tidak berhasil

## Solutions Implemented

### 1. Smart beforeunload with Submit Flag ✅

**File:** `resources/views/candidate/test-taking.blade.php`

```javascript
// Add global flag
let isSubmitting = false;

// Update submitTest()
function submitTest() {
    if (confirm('Are you sure...?')) {
        // Set flag BEFORE submit
        isSubmitting = true;
        
        // Close modal
        closeReview();
        
        // Show loading overlay
        showLoadingOverlay('Submitting your test...');
        
        // Clear intervals
        if (timerInterval) clearInterval(timerInterval);
        if (autoSaveInterval) clearInterval(autoSaveInterval);
        
        // Submit form
        setTimeout(() => {
            document.getElementById('testForm').submit();
        }, 1000);
    }
}

// Smart beforeunload
window.addEventListener('beforeunload', function(e) {
    // If submitting, ALLOW page leave without warning
    if (isSubmitting) {
        return undefined; // ← No warning!
    }
    
    // If test active, warn user
    if (testStarted && !isSubmitting) {
        saveAnswers();
        e.preventDefault();
        e.returnValue = 'Your test is still in progress. Are you sure?';
        return e.returnValue;
    }
});
```

### 2. Loading Overlay During Submit ✅

```javascript
function showLoadingOverlay(message) {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.9);
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    `;
    
    overlay.innerHTML = `
        <div style="width: 60px; height: 60px; border: 4px solid rgba(10,132,255,0.3); 
                    border-top-color: #0A84FF; border-radius: 50%; 
                    animation: spin 1s linear infinite;"></div>
        <p style="color: white; font-size: 1.25rem; font-weight: 600;">${message}</p>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.875rem;">
            Please wait, do not close this page
        </p>
    `;
    
    document.body.appendChild(overlay);
}
```

**Benefits:**
- ✅ User sees visual feedback
- ✅ Prevents accidental close during submit
- ✅ Professional UX

### 3. Controller Fix: Proper Status Check & Redirect ✅

**File:** `app/Http/Controllers/Candidate/TestController.php`

```php
public function complete(Request $request, string $token)
{
    $session = TestSession::where('session_token', $token)->firstOrFail();

    // Fix: Check both 'in-progress' and 'started'
    if (!in_array($session->status, ['in-progress', 'started'])) {
        return redirect()
            ->route('candidate.test.show', $token)
            ->with('error', 'Test sudah diselesaikan.');
    }

    // Process answers from form
    $answers = $request->input('answers', []);
    
    // Save all answers to database
    foreach ($answers as $questionIndex => $answerValue) {
        $questions = $session->testTemplate->questions_data ?? [];
        
        if (isset($questions[$questionIndex])) {
            $question = $questions[$questionIndex];
            
            // Prepare answer data
            $answerData = [
                'question_index' => $questionIndex,
                'question_text' => $question['question_text'] ?? '',
                'question_type' => $question['question_type'] ?? '',
                'answer_value' => $answerValue,
            ];
            
            // If multiple choice, include option text
            if ($question['question_type'] === 'multiple-choice') {
                if (isset($question['options'][$answerValue])) {
                    $answerData['answer_text'] = $question['options'][$answerValue];
                }
            }
            
            // Save to database
            $session->testAnswers()->updateOrCreate(
                ['question_id' => $questionIndex],
                [
                    'answer_data' => $answerData,
                    'answered_at' => now(),
                ]
            );
        }
    }

    // Complete test session
    $session->completeWithoutScore();

    // Fix: Redirect to COMPLETION page, NOT test page
    return view('candidate.test-completed', ['testSession' => $session]);
}
```

### 4. Auto-Submit Fix ✅

```javascript
function autoSubmitTest() {
    // Set submitting flag
    isSubmitting = true;
    
    // Show alert
    alert('⏰ Time is up! Your test will be automatically submitted.');
    
    // Show loading overlay
    showLoadingOverlay('Time is up! Submitting your test...');
    
    // Save answers
    saveAnswers();
    
    // Clear intervals
    if (timerInterval) clearInterval(timerInterval);
    if (autoSaveInterval) clearInterval(autoSaveInterval);
    
    // Submit form
    setTimeout(() => {
        document.getElementById('testForm').submit();
    }, 1000);
}
```

## Flow Diagram

### BEFORE (Bug Flow) ❌
```
User klik "Submit Test"
    ↓
confirm() dialog → Yes
    ↓
Form submit started
    ↓
beforeunload triggers ← BUG!
    ↓
Browser shows: "Leave this page?"
    ↓
User klik "Stay"
    ↓
Page reload → Show test page again
    ↓
Timer reset to full duration
    ↓
Questions reset to #1
```

### AFTER (Fixed Flow) ✅
```
User klik "Submit Test"
    ↓
confirm() dialog → Yes
    ↓
isSubmitting = true ← Set flag
    ↓
Show loading overlay
    ↓
Clear intervals
    ↓
Form submit started
    ↓
beforeunload checks flag
    ↓
if (isSubmitting) return undefined ← No warning!
    ↓
Page navigates smoothly
    ↓
Controller: Save answers to DB
    ↓
Controller: Mark status='completed'
    ↓
Controller: Return completion view
    ↓
User sees "Test Completed" page ✅
```

## Testing Checklist

### Manual Submit
- [x] Klik "Review & Submit"
- [x] Modal review muncul
- [x] Klik "Submit Test"
- [x] Confirmation dialog muncul dengan detail
- [x] Klik "OK"
- [x] Loading overlay muncul
- [x] **TIDAK ADA** popup "Leave this page?"
- [x] Form submit ke backend
- [x] Redirect ke halaman "Test Completed"
- [x] Answers tersimpan di database

### Auto Submit (Timer Habis)
- [x] Timer countdown ke 00:00
- [x] Alert "Time is up" muncul
- [x] Loading overlay muncul
- [x] **TIDAK ADA** popup "Leave this page?"
- [x] Form auto-submit ke backend
- [x] Redirect ke halaman "Test Completed"
- [x] Answers tersimpan di database

### Accidental Leave Protection
- [x] Test sedang berlangsung
- [x] User coba close tab/browser
- [x] **ADA** popup "Your test is still in progress"
- [x] User klik "Stay" → Test continue
- [x] User klik "Leave" → Test saved, can resume

### Edge Cases
- [x] Submit tanpa jawaban sama sekali
- [x] Submit dengan beberapa jawaban kosong
- [x] Submit saat connection slow (loading shown)
- [x] Refresh saat loading overlay shown
- [x] Back button setelah completion page

## Database Structure

### test_answers Table
```sql
CREATE TABLE test_answers (
    id BIGINT PRIMARY KEY,
    test_session_id BIGINT,
    question_id INT, -- question index
    answer_data JSON, -- stores full answer info
    is_correct BOOLEAN,
    points_earned DECIMAL(5,2),
    time_spent_seconds INT,
    answered_at TIMESTAMP,
    
    FOREIGN KEY (test_session_id) REFERENCES test_sessions(id)
);
```

### answer_data JSON Structure
```json
{
  "question_index": 0,
  "question_text": "What is Laravel?",
  "question_type": "multiple-choice",
  "answer_value": "1",
  "answer_text": "A PHP framework"
}
```

## Key Changes Summary

| Issue | Before | After |
|-------|--------|-------|
| beforeunload | Always triggers | Only when NOT submitting |
| Loading UX | No indicator | Loading overlay shown |
| Submit redirect | Back to test page | To completion page |
| Status check | `=== 'started'` | `in_array(['in-progress', 'started'])` |
| Answer save | Not implemented | Full answer data saved |
| Timer on "Stay" | Reset | N/A (no warning shown) |

## Benefits

1. **No More Popup Confusion** ✅
   - Submit process is smooth
   - No unexpected "Leave page?" warning
   - Professional UX

2. **Proper Data Persistence** ✅
   - All answers saved to database
   - Can review answers later
   - Audit trail complete

3. **Clear Visual Feedback** ✅
   - Loading overlay during submit
   - User knows what's happening
   - Prevents multiple submissions

4. **Protection Still Works** ✅
   - Accidental close/refresh still protected
   - Only during actual test
   - Not during submit process

## Status: ✅ FIXED & TESTED

Submit sekarang berjalan smooth tanpa popup warning yang membingungkan!
