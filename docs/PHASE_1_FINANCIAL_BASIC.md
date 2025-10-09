# PHASE 1: FINANCIAL BASIC MODULE

**Priority:** 🚨 URGENT  
**Timeline:** 7-10 hari kerja  
**Target Completion:** 12 Oktober 2025

---

## 1. OBJECTIVES

### Business Goals
- ✅ Track contract value & DP per proyek
- ✅ Record pembayaran masuk dari klien
- ✅ Log pengeluaran per proyek (vendor, operasional)
- ✅ Monitor cash position real-time
- ✅ Calculate profit margin per proyek
- ✅ Dashboard keuangan untuk quick insight

### Technical Goals
- Extend existing `projects` table
- Create 3 new tables: `project_payments`, `project_expenses`, `cash_accounts`
- Build CRUD UI for payments & expenses
- Create financial dashboard
- Maintain data integrity & audit trail

---

## 2. DATABASE SCHEMA

### 2.1 Extend `projects` Table

```sql
-- Migration: add_financial_columns_to_projects_table
ALTER TABLE projects ADD COLUMN contract_value DECIMAL(15,2) DEFAULT 0 COMMENT 'Nilai kontrak total';
ALTER TABLE projects ADD COLUMN down_payment DECIMAL(15,2) DEFAULT 0 COMMENT 'Uang muka (DP)';
ALTER TABLE projects ADD COLUMN payment_received DECIMAL(15,2) DEFAULT 0 COMMENT 'Total pembayaran diterima';
ALTER TABLE projects ADD COLUMN total_expenses DECIMAL(15,2) DEFAULT 0 COMMENT 'Total pengeluaran';
ALTER TABLE projects ADD COLUMN profit_margin DECIMAL(5,2) DEFAULT 0 COMMENT 'Profit margin (%)';
ALTER TABLE projects ADD COLUMN payment_terms TEXT COMMENT 'Termin pembayaran (DP 50%, Progress 30%, Final 20%)';
ALTER TABLE projects ADD COLUMN payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid';
```

**Field Descriptions:**
- `contract_value`: Total nilai kontrak (misal: Rp 180.000.000 untuk PT Asia Con)
- `down_payment`: Nominal DP yang disepakati (misal: Rp 90.000.000)
- `payment_received`: Total yang sudah diterima secara aktual
- `total_expenses`: Auto-calculated dari sum project_expenses
- `profit_margin`: Auto-calculated: ((payment_received - total_expenses) / payment_received) * 100
- `payment_terms`: Free text untuk termin (contoh: "DP 50%, Kemajuan 30%, Pelunasan 20%")
- `payment_status`: 
  - `unpaid`: payment_received = 0
  - `partial`: 0 < payment_received < contract_value
  - `paid`: payment_received >= contract_value

### 2.2 New Table: `project_payments`

```sql
CREATE TABLE project_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    payment_date DATE NOT NULL COMMENT 'Tanggal terima pembayaran',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Nominal pembayaran',
    payment_type ENUM('dp', 'progress', 'final', 'other') NOT NULL DEFAULT 'other',
    payment_method ENUM('transfer', 'cash', 'check', 'other') DEFAULT 'transfer',
    bank_account_id BIGINT UNSIGNED NULL COMMENT 'Rekening tujuan',
    reference_number VARCHAR(100) NULL COMMENT 'Nomor referensi/bukti transfer',
    description TEXT NULL COMMENT 'Keterangan tambahan',
    receipt_file VARCHAR(255) NULL COMMENT 'Path file bukti pembayaran',
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_account_id) REFERENCES cash_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_project_payment (project_id, payment_date),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Use Cases:**
```
PT Asia Con:
- payment_date: 2025-09-15
- amount: 90000000
- payment_type: dp
- payment_method: transfer
- bank_account_id: 1 (BTN)
- reference_number: TRF20250915001
- description: "DP 50% untuk proyek UKL-UPL PT Asia Con"
```

### 2.3 New Table: `project_expenses`

```sql
CREATE TABLE project_expenses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NULL COMMENT 'NULL = general expense',
    expense_date DATE NOT NULL COMMENT 'Tanggal pengeluaran',
    category ENUM('vendor', 'laboratory', 'survey', 'travel', 'operational', 'tax', 'other') NOT NULL,
    vendor_name VARCHAR(255) NULL COMMENT 'Nama vendor/penerima',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Nominal pengeluaran',
    payment_method ENUM('transfer', 'cash', 'check', 'other') DEFAULT 'transfer',
    bank_account_id BIGINT UNSIGNED NULL COMMENT 'Rekening sumber',
    description TEXT NULL COMMENT 'Keterangan pengeluaran',
    receipt_file VARCHAR(255) NULL COMMENT 'Path file bukti pembayaran',
    is_billable BOOLEAN DEFAULT TRUE COMMENT 'Apakah bisa ditagihkan ke klien?',
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_account_id) REFERENCES cash_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_project_expense (project_id, expense_date),
    INDEX idx_expense_date (expense_date),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Use Cases:**
