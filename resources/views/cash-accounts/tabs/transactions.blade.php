{{-- Riwayat Transaksi (Timeline View) --}}
<div>
    <div class="mb-3">
        <h3 class="text-base font-semibold" style="color: #FFFFFF;">
            <i class="fas fa-history mr-2" style="color: rgba(235, 235, 245, 0.4);"></i>
            Riwayat Transaksi Terbaru
        </h3>
        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
            15 transaksi terakhir dari semua akun
        </p>
    </div>

    @if(count($recentTransactions) > 0)
        <div class="space-y-2">
            @php
                $currentDate = null;
            @endphp
            
            @foreach($recentTransactions as $transaction)
                @php
                    $transactionDate = \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d');
                    $showDateHeader = $currentDate !== $transactionDate;
                    $currentDate = $transactionDate;
                    
                    // Determine transaction style
                    $typeStyles = [
                        'inflow' => [
                            'icon' => 'fa-arrow-down',
                            'iconColor' => 'rgba(52, 199, 89, 0.8)',
                            'bgColor' => 'rgba(52, 199, 89, 0.08)',
                            'borderColor' => 'rgba(52, 199, 89, 0.2)',
                            'amountColor' => 'rgba(52, 199, 89, 1)',
                            'label' => 'Pemasukan'
                        ],
                        'outflow' => [
                            'icon' => 'fa-arrow-up',
                            'iconColor' => 'rgba(255, 59, 48, 0.8)',
                            'bgColor' => 'rgba(255, 59, 48, 0.08)',
                            'borderColor' => 'rgba(255, 59, 48, 0.2)',
                            'amountColor' => 'rgba(255, 59, 48, 1)',
                            'label' => 'Pengeluaran'
                        ],
                        'kasbon' => [
                            'icon' => 'fa-hand-holding-usd',
                            'iconColor' => 'rgba(255, 149, 0, 0.8)',
                            'bgColor' => 'rgba(255, 149, 0, 0.08)',
                            'borderColor' => 'rgba(255, 149, 0, 0.2)',
                            'amountColor' => 'rgba(255, 149, 0, 1)',
                            'label' => 'Kasbon'
                        ]
                    ];
                    $style = $typeStyles[$transaction['type']];
                @endphp
                
                @if($showDateHeader)
                    <div class="flex items-center my-3">
                        <div class="flex-grow h-px" style="background: rgba(255, 255, 255, 0.08);"></div>
                        <span class="px-2.5 text-xs font-medium" style="color: rgba(235, 235, 245, 0.5);">
                            {{ \Carbon\Carbon::parse($transaction['date'])->isoFormat('dddd, D MMMM Y') }}
                        </span>
                        <div class="flex-grow h-px" style="background: rgba(255, 255, 255, 0.08);"></div>
                    </div>
                @endif
                
                <div class="p-2.5 rounded-apple transition-all duration-300"
                     style="background: {{ $style['bgColor'] }}; border-left: 2px solid {{ $style['borderColor'] }};"
                     onmouseover="this.style.transform='translateX(3px)'"
                     onmouseout="this.style.transform='translateX(0)'">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start flex-1">
                            <div class="flex-shrink-0 h-7 w-7 flex items-center justify-center rounded-full"
                                 style="background: rgba(255, 255, 255, 0.08);">
                                <i class="fas {{ $style['icon'] }} text-xs" style="color: {{ $style['iconColor'] }};"></i>
                            </div>
                            
                            <div class="ml-2.5 flex-1">
                                <div class="flex items-center">
                                    <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                                          style="background: {{ $style['bgColor'] }}; color: {{ $style['iconColor'] }};">
                                        {{ $style['label'] }}
                                    </span>
                                    <span class="ml-2 text-xs" style="color: rgba(235, 235, 245, 0.4);">
                                        {{ \Carbon\Carbon::parse($transaction['date'])->format('H:i') }}
                                    </span>
                                </div>
                                
                                <p class="mt-1 text-xs font-medium" style="color: rgba(235, 235, 245, 0.9);">
                                    {{ $transaction['description'] }}
                                </p>
                                
                                @if(isset($transaction['project_name']) && $transaction['project_name'])
                                    <div class="mt-1 flex items-center text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                        <i class="fas fa-project-diagram text-xs mr-1" style="color: rgba(235, 235, 245, 0.4);"></i>
                                        @if(isset($transaction['project_id']))
                                            <a href="{{ route('projects.show', $transaction['project_id']) }}" 
                                               class="hover:underline"
                                               style="color: rgba(0, 122, 255, 0.9);">
                                                {{ $transaction['project_name'] }}
                                            </a>
                                        @else
                                            {{ $transaction['project_name'] }}
                                        @endif
                                    </div>
                                @endif
                                
                                @if(isset($transaction['account_name']))
                                    <div class="mt-0.5 flex items-center text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                        <i class="fas fa-wallet text-xs mr-1" style="color: rgba(235, 235, 245, 0.4);"></i>
                                        {{ $transaction['account_name'] }}
                                    </div>
                                @endif
                                
                                @if(isset($transaction['notes']) && $transaction['notes'])
                                    <div class="mt-1.5 p-1.5 rounded" style="background: rgba(0, 0, 0, 0.15);">
                                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-sticky-note text-xs mr-1" style="color: rgba(235, 235, 245, 0.4);"></i>
                                            {{ $transaction['notes'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="ml-3 text-right flex-shrink-0">
                            <div class="text-sm font-bold" style="color: {{ $style['amountColor'] }};">
                                {{ $transaction['type'] === 'inflow' ? '+' : '-' }}Rp {{ number_format($transaction['amount']) }}
                            </div>
                            @if(isset($transaction['balance_after']))
                                <div class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.4);">
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
            <div class="p-3 rounded-apple" style="background: rgba(52, 199, 89, 0.08);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgba(235, 235, 245, 0.5);">Total Pemasukan</p>
                        <p class="text-sm font-bold" style="color: rgba(52, 199, 89, 1);">
                            @php
                                $totalInflow = collect($recentTransactions)
                                    ->where('type', 'inflow')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalInflow) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(52, 199, 89, 0.15);">
                        <i class="fas fa-arrow-down text-xs" style="color: rgba(52, 199, 89, 0.8);"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple" style="background: rgba(255, 59, 48, 0.08);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgba(235, 235, 245, 0.5);">Total Pengeluaran</p>
                        <p class="text-sm font-bold" style="color: rgba(255, 59, 48, 1);">
                            @php
                                $totalOutflow = collect($recentTransactions)
                                    ->where('type', 'outflow')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalOutflow) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(255, 59, 48, 0.15);">
                        <i class="fas fa-arrow-up text-xs" style="color: rgba(255, 59, 48, 0.8);"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-3 rounded-apple" style="background: rgba(255, 149, 0, 0.08);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgba(235, 235, 245, 0.5);">Total Kasbon</p>
                        <p class="text-sm font-bold" style="color: rgba(255, 149, 0, 1);">
                            @php
                                $totalKasbon = collect($recentTransactions)
                                    ->where('type', 'kasbon')
                                    ->sum('amount');
                            @endphp
                            Rp {{ number_format($totalKasbon) }}
                        </p>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full"
                         style="background: rgba(255, 149, 0, 0.15);">
                        <i class="fas fa-hand-holding-usd text-xs" style="color: rgba(255, 149, 0, 0.8);"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- View All Link -->
        <div class="mt-3 text-center">
            <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                Menampilkan 15 transaksi terbaru. 
                <a href="#" class="font-medium hover:underline" style="color: rgba(0, 122, 255, 0.9);">
                    Lihat Semua Transaksi
                </a>
            </p>
        </div>
    @else
        <div class="py-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-receipt text-3xl mb-2" style="color: rgba(235, 235, 245, 0.25);"></i>
                <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.5);">
                    Belum ada transaksi
                </p>
                <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.4);">
                    Transaksi akan muncul di sini setelah Anda menambahkan pembayaran atau pengeluaran
                </p>
            </div>
        </div>
    @endif
</div>
