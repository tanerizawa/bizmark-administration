@extends('layouts.app')

@section('title', 'Edit Akun Kas')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('cash-accounts.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Akun Kas</h1>
            <p class="mt-1 text-dark-text-secondary">Edit informasi akun {{ $cashAccount->account_name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-lg p-6">
        <form action="{{ route('cash-accounts.update', $cashAccount) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Account Name -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Nama Akun <span class="text-apple-red">*</span>
                    </label>
                    <input type="text" name="account_name" required maxlength="255"
                           value="{{ old('account_name', $cashAccount->account_name) }}"
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
                        <option value="bank" {{ old('account_type', $cashAccount->account_type) == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="cash" {{ old('account_type', $cashAccount->account_type) == 'cash' ? 'selected' : '' }}>Kas Tunai</option>
                        <option value="receivable" {{ old('account_type', $cashAccount->account_type) == 'receivable' ? 'selected' : '' }}>Piutang</option>
                        <option value="payable" {{ old('account_type', $cashAccount->account_type) == 'payable' ? 'selected' : '' }}>Hutang</option>
                    </select>
                    @error('account_type')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bank Name -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Nama Bank
                        </label>
                        <input type="text" name="bank_name" maxlength="255"
                               value="{{ old('bank_name', $cashAccount->bank_name) }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('bank_name') border-2 border-red-500 @enderror"
                               placeholder="Contoh: Bank Tabungan Negara">
                        @error('bank_name')
                        <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Nomor Rekening
                        </label>
                        <input type="text" name="account_number" maxlength="100"
                               value="{{ old('account_number', $cashAccount->account_number) }}"
                               class="input-dark w-full px-4 py-2.5 rounded-lg @error('account_number') border-2 border-red-500 @enderror"
                               placeholder="Contoh: 1234567890">
                        @error('account_number')
                        <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Balance (Read Only) -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Saldo Saat Ini (Rp)
                    </label>
                    <div class="px-4 py-2.5 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-lg font-bold text-white">
                            Rp {{ number_format($cashAccount->current_balance, 0, ',', '.') }}
                        </p>
                    </div>
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        Saldo tidak dapat diedit manual. Berubah otomatis dari transaksi.
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Status Akun
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="is_active" value="1"
                                   {{ old('is_active', $cashAccount->is_active) == 1 ? 'checked' : '' }}
                                   class="form-radio text-apple-green">
                            <span class="text-dark-text-primary/80">Aktif</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="is_active" value="0"
                                   {{ old('is_active', $cashAccount->is_active) == 0 ? 'checked' : '' }}
                                   class="form-radio text-dark-text-tertiary">
                            <span class="text-dark-text-primary/80">Nonaktif</span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-dark-text-tertiary/80">
                        Akun nonaktif tidak dapat dipilih untuk transaksi baru
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3"
                              class="input-dark w-full px-4 py-2.5 rounded-lg @error('notes') border-2 border-red-500 @enderror"
                              placeholder="Catatan atau deskripsi akun...">{{ old('notes', $cashAccount->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Summary -->
                <div class="rounded-lg p-4 bg-apple-blue/10 border-l-4 border-apple-blue">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-3 text-apple-blue"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-2 text-apple-blue">Ringkasan Transaksi</p>
                            <div class="grid grid-cols-2 gap-4 text-sm text-dark-text-primary/80">
                                <div>
                                    <p class="text-dark-text-secondary">Saldo Awal:</p>
                                    <p class="font-semibold">Rp {{ number_format($cashAccount->initial_balance, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-dark-text-secondary">Total Transaksi:</p>
                                    <p class="font-semibold">{{ $cashAccount->payments()->count() + $cashAccount->expenses()->count() }}</p>
                                </div>
                            </div>
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
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors bg-apple-orange/90 text-white">
                    <i class="fas fa-save mr-2"></i>Update Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
