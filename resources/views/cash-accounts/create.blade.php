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
            <h1 class="text-3xl font-bold text-white">Tambah Akun Kas</h1>
            <p class="mt-1 text-dark-text-secondary">Buat akun bank atau kas baru</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-lg p-6">
        <form action="{{ route('cash-accounts.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Account Name -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Nama Akun <span class="text-apple-red">*</span>
                    </label>
                    <input type="text" name="account_name" required maxlength="255"
                           value="{{ old('account_name') }}"
                           class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_name') border-2 border-red-500 @enderror"
                           placeholder="Contoh: Bank BTN Operasional">
                    @error('account_name')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Tipe Akun <span class="text-apple-red">*</span>
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
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        Bank: Rekening bank | Kas: Uang tunai | Piutang: Uang yang akan diterima | Hutang: Kewajiban
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bank Name -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Nama Bank
                        </label>
                        <input type="text" name="bank_name" maxlength="255"
                               value="{{ old('bank_name') }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('bank_name') border-2 border-red-500 @enderror"
                               placeholder="Contoh: Bank Tabungan Negara">
                        @error('bank_name')
                        <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-dark-text-tertiary/80">Hanya untuk tipe Bank</p>
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Nomor Rekening
                        </label>
                        <input type="text" name="account_number" maxlength="100"
                               value="{{ old('account_number') }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_number') border-2 border-red-500 @enderror"
                               placeholder="Contoh: 1234567890">
                        @error('account_number')
                        <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Initial Balance -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Saldo Awal (Rp) <span class="text-apple-red">*</span>
                    </label>
                    <input type="number" name="initial_balance" required min="0" step="0.01"
                           value="{{ old('initial_balance', 0) }}"
                           class="input-dark w-full px-4 py-2.5 rounded-lg @error('initial_balance') border-2 border-red-500 @enderror"
                           placeholder="0">
                    @error('initial_balance')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        Saldo awal akan menjadi saldo saat ini. Transaksi selanjutnya akan mengubah saldo saat ini.
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3"
                              class="input-dark w-full px-4 py-2.5 rounded-lg @error('notes') border-2 border-red-500 @enderror"
                              placeholder="Catatan atau deskripsi akun...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="rounded-lg p-4 bg-apple-blue/10 border-l-4 border-apple-blue">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-3 text-apple-blue"></i>
                        <div>
                            <p class="text-sm font-medium mb-1 text-apple-blue">Informasi Penting</p>
                            <p class="text-sm text-dark-text-primary/80">
                                Akun akan otomatis diatur sebagai aktif. Saldo akan berubah otomatis saat ada transaksi pembayaran atau pengeluaran.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-white/10">
                <a href="{{ route('cash-accounts.index') }}"
                   class="px-6 py-2.5 rounded-lg font-medium transition-colors bg-white/10 text-dark-text-primary/80">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors bg-apple-green/90 text-white">
                    <i class="fas fa-save mr-2"></i>Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