```
Expense untuk Gambar PT MCM:
- project_id: 5 (PT MCM)
- expense_date: 2025-09-20
- category: vendor
- vendor_name: "Drafter XYZ"
- amount: 15000000
- payment_method: transfer
- bank_account_id: 1 (BTN)
- description: "Pembuatan gambar site plan"
- is_billable: true
```

### 2.4 New Table: `cash_accounts`

```sql
CREATE TABLE cash_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_name VARCHAR(100) NOT NULL COMMENT 'Nama akun (Bank BTN, Cash, dll)',
    account_type ENUM('bank', 'cash', 'receivable', 'payable') NOT NULL DEFAULT 'bank',
    account_number VARCHAR(50) NULL COMMENT 'Nomor rekening',
    bank_name VARCHAR(100) NULL COMMENT 'Nama bank',
    account_holder VARCHAR(255) NULL COMMENT 'Nama pemilik rekening',
    current_balance DECIMAL(15,2) DEFAULT 0 COMMENT 'Saldo saat ini',
    initial_balance DECIMAL(15,2) DEFAULT 0 COMMENT 'Saldo awal',
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_account_type (account_type, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Initial Data:**
```sql
INSERT INTO cash_accounts (account_name, account_type, current_balance, notes) VALUES
('Bank BTN', 'bank', 46000000, 'Rekening operasional utama'),
('Cash', 'cash', 8000000, 'Kas tunai'),
('Mr. Gobs (Piutang)', 'receivable', 20000000, 'Piutang ke Mr. Gobs');
```

---

## 3. BUSINESS LOGIC

### 3.1 Auto-Calculations

#### Calculate `payment_received` (di ProjectPayment Model)
```php
protected static function booted()
{
    static::created(function ($payment) {
        $payment->project->updatePaymentReceived();
    });
    
    static::updated(function ($payment) {
        $payment->project->updatePaymentReceived();
    });
    
    static::deleted(function ($payment) {
        $payment->project->updatePaymentReceived();
    });
}
```

#### Calculate `total_expenses` (di ProjectExpense Model)
```php
protected static function booted()
{
    static::created(function ($expense) {
        if ($expense->project) {
            $expense->project->updateTotalExpenses();
        }
    });
    
    static::updated(function ($expense) {
        if ($expense->project) {
            $expense->project->updateTotalExpenses();
        }
    });
    
    static::deleted(function ($expense) {
        if ($expense->project) {
            $expense->project->updateTotalExpenses();
        }
    });
}
```

#### Calculate `profit_margin` (di Project Model)
```php
public function updatePaymentReceived()
{
    $this->payment_received = $this->payments()->sum('amount');
    $this->updatePaymentStatus();
    $this->updateProfitMargin();
    $this->save();
}

public function updateTotalExpenses()
{
    $this->total_expenses = $this->expenses()->sum('amount');
    $this->updateProfitMargin();
    $this->save();
}

