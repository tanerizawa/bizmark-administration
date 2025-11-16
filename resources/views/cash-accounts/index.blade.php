@extends('layouts.app')

@section('title', 'Keuangan')
@section('page-title', 'Manajemen Keuangan')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    {{-- Hero Section Dashboard-Style --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Financial Control Center</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Kelola Kas & Rekening dengan Insight Real-Time
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Monitor arus kas, piutang, dan tren finansial dalam satu dashboard komprehensif sesuai PSAK.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-sync-alt mr-2"></i>Last sync: {{ now()->format('d M Y, H:i') }}</p>
                    <p><i class="fas fa-shield-alt mr-2"></i>Finance Team Access</p>
                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('cash-accounts.create') }}" class="btn-primary-sm">
                            <i class="fas fa-plus mr-2"></i>Tambah Akun
                        </a>
                        <button onclick="showPeriodFilter()" class="btn-secondary-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>Filter Periode
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Aset Likuid</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color:#FFFFFF;">Rp {{ number_format($financialSummary['liquid_assets'] / 1000000, 1) }}M</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Bank + Kas Tunai</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,149,0,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Piutang Outstanding</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color:#FFFFFF;">Rp {{ number_format($financialSummary['total_receivables'] / 1000000, 1) }}M</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Invoice + Kasbon</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Kas Masuk Periode</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">Rp {{ number_format($financialSummary['cash_inflow_this_month'] / 1000000, 1) }}M</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $startDate->isoFormat('MMM YYYY') }}</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: {{ $financialSummary['net_cash_flow'] >= 0 ? 'rgba(52,199,89,0.12)' : 'rgba(255,59,48,0.12)' }};">
                    <p class="text-xs uppercase tracking-widest" style="color: {{ $financialSummary['net_cash_flow'] >= 0 ? 'rgba(52,199,89,0.9)' : 'rgba(255,59,48,0.9)' }};">Net Arus Kas</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color:#FFFFFF;">{{ $financialSummary['net_cash_flow'] >= 0 ? '+' : '' }}Rp {{ number_format($financialSummary['net_cash_flow'] / 1000000, 1) }}M</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $financialSummary['net_cash_flow'] >= 0 ? 'Surplus' : 'Defisit' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success flex items-center gap-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Period Filter & Insights --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Periode Aktif</p>
                <h2 class="text-2xl font-semibold text-white">{{ $startDate->isoFormat('D MMM Y') }} - {{ $endDate->isoFormat('D MMM Y') }}</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    @php $daysDiff = $startDate->diffInDays($endDate) + 1; @endphp
                    Menampilkan data {{ $daysDiff }} hari dengan {{ count($recentTransactions) }} transaksi tercatat.
                </p>
            </div>
            <button onclick="showPeriodFilter()" class="btn-secondary-sm">
                <i class="fas fa-calendar-alt mr-2"></i>Ubah Periode
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Kas Keluar</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(255,59,48,0.18); color: rgba(255,59,48,0.9);">Expense</span>
                </div>
                <p class="text-3xl font-bold text-white">Rp {{ number_format($financialSummary['cash_outflow_this_month'] / 1000000, 1) }}M</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Pengeluaran periode berjalan untuk operasional dan proyek.
                </p>
            </div>

            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Trend Arus Kas</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: {{ $financialSummary['is_positive_trend'] ? 'rgba(52,199,89,0.18)' : 'rgba(255,59,48,0.18)' }}; color: {{ $financialSummary['is_positive_trend'] ? 'rgba(52,199,89,0.9)' : 'rgba(255,59,48,0.9)' }};">
                        {{ $financialSummary['is_positive_trend'] ? 'Positif' : 'Negatif' }}
                    </span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $financialSummary['is_positive_trend'] ? '+' : '' }}{{ $financialSummary['cash_flow_trend'] }}%</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Perubahan dibanding bulan lalu {{ $financialSummary['is_positive_trend'] ? 'meningkat' : 'menurun' }}.
                </p>
            </div>

            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Rekening Aktif</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.18); color: rgba(10,132,255,0.9);">Live</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $accounts->count() }}</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Bank & kas terdaftar dalam sistem akuntansi.
                </p>
            </div>
        </div>
    </section>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2" role="tablist">
                <button onclick="switchTab('cash-flow')" id="tab-cash-flow" 
                        class="tab-button active px-4 py-2.5 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-chart-line mr-2"></i>Laporan Arus Kas
                </button>
                <button onclick="switchTab('accounts')" id="tab-accounts"
                        class="tab-button px-4 py-2.5 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-university mr-2"></i>Rekening Bank & Kas
                </button>
                <button onclick="switchTab('transactions')" id="tab-transactions"
                        class="tab-button px-4 py-2.5 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                </button>
            </div>
        </div>

        <div class="p-6">
        <div class="p-6">
            <!-- Tab 1: Cash Flow Statement -->
            <div id="content-cash-flow" class="tab-content">
                @include('cash-accounts.tabs.cash-flow', ['cashFlowStatement' => $cashFlowStatement])
            </div>

            <!-- Tab 2: Accounts List -->
            <div id="content-accounts" class="tab-content hidden">
                @include('cash-accounts.tabs.accounts', ['accounts' => $accounts])
            </div>

            <!-- Tab 3: Transactions Timeline -->
            <div id="content-transactions" class="tab-content hidden">
                @include('cash-accounts.tabs.transactions', ['recentTransactions' => $recentTransactions])
            </div>
        </div>
    </section>
