@extends('layouts.app')

@section('title', 'Rekonsiliasi Bank')
@section('page-title', 'Rekonsiliasi Bank')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pengelolaan Keuangan</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Rekonsiliasi Bank
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Rekonsiliasi laporan bank dengan transaksi sistem untuk memastikan keakuratan dan keseimbangan data keuangan.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('reconciliations.create') }}" 
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold" 
                       style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        <i class="fas fa-plus mr-2"></i>
                        Mulai Rekonsiliasi Baru
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <!-- Total Reconciliations -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Rekonsiliasi</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $reconciliations->total() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Seluruh periode</p>
                </div>

                <!-- Completed -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Selesai</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $reconciliations->where('status', 'completed')->count() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Rekonsiliasi tuntas</p>
                </div>

                <!-- In Progress -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Sedang Proses</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">{{ $reconciliations->where('status', 'in_progress')->count() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu diselesaikan</p>
                </div>

                <!-- Balanced -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Seimbang</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $reconciliations->filter(fn($r) => $r->difference == 0)->count() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Selisih = 0</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <div class="card-elevated rounded-apple-lg mb-5 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.45);">
            <h3 class="text-sm font-semibold uppercase tracking-wide" style="color: rgba(235, 235, 245, 0.8);">Pencarian & Filter</h3>
        </div>
        <div class="p-4 md:p-5">
            <form method="GET" action="{{ route('reconciliations.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Cash Account Filter -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Akun Kas</label>
                        <select name="cash_account_id" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Akun</option>
                            @foreach($cashAccounts as $account)
                                <option value="{{ $account->id }}" {{ request('cash_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Status</label>
                        <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Status</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Ditinjau</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Sampai Tanggal</label>
                        <div class="flex space-x-2">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-dark flex-1 px-3 py-2 rounded-apple text-sm">
                            <button type="submit" class="btn-secondary-sm px-4">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('reconciliations.index') }}" class="btn-secondary-sm px-4">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reconciliations Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead style="background-color: rgba(28,28,30,0.45);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Akun Kas</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Periode</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Saldo Buku</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Saldo Bank</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Selisih</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                @forelse($reconciliations as $reconciliation)
                <tr class="hover-lift transition-apple" style="border-bottom: 1px solid rgba(235, 235, 245, 0.05);">
                    <td class="px-4 py-2.5 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $reconciliation->reconciliation_date->locale('id')->isoFormat('D MMM Y') }}
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="font-semibold text-sm text-dark-text-primary">{{ $reconciliation->cashAccount->account_name }}</div>
                        <div class="text-xs text-dark-text-secondary mt-1">{{ $reconciliation->cashAccount->bank_name }}</div>
                    </td>
                    <td class="px-4 py-2.5 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $reconciliation->start_date->locale('id')->isoFormat('D MMM') }} - {{ $reconciliation->end_date->locale('id')->isoFormat('D MMM Y') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-medium" style="color: rgba(235, 235, 245, 0.9);">
                        Rp {{ number_format($reconciliation->closing_balance_book, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-medium" style="color: rgba(235, 235, 245, 0.9);">
                        Rp {{ number_format($reconciliation->closing_balance_bank, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-bold {{ $reconciliation->difference == 0 ? '' : '' }}" style="color: {{ $reconciliation->difference == 0 ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)' }};">
                        Rp {{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                        @if($reconciliation->status === 'in_progress')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(255,159,10,0.15); color: rgba(255,159,10,1);">
                                <i class="fas fa-hourglass-half mr-1"></i> Proses
                            </span>
                        @elseif($reconciliation->status === 'completed')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        @elseif($reconciliation->status === 'reviewed')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                <i class="fas fa-eye mr-1"></i> Ditinjau
                            </span>
                        @elseif($reconciliation->status === 'approved')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                <i class="fas fa-check-double mr-1"></i> Disetujui
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-1.5">
                            @if($reconciliation->status === 'in_progress')
                                <a href="{{ route('reconciliations.match', $reconciliation) }}" 
                                   class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                   style="background-color: rgba(10,132,255,0.15); color: var(--apple-blue); border: 1px solid rgba(10,132,255,0.25);" 
                                   title="Lanjutkan Pencocokan">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                            @else
                                <a href="{{ route('reconciliations.show', $reconciliation) }}" 
                                   class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                   style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.25);" 
                                   title="Lihat Laporan">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            @endif
                            @if($reconciliation->status === 'in_progress')
                                <form action="{{ route('reconciliations.destroy', $reconciliation) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus rekonsiliasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                            style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red); border: 1px solid rgba(255, 59, 48, 0.25);" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-6xl mb-6" style="color: rgba(235, 235, 245, 0.3);"></i>
                            <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Rekonsiliasi Bank</h3>
                            <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">Mulai dengan membuat rekonsiliasi pertama Anda</p>
                            <a href="{{ route('reconciliations.create') }}" 
                               class="btn-primary inline-flex items-center px-6 py-3 rounded-apple font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Mulai Rekonsiliasi Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($reconciliations->hasPages())
    <div class="rounded-apple-lg px-4 py-3 mt-4" style="background-color: #2C2C2E; border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);">
        {{ $reconciliations->links('pagination::tailwind') }}
    </div>
    @endif

@if(session('success'))
<div class="alert alert-success flex items-center gap-3 mb-5">
    <i class="fas fa-check-circle text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger flex items-center gap-3 mb-5">
    <i class="fas fa-exclamation-circle text-lg"></i>
    <span>{{ session('error') }}</span>
</div>
@endif
@endsection
