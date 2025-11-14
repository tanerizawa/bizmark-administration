@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">💰 Verifikasi Pembayaran</h1>
        <p class="text-sm" style="color: rgba(235,235,245,0.75);">Tinjau dan verifikasi pembayaran manual dari client</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(255,149,0,0.8);">
                        Menunggu Verifikasi
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">{{ $payments->where('status', 'processing')->count() }}</h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pembayaran pending</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(255,149,0,0.15);">
                    <i class="fas fa-clock text-xl" style="color: rgba(255,149,0,0.9);"></i>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(52,199,89,0.8);">
                        Terverifikasi Hari Ini
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        {{ \App\Models\Payment::where('status', 'success')->whereDate('verified_at', today())->count() }}
                    </h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pembayaran terverifikasi</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(52,199,89,0.15);">
                    <i class="fas fa-check-circle text-xl" style="color: rgba(52,199,89,0.9);"></i>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(175,82,222,0.8);">
                        Total Amount
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">
                        Rp {{ number_format($payments->sum('amount') / 1000000, 1) }}M
                    </h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Total pembayaran</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(175,82,222,0.15);">
                    <i class="fas fa-money-bill-wave text-xl" style="color: rgba(175,82,222,0.9);"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments List --}}
    <div class="card-elevated rounded-apple-xl overflow-hidden">
        {{-- Filter Tabs --}}
        <div class="px-6 py-4" style="background: rgba(28,28,30,0.5); border-bottom: 1px solid rgba(84,84,88,0.35);">
            <div class="flex gap-4">
                <button class="tab-btn active" data-status="all">
                    <span class="text-sm">Semua ({{ $payments->count() }})</span>
                </button>
                <button class="tab-btn" data-status="processing">
                    <span class="text-sm">Menunggu ({{ $payments->where('status', 'processing')->count() }})</span>
                </button>
                <button class="tab-btn" data-status="pending">
                    <span class="text-sm">Pending ({{ $payments->where('status', 'pending')->count() }})</span>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background: rgba(28,28,30,0.5); border-bottom: 1px solid rgba(84,84,88,0.35);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Payment</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Client</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aplikasi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Amount</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Bank</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Action</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid rgba(84,84,88,0.35);">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-opacity-50 transition payment-row" data-status="{{ $payment->status }}" 
                            style="border-bottom: 1px solid rgba(84,84,88,0.35);">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white">{{ $payment->payment_number }}</div>
                                <div class="text-xs" style="color: rgba(235,235,245,0.6);">{{ ucfirst($payment->payment_type) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white">{{ $payment->client->name }}</div>
                                <div class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $payment->client->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white">{{ $payment->quotation->application->application_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white">{{ $payment->bank_name }}</div>
                                <div class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $payment->account_holder }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm" style="color: rgba(235,235,245,0.75);">
                                    {{ $payment->created_at->format('d M Y H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'processing' => ['bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)', 'border' => 'rgba(255,149,0,0.3)'],
                                        'success' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)'],
                                        'failed' => ['bg' => 'rgba(255,59,48,0.15)', 'text' => 'rgba(255,59,48,1)', 'border' => 'rgba(255,59,48,0.3)'],
                                        'pending' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)'],
                                    ];
                                    $color = $statusColors[$payment->status] ?? $statusColors['pending'];
                                @endphp
                                <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-apple"
                                      style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border: 1px solid {{ $color['border'] }};">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'processing' || $payment->status === 'pending')
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="text-sm font-medium" style="color: rgba(10,132,255,0.9);">
                                        <i class="fas fa-eye mr-1"></i>Review
                                    </a>
                                @else
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="text-sm" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-4xl mb-3" style="color: rgba(235,235,245,0.3);"></i>
                                <p class="text-sm" style="color: rgba(235,235,245,0.6);">Tidak ada pembayaran manual</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid rgba(84,84,88,0.35);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.tab-btn {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 500;
    color: rgba(235,235,245,0.6);
    transition: all 0.2s;
    background: transparent;
    border: none;
}
.tab-btn:hover {
    background: rgba(44,44,46,0.5);
    color: rgba(235,235,245,0.9);
}
.tab-btn.active {
    background: rgba(10,132,255,0.15);
    color: rgba(10,132,255,1);
    border: 1px solid rgba(10,132,255,0.3);
}
</style>

<script>
// Filter by status
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const status = this.dataset.status;
        
        // Filter rows
        document.querySelectorAll('.payment-row').forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
