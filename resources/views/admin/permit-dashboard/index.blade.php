@extends('layouts.app')

@section('title', 'Permit Application Dashboard')
@section('page-title', 'Permit Application Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="card-elevated rounded-apple-xl p-5 md:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
                    📋 Permit Application Dashboard
                </h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                    Overview lengkap status permohonan izin, pembayaran, dan revenue
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.permit-applications.index') }}" class="btn-primary-sm">
                    <i class="fas fa-list mr-2"></i>Semua Aplikasi
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-money-bill-wave mr-2"></i>Pembayaran
                </a>
            </div>
        </div>
    </div>

    {{-- Top Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Pending Applications --}}
        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(255,149,0,0.8);">
                        Perlu Review
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">{{ $pendingApplications }}</h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Aplikasi pending</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(255,149,0,0.15);">
                    <i class="fas fa-clipboard-check text-xl" style="color: rgba(255,149,0,0.9);"></i>
                </div>
            </div>
            <a href="{{ route('admin.permit-applications.index', ['status' => 'submitted']) }}" 
               class="text-xs mt-4 inline-flex items-center" style="color: rgba(255,149,0,0.9);">
                Lihat detail <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        {{-- Need Quotation --}}
        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(10,132,255,0.8);">
                        Perlu Quotation
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">{{ $needQuotation }}</h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum ada penawaran</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(10,132,255,0.15);">
                    <i class="fas fa-file-invoice-dollar text-xl" style="color: rgba(10,132,255,0.9);"></i>
                </div>
            </div>
            <a href="{{ route('admin.permit-applications.index', ['status' => 'under_review']) }}" 
               class="text-xs mt-4 inline-flex items-center" style="color: rgba(10,132,255,0.9);">
                Buat quotation <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        {{-- Pending Payments --}}
        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(191,90,242,0.8);">
                        Perlu Verifikasi
                    </p>
                    <h3 class="text-3xl font-bold text-white mb-1">{{ $pendingPayments }}</h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pembayaran pending</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(191,90,242,0.15);">
                    <i class="fas fa-money-check-alt text-xl" style="color: rgba(191,90,242,0.9);"></i>
                </div>
            </div>
            <a href="{{ route('admin.payments.index', ['status' => 'processing']) }}" 
               class="text-xs mt-4 inline-flex items-center" style="color: rgba(191,90,242,0.9);">
                Verifikasi sekarang <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        {{-- Revenue This Month --}}
        <div class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(52,199,89,0.8);">
                        Revenue Bulan Ini
                    </p>
                    <h3 class="text-2xl font-bold text-white mb-1">
                        Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $applicationsThisMonth }} aplikasi</p>
                </div>
                <div class="w-12 h-12 rounded-apple flex items-center justify-center" 
                     style="background: rgba(52,199,89,0.15);">
                    <i class="fas fa-chart-line text-xl" style="color: rgba(52,199,89,0.9);"></i>
                </div>
            </div>
            <p class="text-xs mt-4" style="color: rgba(235,235,245,0.6);">
                Total: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Applications by Status Chart --}}
        <div class="lg:col-span-2 card-elevated rounded-apple-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-chart-pie mr-2" style="color: rgba(10,132,255,0.9);"></i>
                Distribusi Status Aplikasi
            </h2>
            <div class="relative" style="height: 300px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="card-elevated rounded-apple-xl p-6">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-tachometer-alt mr-2" style="color: rgba(52,199,89,0.9);"></i>
                Quick Stats
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: rgba(235,235,245,0.75);">Total Aplikasi</span>
                    <span class="text-lg font-bold text-white">{{ $totalApplications }}</span>
                </div>
                <div class="h-px" style="background: rgba(235,235,245,0.1);"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: rgba(235,235,245,0.75);">Bulan Ini</span>
                    <span class="text-lg font-bold text-white">{{ $applicationsThisMonth }}</span>
                </div>
                <div class="h-px" style="background: rgba(235,235,245,0.1);"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: rgba(235,235,245,0.75);">Active Projects</span>
                    <span class="text-lg font-bold text-white">{{ $activeProjects }}</span>
                </div>
                <div class="h-px" style="background: rgba(235,235,245,0.1);"></div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm" style="color: rgba(235,235,245,0.75);">Conversion Rate</span>
                    <span class="text-lg font-bold" style="color: rgba(52,199,89,0.9);">
                        {{ $totalApplications > 0 ? number_format(($activeProjects / $totalApplications) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card-elevated rounded-apple-xl p-6">
        <h2 class="text-xl font-bold text-white mb-6">
            <i class="fas fa-history mr-2" style="color: rgba(255,149,0,0.9);"></i>
            Recent Activity
        </h2>
        
        @if($recentActivity->count() > 0)
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                    <div class="flex items-start gap-4 p-4 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <div class="w-10 h-10 rounded-apple flex items-center justify-center flex-shrink-0" 
                             style="background: rgba(10,132,255,0.15);">
                            <i class="fas fa-exchange-alt text-sm" style="color: rgba(10,132,255,0.9);"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white mb-1">
                                <strong>{{ $activity->application->application_number }}</strong> - 
                                {{ $activity->application->permitType->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                Status changed from 
                                <span class="px-2 py-0.5 rounded" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,0.9);">
                                    {{ ucfirst(str_replace('_', ' ', $activity->from_status)) }}
                                </span>
                                to 
                                <span class="px-2 py-0.5 rounded" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,0.9);">
                                    {{ ucfirst(str_replace('_', ' ', $activity->to_status)) }}
                                </span>
                            </p>
                            @if($activity->notes)
                                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">
                                    {{ $activity->notes }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                            <a href="{{ route('admin.permit-applications.show', $activity->application_id) }}" 
                               class="text-xs mt-1 inline-flex items-center" style="color: rgba(10,132,255,0.9);">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-8" style="color: rgba(235,235,245,0.5);">
                Belum ada aktivitas
            </p>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('statusChart');
    
    const statusData = @json($applicationsByStatus);
    
    const statusLabels = {
        'draft': 'Draft',
        'submitted': 'Submitted',
        'under_review': 'Under Review',
        'quoted': 'Quoted',
        'quotation_accepted': 'Quotation Accepted',
        'payment_pending': 'Payment Pending',
        'payment_verified': 'Payment Verified',
        'converted_to_project': 'Converted to Project',
        'rejected': 'Rejected',
        'cancelled': 'Cancelled'
    };
    
    const statusColors = {
        'draft': 'rgba(142,142,147,0.8)',
        'submitted': 'rgba(255,149,0,0.8)',
        'under_review': 'rgba(10,132,255,0.8)',
        'quoted': 'rgba(191,90,242,0.8)',
        'quotation_accepted': 'rgba(48,209,88,0.8)',
        'payment_pending': 'rgba(255,204,0,0.8)',
        'payment_verified': 'rgba(50,215,75,0.8)',
        'converted_to_project': 'rgba(52,199,89,0.9)',
        'rejected': 'rgba(255,59,48,0.8)',
        'cancelled': 'rgba(142,142,147,0.6)'
    };
    
    const labels = Object.keys(statusData).map(key => statusLabels[key] || key);
    const data = Object.values(statusData);
    const backgroundColors = Object.keys(statusData).map(key => statusColors[key] || 'rgba(142,142,147,0.5)');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderWidth: 2,
                borderColor: 'rgba(0,0,0,0.3)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: 'rgba(235,235,245,0.85)',
                        font: {
                            size: 11,
                            family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto'
                        },
                        padding: 15,
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(235,235,245,0.2)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