public function updateProfitMargin()
{
    if ($this->payment_received > 0) {
        $profit = $this->payment_received - $this->total_expenses;
        $this->profit_margin = ($profit / $this->payment_received) * 100;
    } else {
        $this->profit_margin = 0;
    }
}

public function updatePaymentStatus()
{
    if ($this->payment_received == 0) {
        $this->payment_status = 'unpaid';
    } elseif ($this->payment_received >= $this->contract_value) {
        $this->payment_status = 'paid';
    } else {
        $this->payment_status = 'partial';
    }
}
```

### 3.2 Cash Account Balance Update

```php
// Ketika payment masuk
public function recordPayment($amount, $bankAccountId)
{
    $account = CashAccount::find($bankAccountId);
    if ($account) {
        $account->current_balance += $amount;
        $account->save();
    }
}

// Ketika expense keluar
public function recordExpense($amount, $bankAccountId)
{
    $account = CashAccount::find($bankAccountId);
    if ($account) {
        $account->current_balance -= $amount;
        $account->save();
    }
}
```

### 3.3 Validation Rules

**ProjectPayment:**
```php
'project_id' => 'required|exists:projects,id',
'payment_date' => 'required|date|before_or_equal:today',
'amount' => 'required|numeric|min:0',
'payment_type' => 'required|in:dp,progress,final,other',
'payment_method' => 'required|in:transfer,cash,check,other',
```

**ProjectExpense:**
```php
'expense_date' => 'required|date|before_or_equal:today',
'category' => 'required|in:vendor,laboratory,survey,travel,operational,tax,other',
'amount' => 'required|numeric|min:0',
'vendor_name' => 'required_if:category,vendor|max:255',
```

---

## 4. UI/UX DESIGN

### 4.1 Project Show Page - Tab Keuangan

**Location:** `/projects/{id}` - Tab "Keuangan"

**Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ PROJECT: PT ASIA CON                                    │
│ [Detail] [Tasks] [Documents] [Keuangan] ← Active Tab   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 📊 RINGKASAN KEUANGAN                                   │
│ ┌──────────────┬──────────────┬──────────────┐         │
│ │ Nilai Kontrak│ Pembayaran   │ Piutang      │         │
│ │ Rp 180 juta  │ Rp 90 juta   │ Rp 90 juta   │         │
│ │              │ (50%)        │ (50%)        │         │
│ └──────────────┴──────────────┴──────────────┘         │
│ ┌──────────────┬──────────────┬──────────────┐         │
│ │ Pengeluaran  │ Profit       │ Margin       │         │
│ │ Rp 25 juta   │ Rp 65 juta   │ 72%          │         │
│ └──────────────┴──────────────┴──────────────┘         │
│                                                         │
│ 💰 PEMBAYARAN MASUK                    [+ Tambah]       │
│ ┌─────────────────────────────────────────────────┐   │
│ │ 15 Sep 2025 │ DP 50%     │ Rp 90.000.000 │ BTN │   │
│ │ - Transfer  │ TRF20250915001                    │   │
│ └─────────────────────────────────────────────────┘   │
│                                                         │
│ 💸 PENGELUARAN                         [+ Tambah]       │
│ ┌─────────────────────────────────────────────────┐   │
│ │ 20 Sep 2025 │ Vendor     │ Rp 15.000.000 │ BTN │   │
│ │ Drafter XYZ │ Gambar Site Plan              │   │
│ ├─────────────────────────────────────────────────┤   │
│ │ 22 Sep 2025 │ Laboratory │ Rp 10.000.000 │ BTN │   │
│ │ Lab ABC     │ Uji Udara & Air               │   │
│ └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### 4.2 Dashboard Keuangan

**Location:** `/dashboard` - Card Keuangan

**Metrics:**
- Total DP Masuk Bulan Ini
- Total Pengeluaran Bulan Ini
- Cash Position (saldo semua akun)
- Top 3 Proyek by Revenue
- Piutang Outstanding

### 4.3 Form Input Payment

**Modal/Page:** "Catat Pembayaran"

```
Tanggal Pembayaran: [Date Picker]
Nominal:           [Rp _________]
Tipe Pembayaran:   [Dropdown: DP / Progress / Final / Other]
Metode Pembayaran: [Dropdown: Transfer / Cash / Check]
Rekening Tujuan:   [Dropdown: Bank BTN / Cash]
Nomor Referensi:   [Text: TRF20250915001]
Keterangan:        [Textarea]
Upload Bukti:      [File Upload]