</div>

{{-- Period Filter Modal --}}
<div id="periodModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-xl max-w-3xl w-full p-6 space-y-5" style="background: var(--dark-bg-elevated);">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-white">Filter Periode Laporan</h3>
                <p class="text-sm mt-1" style="color: rgba(235,235,245,0.65);">Pilih rentang waktu untuk analisis finansial</p>
            </div>
            <button onclick="closePeriodModal()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 transition-apple">
                <i class="fas fa-times" style="color: rgba(235,235,245,0.7);"></i>
            </button>
        </div>

        <form method="GET" action="{{ route('cash-accounts.index') }}" id="periodFilterForm" class="space-y-4">
            <!-- Filter Type Selector -->
            <div class="grid grid-cols-4 gap-2">
                <button type="button" onclick="setFilterType('month')" 
                        class="filter-type-btn {{ $filterType == 'month' ? 'active' : '' }}" data-type="month">
                    <i class="fas fa-calendar mr-2"></i>Bulan
                </button>
                <button type="button" onclick="setFilterType('quarter')" 
                        class="filter-type-btn {{ $filterType == 'quarter' ? 'active' : '' }}" data-type="quarter">
                    <i class="fas fa-calendar-week mr-2"></i>Kuartal
                </button>
                <button type="button" onclick="setFilterType('year')" 
                        class="filter-type-btn {{ $filterType == 'year' ? 'active' : '' }}" data-type="year">
                    <i class="fas fa-calendar-alt mr-2"></i>Tahun
                </button>
                <button type="button" onclick="setFilterType('custom')" 
                        class="filter-type-btn {{ $filterType == 'custom' ? 'active' : '' }}" data-type="custom">
                    <i class="fas fa-sliders-h mr-2"></i>Custom
                </button>
            </div>

            <input type="hidden" name="filter_type" id="filter_type" value="{{ $filterType }}">

            <!-- Month Filter -->
            <div id="monthFilter" class="space-y-3 {{ $filterType != 'month' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Bulan</label>
                        <select name="month" class="w-full">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Tahun</label>
                        <select name="year" class="w-full">
                            @for($y = 2020; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quarter Filter -->
            <div id="quarterFilter" class="space-y-3 {{ $filterType != 'quarter' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Kuartal</label>
                        <select name="quarter" class="w-full">
                            <option value="1" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                            <option value="2" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                            <option value="3" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                            <option value="4" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 4 ? 'selected' : '' }}>Q4 (Okt-Des)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Tahun</label>
                        <select name="year" class="w-full">
                            @for($y = 2020; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Year Filter -->
            <div id="yearFilter" class="space-y-3 {{ $filterType != 'year' ? 'hidden' : '' }}">
                <div>
                    <label class="text-sm font-medium text-white mb-2 block">Tahun</label>
                    <select name="year" class="w-full">
                        @for($y = 2020; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Custom Date Range -->
            <div id="customFilter" class="space-y-3 {{ $filterType != 'custom' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="w-full">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-white mb-2 block">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="w-full">
                    </div>
                </div>
            </div>

            <!-- Quick Shortcuts -->
            @if(count($availablePeriods) > 0)
            <div class="pt-4 border-t" style="border-color: var(--dark-separator);">
                <p class="text-xs font-semibold text-white mb-2">Shortcut Periode:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(array_slice($availablePeriods, 0, 6) as $period)
                        <a href="{{ route('cash-accounts.index', ['filter_type' => 'month', 'month' => $period['month'], 'year' => $period['year']]) }}"
                           class="text-xs px-3 py-1.5 rounded-apple transition-apple"
                           style="background: rgba(10,132,255,0.1); color: rgba(10,132,255,0.9); border: 1px solid rgba(10,132,255,0.2);">
                            {{ \Carbon\Carbon::create($period['year'], $period['month'], 1)->isoFormat('MMM YYYY') }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <button type="button" onclick="closePeriodModal()" class="btn-secondary-sm px-6">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.tab-button {
    color: rgba(235, 235, 245, 0.6);
    background-color: transparent;
}

.tab-button:hover {
    color: rgba(235, 235, 245, 0.9);
    background-color: rgba(255, 255, 255, 0.05);
}

.tab-button.active {
    color: #FFFFFF;
    background-color: rgba(0, 122, 255, 0.15);
    border: 1px solid rgba(0, 122, 255, 0.3);
}

.tab-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.filter-type-btn {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: var(--dark-bg-tertiary);
    border: 1px solid var(--dark-separator);
    color: rgba(235, 235, 245, 0.7);
}

.filter-type-btn:hover {
    background-color: var(--dark-bg-secondary);
    border-color: var(--apple-blue);
    color: rgba(235, 235, 245, 0.9);
}

.filter-type-btn.active {
    background-color: rgba(0, 122, 255, 0.15);
    border-color: var(--apple-blue);
    color: #FFFFFF;
}
</style>

<script>
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.add('active');
}

function showPeriodFilter() {
    document.getElementById('periodModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePeriodModal() {
    document.getElementById('periodModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function setFilterType(type) {
    document.getElementById('filter_type').value = type;
    
    // Hide all filter sections
    document.getElementById('monthFilter').classList.add('hidden');
    document.getElementById('quarterFilter').classList.add('hidden');
    document.getElementById('yearFilter').classList.add('hidden');
    document.getElementById('customFilter').classList.add('hidden');
    
    // Remove active class from all buttons
    document.querySelectorAll('.filter-type-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected filter section
    document.getElementById(type + 'Filter').classList.remove('hidden');
    
    // Add active class to selected button
    document.querySelector(`[data-type="${type}"]`).classList.add('active');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePeriodModal();
    }
});

// Close modals on backdrop click
document.getElementById('periodModal').addEventListener('click', function(e) {
    if (e.target === this) closePeriodModal();
});
</script>
@endsection
                    <button type="button" onclick="setFilterType('month')" 
                            class="px-3 py-1.5 text-xs font-medium transition-all {{ $filterType == 'month' ? 'filter-active' : 'filter-inactive' }}"
                            style="background: {{ $filterType == 'month' ? 'rgba(0, 122, 255, 0.2)' : 'transparent' }}; color: {{ $filterType == 'month' ? 'rgba(0, 122, 255, 1)' : 'rgba(235, 235, 245, 0.6)' }};">
                        Bulan
