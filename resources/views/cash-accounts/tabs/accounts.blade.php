{{-- Rekening Bank & Kas (Enhanced Accounts Table) --}}
<div>
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">
                <i class="fas fa-university mr-2" style="color: rgba(235, 235, 245, 0.4);"></i>
                Daftar Rekening Bank & Kas
            </h3>
            <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
                Kelola semua akun kas dan bank perusahaan
            </p>
        </div>
        <a href="{{ route('cash-accounts.create') }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-all duration-300"
           style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
            <i class="fas fa-plus text-xs mr-1.5"></i>
            Tambah Akun
        </a>
    </div>

    <div class="overflow-x-auto rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
        <table class="min-w-full divide-y" style="border-color: rgba(255, 255, 255, 0.08);">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.02);">
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Nama Akun
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Tipe
                    </th>
                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Bank / No. Rekening
                    </th>
                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Saldo Awal
                    </th>
                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Saldo Saat Ini
                    </th>
                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Status
                    </th>
                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider" 
                        style="color: rgba(235, 235, 245, 0.5);">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: rgba(255, 255, 255, 0.05);">
                @forelse($accounts as $account)
                <tr class="hover:bg-opacity-50 transition-colors duration-200" 
                    style="background: transparent;"
                    onmouseover="this.style.background='rgba(255, 255, 255, 0.02)'" 
                    onmouseout="this.style.background='transparent'">
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full"
                                 style="background: rgba(255, 255, 255, 0.05);">
                                <i class="fas {{ $account->account_type === 'bank' ? 'fa-building-columns' : 'fa-wallet' }} text-xs" 
                                   style="color: rgba(235, 235, 245, 0.4);"></i>
                            </div>
                            <div class="ml-2.5">
                                <div class="text-xs font-medium" style="color: #FFFFFF;">
                                    {{ $account->account_name }}
                                </div>
                                @if($account->description)
                                <div class="text-xs" style="color: rgba(235, 235, 245, 0.4);">
                                    {{ Str::limit($account->description, 35) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap">
                        @php
                            $typeColors = [
                                'bank' => ['bg' => 'rgba(235, 235, 245, 0.1)', 'text' => 'rgba(235, 235, 245, 0.7)', 'label' => 'Bank'],
                                'cash' => ['bg' => 'rgba(235, 235, 245, 0.1)', 'text' => 'rgba(235, 235, 245, 0.7)', 'label' => 'Kas'],
                                'receivable' => ['bg' => 'rgba(255, 149, 0, 0.1)', 'text' => 'rgba(255, 149, 0, 0.8)', 'label' => 'Piutang'],
                                'payable' => ['bg' => 'rgba(255, 59, 48, 0.1)', 'text' => 'rgba(255, 59, 48, 0.8)', 'label' => 'Hutang']
                            ];
                            $typeStyle = $typeColors[$account->account_type] ?? $typeColors['cash'];
                        @endphp
                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple"
                              style="background: {{ $typeStyle['bg'] }}; color: {{ $typeStyle['text'] }};">
                            {{ $typeStyle['label'] }}
                        </span>
                    </td>
                    <td class="px-3 py-2.5">
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                            @if($account->bank_name)
                                <div class="font-medium">{{ $account->bank_name }}</div>
                            @endif
                            @if($account->account_number)
                                <div class="text-xs" style="color: rgba(235, 235, 245, 0.4);">
                                    {{ $account->account_number }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-right">
                        <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">
                            Rp {{ number_format($account->initial_balance) }}
                        </div>
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-right">
                        <div class="text-sm font-bold" 
                             style="color: {{ $account->current_balance >= 0 ? '#34C759' : '#FF3B30' }};">
                            Rp {{ number_format($account->current_balance) }}
                        </div>
                        @php
                            $diff = $account->current_balance - $account->initial_balance;
                        @endphp
                        @if($diff != 0)
                        <div class="text-xs mt-0.5" style="color: {{ $diff >= 0 ? 'rgba(52, 199, 89, 0.7)' : 'rgba(255, 59, 48, 0.7)' }};">
                            {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-center">
                        @if($account->is_active)
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple"
                                  style="background: rgba(52, 199, 89, 0.1); color: rgba(52, 199, 89, 0.9);">
                                <i class="fas fa-check-circle text-xs mr-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-medium rounded-apple"
                                  style="background: rgba(255, 59, 48, 0.1); color: rgba(255, 59, 48, 0.9);">
                                <i class="fas fa-times-circle text-xs mr-1"></i>
                                Non-aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center space-x-1.5">
                            <a href="{{ route('cash-accounts.show', $account->id) }}" 
                               class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300"
                               style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                               onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.color='rgba(0, 122, 255, 1)'"
                               onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.color='rgba(235, 235, 245, 0.6)'"
                               title="Lihat Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('cash-accounts.edit', $account->id) }}" 
                               class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300"
                               style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                               onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.color='rgba(255, 149, 0, 1)'"
                               onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.color='rgba(235, 235, 245, 0.6)'"
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
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-apple transition-all duration-300"
                                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);"
                                        onmouseover="this.style.background='rgba(255, 59, 48, 0.15)'; this.style.color='rgba(255, 59, 48, 1)'"
                                        onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.color='rgba(235, 235, 245, 0.6)'"
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
                            <i class="fas fa-inbox text-3xl mb-2" style="color: rgba(235, 235, 245, 0.25);"></i>
                            <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.5);">
                                Belum ada akun kas atau bank
                            </p>
                            <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.4);">
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
            <div class="p-3 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(255, 255, 255, 0.05);">
                        <i class="fas fa-building-columns text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Total Rekening Bank</p>
                        <p class="text-lg font-bold" style="color: #FFFFFF;">
                            {{ $accounts->where('account_type', 'bank')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(255, 255, 255, 0.05);">
                        <i class="fas fa-wallet text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Total Kas Tunai</p>
                        <p class="text-lg font-bold" style="color: #FFFFFF;">
                            {{ $accounts->where('account_type', 'cash')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(255, 255, 255, 0.05);">
                        <i class="fas fa-check-circle text-xs" style="color: rgba(235, 235, 245, 0.4);"></i>
                    </div>
                    <div class="ml-2.5">
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Akun Aktif</p>
                        <p class="text-lg font-bold" style="color: #FFFFFF;">
                            {{ $accounts->where('is_active', true)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