[Simpan] [Batal]
```

### 4.4 Form Input Expense

**Modal/Page:** "Catat Pengeluaran"

```
Tanggal:           [Date Picker]
Kategori:          [Dropdown: Vendor / Lab / Survey / Travel / Operasional / Pajak]
Nama Vendor:       [Text] (required jika kategori = Vendor)
Nominal:           [Rp _________]
Metode Pembayaran: [Dropdown: Transfer / Cash / Check]
Rekening Sumber:   [Dropdown: Bank BTN / Cash]
Keterangan:        [Textarea]
Billable ke Klien: [Checkbox] (default: Yes)
Upload Bukti:      [File Upload]

[Simpan] [Batal]
```

---

## 5. ROUTES & CONTROLLERS

### 5.1 Routes

```php
// routes/web.php

// Project Payments
Route::get('/projects/{project}/payments', [ProjectPaymentController::class, 'index'])
    ->name('projects.payments.index');
Route::get('/projects/{project}/payments/create', [ProjectPaymentController::class, 'create'])
    ->name('projects.payments.create');
Route::post('/projects/{project}/payments', [ProjectPaymentController::class, 'store'])
    ->name('projects.payments.store');
Route::delete('/projects/payments/{payment}', [ProjectPaymentController::class, 'destroy'])
    ->name('projects.payments.destroy');

// Project Expenses
Route::get('/projects/{project}/expenses', [ProjectExpenseController::class, 'index'])
    ->name('projects.expenses.index');
Route::get('/projects/{project}/expenses/create', [ProjectExpenseController::class, 'create'])
    ->name('projects.expenses.create');
Route::post('/projects/{project}/expenses', [ProjectExpenseController::class, 'store'])
    ->name('projects.expenses.store');
Route::delete('/projects/expenses/{expense}', [ProjectExpenseController::class, 'destroy'])
    ->name('projects.expenses.destroy');

// Cash Accounts
Route::resource('cash-accounts', CashAccountController::class);

// Financial Dashboard
Route::get('/financial/dashboard', [FinancialController::class, 'dashboard'])
    ->name('financial.dashboard');
