@extends('layouts.app')

@section('title', 'Dashboard Beta Tester')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Hero Header -->
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full transform translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-apple-green opacity-20 blur-3xl rounded-full transform -translate-x-1/3 translate-y-1/3"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] mb-2" style="color: rgba(235,235,245,0.6);">
                        Beta Tester Management
                    </p>
                    <h1 class="text-3xl font-bold mb-2" style="color: white;">
                        <i class="fas fa-users-cog mr-2"></i>
                        Dashboard Beta Tester
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.8);">
                        Monitor dan kelola program beta testing
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.beta-tester.index') }}" class="btn-primary-sm">
                        <i class="fas fa-list mr-2"></i>
                        Lihat Semua Beta Tester
                    </a>
                    <a href="{{ route('admin.beta-tester.export') }}" class="btn-secondary-sm">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Beta Testers -->
        <div class="card-elevated rounded-apple-lg p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(10,132,255,1) 0%, rgba(37,99,235,1) 100%);">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                    Total
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['total'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                Total Beta Tester
            </p>
        </div>

        <!-- Pending Documents -->
        <div class="card-elevated rounded-apple-lg p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(255,214,10,1) 0%, rgba(217,119,6,1) 100%);">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">
                    Pending
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['pending_documents'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                Menunggu Tanda Tangan
            </p>
        </div>

        <!-- Active Testers -->
        <div class="card-elevated rounded-apple-lg p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(48,209,88,1) 0%, rgba(5,150,105,1) 100%);">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                    Active
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['active'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                Beta Tester Aktif
            </p>
        </div>

        <!-- Completed -->
        <div class="card-elevated rounded-apple-lg p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(175,82,222,1) 0%, rgba(124,58,237,1) 100%);">
                    <i class="fas fa-flag-checkered text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">
                    Done
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['completed'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                Selesai
            </p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Registration Trend Chart -->
        <section class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold" style="color: white;">
                    <i class="fas fa-chart-line mr-2" style="color: rgba(10,132,255,1);"></i>
                    Tren Pendaftaran
                </h3>
                <span class="text-xs uppercase tracking-wider px-3 py-1 rounded-apple" 
                      style="color: rgba(235,235,245,0.6); background: rgba(255,255,255,0.05);">
                    30 Hari Terakhir
                </span>
            </div>
            <div style="position: relative; height: 250px;">
                <canvas id="registrationTrendChart"></canvas>
            </div>
        </section>

        <!-- Document Status -->
        <section class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold" style="color: white;">
                    <i class="fas fa-file-signature mr-2" style="color: rgba(48,209,88,1);"></i>
                    Status Dokumen
                </h3>
            </div>
            
            <!-- Progress Bars -->
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold" style="color: white;">
                            <i class="fas fa-check-double mr-2" style="color: rgba(48,209,88,1);"></i>
                            Semua Dokumen Signed
                        </span>
                        <span class="text-sm font-bold" style="color: rgba(48,209,88,1);">
                            {{ $documentStats['signed_both'] }}
                        </span>
                    </div>
                    <div class="w-full rounded-apple h-3 overflow-hidden" style="background: rgba(255,255,255,0.1);">
                        <div class="h-3 rounded-apple transition-all duration-500"
                             style="width: {{ $stats['total'] > 0 ? ($documentStats['signed_both'] / $stats['total'] * 100) : 0 }}%; 
                                    background: linear-gradient(90deg, rgba(48,209,88,1) 0%, rgba(5,150,105,1) 100%);">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold" style="color: white;">
                            <i class="fas fa-check mr-2" style="color: rgba(10,132,255,1);"></i>
                            Sebagian Signed
                        </span>
                        <span class="text-sm font-bold" style="color: rgba(10,132,255,1);">
                            {{ $documentStats['signed_partial'] }}
                        </span>
                    </div>
                    <div class="w-full rounded-apple h-3 overflow-hidden" style="background: rgba(255,255,255,0.1);">
                        <div class="h-3 rounded-apple transition-all duration-500"
                             style="width: {{ $stats['total'] > 0 ? ($documentStats['signed_partial'] / $stats['total'] * 100) : 0 }}%; 
                                    background: linear-gradient(90deg, rgba(10,132,255,1) 0%, rgba(37,99,235,1) 100%);">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold" style="color: white;">
                            <i class="fas fa-times mr-2" style="color: rgba(255,214,10,1);"></i>
                            Belum Signed
                        </span>
                        <span class="text-sm font-bold" style="color: rgba(255,214,10,1);">
                            {{ $documentStats['unsigned'] }}
                        </span>
                    </div>
                    <div class="w-full rounded-apple h-3 overflow-hidden" style="background: rgba(255,255,255,0.1);">
                        <div class="h-3 rounded-apple transition-all duration-500"
                             style="width: {{ $stats['total'] > 0 ? ($documentStats['unsigned'] / $stats['total'] * 100) : 0 }}%; 
                                    background: linear-gradient(90deg, rgba(255,214,10,1) 0%, rgba(217,119,6,1) 100%);">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Registrations -->
        <section class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold" style="color: white;">
                    <i class="fas fa-user-plus mr-2" style="color: rgba(10,132,255,1);"></i>
                    Pendaftaran Terbaru
                </h3>
                <a href="{{ route('admin.beta-tester.index') }}" 
                   class="text-sm font-semibold hover:underline transition-colors"
                   style="color: rgba(10,132,255,1);">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                            <th class="text-left py-3 px-2 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Nama
                            </th>
                            <th class="text-left py-3 px-2 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Universitas
                            </th>
                            <th class="text-left py-3 px-2 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Status
                            </th>
                            <th class="text-right py-3 px-2 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRegistrations as $tester)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);" class="hover:bg-white hover:bg-opacity-5 transition-colors">
                            <td class="py-3 px-2">
                                <div>
                                    <p class="font-semibold text-sm" style="color: white;">
                                        {{ $tester->full_name }}
                                    </p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                        {{ $tester->registration_number }}
                                    </p>
                                </div>
                            </td>
                            <td class="py-3 px-2">
                                <p class="text-sm" style="color: rgba(235,235,245,0.8);">
                                    {{ Str::limit($tester->university, 20) }}
                                </p>
                            </td>
                            <td class="py-3 px-2">
                                @php
                                    $statusConfig = [
                                        'registered' => ['color' => 'rgba(235,235,245,1)', 'bg' => 'rgba(235,235,245,0.15)', 'label' => 'Terdaftar', 'icon' => 'user-plus'],
                                        'documents_pending' => ['color' => 'rgba(255,214,10,1)', 'bg' => 'rgba(255,214,10,0.15)', 'label' => 'Pending', 'icon' => 'clock'],
                                        'documents_signed' => ['color' => 'rgba(10,132,255,1)', 'bg' => 'rgba(10,132,255,0.15)', 'label' => 'Signed', 'icon' => 'check'],
                                        'active' => ['color' => 'rgba(48,209,88,1)', 'bg' => 'rgba(48,209,88,0.15)', 'label' => 'Aktif', 'icon' => 'check-circle'],
                                        'completed' => ['color' => 'rgba(175,82,222,1)', 'bg' => 'rgba(175,82,222,0.15)', 'label' => 'Selesai', 'icon' => 'flag-checkered'],
                                        'rejected' => ['color' => 'rgba(255,69,58,1)', 'bg' => 'rgba(255,69,58,0.15)', 'label' => 'Ditolak', 'icon' => 'times-circle'],
                                    ];
                                    $config = $statusConfig[$tester->status] ?? $statusConfig['registered'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-apple text-xs font-semibold"
                                      style="background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-right">
                                <a href="{{ route('admin.beta-tester.show', $tester) }}"
                                   class="text-sm font-semibold hover:underline transition-colors"
                                   style="color: rgba(10,132,255,1);">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center" style="color: rgba(235,235,245,0.6);">
                                <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                                <p>Belum ada pendaftaran</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- University Distribution -->
        <section class="card-elevated rounded-apple-lg p-5">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold" style="color: white;">
                    <i class="fas fa-university mr-2" style="color: rgba(48,209,88,1);"></i>
                    Distribusi Universitas
                </h3>
                <span class="text-xs uppercase tracking-wider px-3 py-1 rounded-apple" 
                      style="color: rgba(235,235,245,0.6); background: rgba(255,255,255,0.05);">
                    Top 10
                </span>
            </div>

            <div class="space-y-3">
                @forelse($universityStats as $index => $uni)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <span class="flex-shrink-0 w-6 h-6 rounded-apple flex items-center justify-center text-xs font-bold text-white"
                                  style="background: linear-gradient(135deg, {{ $index % 2 == 0 ? 'rgba(10,132,255,1)' : 'rgba(48,209,88,1)' }} 0%, {{ $index % 2 == 0 ? 'rgba(37,99,235,1)' : 'rgba(5,150,105,1)' }} 100%);">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-medium truncate" style="color: white;">
                                {{ $uni->university }}
                            </span>
                        </div>
                        <span class="text-sm font-bold ml-3" style="color: rgba(10,132,255,1);">
                            {{ $uni->count }}
                        </span>
                    </div>
                    <div class="w-full rounded-apple h-2 overflow-hidden" style="background: rgba(255,255,255,0.1);">
                        <div class="h-2 rounded-apple transition-all duration-500"
                             style="width: {{ $stats['total'] > 0 ? ($uni->count / $stats['total'] * 100) : 0 }}%; 
                                    background: linear-gradient(90deg, {{ $index % 2 == 0 ? 'rgba(10,132,255,1)' : 'rgba(48,209,88,1)' }} 0%, {{ $index % 2 == 0 ? 'rgba(37,99,235,1)' : 'rgba(5,150,105,1)' }} 100%);">
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                    <i class="fas fa-chart-bar text-4xl mb-3 opacity-50"></i>
                    <p>Belum ada data</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Registration Trend Chart
    const ctx = document.getElementById('registrationTrendChart');
    if (ctx) {
        const data = @json($registrationTrend);
        
        // Fill missing days with 0 count for better visualization
        const filledData = [];
        const today = new Date();
        for (let i = 29; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            const dateString = date.toISOString().split('T')[0];
            
            const existingData = data.find(d => d.date === dateString);
            filledData.push({
                date: dateString,
                count: existingData ? existingData.count : 0
            });
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: filledData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Pendaftaran',
                    data: filledData.map(d => d.count),
                    borderColor: 'rgba(10,132,255,1)',
                    backgroundColor: 'rgba(10,132,255,0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(10,132,255,1)',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(10,132,255,1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return context.parsed.y + ' pendaftaran';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: 'rgba(235,235,245,0.6)',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(235,235,245,0.6)',
                            font: {
                                size: 12
                            },
                            maxTicksLimit: 8
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
