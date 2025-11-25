# Bank Reconciliation CSV Format Support

## Summary
Sistem rekonsiliasi bank sekarang mendukung **2 format CSV**:
1. **BCA Format** (Bank Central Asia) - Format resmi dengan header/footer khusus
2. **Standard/BTN Format** - Format generik dengan kolom debit/credit terpisah

## BCA Format Specification

### File Structure
```
Account No.,=,'1091806504
Name,=,ODANG RODIANA           
Currency,=,IDR

Date,Description,Branch,Amount,,Balance
'07/09,KARTU DEBIT IDM T72M-BABAKAN R,'0998,511400.00,DB,897447.23
'18/09,TRSF E-BANKING CR ODANG RODIANA K,'0006,800000.00,CR,1060447.23
...

Starting Balance,=,1408847.23
Credit,=,3800000.00
Debet,=,5030400.00
Ending Balance,=,178447.23
```

### Format Characteristics

#### Header Section (3 rows)
- Row 1: `Account No.,=,'1091806504`
- Row 2: `Name,=,ODANG RODIANA`
- Row 3: `Currency,=,IDR`
- Row 4: Empty line

#### Column Headers
`Date,Description,Branch,Amount,,Balance`
- 6 columns total
- Column 5 is empty (placeholder for DB/CR indicator)

#### Data Section
**Column Mapping:**
- Column 0: `Date` - Format: `'DD/MM` (with quote prefix, no year)
- Column 1: `Description` - Transaction description
- Column 2: `Branch` - Branch code with quote prefix (e.g., `'0998`)
- Column 3: `Amount` - Transaction amount (always positive)
- Column 4: `Type` - `DB` (debit) or `CR` (credit) indicator
- Column 5: `Balance` - Running balance after transaction

**Date Format:**
- Format: `'07/09` (quote prefix + DD/MM)
- Year inference: Uses reconciliation period dates
- If month > current month → assumes previous year

**Type Indicators:**
- `DB` or `DEBIT` → Expense (debit amount)
- `CR` or `CREDIT` → Income (credit amount)
- If empty: Parse description for keywords (TRANSFER, TRSF, BI-FAST CR, SWITCHING CR)

#### Footer Section (4 rows)
- Starting Balance: `Starting Balance,=,1408847.23`
- Total Credit: `Credit,=,3800000.00`
- Total Debit: `Debet,=,5030400.00`
- Ending Balance: `Ending Balance,=,178447.23`

### Parser Logic

```php
private function importBCAFormat($reconciliation, $handle)
{
    // 1. Skip header rows (3 rows + empty + column headers)
    // 2. Read all data rows
    // 3. Detect and remove footer (last 4 rows)
    
    foreach ($dataRows as $row) {
        // Clean quote prefix
        $date = ltrim($row[0], "'"); // '07/09 → 07/09
        $branch = ltrim($row[2], "'"); // '0998 → 0998
        
        // Parse date with year inference
        $year = $reconciliation->start_date->format('Y');
        $transactionDate = "{$year}-{$month}-{$day}";
        
        // Split amount by type indicator
        if ($row[4] === 'DB') {
            $debitAmount = $row[3];
            $creditAmount = 0;
        } else {
            $debitAmount = 0;
            $creditAmount = $row[3];
        }
        
        // Add branch to description
        $fullDescription = $row[1] . " [Branch: {$branch}]";
    }
}
```

## Standard/BTN Format Specification

### File Structure
```
Date,Description,Debit,Credit,Balance,Reference
2025-09-07,KARTU DEBIT IDM T72M,511400.00,0,897447.23,REF001
2025-09-18,TRSF E-BANKING,0,800000.00,1060447.23,REF002
```

### Format Characteristics

**Column Mapping:**
- Column 0: `Date` - Format: `YYYY-MM-DD` or any parseable date
- Column 1: `Description` - Transaction description
- Column 2: `Debit` - Expense amount (or 0)
- Column 3: `Credit` - Income amount (or 0)
- Column 4: `Balance` - Running balance
- Column 5: `Reference` - Optional reference number

### Parser Logic

