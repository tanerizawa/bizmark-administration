{{-- Rekening Bank & Kas (Enhanced Accounts Table) --}}
<div>
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h3 class="text-base font-semibold text-white">
                <i class="fas fa-university mr-2 text-dark-text-tertiary/40"></i>
                Daftar Rekening Bank & Kas
            </h3>
            <p class="text-xs mt-0.5 text-dark-text-tertiary/80">
                Kelola semua akun kas dan bank perusahaan
            </p>
        </div>
        <a href="{{ route('cash-accounts.create') }}"
           class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-all duration-300 bg-apple-blue text-white">
            <i class="fas fa-plus text-xs mr-1.5"></i>
            Tambah Akun
        </a>
    </div>

    <div class="overflow-x-auto rounded-apple bg-white/[0.02]">
        <table class="min-w-full divide-y divide-white/5">
            <thead>
                <tr class="bg-white/[0.02]">
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Nama Akun
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Tipe
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Bank / No. Rekening
                    </th>
                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Saldo Awal
                    </th>
                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Saldo Saat Ini
                    </th>
                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Status
                    </th>
                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider text-dark-text-tertiary/80">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($accounts as $account)
                <tr class="hover:bg-white/[0.02] transition-colors duration-200">
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-white/5">
                                <i class="fas {{ $account->account_type === 'bank' ? 'fa-building-columns' : 'fa-wallet' }} text-xs text-dark-text-tertiary/40"></i>
                            </div>
                            <div class="ml-2.5">
                                <div class="text-xs font-medium text-white">
                                    {{ $account->account_name }}
                                </div>
                                @if($account->description)
                                <div class="text-xs text-dark-text-tertiary/60">
                                    {{ Str::limit($account->description, 35) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        @php
                            $typeClasses = [
                                'bank' => 'bg-white/10 text-dark-text-primary/70',
                                'cash' => 'bg-white/10 text-dark-text-primary/70',
                                'receivable' => 'bg-apple-orange/10 text-apple-orange/80',
                                'payable' => 'bg-apple-red/10 text-apple-red/80'
                            ];
                            $typeLabels = [
                                'bank' => 'Bank',
                                'cash' => 'Kas',
                                'receivable' => 'Piutang',
                                'payable' => 'Hutang'
                            ];
                            $typeClass = $typeClasses[$account->account_type] ?? $typeClasses['cash'];
                            $typeLabel = $typeLabels[$account->account_type] ?? $typeLabels['cash'];
                        @endphp
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple {{ $typeClass }}">
                            {{ $typeLabel }}
                        </span>
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="text-xs text-dark-text-primary/70">
                            @if($account->bank_name)
                                <div class="font-medium">{{ $account->bank_name }}</div>
                            @endif
                            @if($account->account_number)
                                <div class="text-xs text-dark-text-tertiary/60">
                                    {{ $account->account_number }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-right">
                        <div class="text-xs font-medium text-dark-text-secondary">
                            Rp {{ number_format($account->initial_balance) }}
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-right">
                        <div class="text-sm font-bold {{ $account->current_balance >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                            Rp {{ number_format($account->current_balance) }}
                        </div>
                        @php
                            $diff = $account->current_balance - $account->initial_balance;
                        @endphp
                        @if($diff != 0)
                        <div class="text-xs mt-0.5 {{ $diff >= 0 ? 'text-apple-green/70' : 'text-apple-red/70' }}">
                            {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-center">
                        @if($account->is_active)
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple bg-apple-green/10 text-apple-green/90">
                                <i class="fas fa-check-circle text-xs mr-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple bg-apple-red/10 text-apple-red/90">
                                <i class="fas fa-times-circle text-xs mr-1"></i>
                                Non-aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center space-x-1.5">
                            <a href="{{ route('cash-accounts.show', $account->id) }}"
                               class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300 bg-white/5 text-dark-text-secondary hover:bg-white/10 hover:text-apple-blue"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('cash-accounts.edit', $account->id) }}"
                               class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300 bg-white/5 text-dark-text-secondary hover:bg-white/10 hover:text-apple-orange"
                               title="Edit">
                                <i class="fas fa-pencil text-xs"></i>
                            </a>
                            <form action="{{ route('cash-accounts.destroy', $account->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300 bg-white/5 text-dark-text-secondary hover:bg-apple-red/15 hover:text-apple-red"
                                        title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-3 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-3xl mb-2 text-dark-text-tertiary/25"></i>
                            <p class="text-sm font-medium text-dark-text-tertiary/80">
                                Belum ada akun kas atau bank
                            </p>
                            <p class="text-xs mt-0.5 text-dark-text-tertiary/60">
                                Klik tombol "Tambah Akun" untuk membuat akun baru
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($accounts->count() > 0)
    <div class="mt-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-3 rounded-apple bg-white/[0.02]">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-white/5">
                        <i class="fas fa-building-columns text-xs text-dark-text-tertiary/40"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs text-dark-text-tertiary/80">Total Rekening Bank</p>
                        <p class="text-lg font-bold text-white">
                            {{ $accounts->where('account_type', 'bank')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple bg-white/[0.02]">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-white/5">
                        <i class="fas fa-wallet text-xs text-dark-text-tertiary/40"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs text-dark-text-tertiary/80">Total Kas Tunai</p>
                        <p class="text-lg font-bold text-white">
                            {{ $accounts->where('account_type', 'cash')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple bg-white/[0.02]">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-white/5">
                        <i class="fas fa-check-circle text-xs text-dark-text-tertiary/40"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs text-dark-text-tertiary/80">Akun Aktif</p>
                        <p class="text-lg font-bold text-white">
                            {{ $accounts->where('is_active', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
