@extends('mobile.layouts.app')

@section('title', 'Piutang')

@section('content')
<div class="pb-20">

    {{-- Summary Card --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Piutang</h2>
                <p class="text-sm opacity-90">Aging receivables</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
        </div>

        @php
            $totalReceivables = ($summary['current'] ?? 0) + ($summary['1-30'] ?? 0) + ($summary['31-60'] ?? 0) + ($summary['60+'] ?? 0);
        @endphp
        <div class="text-center mb-4">
            <div class="text-sm opacity-75">Total Piutang</div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</div>
        </div>

        <div class="grid grid-cols-4 gap-2">
            <div class="bg-white/10 rounded-lg p-2 border border-white/20 text-center">
                <div class="text-xs opacity-75">Current</div>
                <div class="text-sm font-bold">{{ number_format(($summary['current'] ?? 0) / 1000000, 1) }}jt</div>
            </div>
            <div class="bg-white/10 rounded-lg p-2 border border-white/20 text-center">
                <div class="text-xs opacity-75">1-30 hr</div>
                <div class="text-sm font-bold text-amber-200">{{ number_format(($summary['1-30'] ?? 0) / 1000000, 1) }}jt</div>
            </div>
            <div class="bg-white/10 rounded-lg p-2 border border-white/20 text-center">
                <div class="text-xs opacity-75">31-60 hr</div>
                <div class="text-sm font-bold text-orange-200">{{ number_format(($summary['31-60'] ?? 0) / 1000000, 1) }}jt</div>
            </div>
            <div class="bg-white/10 rounded-lg p-2 border border-white/20 text-center">
                <div class="text-xs opacity-75">60+ hr</div>
                <div class="text-sm font-bold text-red-200">{{ number_format(($summary['60+'] ?? 0) / 1000000, 1) }}jt</div>
            </div>
        </div>
    </div>

    {{-- Receivables List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Daftar Piutang</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($receivables as $item)
            <a href="{{ route('mobile.financial.invoice', $item['id']) }}"
               class="block p-4 active:bg-gray-50 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        @if($item['aging_bucket'] === 'current') bg-green-50
                        @elseif($item['aging_bucket'] === '1-30') bg-amber-50
                        @elseif($item['aging_bucket'] === '31-60') bg-orange-50
                        @else bg-red-50
                        @endif">
                        <i class="fas fa-file-invoice text-sm
                            @if($item['aging_bucket'] === 'current') text-green-600
                            @elseif($item['aging_bucket'] === '1-30') text-amber-600
                            @elseif($item['aging_bucket'] === '31-60') text-orange-600
                            @else text-red-600
                            @endif"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $item['invoice_number'] }}</h4>
                            @if($item['days_overdue'] > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-red-100 text-red-700">
                                {{ $item['days_overdue'] }}hr
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">{{ $item['client'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            <i class="fas fa-calendar mr-1"></i>
                            Jatuh tempo: {{ \Carbon\Carbon::parse($item['due_date'])->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-bold text-gray-900">
                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium mt-1 inline-block
                            @if($item['status'] === 'sent') bg-blue-100 text-blue-700
                            @elseif($item['status'] === 'overdue') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($item['status']) }}
                        </span>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-8 text-center text-sm text-gray-400">
                <i class="fas fa-check-circle text-3xl text-green-300 mb-2 block"></i>
                Tidak ada piutang outstanding
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
