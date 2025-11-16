@extends('layouts.app')

@section('title', 'Dashboard Perizinan')
@section('page-title', 'Dashboard Perizinan')

@section('content')
@php
    $statusColors = [
        'draft' => 'bg-slate-100 text-slate-700',
        'submitted' => 'bg-blue-100 text-blue-700',
        'under_review' => 'bg-amber-100 text-amber-700',
        'document_incomplete' => 'bg-red-100 text-red-700',
        'quoted' => 'bg-purple-100 text-purple-700',
        'quotation_accepted' => 'bg-emerald-100 text-emerald-700',
        'payment_pending' => 'bg-amber-100 text-amber-700',
        'payment_verified' => 'bg-emerald-100 text-emerald-700',
        'in_progress' => 'bg-sky-100 text-sky-700',
        'completed' => 'bg-green-100 text-green-700',
        'cancelled' => 'bg-gray-100 text-gray-600',
    ];

    $statusTotal = max(1, array_sum($applicationsByStatus->toArray() ?? []));
    $reviewRatio = $totalApplications > 0 ? round(($pendingApplications / $totalApplications) * 100) : 0;
    $quotationRatio = $totalApplications > 0 ? round(($needQuotation / $totalApplications) * 100) : 0;
    $conversionRate = $totalApplications > 0 ? round(($activeProjects / $totalApplications) * 100) : 0;
    $monthlyShare = $totalApplications > 0 ? round(($applicationsThisMonth / $totalApplications) * 100) : 0;
@endphp

