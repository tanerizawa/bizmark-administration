<div class="card-apple">
    {{-- Header with Search and Filters --}}
    <div class="p-4 md:p-5 border-b" style="border-color: var(--dark-separator);">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
            <h2 class="text-lg font-semibold text-dark-text-primary">Rekonsiliasi Bank</h2>
            
            <div class="flex flex-col sm:flex-row gap-2.5 md:gap-3">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.master-data.index') }}" class="flex-1 sm:w-64">
                    <input type="hidden" name="tab" value="reconciliations">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('cash_account_id'))
                        <input type="hidden" name="cash_account_id" value="{{ request('cash_account_id') }}">
                    @endif
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari referensi atau catatan..."
                               class="input-apple text-sm">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                {{-- Filter by Status --}}
                <form method="GET" action="{{ route('admin.master-data.index') }}">
                    <input type="hidden" name="tab" value="reconciliations">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('cash_account_id'))
                        <input type="hidden" name="cash_account_id" value="{{ request('cash_account_id') }}">
                    @endif
                    <select name="status" 
                            onchange="this.form.submit()"
                            class="input-apple text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </form>

                {{-- Filter by Cash Account --}}
                @if($cashAccountsList->isNotEmpty())
                    <form method="GET" action="{{ route('admin.master-data.index') }}">
                        <input type="hidden" name="tab" value="reconciliations">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <select name="cash_account_id" 
                                onchange="this.form.submit()"
                                class="input-apple text-sm">
                            <option value="">Semua Akun</option>
                            @foreach($cashAccountsList as $account)
                                <option value="{{ $account->id }}" {{ request('cash_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Add Button --}}
                <a href="{{ route('reconciliations.create') }}" 
                   class="btn-apple-primary text-sm whitespace-nowrap">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Rekonsiliasi
                </a>
            </div>
        </div>
    </div>

    {{-- Reconciliations List --}}
    <div class="divide-y" style="border-color: var(--dark-separator);">
        @forelse($reconciliations as $reconciliation)
            <div class="p-6 hover-apple">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-dark-text-primary">
                                {{ $reconciliation->cashAccount->bank_name ?? 'Unknown Account' }}
                            </h3>
                            
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'badge-apple-orange', 'label' => 'Menunggu'],
                                    'completed' => ['class' => 'badge-apple-green', 'label' => 'Selesai'],
                                    'failed' => ['class' => 'badge-apple-red', 'label' => 'Gagal'],
                                ];
                                $status = $statusConfig[$reconciliation->status] ?? $statusConfig['pending'];
                            @endphp
                            
                            <span class="{{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="flex items-center text-dark-text-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $reconciliation->reconciliation_date ? $reconciliation->reconciliation_date->format('d M Y') : '-' }}</span>
                            </div>

                            <div class="flex items-center text-dark-text-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                <span>{{ $reconciliation->cashAccount->account_number ?? '-' }}</span>
                            </div>

                            <div class="flex items-center text-dark-text-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>{{ $reconciliation->status }}</span>
                            </div>
                        </div>

                        @if($reconciliation->notes)
                            <p class="mt-3 text-sm text-dark-text-secondary">
                                {{ $reconciliation->notes }}
                            </p>
                        @endif

                        @if($reconciliation->closing_balance_book || $reconciliation->closing_balance_bank)
                            <div class="mt-3 flex items-center gap-6 text-sm">
                                @if($reconciliation->closing_balance_book)
                                    <div>
                                        <span class="text-dark-text-tertiary">Saldo Buku:</span>
                                        <span class="font-semibold text-dark-text-primary ml-1">
                                            Rp {{ number_format($reconciliation->closing_balance_book, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                @if($reconciliation->closing_balance_bank)
                                    <div>
                                        <span class="text-dark-text-tertiary">Saldo Bank:</span>
                                        <span class="font-semibold text-dark-text-primary ml-1">
                                            Rp {{ number_format($reconciliation->closing_balance_bank, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                @if($reconciliation->difference)
                                    <div>
                                        <span class="text-dark-text-tertiary">Selisih:</span>
                                        <span class="font-semibold {{ $reconciliation->difference > 0 ? 'text-apple-green' : 'text-apple-red' }} ml-1">
                                            Rp {{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('reconciliations.show', $reconciliation) }}" 
                           class="btn-icon-apple"
                           title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>

                        <a href="{{ route('reconciliations.edit', $reconciliation) }}" 
                           class="btn-icon-apple text-apple-blue"
                           title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <form action="{{ route('reconciliations.destroy', $reconciliation) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus rekonsiliasi ini?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-icon-apple text-apple-red"
                                    title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="icon-circle-apple bg-gray-500 bg-opacity-10 w-16 h-16 mx-auto mb-4">
                    <svg class="w-8 h-8 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-dark-text-primary mb-2">Belum ada rekonsiliasi</h3>
                <p class="text-dark-text-secondary mb-6">
                    @if(request('search') || request('status') || request('cash_account_id'))
                        Tidak ada rekonsiliasi yang sesuai dengan filter Anda
                    @else
                        Mulai dengan menambahkan rekonsiliasi bank pertama
                    @endif
                </p>
                @if(!request('search') && !request('status') && !request('cash_account_id'))
                    <a href="{{ route('reconciliations.create') }}" 
                       class="btn-apple-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Rekonsiliasi
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reconciliations->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--dark-separator);">
            {{ $reconciliations->links() }}
        </div>
    @endif
</div>
