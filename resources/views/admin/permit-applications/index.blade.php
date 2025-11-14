@extends('layouts.app')

@section('title', 'Manajemen Permohonan Izin')
@section('page-title', 'Manajemen Permohonan Izin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">📋 Manajemen Permohonan Izin</h1>
        <p class="text-sm" style="color: rgba(235,235,245,0.75);">Review dan kelola permohonan izin dari client</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Total</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</h3>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(10,132,255,0.8);">Submitted</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['submitted'] ?? 0 }}</h3>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(255,204,0,0.8);">Under Review</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['under_review'] ?? 0 }}</h3>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(175,82,222,0.8);">Quoted</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['quoted'] ?? 0 }}</h3>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(10,132,255,0.8);">In Progress</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['in_progress'] ?? 0 }}</h3>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(52,199,89,0.8);">Completed</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['completed'] ?? 0 }}</h3>
        </div>
    </div>

    @if(session('success'))
        <div class="card-elevated rounded-apple-lg p-4" style="background: rgba(52,199,89,0.15); border: 1px solid rgba(52,199,89,0.3);">
            <p class="text-sm" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="card-elevated rounded-apple-lg p-4" style="background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3);">
            <p class="text-sm" style="color: rgba(255,59,48,1);">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card-elevated rounded-apple-xl p-5 md:p-6">
        <h2 class="text-lg font-semibold text-white mb-4">🔍 Filter Aplikasi</h2>
        <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="No. aplikasi atau nama client"
                       class="w-full px-4 py-2.5 rounded-apple text-sm text-white placeholder-gray-500"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                    <option value="payment_pending" {{ request('status') == 'payment_pending' ? 'selected' : '' }}>Payment Pending</option>
                    <option value="payment_verified" {{ request('status') == 'payment_verified' ? 'selected' : '' }}>Payment Verified</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="documents_ready" {{ request('status') == 'documents_ready' ? 'selected' : '' }}>Documents Ready</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Tipe Bisnis</label>
                <select name="business_type" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Tipe</option>
                    <option value="perorangan" {{ request('business_type') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                    <option value="cv" {{ request('business_type') == 'cv' ? 'selected' : '' }}>CV</option>
                    <option value="pt" {{ request('business_type') == 'pt' ? 'selected' : '' }}>PT</option>
                    <option value="yayasan" {{ request('business_type') == 'yayasan' ? 'selected' : '' }}>Yayasan</option>
                    <option value="koperasi" {{ request('business_type') == 'koperasi' ? 'selected' : '' }}>Koperasi</option>
                    <option value="lainnya" {{ request('business_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Client</label>
                <select name="client_id" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.6);">Tanggal Submit</label>
                <input type="date" name="submitted_date" value="{{ request('submitted_date') }}"
                       class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
            </div>
            <div class="md:col-span-2 lg:col-span-5 flex gap-3">
                <button type="submit" class="btn-primary-sm">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.permit-applications.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Applications Table --}}
    <div class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background: rgba(28,28,30,0.5); border-bottom: 1px solid rgba(84,84,88,0.35);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">No. Aplikasi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Client</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Jenis Izin</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Harga</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody style="border-top: 1px solid rgba(84,84,88,0.35);">
                    @forelse($applications as $app)
                        <tr style="border-bottom: 1px solid rgba(84,84,88,0.35);" class="hover:bg-opacity-50 transition">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-white">{{ $app->application_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-white">{{ $app->client->name }}</div>
                                <div class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $app->client->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-layer-group mt-1" style="color: rgba(175,82,222,0.9);"></i>
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $app->permitPackage->package_name }}</div>
                                        <div class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                                            {{ $app->permitPackage->permits->count() }} izin
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'draft' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)'],
                                        'submitted' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)', 'border' => 'rgba(10,132,255,0.3)'],
                                        'under_review' => ['bg' => 'rgba(255,204,0,0.15)', 'text' => 'rgba(255,204,0,1)', 'border' => 'rgba(255,204,0,0.3)'],
                                        'quoted' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)', 'border' => 'rgba(175,82,222,0.3)'],
                                        'payment_pending' => ['bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)', 'border' => 'rgba(255,149,0,0.3)'],
                                        'payment_verified' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)'],
                                        'in_progress' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)', 'border' => 'rgba(10,132,255,0.3)'],
                                        'documents_ready' => ['bg' => 'rgba(90,200,250,0.15)', 'text' => 'rgba(90,200,250,1)', 'border' => 'rgba(90,200,250,0.3)'],
                                        'completed' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)'],
                                        'rejected' => ['bg' => 'rgba(255,59,48,0.15)', 'text' => 'rgba(255,59,48,1)', 'border' => 'rgba(255,59,48,0.3)'],
                                        'cancelled' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)'],
                                    ];
                                    $color = $statusColors[$app->status] ?? $statusColors['draft'];
                                @endphp
                                <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-apple"
                                      style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border: 1px solid {{ $color['border'] }};">
                                    {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm" style="color: rgba(235,235,245,0.75);">
                                    {{ $app->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($app->quotation)
                                    <span class="text-sm font-semibold text-white">
                                        Rp {{ number_format($app->quotation->total_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color: rgba(235,235,245,0.6);">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.permit-applications.show', $app->id) }}" 
                                   class="text-sm" style="color: rgba(10,132,255,0.9);">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-folder-open text-4xl mb-3" style="color: rgba(235,235,245,0.3);"></i>
                                <p class="text-sm" style="color: rgba(235,235,245,0.6);">Tidak ada aplikasi ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid rgba(84,84,88,0.35);">
                {{ $applications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