```php
private function importStandardFormat($reconciliation, $handle)
{
    $header = fgetcsv($handle); // Skip header
    
    while (($row = fgetcsv($handle)) !== false) {
        $transactionDate = $this->parseDate($row[0]);
        $description = trim($row[1]);
        $debitAmount = $this->parseAmount($row[2]);
        $creditAmount = $this->parseAmount($row[3]);
        $runningBalance = $this->parseAmount($row[4]);
        $referenceNumber = trim($row[5]);
    }
}
```

## Format Detection

System automatically detects CSV format by reading first line:

```php
private function importCSV($reconciliation, $file)
{
    $firstLine = fgets($handle);
    
    // BCA format: First line contains "Account No."
    $isBCAFormat = stripos($firstLine, 'Account No') !== false;
    
    if ($isBCAFormat) {
        return $this->importBCAFormat($reconciliation, $handle);
    } else {
        return $this->importStandardFormat($reconciliation, $handle);
    }
}
```

## Amount Parsing

Handles both Indonesian and International formats:

```php
private function parseAmount($value)
{
    // Remove currency symbols and spaces
    $value = preg_replace('/[^\d,.-]/', '', $value);
    
    // Indonesian format: 1.234.567,89 → 1234567.89
    if (preg_match('/\d+\.\d+,\d+/', $value)) {
        return (float) str_replace(['.', ','], ['', '.'], $value);
    }
    
    // International format: 1,234,567.89 → 1234567.89
    return (float) str_replace(',', '', $value);
}
```

## Date Parsing

```php
private function parseDate($value)
{
    // Remove quotes and clean
    $value = trim(str_replace("'", '', $value));
    
    try {
        return Carbon::parse($value)->format('Y-m-d');
    } catch (\Exception $e) {
        return now()->format('Y-m-d');
    }
}
```

## Import Validation

### BCA Format Test Results

```
✅ Total Transactions: 17
✅ Total Debit:  Rp 5,030,400.00 (Match with footer)
✅ Total Credit: Rp 3,800,000.00 (Match with footer)
✅ Net Change:   Rp -1,230,400.00
✅ Date Range:   2025-09-07 to 2025-09-29
✅ Income:       4 transactions
✅ Expense:      13 transactions
```

### Sample Parsed Data

```
1. Date: 2025-09-07
   Description: KARTU DEBIT IDM T72M-BABAKAN R [Branch: 0998]
   Type: expense
   Debit:  Rp 511,400.00
   Credit: Rp 0.00
   Balance: Rp 897,447.23

2. Date: 2025-09-18
   Description: TRSF E-BANKING CR 1809/FTSCY/WS95051 [Branch: 0006]
   Type: income
   Debit:  Rp 0.00
   Credit: Rp 800,000.00
   Balance: Rp 1,060,447.23
```

## Database Schema

### bank_statement_entries

```sql
CREATE TABLE bank_statement_entries (
    id BIGSERIAL PRIMARY KEY,
    reconciliation_id BIGINT REFERENCES bank_reconciliations(id),
    transaction_date DATE NOT NULL,
    description TEXT,
    debit_amount DECIMAL(15,2) DEFAULT 0,
    credit_amount DECIMAL(15,2) DEFAULT 0,
    running_balance DECIMAL(15,2),
    reference_number VARCHAR(100),
    
    -- Matching fields
    is_matched BOOLEAN DEFAULT FALSE,
    matched_transaction_type VARCHAR(50),
    matched_transaction_id BIGINT,
    match_confidence VARCHAR(20),
    match_notes TEXT,
    unmatch_reason VARCHAR(100),
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Usage Flow

### 1. Create Reconciliation
```php
$reconciliation = BankReconciliation::create([
    'cash_account_id' => $cashAccount->id,
    'reconciliation_date' => '2025-09-29',
    'start_date' => '2025-09-07',
    'end_date' => '2025-09-29',
    'opening_balance_book' => $cashAccount->initial_balance,
    'opening_balance_bank' => 1408847.23,
    'closing_balance_book' => $cashAccount->current_balance,
    'closing_balance_bank' => 178447.23,
    'adjusted_bank_balance' => 178447.23,
    'adjusted_book_balance' => $cashAccount->current_balance,
    'difference' => 0,
    'status' => 'in_progress',
    'bank_statement_format' => 'BCA' // or 'BTN', 'Generic'
]);
```

### 2. Upload & Import CSV
```php
$controller = new BankReconciliationController();
$count = $controller->importCSV($reconciliation, $uploadedFile);
// Auto-detects format and imports entries
```

### 3. View Imported Entries
```php
$entries = BankStatementEntry::where('reconciliation_id', $reconciliation->id)
    ->orderBy('transaction_date')
    ->get();

