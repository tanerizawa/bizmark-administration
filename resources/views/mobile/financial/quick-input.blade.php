@extends('mobile.layouts.app')

@section('title', 'Input Keuangan')

@section('header-actions')
<button onclick="history.back()" class="text-gray-600 hover:text-gray-900 transition-colors">
    <i class="fas fa-times text-xl"></i>
</button>
@endsection

@section('content')
<div x-data="financialInput()" class="pb-20">
    {{-- Type Toggle --}}
    <div class="sticky top-16 bg-white z-10 px-3 pt-3 pb-2 border-b border-gray-100">
        <div class="inline-flex bg-gray-100 rounded-lg p-1 w-full">
            <button 
                @click="type = 'income'" 
                :class="type === 'income' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" 
                class="flex-1 py-2 rounded-md font-medium transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-arrow-down text-xs"></i>
                <span>Uang Masuk</span>
            </button>
            <button 
                @click="type = 'expense'" 
                :class="type === 'expense' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" 
                class="flex-1 py-2 rounded-md font-medium transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-arrow-up text-xs"></i>
                <span>Uang Keluar</span>
            </button>
        </div>
    </div>

    <form @submit.prevent="submitTransaction" class="p-3 space-y-3">
        {{-- Amount Input --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">Rp</span>
                <input 
                    type="text" 
                    x-model="amount" 
                    @input="formatAmount"
                    inputmode="numeric"
                    placeholder="0"
                    class="w-full pl-12 pr-3 py-3 text-xl font-bold border-2 border-gray-200 rounded-lg 
                           focus:border-[#0077b5] focus:ring-2 focus:ring-blue-100 transition-all"
                    required>
            </div>

            {{-- Quick Amount Buttons --}}
            <div class="grid grid-cols-4 gap-2 mt-2">
                <button 
                    type="button"
                    @click="setQuickAmount(100000)" 
                    class="py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 
                           active:scale-95 transition-all">
                    100K
                </button>
                <button 
                    type="button"
                    @click="setQuickAmount(500000)" 
                    class="py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 
                           active:scale-95 transition-all">
                    500K
                </button>
                <button 
                    type="button"
                    @click="setQuickAmount(1000000)" 
                    class="py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 
                           active:scale-95 transition-all">
                    1M
                </button>
                <button 
                    type="button"
                    @click="setQuickAmount(5000000)" 
                    class="py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 
                           active:scale-95 transition-all">
                    5M
                </button>
            </div>
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
            <select 
                x-model="category" 
                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg 
                       focus:border-[#0077b5] focus:ring-2 focus:ring-blue-100 transition-all text-sm"
                required>
                <option value="">Pilih kategori...</option>
                <template x-if="type === 'income'">
                    <optgroup label="Pemasukan">
                        <option value="client_payment">Pembayaran Klien</option>
                        <option value="down_payment">DP Proyek</option>
                        <option value="final_payment">Pelunasan</option>
                        <option value="other_income">Pemasukan Lainnya</option>
                    </optgroup>
                </template>
                <template x-if="type === 'expense'">
                    <optgroup label="Pengeluaran">
                        <option value="operational">Operasional</option>
                        <option value="salary">Gaji Karyawan</option>
                        <option value="tax_payment">Pembayaran Pajak</option>
                        <option value="permit_fee">Biaya Perizinan</option>
                        <option value="transport">Transportasi</option>
                        <option value="other_expense">Pengeluaran Lainnya</option>
                    </optgroup>
                </template>
            </select>
        </div>

        {{-- Project Link (Optional) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Terkait Proyek <span class="text-gray-400 text-xs">(opsional)</span>
            </label>
            <select 
                x-model="project_id" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl 
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                <option value="">Tidak terkait proyek</option>
                @foreach($projects ?? [] as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-gray-400 text-xs">(opsional)</span>
            </label>
            <textarea 
                x-model="description" 
                rows="3"
                placeholder="Tambahkan catatan atau detail transaksi..."
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl 
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"></textarea>
        </div>

        {{-- Receipt Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Bukti Transaksi <span class="text-gray-400 text-xs">(opsional)</span>
            </label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                <input 
                    type="file" 
                    id="receiptInput" 
                    @change="handleFileUpload" 
                    accept="image/*"
                    capture="environment"
                    class="hidden">
                
                <template x-if="!receipt">
                    <div>
                        <i class="fas fa-camera text-gray-400 text-3xl mb-3"></i>
                        <p class="text-sm text-gray-600 mb-3">Foto struk atau bukti pembayaran</p>
                        <button 
                            type="button"
                            @click="document.getElementById('receiptInput').click()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 
                                   rounded-lg text-sm font-medium text-gray-700 active:scale-95 transition-all">
                            <i class="fas fa-upload"></i>
                            Pilih File
                        </button>
                    </div>
                </template>

                <template x-if="receipt">
                    <div>
                        <img :src="receiptPreview" class="max-h-48 mx-auto rounded-lg mb-3">
                        <p class="text-sm text-gray-600 mb-2" x-text="receipt.name"></p>
                        <button 
                            type="button"
                            @click="removeReceipt" 
                            class="text-red-600 text-sm font-medium hover:text-red-700">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Date & Time --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu</label>
            <input 
                type="datetime-local" 
                x-model="transaction_date" 
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl 
                       focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                required>
        </div>

        {{-- Submit Button --}}
        <div class="pt-3 sticky bottom-0 bg-white pb-3 -mx-3 px-3 border-t border-gray-100">
            <button 
                type="submit"
                :disabled="loading || !amount || !category"
                class="w-full py-3 bg-[#0077b5] hover:bg-[#004182] text-white rounded-lg font-bold 
                       disabled:opacity-50 disabled:cursor-not-allowed 
                       active:scale-95 transition-all flex items-center justify-center gap-2">
                <template x-if="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                </template>
                <template x-if="!loading">
                    <span x-text="type === 'income' ? 'Simpan Pemasukan' : 'Simpan Pengeluaran'"></span>
                </template>
            </button>
        </div>
    </form>
</div>

<script>
function financialInput() {
    return {
        type: new URLSearchParams(window.location.search).get('type') || 'income',
        amount: '',
        rawAmount: 0,
        category: '',
        project_id: '',
        description: '',
        receipt: null,
        receiptPreview: null,
        transaction_date: new Date().toISOString().slice(0, 16),
        loading: false,

        formatAmount() {
            // Remove non-numeric characters
            let num = this.amount.replace(/\D/g, '');
            this.rawAmount = parseInt(num) || 0;
            
            // Format with thousand separator
            this.amount = this.rawAmount.toLocaleString('id-ID');
        },

        setQuickAmount(value) {
            this.rawAmount = value;
            this.amount = value.toLocaleString('id-ID');
            
            // Haptic feedback
            if ('vibrate' in navigator) {
                navigator.vibrate(10);
            }
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.receipt = file;
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.receiptPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removeReceipt() {
            this.receipt = null;
            this.receiptPreview = null;
            document.getElementById('receiptInput').value = '';
        },

        async submitTransaction() {
            if (this.loading || !this.rawAmount || !this.category) return;

            this.loading = true;

            try {
                const formData = new FormData();
                formData.append('type', this.type);
                formData.append('amount', this.rawAmount);
                formData.append('category', this.category);
                formData.append('transaction_date', this.transaction_date);
                
                if (this.project_id) {
                    formData.append('project_id', this.project_id);
                }
                
                if (this.description) {
                    formData.append('description', this.description);
                }
                
                if (this.receipt) {
                    formData.append('receipt', this.receipt);
                }

                const response = await fetch('{{ mobile_route("financial.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Success haptic
                    if ('vibrate' in navigator) {
                        navigator.vibrate([20, 50, 20]);
                    }

                    // Show success message
                    alert(data.message || 'Transaksi berhasil disimpan!');
                    
                    // Redirect to financial page or dashboard
                    window.location.href = '{{ mobile_route("financial.index") }}';
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menyimpan transaksi: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
