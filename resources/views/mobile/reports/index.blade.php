@extends('mobile.layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="pb-20">
    
    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-xl font-bold">Laporan</h2>
                <p class="text-sm opacity-90">Analisa & insight bisnis</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>
        
        {{-- Period Selector --}}
        <div class="grid grid-cols-3 gap-2">
            <button class="py-2 bg-white/10 rounded-lg text-sm font-medium border border-white/20">
                Bulan Ini
            </button>
            <button class="py-2 bg-white text-[#0077b5] rounded-lg text-sm font-medium">
                3 Bulan
            </button>
            <button class="py-2 bg-white/10 rounded-lg text-sm font-medium border border-white/20">
                1 Tahun
            </button>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-green-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Revenue</div>
            </div>
            <div class="text-xl font-bold text-gray-900">Rp {{ number_format($metrics['revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-green-600 mt-1">
                <i class="fas fa-arrow-up"></i> {{ $metrics['revenue_growth'] }}%
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-red-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Expenses</div>
            </div>
            <div class="text-xl font-bold text-gray-900">Rp {{ number_format($metrics['expenses'], 0, ',', '.') }}</div>
            <div class="text-xs text-red-600 mt-1">
                <i class="fas fa-arrow-up"></i> {{ $metrics['expense_growth'] }}%
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Projects</div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $metrics['active_projects'] }}</div>
            <div class="text-xs text-gray-600 mt-1">{{ $metrics['completed_projects'] }} selesai</div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percent text-purple-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Profit Margin</div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $metrics['profit_margin'] }}%</div>
            <div class="text-xs text-gray-600 mt-1">Target: 25%</div>
        </div>
    </div>

    {{-- Report Categories --}}
    <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">KATEGORI LAPORAN</h3>
    <div class="space-y-2 mb-4">
        <a href="{{ mobile_route('reports.financial') }}" 
           class="block bg-white rounded-xl p-4 border border-gray-200 active:bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">Laporan Keuangan</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Cash flow, P&L, Balance sheet</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </a>
        
        <a href="{{ mobile_route('reports.projects') }}" 
           class="block bg-white rounded-xl p-4 border border-gray-200 active:bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">Laporan Proyek</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Progress, timeline, resources</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </a>
        
        <a href="{{ mobile_route('reports.team') }}" 
           class="block bg-white rounded-xl p-4 border border-gray-200 active:bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">Laporan Tim</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Productivity, workload, performance</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </a>
        
        <a href="{{ mobile_route('reports.clients') }}" 
           class="block bg-white rounded-xl p-4 border border-gray-200 active:bg-gray-50">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-orange-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">Laporan Klien</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Client satisfaction, retention</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </a>
    </div>

    {{-- Quick Charts --}}
    <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">GRAFIK CEPAT</h3>
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h4 class="font-medium text-gray-900 mb-3">Revenue vs Expenses (3 Bulan)</h4>
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-chart-bar text-4xl mb-2"></i>
            <p class="text-sm">Chart akan ditampilkan di sini</p>
        </div>
    </div>

    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h4 class="font-medium text-gray-900 mb-3">Project Status Distribution</h4>
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-chart-pie text-4xl mb-2"></i>
            <p class="text-sm">Chart akan ditampilkan di sini</p>
        </div>
    </div>

</div>
@endsection
