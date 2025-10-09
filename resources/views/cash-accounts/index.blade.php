@extends('layouts.app')

@section('title', 'Keuangan')
@section('page-title', 'Manajemen Keuangan')

@section('content')
<div class="max-w-full">
    <!-- Header with Period Filter -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
        <div>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Laporan keuangan komprehensif sesuai PSAK</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Period Filter -->
            <form method="GET" action="{{ route('cash-accounts.index') }}" class="flex items-center space-x-2" id="periodFilterForm">
                <!-- Filter Type Selector -->
                <div class="flex items-center rounded-apple overflow-hidden" style="background: rgba(255, 255, 255, 0.05);">
                    <button type="button" onclick="setFilterType('month')" 
                            class="px-3 py-1.5 text-xs font-medium transition-all {{ $filterType == 'month' ? 'filter-active' : 'filter-inactive' }}"
                            style="background: {{ $filterType == 'month' ? 'rgba(0, 122, 255, 0.2)' : 'transparent' }}; color: {{ $filterType == 'month' ? 'rgba(0, 122, 255, 1)' : 'rgba(235, 235, 245, 0.6)' }};">
                        Bulan
                    </button>
                    <button type="button" onclick="setFilterType('quarter')" 
                            class="px-3 py-1.5 text-xs font-medium transition-all {{ $filterType == 'quarter' ? 'filter-active' : 'filter-inactive' }}"
                            style="background: {{ $filterType == 'quarter' ? 'rgba(0, 122, 255, 0.2)' : 'transparent' }}; color: {{ $filterType == 'quarter' ? 'rgba(0, 122, 255, 1)' : 'rgba(235, 235, 245, 0.6)' }};">
                        Kuartal
                    </button>
                    <button type="button" onclick="setFilterType('year')" 
                            class="px-3 py-1.5 text-xs font-medium transition-all {{ $filterType == 'year' ? 'filter-active' : 'filter-inactive' }}"
                            style="background: {{ $filterType == 'year' ? 'rgba(0, 122, 255, 0.2)' : 'transparent' }}; color: {{ $filterType == 'year' ? 'rgba(0, 122, 255, 1)' : 'rgba(235, 235, 245, 0.6)' }};">
                        Tahun
                    </button>
                    <button type="button" onclick="setFilterType('custom')" 
                            class="px-3 py-1.5 text-xs font-medium transition-all {{ $filterType == 'custom' ? 'filter-active' : 'filter-inactive' }}"
                            style="background: {{ $filterType == 'custom' ? 'rgba(0, 122, 255, 0.2)' : 'transparent' }}; color: {{ $filterType == 'custom' ? 'rgba(0, 122, 255, 1)' : 'rgba(235, 235, 245, 0.6)' }};">
                        Custom
                    </button>
                </div>
                
                <input type="hidden" name="filter_type" id="filter_type" value="{{ $filterType }}">
                
                <!-- Month Filter (shown when filter_type = month) -->
                <div id="monthFilter" class="flex items-center space-x-2 px-3 py-1.5 rounded-apple {{ $filterType != 'month' ? 'hidden' : '' }}" style="background: rgba(255, 255, 255, 0.05);">
                    <i class="fas fa-calendar-alt text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    <select name="month" 
                            class="bg-transparent border-none text-xs font-medium focus:outline-none cursor-pointer"
                            style="color: rgba(235, 235, 245, 0.9);"
                            onchange="document.getElementById('periodFilterForm').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                    <select name="year"
                            class="bg-transparent border-none text-xs font-medium focus:outline-none cursor-pointer"
                            style="color: rgba(235, 235, 245, 0.9);"
                            onchange="document.getElementById('periodFilterForm').submit()">
                        @for($y = 2020; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <!-- Quarter Filter (shown when filter_type = quarter) -->
                <div id="quarterFilter" class="flex items-center space-x-2 px-3 py-1.5 rounded-apple {{ $filterType != 'quarter' ? 'hidden' : '' }}" style="background: rgba(255, 255, 255, 0.05);">
                    <i class="fas fa-calendar-alt text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    <select name="quarter" 
                            class="bg-transparent border-none text-xs font-medium focus:outline-none cursor-pointer"
                            style="color: rgba(235, 235, 245, 0.9);"
                            onchange="document.getElementById('periodFilterForm').submit()">
                        <option value="1" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                        <option value="2" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                        <option value="3" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                        <option value="4" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 4 ? 'selected' : '' }}>Q4 (Okt-Des)</option>
                    </select>
                    <select name="year"
                            class="bg-transparent border-none text-xs font-medium focus:outline-none cursor-pointer"
                            style="color: rgba(235, 235, 245, 0.9);"
                            onchange="document.getElementById('periodFilterForm').submit()">
                        @for($y = 2020; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <!-- Year Filter (shown when filter_type = year) -->
                <div id="yearFilter" class="flex items-center space-x-2 px-3 py-1.5 rounded-apple {{ $filterType != 'year' ? 'hidden' : '' }}" style="background: rgba(255, 255, 255, 0.05);">
                    <i class="fas fa-calendar-alt text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    <select name="year"
                            class="bg-transparent border-none text-xs font-medium focus:outline-none cursor-pointer"
                            style="color: rgba(235, 235, 245, 0.9);"
                            onchange="document.getElementById('periodFilterForm').submit()">
                        @for($y = 2020; $y <= date('Y'); $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <!-- Custom Date Range Filter (shown when filter_type = custom) -->
                <div id="customFilter" class="flex items-center space-x-2 {{ $filterType != 'custom' ? 'hidden' : '' }}">
                    <div class="flex items-center px-3 py-1.5 rounded-apple" style="background: rgba(255, 255, 255, 0.05);">
                        <i class="fas fa-calendar text-xs mr-2" style="color: rgba(235, 235, 245, 0.4);"></i>
                        <input type="date" name="start_date" 
                               value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                               class="bg-transparent border-none text-xs font-medium focus:outline-none"
                               style="color: rgba(235, 235, 245, 0.9);">
                    </div>
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.5);">s/d</span>
                    <div class="flex items-center px-3 py-1.5 rounded-apple" style="background: rgba(255, 255, 255, 0.05);">
                        <input type="date" name="end_date"
                               value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                               class="bg-transparent border-none text-xs font-medium focus:outline-none"
                               style="color: rgba(235, 235, 245, 0.9);">
                    </div>
                    <button type="submit" 
                            class="px-3 py-1.5 rounded-apple text-xs font-medium transition-all"
                            style="background: rgba(0, 122, 255, 0.2); color: rgba(0, 122, 255, 1);">
                        <i class="fas fa-search mr-1"></i>Terapkan
                    </button>
                </div>
            </form>
            
            <a href="{{ route('cash-accounts.create') }}" 
               class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>
                Tambah Akun
            </a>
        </div>
    </div>
    
    <!-- Period Indicator -->
    <div class="mb-4 flex items-center justify-between px-3 py-2 rounded-apple" style="background: rgba(0, 122, 255, 0.08);">
        <div class="flex items-center space-x-2">
            <i class="fas fa-info-circle text-xs" style="color: rgba(0, 122, 255, 0.8);"></i>
            <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                Menampilkan data periode: <strong>{{ $startDate->isoFormat('D MMM Y') }} - {{ $endDate->isoFormat('D MMM Y') }}</strong>
                @php
                    $daysDiff = $startDate->diffInDays($endDate) + 1;
                @endphp
                <span style="color: rgba(235, 235, 245, 0.5);">({{ $daysDiff }} hari)</span>
            </span>
        </div>
        @if(count($availablePeriods) > 0)
        <div class="flex items-center space-x-2">
            <span class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Shortcut:</span>
            @foreach(array_slice($availablePeriods, 0, 3) as $period)
                <a href="{{ route('cash-accounts.index', ['filter_type' => 'month', 'month' => $period['month'], 'year' => $period['year']]) }}"
                   class="text-xs px-2 py-0.5 rounded hover:bg-opacity-20 transition-all"
                   style="background: rgba(0, 122, 255, {{ $filterType == 'month' && $selectedMonth == $period['month'] && $selectedYear == $period['year'] ? '0.2' : '0.1' }}); color: rgba(0, 122, 255, 1);">
                    {{ \Carbon\Carbon::create($period['year'], $period['month'], 1)->isoFormat('MMM YY') }}
                </a>
            @endforeach
        </div>
        @endif
    </div>
    
    <script>
    function setFilterType(type) {
        document.getElementById('filter_type').value = type;
        
        // Hide all filter sections
        document.getElementById('monthFilter').classList.add('hidden');
        document.getElementById('quarterFilter').classList.add('hidden');
        document.getElementById('yearFilter').classList.add('hidden');
        document.getElementById('customFilter').classList.add('hidden');
        
        // Show selected filter section
        if (type === 'month') {
            document.getElementById('monthFilter').classList.remove('hidden');
        } else if (type === 'quarter') {
            document.getElementById('quarterFilter').classList.remove('hidden');
        } else if (type === 'year') {
            document.getElementById('yearFilter').classList.remove('hidden');
        } else if (type === 'custom') {
            document.getElementById('customFilter').classList.remove('hidden');
            return; // Don't auto-submit for custom, wait for user to click "Terapkan"
        }
        
        // Auto-submit for non-custom filters
        if (type !== 'custom') {
            document.getElementById('periodFilterForm').submit();
        }
    }
    </script>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-apple" style="background: rgba(52, 199, 89, 0.2); border-left: 4px solid rgba(52, 199, 89, 1);">
        <p style="color: rgba(52, 199, 89, 1);">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-apple" style="background: rgba(255, 59, 48, 0.2); border-left: 4px solid rgba(255, 59, 48, 1);">
        <p style="color: rgba(255, 59, 48, 1);">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </p>
    </div>
    @endif

    <!-- Financial Dashboard Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Total Liquid Assets -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Aset Likuid</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">Rp {{ number_format($financialSummary['liquid_assets'] / 1000000, 1) }}M</div>
                    <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">Bank + Kas Tunai</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(0, 122, 255, 0.15);">
                    <i class="fas fa-wallet text-xl" style="color: rgba(0, 122, 255, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Outstanding Receivables -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Piutang Outstanding</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">Rp {{ number_format($financialSummary['total_receivables'] / 1000000, 1) }}M</div>
                    <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">Invoice + Kasbon</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 149, 0, 0.15);">
                    <i class="fas fa-clock text-xl" style="color: rgba(255, 149, 0, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Cash Inflow -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Kas Masuk Bulan Ini</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">Rp {{ number_format($financialSummary['cash_inflow_this_month'] / 1000000, 1) }}M</div>
                    <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">{{ now()->format('F Y') }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(52, 199, 89, 0.15);">
                    <i class="fas fa-arrow-down text-xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Cash Outflow -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Kas Keluar Bulan Ini</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">Rp {{ number_format($financialSummary['cash_outflow_this_month'] / 1000000, 1) }}M</div>
                    <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">{{ now()->format('F Y') }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 59, 48, 0.15);">
                    <i class="fas fa-arrow-up text-xl" style="color: rgba(255, 59, 48, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Net Arus Kas</div>
                    <div class="text-2xl font-bold mt-1" style="color: {{ $financialSummary['net_cash_flow'] >= 0 ? '#34C759' : '#FF3B30' }};">
                        {{ $financialSummary['net_cash_flow'] >= 0 ? '+' : '' }}Rp {{ number_format($financialSummary['net_cash_flow'] / 1000000, 1) }}M
                    </div>
                    <div class="text-xs mt-1" style="color: {{ $financialSummary['net_cash_flow'] >= 0 ? 'rgba(52, 199, 89, 0.8)' : 'rgba(255, 59, 48, 0.8)' }};">
                        {{ $financialSummary['net_cash_flow'] >= 0 ? 'Surplus' : 'Defisit' }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: {{ $financialSummary['net_cash_flow'] >= 0 ? 'rgba(52, 199, 89, 0.15)' : 'rgba(255, 59, 48, 0.15)' }};">
                    <i class="fas fa-chart-line text-xl" style="color: {{ $financialSummary['net_cash_flow'] >= 0 ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};"></i>
                </div>
            </div>
        </div>

        <!-- Cash Flow Trend -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Trend vs Bulan Lalu</div>
                    <div class="text-2xl font-bold mt-1" style="color: {{ $financialSummary['is_positive_trend'] ? '#34C759' : '#FF3B30' }};">
                        {{ $financialSummary['is_positive_trend'] ? '+' : '' }}{{ $financialSummary['cash_flow_trend'] }}%
                    </div>
                    <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                        {{ $financialSummary['is_positive_trend'] ? 'Meningkat' : 'Menurun' }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: {{ $financialSummary['is_positive_trend'] ? 'rgba(52, 199, 89, 0.15)' : 'rgba(255, 59, 48, 0.15)' }};">
                    <i class="fas fa-trending-{{ $financialSummary['is_positive_trend'] ? 'up' : 'down' }} text-xl" style="color: {{ $financialSummary['is_positive_trend'] ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="border-b" style="border-color: rgba(84, 84, 88, 0.65);">
            <div class="flex space-x-1 p-2" role="tablist">
                <button onclick="switchTab('cash-flow')" id="tab-cash-flow" 
                        class="tab-button active px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-chart-line mr-2"></i>Laporan Arus Kas
                </button>
                <button onclick="switchTab('accounts')" id="tab-accounts"
                        class="tab-button px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-university mr-2"></i>Rekening Bank & Kas
                </button>
                <button onclick="switchTab('transactions')" id="tab-transactions"
                        class="tab-button px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                </button>
            </div>
        </div>

        <!-- Tab Content -->
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
</style>

<script>
function switchTab(tabName) {
    // Hide all content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activate selected button
    document.getElementById('tab-' + tabName).classList.add('active');
}
</script>
@endsection