<div class="max-w-7xl mx-auto space-y-10">
    {{-- Hero borrowed from mission-control dashboard --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Permit Mission Control</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Selaraskan tim izin, quotation, hingga revenue dengan gaya {{ config('app.name') }} Dashboard.
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Monitor alert penting, lengkapi dokumen, dan percepat konversi proyek tanpa membuka banyak tab.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-clock mr-2"></i>Last sync: {{ now()->format('d M Y, H:i') }}</p>
                    <p><i class="fas fa-user-shield mr-2"></i>Visibility: Ops &amp; Finance lead</p>
                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('admin.permit-applications.index') }}" class="btn-primary-sm">
                            <i class="fas fa-list mr-2"></i>Semua Permohonan
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="btn-secondary-sm">
                            <i class="fas fa-money-bill-wave mr-2"></i>Verifikasi Pembayaran
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.08);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.8);">Perlu Review</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color:#FFFFFF;">{{ $pendingApplications }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $reviewRatio }}% dari seluruh aplikasi</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Perlu Quotation</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(10,132,255,1);">{{ $needQuotation }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $quotationRatio }}% belum punya penawaran</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(191,90,242,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Pending Pembayaran</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color:#FFFFFF;">{{ $pendingPayments }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Butuh verifikasi finance</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Project Aktif</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $activeProjects }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Conversion rate {{ $conversionRate }}%</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Critical focus cards --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Prioritas 01</p>
                <h2 class="text-2xl font-semibold text-white">Critical Focus – Permit Ops</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Menggabungkan backlog review, kesiapan quotation, dan cash pulse supaya tim langsung tahu apa yang harus dilakukan.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.85);">
                {{ $totalApplications }} permohonan aktif
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Review Backlog</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(255,149,0,0.18); color: rgba(255,149,0,0.9);">{{ $reviewRatio }}%</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $pendingApplications }}</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Aplikasi menunggu verifikasi admin, prioritaskan yang sudah submitted &amp; under review.
                </p>
                <div class="flex items-center justify-between text-xs" style="color: rgba(235,235,245,0.6);">
                    <span>Last 30d intake</span>
                    <span>{{ $applicationsThisMonth }} ({{ $monthlyShare }}% share)</span>
                </div>
            </div>

            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Quotation Pipeline</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.18); color: rgba(10,132,255,0.9);">Ops</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $needQuotation }}</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Permohonan tanpa quotation. Percepat agar revenue tak tertahan.
                </p>
                <div class="flex items-center justify-between text-xs" style="color: rgba(235,235,245,0.6);">
                    <span>Potential conversion</span>
                    <span>{{ $conversionRate }}% rate saat ini</span>
                </div>
            </div>

            <div class="card-elevated rounded-apple-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Finance Watch</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(191,90,242,0.18); color: rgba(191,90,242,0.9);">Live</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $pendingPayments }}</p>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Pembayaran butuh verifikasi. Total revenue terkonfirmasi Rp {{ number_format($totalRevenue, 0, ',', '.') }}.
                </p>
                <div class="flex items-center justify-between text-xs" style="color: rgba(235,235,245,0.6);">
                    <span>Revenue bulan ini</span>
                    <span>Rp {{ number_format($revenueThisMonth ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Pipeline & Revenue --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Prioritas 02</p>
                <h2 class="text-2xl font-semibold text-white">Pipeline &amp; Revenue Pulse</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Distribusi status, kinerja pemasukan, serta progres intake bulan berjalan.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 md:gap-4">
            <div class="card-elevated rounded-apple-xl p-6 space-y-4 xl:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Distribusi Status</p>
                        <h3 class="text-xl font-semibold text-white">Rekap permohonan per status</h3>
                    </div>
                    <span class="text-xs" style="color: rgba(235,235,245,0.65);">Total {{ $totalApplications }} permohonan</span>
                </div>
                <div class="space-y-4">
                    @forelse($applicationsByStatus as $status => $count)
                        <div>
                            <div class="flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.8);">
                                <span>{{ ucfirst(str_replace('_',' ', $status)) }}</span>
                                <span class="font-semibold text-white">{{ $count }}</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-apple-blue to-apple-green" style="width: {{ min(100, ($count / $statusTotal) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada data status.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-3">
                <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Revenue Pulse</h3>
                        <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">Finance</span>
                    </div>
                    <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.75);">
                        <div class="flex items-center justify-between">
                            <span>Total revenue</span>
                            <span class="text-base font-semibold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Bulan ini</span>
                            <span class="text-base font-semibold text-white">Rp {{ number_format($revenueThisMonth ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Pending verification</span>
                            <span class="text-base font-semibold text-white">{{ $pendingPayments }} transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">Intake Snapshot</h3>
                        <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(52,199,89,0.18); color: rgba(52,199,89,0.9);">Ops</span>
                    </div>
                    <ul class="space-y-2 text-sm" style="color: rgba(235,235,245,0.75);">
                        <li class="flex items-center justify-between">
                            <span>Masuk bulan ini</span>
                            <span class="text-base font-semibold text-white">{{ $applicationsThisMonth }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Porsi dari total</span>
                            <span class="text-base font-semibold text-white">{{ $monthlyShare }}%</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Konversi proyek</span>
                            <span class="text-base font-semibold text-white">{{ $conversionRate }}%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Activity --}}
    <section class="card-elevated rounded-apple-xl p-6 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Aktivitas</p>
                <h2 class="text-xl font-semibold text-white">Ops feed &amp; status update</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Log perubahan status terbaru lengkap dengan catatan tim.</p>
            </div>
            <a href="{{ route('admin.permit-applications.index') }}" class="btn-secondary-sm">Lihat semua</a>
        </div>
        @if($recentActivity->count() > 0)
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 rounded-apple p-4" style="background: rgba(255,255,255,0.03);">
                        <div class="flex items-start gap-3">
                            <span class="w-10 h-10 rounded-apple flex items-center justify-center" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">
                                <i class="fas fa-exchange-alt text-sm"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $activity->application->application_number }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $activity->application->permitType->name ?? 'N/A' }}</p>
                                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.75);">
                                    <span class="px-2 py-0.5 rounded {{ $statusColors[$activity->from_status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst(str_replace('_',' ', $activity->from_status)) }}</span>
                                    <i class="fas fa-arrow-right mx-2"></i>
                                    <span class="px-2 py-0.5 rounded {{ $statusColors[$activity->to_status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst(str_replace('_',' ', $activity->to_status)) }}</span>
                                </p>
                                @if($activity->notes)
                                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">"{{ $activity->notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-right" style="color: rgba(235,235,245,0.55);">
                            <p>{{ optional($activity->created_at)->format('d M Y H:i') }}</p>
                            <p>{{ optional($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada aktivitas terbaru.</p>
        @endif
    </section>
</div>
@endsection