```

### 5.2 Controller Methods

**ProjectPaymentController:**
```php
public function store(Request $request, Project $project)
{
    $validated = $request->validate([
        'payment_date' => 'required|date|before_or_equal:today',
        'amount' => 'required|numeric|min:0',
        'payment_type' => 'required|in:dp,progress,final,other',
        'payment_method' => 'required|in:transfer,cash,check,other',
        'bank_account_id' => 'nullable|exists:cash_accounts,id',
        'reference_number' => 'nullable|max:100',
        'description' => 'nullable|max:1000',
        'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);
    
    if ($request->hasFile('receipt_file')) {
        $validated['receipt_file'] = $request->file('receipt_file')
            ->store('receipts/payments', 'public');
    }
    
    $validated['created_by'] = auth()->id();
    
    DB::transaction(function () use ($project, $validated) {
        $payment = $project->payments()->create($validated);
        
        // Update cash account balance
        if ($validated['bank_account_id']) {
            $account = CashAccount::find($validated['bank_account_id']);
            $account->current_balance += $validated['amount'];
            $account->save();
        }
        
        // Auto-calculate payment_received, payment_status, profit_margin
        // (handled by model events)
    });
    
    return redirect()->route('projects.show', $project)
        ->with('success', 'Pembayaran berhasil dicatat');
}
```

**ProjectExpenseController:**
```php
public function store(Request $request, Project $project)
{
    $validated = $request->validate([
        'expense_date' => 'required|date|before_or_equal:today',
        'category' => 'required|in:vendor,laboratory,survey,travel,operational,tax,other',
        'vendor_name' => 'required_if:category,vendor|max:255',
        'amount' => 'required|numeric|min:0',
        'payment_method' => 'required|in:transfer,cash,check,other',
        'bank_account_id' => 'nullable|exists:cash_accounts,id',
        'description' => 'nullable|max:1000',
        'is_billable' => 'boolean',
        'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    ]);
    
    if ($request->hasFile('receipt_file')) {
        $validated['receipt_file'] = $request->file('receipt_file')
            ->store('receipts/expenses', 'public');
    }
    
    $validated['created_by'] = auth()->id();
    
    DB::transaction(function () use ($project, $validated) {
        $expense = $project->expenses()->create($validated);
        
        // Update cash account balance
        if ($validated['bank_account_id']) {
            $account = CashAccount::find($validated['bank_account_id']);
            $account->current_balance -= $validated['amount'];
            $account->save();
        }
        
        // Auto-calculate total_expenses, profit_margin
        // (handled by model events)
    });
    
    return redirect()->route('projects.show', $project)
        ->with('success', 'Pengeluaran berhasil dicatat');
}
```

---

## 6. MODELS & RELATIONSHIPS

### 6.1 Project Model

```php
// app/Models/Project.php

class Project extends Model
{
    protected $fillable = [
        // existing fields...
        'contract_value',
        'down_payment',
        'payment_received',
        'total_expenses',
        'profit_margin',
        'payment_terms',
        'payment_status',
    ];
    
    protected $casts = [
        'contract_value' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'payment_received' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'profit_margin' => 'decimal:2',
    ];
    
    // Relationships
    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }
    
    // Computed Properties
    public function getOutstandingReceivableAttribute()
    {
        return $this->contract_value - $this->payment_received;
    }
    
    public function getNetProfitAttribute()
    {
        return $this->payment_received - $this->total_expenses;
    }
    
    public function getProfitPercentageAttribute()
    {
        if ($this->contract_value > 0) {
            return ($this->net_profit / $this->contract_value) * 100;
        }
        return 0;
    }
}
```

### 6.2 ProjectPayment Model

```php
// app/Models/ProjectPayment.php

class ProjectPayment extends Model
{
    protected $fillable = [
        'project_id',
        'payment_date',
        'amount',
        'payment_type',
        'payment_method',
        'bank_account_id',
        'reference_number',
        'description',
        'receipt_file',
        'created_by',
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function bankAccount()
    {
        return $this->belongsTo(CashAccount::class, 'bank_account_id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Events (auto-update project totals)
    protected static function booted()
    {
        static::created(function ($payment) {
            $payment->project->updatePaymentReceived();
        });
        
        static::updated(function ($payment) {
            $payment->project->updatePaymentReceived();
        });
        
        static::deleted(function ($payment) {
            $payment->project->updatePaymentReceived();
        });
    }
}
```

### 6.3 ProjectExpense Model

```php
// app/Models/ProjectExpense.php

class ProjectExpense extends Model
{
    protected $fillable = [
        'project_id',
        'expense_date',
        'category',
        'vendor_name',
        'amount',
        'payment_method',
        'bank_account_id',
        'description',
        'receipt_file',
        'is_billable',
        'created_by',
    ];
    
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'is_billable' => 'boolean',
    ];
    
    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function bankAccount()
    {
        return $this->belongsTo(CashAccount::class, 'bank_account_id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    // Events
    protected static function booted()
    {
        static::created(function ($expense) {
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
        });
        
        static::updated(function ($expense) {
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
        });
        
        static::deleted(function ($expense) {
            if ($expense->project) {
                $expense->project->updateTotalExpenses();
            }
        });
    }
}
```

### 6.4 CashAccount Model

```php
// app/Models/CashAccount.php

class CashAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_type',
        'account_number',
        'bank_name',
        'account_holder',
        'current_balance',
        'initial_balance',
        'is_active',
        'notes',
    ];
    
    protected $casts = [
        'current_balance' => 'decimal:2',
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeBankAccounts($query)
    {
        return $query->where('account_type', 'bank');
    }
}
```

---

## 7. TESTING SCENARIOS

### 7.1 Unit Tests

**ProjectPaymentTest:**
```php
public function test_payment_updates_project_totals()
{
    $project = Project::factory()->create([
        'contract_value' => 180000000,
    ]);
    
    $payment = $project->payments()->create([
        'payment_date' => now(),
        'amount' => 90000000,
        'payment_type' => 'dp',
    ]);
    
    $project->refresh();
    
    $this->assertEquals(90000000, $project->payment_received);
    $this->assertEquals('partial', $project->payment_status);
}
```

**ProjectExpenseTest:**
```php
public function test_expense_updates_project_totals()
{
    $project = Project::factory()->create();
    
    $expense = $project->expenses()->create([
        'expense_date' => now(),
        'category' => 'vendor',
        'amount' => 15000000,
    ]);
    
    $project->refresh();
    
    $this->assertEquals(15000000, $project->total_expenses);
}
```

**ProfitMarginTest:**
```php
public function test_profit_margin_calculation()
{
    $project = Project::factory()->create([
        'contract_value' => 180000000,
    ]);
    
    $project->payments()->create([
        'payment_date' => now(),
        'amount' => 90000000,
        'payment_type' => 'dp',
    ]);
    
    $project->expenses()->create([
        'expense_date' => now(),
        'category' => 'vendor',
        'amount' => 15000000,
    ]);
    
    $project->refresh();
    
    // Profit = 90M - 15M = 75M
    // Margin = (75M / 90M) * 100 = 83.33%
    $this->assertEquals(83.33, round($project->profit_margin, 2));
}
```

### 7.2 Feature Tests

```php
public function test_user_can_record_payment()
{
    $project = Project::factory()->create();
    
    $response = $this->actingAs($this->user)
        ->post(route('projects.payments.store', $project), [
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 90000000,
            'payment_type' => 'dp',
            'payment_method' => 'transfer',
            'reference_number' => 'TRF20250915001',
        ]);
    
    $response->assertRedirect(route('projects.show', $project));
    $this->assertDatabaseHas('project_payments', [
        'project_id' => $project->id,
        'amount' => 90000000,
    ]);
}
```

---

## 8. MIGRATION FILES

### Migration 1: Add Financial Columns

```php
// database/migrations/2025_10_02_000001_add_financial_columns_to_projects_table.php

public function up()
{
    Schema::table('projects', function (Blueprint $table) {
        $table->decimal('contract_value', 15, 2)->default(0)->after('budget');
        $table->decimal('down_payment', 15, 2)->default(0)->after('contract_value');
        $table->decimal('payment_received', 15, 2)->default(0)->after('down_payment');
        $table->decimal('total_expenses', 15, 2)->default(0)->after('payment_received');
        $table->decimal('profit_margin', 5, 2)->default(0)->after('total_expenses');
        $table->text('payment_terms')->nullable()->after('profit_margin');
        $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid')->after('payment_terms');
    });
}

public function down()
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropColumn([
            'contract_value',
            'down_payment',
            'payment_received',
            'total_expenses',
            'profit_margin',
            'payment_terms',
            'payment_status',
        ]);
    });
}
```

### Migration 2: Create Cash Accounts

```php
// database/migrations/2025_10_02_000002_create_cash_accounts_table.php

public function up()
{
    Schema::create('cash_accounts', function (Blueprint $table) {
        $table->id();
        $table->string('account_name', 100);
        $table->enum('account_type', ['bank', 'cash', 'receivable', 'payable'])->default('bank');
        $table->string('account_number', 50)->nullable();
        $table->string('bank_name', 100)->nullable();
        $table->string('account_holder', 255)->nullable();
        $table->decimal('current_balance', 15, 2)->default(0);
        $table->decimal('initial_balance', 15, 2)->default(0);
        $table->boolean('is_active')->default(true);
        $table->text('notes')->nullable();
        $table->timestamps();
        
        $table->index(['account_type', 'is_active']);
    });
}
```

### Migration 3: Create Project Payments

```php
// database/migrations/2025_10_02_000003_create_project_payments_table.php

public function up()
{
    Schema::create('project_payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('project_id')->constrained()->onDelete('cascade');
        $table->date('payment_date');
        $table->decimal('amount', 15, 2);
        $table->enum('payment_type', ['dp', 'progress', 'final', 'other'])->default('other');
        $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])->default('transfer');
        $table->foreignId('bank_account_id')->nullable()->constrained('cash_accounts')->onDelete('set null');
        $table->string('reference_number', 100)->nullable();
        $table->text('description')->nullable();
        $table->string('receipt_file', 255)->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
        
        $table->index(['project_id', 'payment_date']);
        $table->index('payment_date');
    });
}
```

### Migration 4: Create Project Expenses

```php
// database/migrations/2025_10_02_000004_create_project_expenses_table.php

