@extends('layouts.app')

@section('title', 'Tambah Akun Kas')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('cash-accounts.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Tambah Akun Kas</h1>
            <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Buat akun bank atau kas baru</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-lg p-6">
        <form action="{{ route('cash-accounts.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Account Name -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Nama Akun <span style="color: rgba(255, 59, 48, 1);">*</span>
                    </label>
                    <input type="text" name="account_name" required maxlength="255" 
                           value="{{ old('account_name') }}"
                           class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_name') border-2 border-red-500 @enderror" 
                           placeholder="Contoh: Bank BTN Operasional">
                    @error('account_name')
                    <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Tipe Akun <span style="color: rgba(255, 59, 48, 1);">*</span>
                    </label>
                    <select name="account_type" required 
                            class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_type') border-2 border-red-500 @enderror">
                        <option value="">Pilih tipe akun...</option>
                        <option value="bank" {{ old('account_type') == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="cash" {{ old('account_type') == 'cash' ? 'selected' : '' }}>Kas Tunai</option>
                        <option value="receivable" {{ old('account_type') == 'receivable' ? 'selected' : '' }}>Piutang</option>
                        <option value="payable" {{ old('account_type') == 'payable' ? 'selected' : '' }}>Hutang</option>
                    </select>
                    @error('account_type')
                    <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">
                        Bank: Rekening bank | Kas: Uang tunai | Piutang: Uang yang akan diterima | Hutang: Kewajiban
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bank Name -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Nama Bank
                        </label>
                        <input type="text" name="bank_name" maxlength="255" 
                               value="{{ old('bank_name') }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('bank_name') border-2 border-red-500 @enderror" 
                               placeholder="Contoh: Bank Tabungan Negara">
                        @error('bank_name')
                        <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">Hanya untuk tipe Bank</p>
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Nomor Rekening
                        </label>
                        <input type="text" name="account_number" maxlength="100" 
                               value="{{ old('account_number') }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_number') border-2 border-red-500 @enderror" 
                               placeholder="Contoh: 1234567890">
                        @error('account_number')
                        <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Initial Balance -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Saldo Awal (Rp) <span style="color: rgba(255, 59, 48, 1);">*</span>
                    </label>
                    <input type="number" name="initial_balance" required min="0" step="0.01" 
                           value="{{ old('initial_balance', 0) }}"
                           class="input-dark w-full px-4 py-2.5 rounded-lg @error('initial_balance') border-2 border-red-500 @enderror" 
                           placeholder="0">
                    @error('initial_balance')
                    <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">
                        Saldo awal akan menjadi saldo saat ini. Transaksi selanjutnya akan mengubah saldo saat ini.
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3" 
                              class="input-dark w-full px-4 py-2.5 rounded-lg @error('notes') border-2 border-red-500 @enderror" 
                              placeholder="Catatan atau deskripsi akun...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm" style="color: rgba(255, 59, 48, 1);">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="rounded-lg p-4" style="background: rgba(10, 132, 255, 0.1); border-left: 4px solid rgba(10, 132, 255, 1);">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-3" style="color: rgba(10, 132, 255, 1);"></i>
                        <div>
                            <p class="text-sm font-medium mb-1" style="color: rgba(10, 132, 255, 1);">Informasi Penting</p>
                            <p class="text-sm" style="color: rgba(235, 235, 245, 0.8);">
                                Akun akan otomatis diatur sebagai aktif. Saldo akan berubah otomatis saat ada transaksi pembayaran atau pengeluaran.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-8 pt-6" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                <a href="{{ route('cash-accounts.index') }}" 
                   class="px-6 py-2.5 rounded-lg font-medium transition-colors" 
                   style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors" 
                        style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save mr-2"></i>Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
