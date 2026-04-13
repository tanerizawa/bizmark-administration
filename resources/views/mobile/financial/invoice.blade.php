@extends('mobile.layouts.app')

@section('title', 'Invoice #' . ($invoice->invoice_number ?? ''))

@section('content')
<div class="pb-20">

    {{-- Invoice Header --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm opacity-75">Invoice</p>
                    <h2 class="text-xl font-bold">#{{ $invoice->invoice_number }}</h2>
                </div>
                <span class="text-xs px-3 py-1.5 rounded-full font-medium
                    @if($invoice->status === 'paid') bg-green-400 text-green-900
                    @elseif($invoice->status === 'sent') bg-blue-400 text-blue-900
                    @elseif($invoice->status === 'overdue') bg-red-400 text-red-900
                    @else bg-white/20 text-white
                    @endif">
                    {{ ucfirst($invoice->status ?? 'Draft') }}
                </span>
            </div>
            <div class="text-3xl font-bold">
                Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}
            </div>
        </div>

        {{-- Details --}}
        <div class="p-4 space-y-3 text-sm">
            @if($invoice->client)
            <div class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-building text-gray-400 w-5 text-center"></i>
                <div>
                    <span class="text-xs text-gray-400 block">Klien</span>
                    <span class="font-medium text-gray-900">{{ $invoice->client->name }}</span>
                </div>
            </div>
            @endif

            @if($invoice->project)
            <div class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-folder text-gray-400 w-5 text-center"></i>
                <div>
                    <span class="text-xs text-gray-400 block">Proyek</span>
                    <a href="{{ route('mobile.projects.show', $invoice->project_id) }}" class="font-medium text-[#0A66C2]">
                        {{ $invoice->project->name }}
                    </a>
                </div>
            </div>
            @endif

            @if($invoice->issue_date)
            <div class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-calendar text-gray-400 w-5 text-center"></i>
                <div>
                    <span class="text-xs text-gray-400 block">Tanggal Terbit</span>
                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</span>
                </div>
            </div>
            @endif

            @if($invoice->due_date)
            <div class="flex items-center gap-2 
                @if($invoice->due_date < now() && $invoice->status !== 'paid') text-red-600 @else text-gray-600 @endif">
                <i class="fas fa-clock w-5 text-center
                    @if($invoice->due_date < now() && $invoice->status !== 'paid') text-red-400 @else text-gray-400 @endif"></i>
                <div>
                    <span class="text-xs @if($invoice->due_date < now() && $invoice->status !== 'paid') text-red-400 @else text-gray-400 @endif block">Jatuh Tempo</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span>
                    @if($invoice->due_date < now() && $invoice->status !== 'paid')
                    <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full ml-1">
                        {{ now()->diffInDays($invoice->due_date) }} hari lewat
                    </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Invoice Items --}}
    @if($invoice->items && $invoice->items->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Item Invoice</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($invoice->items as $item)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900">{{ $item->description ?? $item->name ?? '-' }}</h4>
                        @if($item->quantity)
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $item->quantity }} × Rp {{ number_format($item->unit_price ?? $item->price ?? 0, 0, ',', '.') }}
                        </p>
                        @endif
                    </div>
                    <div class="text-sm font-bold text-gray-900 flex-shrink-0">
                        Rp {{ number_format($item->amount ?? ($item->quantity * ($item->unit_price ?? $item->price ?? 0)), 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="border-t-2 border-gray-200 p-4">
            @if($invoice->subtotal)
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Subtotal</span>
                <span>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($invoice->tax_amount)
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>PPN ({{ $invoice->tax_rate ?? 11 }}%)</span>
                <span>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($invoice->discount_amount)
            <div class="flex justify-between text-sm text-green-600 mb-1">
                <span>Diskon</span>
                <span>-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                <span>Total</span>
                <span>Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($invoice->notes)
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
        <h3 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
            <i class="fas fa-sticky-note text-gray-400"></i>
            Catatan
        </h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $invoice->notes }}</p>
    </div>
    @endif
</div>
@endsection
