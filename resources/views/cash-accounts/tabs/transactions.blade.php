{{-- Riwayat Transaksi Proyek (Project-Related Transactions Only) --}}
<div>
    <div class="mb-3">
        <h3 class="text-base font-semibold text-white">
            <i class="fas fa-project-diagram mr-2 text-dark-text-tertiary/40"></i>
            Riwayat Transaksi Proyek
        </h3>
        <p class="text-xs mt-0.5 text-dark-text-tertiary/80">
            Transaksi yang terkait dengan proyek dalam periode yang dipilih
        </p>
    </div>

    @if(count($recentTransactions) > 0)
        <div class="space-y-2">
            @php
                $currentDate = null;
                $typeStyles = [
                    'inflow' => [
                        'icon' => 'fa-arrow-down',
                        'rowClass' => 'bg-apple-green/8 border-l-2 border-apple-green/20',
                        'iconClass' => 'text-apple-green/80',
                        'labelClass' => 'bg-apple-green/8 text-apple-green/80',
                        'amountClass' => 'text-apple-green',
                        'label' => 'Pemasukan'
                    ],
                    'outflow' => [
                        'icon' => 'fa-arrow-up',
                        'rowClass' => 'bg-apple-red/8 border-l-2 border-apple-red/20',
                        'iconClass' => 'text-apple-red/80',
                        'labelClass' => 'bg-apple-red/8 text-apple-red/80',
                        'amountClass' => 'text-apple-red',
                        'label' => 'Pengeluaran'
                    ],
                    'kasbon' => [
                        'icon' => 'fa-hand-holding-usd',
                        'rowClass' => 'bg-apple-orange/8 border-l-2 border-apple-orange/20',
                        'iconClass' => 'text-apple-orange/80',
                        'labelClass' => 'bg-apple-orange/8 text-apple-orange/80',
                        'amountClass' => 'text-apple-orange',
                        'label' => 'Kasbon'
                    ]
                ];
                $style = $typeStyles[$transaction['type']];
            @endphp
            
            @foreach($recentTransactions as $transaction)
                @php
                    $transactionDate = \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d');
                    $showDateHeader = $currentDate !== $transactionDate;
                    $currentDate = $transactionDate;
                    $style = $typeStyles[$transaction['type']];
                @endphp
                
                @if($showDateHeader)
                    <div class="flex items-center my-3">
                        <div class="flex-grow h-px bg-white/[0.08]"></div>
                        <span class="px-2.5 text-xs font-medium text-dark-text-tertiary/80">
                            {{ \Carbon\Carbon::parse($transaction['date'])->isoFormat('dddd, D MMMM Y') }}
                        </span>
                        <div class="flex-grow h-px bg-white/[0.08]"></div>
                    </div>
                @endif
                
                <div class="p-2.5 rounded-apple transition-all duration-300 hover:translate-x-0.5 {{ $style['rowClass'] }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <div class="flex-shrink-0 h-7 w-7 flex items-center justify-center rounded-full bg-white/[0.08]">
                                <i class="fas {{ $style['icon'] }} text-xs {{ $style['iconClass'] }}"></i>
                            </div>
                            
                            <div class="ml-2.5 flex-1">
                                <div class="flex items-center">
                                    <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $style['labelClass'] }}">
                                        {{ $style['label'] }}
                                    </span>
                                    <span class="ml-2 text-xs text-dark-text-tertiary/60">
                                        {{ \Carbon\Carbon::parse($transaction['date'])->format('H:i') }}
                                    </span>
                                </div>
                                
                                <p class="mt-1 text-xs font-medium text-dark-text-primary/90">
                                    {{ $transaction['description'] }}
                                </p>
                                
                                @if(isset($transaction['project_name']) && $transaction['project_name'])
                                    <div class="mt-1 flex items-center text-xs text-dark-text-tertiary/80">
                                        <i class="fas fa-project-diagram text-xs mr-1 text-dark-text-tertiary/60"></i>
                                        @if(isset($transaction['project_id']))
                                            <a href="{{ route('projects.show', $transaction['project_id']) }}" 
                                               class="hover:underline text-apple-blue/90">
                                                {{ $transaction['project_name'] }}
                                            </a>
                                        @else
                                            {{ $transaction['project_name'] }}
                                        @endif
                                    </div>
                                @endif
                                
                                @if(isset($transaction['account_name']))
                                    <div class="mt-0.5 flex items-center text-xs text-dark-text-tertiary/80">
                                        <i class="fas fa-wallet text-xs mr-1 text-dark-text-tertiary/60"></i>
                                        {{ $transaction['account_name'] }}
                                    </div>
                                @endif
                                
                                @if(isset($transaction['notes']) && $transaction['notes'])
                                    <div class="mt-1.5 p-1.5 rounded bg-black/15">
                                        <p class="text-xs text-dark-text-tertiary/90">
                                            <i class="fas fa-sticky-note text-xs mr-1 text-dark-text-tertiary/60"></i>
                                            {{ $transaction['notes'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="ml-3 text-right flex-shrink-0">
                            <div class="text-sm font-bold {{ $style['amountClass'] }}">
                                {{ $transaction['type'] === 'inflow' ? '+' : '-' }}Rp {{ number_format($transaction['amount']) }}
                            </div>
                            @if(isset($transaction['balance_after']))
                                <div class="text-xs mt-0.5 text-dark-text-tertiary/60">
                                    Saldo: Rp {{ number_format($transaction['balance_after']) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Summary Stats -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-3 rounded-apple bg-apple-green/8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Total Pemasukan</p>
                        <p class="text-sm font-bold text-apple-green">
                            @php
                                $totalInflow = collect($recentTransactions)
                                    ->where('type', 'inflow')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalInflow) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full bg-apple-green/15">
                        <i class="fas fa-arrow-down text-xs text-apple-green/80"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple bg-apple-red/8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Total Pengeluaran</p>
                        <p class="text-sm font-bold text-apple-red">
                            @php
                                $totalOutflow = collect($recentTransactions)
                                    ->where('type', 'outflow')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalOutflow) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full bg-apple-red/15">
                        <i class="fas fa-arrow-up text-xs text-apple-red/80"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple bg-apple-orange/8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Total Kasbon</p>
                        <p class="text-sm font-bold text-apple-orange">
                            @php
                                $totalKasbon = collect($recentTransactions)
                                    ->where('type', 'kasbon')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalKasbon) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full bg-apple-orange/15">
                        <i class="fas fa-hand-holding-usd text-xs text-apple-orange/80"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View All Link -->
        <div class="mt-3 text-center">
            <p class="text-xs text-dark-text-tertiary/80">
                Menampilkan {{ count($recentTransactions) }} transaksi terbaru dalam periode yang dipilih.
            </p>
        </div>
    @else
        <div class="py-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-receipt text-3xl mb-2 text-dark-text-tertiary/25"></i>
                <p class="text-sm font-medium text-dark-text-tertiary/80">
                    Belum ada transaksi
                </p>
                <p class="text-xs mt-0.5 text-dark-text-tertiary/60">
                    Transaksi akan muncul di sini setelah Anda menambahkan pembayaran atau pengeluaran
                </p>
            </div>
        </div>
    @endif
</div>
