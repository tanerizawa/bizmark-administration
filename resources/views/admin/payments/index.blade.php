@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h1>
        <p class="text-gray-600">Tinjau dan verifikasi pembayaran manual dari client</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $payments->where('status', 'processing')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Terverifikasi Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ \App\Models\Payment::where('status', 'success')->whereDate('verified_at', today())->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-purple-600">
                        Rp {{ number_format($payments->sum('amount') / 1000000, 1) }}M
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Filter Tabs -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-3">
            <div class="flex gap-4">
                <button class="tab-btn active" data-status="all">
                    Semua ({{ $payments->count() }})
                </button>
                <button class="tab-btn" data-status="processing">
                    Menunggu ({{ $payments->where('status', 'processing')->count() }})
                </button>
                <button class="tab-btn" data-status="pending">
                    Pending ({{ $payments->where('status', 'pending')->count() }})
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aplikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 payment-row" data-status="{{ $payment->status }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->payment_number }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($payment->payment_type) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->client->name }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->client->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->quotation->application->application_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $payment->bank_name }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->account_holder }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($payment->status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($payment->status === 'success') bg-green-100 text-green-800
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($payment->status === 'processing' || $payment->status === 'pending')
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="text-purple-600 hover:text-purple-900 font-medium">
                                        <i class="fas fa-eye mr-1"></i>Review
                                    </a>
                                @else
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" 
                                       class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-eye mr-1"></i>Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p>Tidak ada pembayaran manual</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.tab-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    color: #6B7280;
    transition: all 0.2s;
}
.tab-btn:hover {
    background: #F3F4F6;
}
.tab-btn.active {
    background: #7C3AED;
    color: white;
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