foreach ($entries as $entry) {
    echo "{$entry->transaction_date} - {$entry->description}\n";
    echo "Debit: " . number_format($entry->debit_amount, 2) . "\n";
    echo "Credit: " . number_format($entry->credit_amount, 2) . "\n";
}
```

### 4. Match Transactions
```php
// Auto-matching by amount and date
$entry->update([
    'is_matched' => true,
    'matched_transaction_type' => 'payment',
    'matched_transaction_id' => $payment->id,
    'match_confidence' => 'exact'
]);
```

## Error Handling

### Common Issues & Solutions

**Issue 1: Date parsing fails**
- **Cause**: Invalid date format or missing year
- **Solution**: Uses reconciliation period dates for year inference
- **Fallback**: Current date if parsing fails

**Issue 2: Amount mismatch with footer**
- **Cause**: Row parsing error or footer format change
- **Solution**: Log warning, continue with parsed amounts
- **Validation**: Compare totals with footer values after import

**Issue 3: Empty rows or malformed data**
- **Cause**: CSV export issues, extra newlines
- **Solution**: Skip empty rows, validate column count
- **Logging**: Records skipped rows for review

**Issue 4: Quote prefix not cleaned**
- **Cause**: Excel/CSV export preserves quote literals
- **Solution**: `ltrim($value, "'")` before parsing
- **Applied to**: Date and Branch columns

## Logging

Import process logs to `storage/logs/laravel.log`:

```
[2025-11-23 00:43:35] INFO: Detected BCA CSV format for reconciliation #8
[2025-11-23 00:43:35] INFO: Row 5: Data section header detected
[2025-11-23 00:43:35] INFO: BCA Row 6 parsed: Date=2025-09-07, Debit=511400, Credit=0, Balance=897447.23
[2025-11-23 00:43:35] INFO: Row 22: Footer detected - Starting Balance
[2025-11-23 00:43:35] INFO: Successfully imported 17 BCA entries for reconciliation #8
```

## Testing

Test script: `test_bca_import.php`

```bash
php test_bca_import.php

# Output:
✅ CSV File found: test_bca_statement.csv
✅ Reconciliation ID: 8
✅ Import completed: 17 entries imported
✅ VALIDATION PASSED: Amounts match CSV footer!
```

## Future Enhancements

1. **Add More Bank Formats**
   - Bank Mandiri
   - BNI (Bank Negara Indonesia)
   - BRI (Bank Rakyat Indonesia)
   - BTN (Bank Tabungan Negara)

2. **Format Selection UI**
   - Dropdown: "Select Bank Format"
   - Auto-detection with manual override

3. **Advanced Matching**
   - Fuzzy matching by description similarity
   - Date tolerance (±3 days)
   - Amount tolerance (±0.01%)
   - ML-based matching suggestions

4. **Bulk Reconciliation**
   - Import multiple periods at once
   - Batch matching across periods
   - Carry forward unmatched items

## Related Files

- **Controller**: `app/Http/Controllers/BankReconciliationController.php`
- **Model**: `app/Models/BankStatementEntry.php`
- **Migration**: `database/migrations/2025_10_10_181100_create_bank_statement_entries_table.php`
- **Test Script**: `test_bca_import.php`
- **Sample CSV**: `storage/app/test_bca_statement.csv`

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review test output: `php test_bca_import.php`
3. Verify CSV format matches specification above
4. Contact: odangrodiana@gmail.com

---
**Last Updated**: 2025-11-23  
**Version**: 1.0  
**Status**: ✅ Production Ready
