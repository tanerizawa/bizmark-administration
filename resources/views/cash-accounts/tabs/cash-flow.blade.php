{{-- Laporan Arus Kas (PSAK 2 Compliant) --}}
<div>
    <div class="mb-3">
        <h3 class="text-base font-semibold text-white">
            <i class="fas fa-file-invoice-dollar mr-2 text-dark-text-tertiary/40"></i>
            Laporan Arus Kas
        </h3>
        <p class="text-xs mt-0.5 text-dark-text-tertiary/80">
            Periode: {{ $cashFlowStatement['period_start'] }} - {{ $cashFlowStatement['period_end'] }}
        </p>
    </div>

    <div class="space-y-3">
        <!-- AKTIVITAS OPERASI -->
        <div class="p-3 rounded-apple bg-white/[0.02]">
            <h4 class="text-sm font-semibold mb-2 text-dark-text-secondary">
                <i class="fas fa-circle text-xs mr-1.5 text-dark-text-tertiary/30"></i>
                AKTIVITAS OPERASI
            </h4>
            
            <div class="space-y-1.5">
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs text-dark-text-primary/70">
                        <i class="fas fa-arrow-down text-xs mr-1.5 text-dark-text-tertiary/40"></i>
                        Penerimaan dari pelanggan
                    </span>
                    <span class="text-xs font-semibold text-apple-green">
                        Rp {{ number_format($cashFlowStatement['operating_receipts']) }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs text-dark-text-primary/70">
                        <i class="fas fa-arrow-up text-xs mr-1.5 text-dark-text-tertiary/40"></i>
                        Pembayaran operasional & rekanan
                    </span>
                    <span class="text-xs font-semibold text-apple-red">
                        (Rp {{ number_format($cashFlowStatement['operating_payments']) }})
                    </span>
                </div>
                
                <div class="border-t border-white/5 pt-1.5 mt-1.5">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-sm font-medium text-dark-text-primary/90">
                            Kas bersih dari aktivitas operasi
                        </span>
                        <span class="text-sm font-bold {{ $cashFlowStatement['net_operating'] >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                            Rp {{ number_format($cashFlowStatement['net_operating']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AKTIVITAS PENDANAAN -->
        <div class="p-3 rounded-apple bg-white/[0.02]">
            <h4 class="text-sm font-semibold mb-2 text-dark-text-secondary">
                <i class="fas fa-circle text-xs mr-1.5 text-dark-text-tertiary/30"></i>
                AKTIVITAS PENDANAAN
            </h4>
            
            <div class="space-y-1.5">
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs text-dark-text-primary/70">
                        <i class="fas fa-hand-holding-usd text-xs mr-1.5 text-dark-text-tertiary/40"></i>
                        Kasbon/pinjaman diberikan
                    </span>
                    <span class="text-xs font-semibold text-apple-red">
                        (Rp {{ number_format($cashFlowStatement['kasbon_given']) }})
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs text-dark-text-primary/70">
                        <i class="fas fa-arrow-down text-xs mr-1.5 text-dark-text-tertiary/40"></i>
                        Pelunasan kasbon diterima
                    </span>
                    <span class="text-xs font-semibold text-apple-green">
                        Rp {{ number_format($cashFlowStatement['kasbon_received']) }}
                    </span>
                </div>
                
                <div class="border-t border-white/5 pt-1.5 mt-1.5">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-sm font-medium text-dark-text-primary/90">
                            Kas bersih dari aktivitas pendanaan
                        </span>
                        <span class="text-sm font-bold {{ $cashFlowStatement['net_financing'] >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                            Rp {{ number_format($cashFlowStatement['net_financing']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="p-3 rounded-apple bg-apple-blue/8 border border-apple-blue/20">
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-dark-text-primary/90">
                        Kenaikan/(Penurunan) kas dan setara kas
                    </span>
                    <span class="text-base font-bold {{ $cashFlowStatement['net_change'] >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                        Rp {{ number_format($cashFlowStatement['net_change']) }}
                    </span>
                </div>
                
                <div class="border-t border-white/10 pt-2">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs text-dark-text-secondary">
                            Kas dan setara kas awal periode
                        </span>
                        <span class="text-xs font-semibold text-dark-text-primary/80">
                            Rp {{ number_format($cashFlowStatement['cash_beginning']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-1.5 border-t border-white/10">
                        <span class="text-sm font-bold text-white">
                            Kas dan setara kas akhir periode
                        </span>
                        <span class="text-base font-bold text-apple-blue">
                            Rp {{ number_format($cashFlowStatement['cash_ending']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PSAK Compliance Note -->
        <div class="mt-3 p-2 rounded-apple bg-white/[0.02] border-l-2 border-white/10">
            <p class="text-xs text-dark-text-tertiary/80">
                <i class="fas fa-info-circle text-xs mr-1.5 text-dark-text-tertiary/40"></i>
                <strong>Catatan:</strong> Laporan arus kas ini disusun berdasarkan PSAK 2 menggunakan metode langsung (direct method).
                Aktivitas investasi tidak ditampilkan karena tidak ada transaksi investasi dalam periode ini.
            </p>
        </div>
    </div>
</div>