public function up()
{
    Schema::create('project_expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
        $table->date('expense_date');
        $table->enum('category', ['vendor', 'laboratory', 'survey', 'travel', 'operational', 'tax', 'other']);
        $table->string('vendor_name', 255)->nullable();
        $table->decimal('amount', 15, 2);
        $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])->default('transfer');
        $table->foreignId('bank_account_id')->nullable()->constrained('cash_accounts')->onDelete('set null');
        $table->text('description')->nullable();
        $table->string('receipt_file', 255)->nullable();
        $table->boolean('is_billable')->default(true);
        $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
        
        $table->index(['project_id', 'expense_date']);
        $table->index('expense_date');
        $table->index('category');
    });
}
```

---

## 9. SEEDER (Initial Data)

```php
// database/seeders/CashAccountSeeder.php

public function run()
{
    CashAccount::create([
        'account_name' => 'Bank BTN',
        'account_type' => 'bank',
        'account_number' => '1234567890',
        'bank_name' => 'Bank Tabungan Negara',
        'current_balance' => 46000000,
        'initial_balance' => 46000000,
        'is_active' => true,
    ]);
    
    CashAccount::create([
        'account_name' => 'Cash',
        'account_type' => 'cash',
        'current_balance' => 8000000,
        'initial_balance' => 8000000,
        'is_active' => true,
    ]);
    
    CashAccount::create([
        'account_name' => 'Mr. Gobs',
        'account_type' => 'receivable',
        'current_balance' => 20000000,
        'initial_balance' => 20000000,
        'is_active' => true,
        'notes' => 'Piutang ke Mr. Gobs',
    ]);
}
```

---

## 10. CHECKLIST IMPLEMENTATION

### Development Tasks

- [ ] **Database:**
  - [ ] Create 4 migration files
  - [ ] Run migrations
  - [ ] Create seeder for cash_accounts
  - [ ] Run seeder

- [ ] **Models:**
  - [ ] Update Project model (relationships, computed properties)
  - [ ] Create ProjectPayment model
  - [ ] Create ProjectExpense model
  - [ ] Create CashAccount model

- [ ] **Controllers:**
  - [ ] Create ProjectPaymentController
  - [ ] Create ProjectExpenseController
  - [ ] Create CashAccountController
  - [ ] Update ProjectController (add financial tab)

- [ ] **Views:**
  - [ ] Create projects/show - tab Keuangan
  - [ ] Create payments/create form
  - [ ] Create expenses/create form
  - [ ] Create cash-accounts CRUD views
  - [ ] Update dashboard with financial cards

- [ ] **Routes:**
  - [ ] Add payment routes
  - [ ] Add expense routes
  - [ ] Add cash account routes

- [ ] **Testing:**
  - [ ] Write unit tests
  - [ ] Write feature tests
  - [ ] Manual testing with real data

- [ ] **Documentation:**
  - [ ] User guide for financial module
  - [ ] API documentation (if needed)

### Data Migration Tasks

- [ ] **Existing Projects:**
  - [ ] Input contract_value untuk 6 proyek aktif
  - [ ] Input down_payment untuk 6 proyek
  - [ ] Input payment_terms

- [ ] **Payments History:**
  - [ ] Record payment PT Asia Con (Rp 90 juta)
  - [ ] Record payment PT Putra Jaya (Rp 30 juta)
  - [ ] Record payment PT Maulida (Rp 10 juta)
  - [ ] Record payment PT MCM (Rp 15 juta)
  - [ ] Record payment Nusantara (Rp 15 juta)
  - [ ] Record payment PT RAS (Rp 2 juta)

- [ ] **Expenses History:**
  - [ ] Record expense gambar PT MCM (Rp 15 juta)
  - [ ] Record expense Big MAN (Rp 5 juta)
  - [ ] Record expense Mr. Gobs (Rp 14 juta)
  - [ ] Record expense sidang (Rp 10 juta)
  - [ ] Record expense pajak (Rp 14 juta)
  - [ ] Record expense operasional (Rp 6 juta)
  - [ ] Record expense Odang (Rp 9 juta)

---

## 11. ACCEPTANCE CRITERIA

Phase 1 dianggap **DONE** jika:

### Functional Requirements
- ✅ User bisa input contract value & DP di project
- ✅ User bisa record payment received dengan bukti
- ✅ User bisa log expense per proyek dengan bukti
- ✅ System auto-calculate payment_received dari sum payments
- ✅ System auto-calculate total_expenses dari sum expenses
- ✅ System auto-calculate profit_margin
- ✅ System auto-update payment_status (unpaid/partial/paid)
- ✅ Dashboard show total DP masuk, expense, cash position
- ✅ Project show page display financial summary
- ✅ Cash account balance auto-update saat payment/expense

### Data Quality
- ✅ Semua 6 proyek existing sudah input contract value
- ✅ Semua payment history (total Rp 147 juta) sudah tercatat
- ✅ Semua expense history (total Rp 73 juta) sudah tercatat
- ✅ Cash account balance match real (BTN 46jt, Cash 8jt, Mr. Gobs 20jt)

### User Experience
- ✅ Form payment/expense mudah digunakan (< 2 menit input)
- ✅ Dashboard load time < 2 detik
- ✅ Mobile responsive
- ✅ Dark mode consistent

### Testing
- ✅ All unit tests pass
- ✅ All feature tests pass
- ✅ Manual testing dengan user real done
- ✅ No critical bugs

---

## 12. RISKS & MITIGATION

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Data migration error (salah input amount) | HIGH | MEDIUM | Double-check dengan laporan manual, validation strict |
| Performance issue (banyak calculation) | MEDIUM | LOW | Use database observers efficiently, cache where needed |
| User confusion (terlalu banyak field) | MEDIUM | MEDIUM | Mandatory fields only, good UX, tooltips |
| Cash account balance tidak match | HIGH | LOW | Transaction-based update, audit trail |

---

## 13. SUCCESS METRICS

**Post-Implementation (After 2 weeks):**
- ✅ 100% proyek sudah ada financial data
- ✅ Daily payment/expense entry < 5 menit
- ✅ Dashboard accuracy 100%
- ✅ User satisfaction > 4/5
- ✅ Zero data entry errors

---

**Document Version:** 1.0  
**Created:** 2 Oktober 2025  
**Target Completion:** 12 Oktober 2025  
**Status:** 🟡 Ready for Development
