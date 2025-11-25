<div class="card-apple">
    {{-- Header with Search and Actions --}}
    <div class="p-4 md:p-5 border-b" style="border-color: var(--dark-separator);">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4">
            <h2 class="text-lg font-semibold text-dark-text-primary">Akun Kas</h2>
            
            <div class="flex flex-col sm:flex-row gap-2.5 md:gap-3">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.master-data.index') }}" class="flex-1 sm:w-64">
                    <input type="hidden" name="tab" value="cash-accounts">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari akun kas..."
                               class="input-apple">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                {{-- Filter by Status --}}
                <form method="GET" action="{{ route('admin.master-data.index') }}">
                    <input type="hidden" name="tab" value="cash-accounts">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="status" 
                            onchange="this.form.submit()"
                            class="input-apple">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </form>

                {{-- Add Button --}}
                <a href="{{ route('cash-accounts.create') }}" 
                   class="btn-apple-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Akun Kas
                </a>
            </div>
        </div>
    </div>

    {{-- Cash Accounts List --}}
    <div class="divide-y" style="border-color: var(--dark-separator);">
        @forelse($cashAccounts as $account)
            <div class="p-4 md:p-5 hover-apple">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-dark-text-primary">
                                {{ $account->bank_name }}
                            </h3>
                            @if($account->is_active)
                                <span class="badge-apple-green">
                                    Aktif
                                </span>
                            @else
                                <span class="badge-apple-gray">
                                    Nonaktif
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 text-sm">
                            <div class="flex items-center text-dark-text-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                                <span>{{ $account->account_number }}</span>
                            </div>

                            <div class="flex items-center text-dark-text-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>{{ $account->account_holder }}</span>
                            </div>

                            <div class="flex items-center font-semibold text-dark-text-primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Rp {{ number_format($account->current_balance, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($account->description)
                            <p class="mt-3 text-sm text-dark-text-secondary">
                                {{ $account->description }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('cash-accounts.edit', $account) }}" 
                           class="btn-icon-apple text-apple-blue">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        
                        <form action="{{ route('cash-accounts.destroy', $account) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus akun kas ini?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-icon-apple text-apple-red">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-dark-text-primary mb-2">Belum ada akun kas</h3>
                <p class="text-dark-text-secondary mb-6">Mulai dengan menambahkan akun kas pertama Anda</p>
                <a href="{{ route('cash-accounts.create') }}" 
                   class="btn-apple-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Akun Kas
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($cashAccounts->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--dark-separator);">
            {{ $cashAccounts->links() }}
        </div>
    @endif
</div>
