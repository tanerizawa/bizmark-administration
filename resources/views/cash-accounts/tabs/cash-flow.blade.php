{{-- Laporan Arus Kas (PSAK 2 Compliant) --}}
<div>
    <div class="mb-3">
        <h3 class="text-base font-semibold" style="color: #FFFFFF;">
            <i class="fas fa-file-invoice-dollar mr-2" style="color: rgba(235, 235, 245, 0.4);"></i>
            Laporan Arus Kas
        </h3>
        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
            Periode: {{ $cashFlowStatement['period_start'] }} - {{ $cashFlowStatement['period_end'] }}
        </p>
    </div>

    <div class="space-y-3">
        <!-- AKTIVITAS OPERASI -->
        <div class="p-3 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-sm font-semibold mb-2" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-circle text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.3);"></i>
                AKTIVITAS OPERASI
            </h4>
            
            <div class="space-y-1.5">
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                        <i class="fas fa-arrow-down text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                        Penerimaan dari pelanggan
                    </span>
                    <span class="text-xs font-semibold" style="color: rgba(52, 199, 89, 1);">
                        Rp {{ number_format($cashFlowStatement['operating_receipts']) }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                        <i class="fas fa-arrow-up text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                        Pembayaran operasional & rekanan
                    </span>
                    <span class="text-xs font-semibold" style="color: rgba(255, 59, 48, 1);">
                        (Rp {{ number_format($cashFlowStatement['operating_payments']) }})
                    </span>
                </div>
                
                <div class="border-t pt-1.5 mt-1.5" style="border-color: rgba(255, 255, 255, 0.08);">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.9);">
                            Kas bersih dari aktivitas operasi
                        </span>
                        <span class="text-sm font-bold" style="color: {{ $cashFlowStatement['net_operating'] >= 0 ? '#34C759' : '#FF3B30' }};">
                            Rp {{ number_format($cashFlowStatement['net_operating']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AKTIVITAS PENDANAAN -->
        <div class="p-3 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-sm font-semibold mb-2" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-circle text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.3);"></i>
                AKTIVITAS PENDANAAN
            </h4>
            
            <div class="space-y-1.5">
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                        <i class="fas fa-hand-holding-usd text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                        Kasbon/pinjaman diberikan
                    </span>
                    <span class="text-xs font-semibold" style="color: rgba(255, 59, 48, 1);">
                        (Rp {{ number_format($cashFlowStatement['kasbon_given']) }})
                    </span>
                </div>
                
                <div class="flex justify-between items-center py-1">
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">
                        <i class="fas fa-arrow-down text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                        Pelunasan kasbon diterima
                    </span>
                    <span class="text-xs font-semibold" style="color: rgba(52, 199, 89, 1);">
                        Rp {{ number_format($cashFlowStatement['kasbon_received']) }}
                    </span>
                </div>
                
                <div class="border-t pt-1.5 mt-1.5" style="border-color: rgba(255, 255, 255, 0.08);">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.9);">
                            Kas bersih dari aktivitas pendanaan
                        </span>
                        <span class="text-sm font-bold" style="color: {{ $cashFlowStatement['net_financing'] >= 0 ? '#34C759' : '#FF3B30' }};">
                            Rp {{ number_format($cashFlowStatement['net_financing']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="p-3 rounded-apple" style="background: rgba(0, 122, 255, 0.08); border: 1px solid rgba(0, 122, 255, 0.2);">
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.9);">
                        Kenaikan/(Penurunan) kas dan setara kas
                    </span>
                    <span class="text-base font-bold" style="color: {{ $cashFlowStatement['net_change'] >= 0 ? '#34C759' : '#FF3B30' }};">
                        Rp {{ number_format($cashFlowStatement['net_change']) }}
                    </span>
                </div>
                
                <div class="border-t pt-2" style="border-color: rgba(255, 255, 255, 0.15);">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                            Kas dan setara kas awal periode
                        </span>
                        <span class="text-xs font-semibold" style="color: rgba(235, 235, 245, 0.8);">
                            Rp {{ number_format($cashFlowStatement['cash_beginning']) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center pt-1.5 border-t" style="border-color: rgba(255, 255, 255, 0.15);">
                        <span class="text-sm font-bold" style="color: #FFFFFF;">
                            Kas dan setara kas akhir periode
                        </span>
                        <span class="text-base font-bold" style="color: rgba(0, 122, 255, 1);">
                            Rp {{ number_format($cashFlowStatement['cash_ending']) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- PSAK Compliance Note -->
        <div class="mt-3 p-2 rounded-apple" style="background: rgba(255, 255, 255, 0.02); border-left: 2px solid rgba(235, 235, 245, 0.2);">
            <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                <i class="fas fa-info-circle text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                <strong>Catatan:</strong> Laporan arus kas ini disusun berdasarkan PSAK 2 menggunakan metode langsung (direct method). 
                Aktivitas investasi tidak ditampilkan karena tidak ada transaksi investasi dalam periode ini.
            </p>
        </div>
    </div>
</div>
