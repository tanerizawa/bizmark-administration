@extends('layouts.app')

@section('title', 'Mulai Rekonsiliasi Baru')
@section('page-title', 'Mulai Rekonsiliasi Bank Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('reconciliations.index') }}" 
           class="inline-flex items-center text-sm" style="color: rgba(0, 122, 255, 1);">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Rekonsiliasi
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-apple p-6 shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-2" style="color: rgba(235, 235, 245, 0.9);">Informasi Rekonsiliasi</h2>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                Upload bank statement dan mulai proses rekonsiliasi dengan transaksi sistem
            </p>
        </div>

        <form action="{{ route('reconciliations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Cash Account Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    Akun Kas/Bank <span class="text-red-500">*</span>
                </label>
                <select name="cash_account_id" required
                        class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cash_account_id') ring-2 ring-red-500 @enderror"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    <option value="">Pilih akun kas/bank</option>
                    @foreach($cashAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('cash_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }} - {{ $account->bank_name }} ({{ $account->account_number }})
                        </option>
                    @endforeach
                </select>
                @error('cash_account_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Period Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') ring-2 ring-red-500 @enderror"
                           style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="w-full px-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') ring-2 ring-red-500 @enderror"
                           style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bank Balances -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Saldo Awal Bank <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.6);">Rp</span>
                        <input type="number" name="opening_balance_bank" value="{{ old('opening_balance_bank') }}" required step="0.01"
                               class="w-full pl-12 pr-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('opening_balance_bank') ring-2 ring-red-500 @enderror"
                               style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);"
                               placeholder="0.00">
                    </div>
                    @error('opening_balance_bank')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">Saldo awal dari bank statement</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Saldo Akhir Bank <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.6);">Rp</span>
                        <input type="number" name="closing_balance_bank" value="{{ old('closing_balance_bank') }}" required step="0.01"
                               class="w-full pl-12 pr-4 py-3 rounded-apple text-sm border-none focus:outline-none focus:ring-2 focus:ring-blue-500 @error('closing_balance_bank') ring-2 ring-red-500 @enderror"
                               style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);"
                               placeholder="0.00">
                    </div>
                    @error('closing_balance_bank')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">Saldo akhir dari bank statement</p>
                </div>
            </div>

            <!-- Bank Statement Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    Upload Bank Statement <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-dashed rounded-apple p-6 text-center hover:border-blue-500 transition-colors cursor-pointer @error('bank_statement') border-red-500 @enderror"
                     style="border-color: rgba(235, 235, 245, 0.2); background: rgba(255, 255, 255, 0.02);"
                     id="dropZone">
                    <input type="file" name="bank_statement" id="bankStatementInput" accept=".csv,.xlsx,.xls" required class="hidden">
                    <div id="uploadPrompt">
                        <i class="fas fa-cloud-upload-alt text-4xl mb-3" style="color: rgba(0, 122, 255, 0.6);"></i>
                        <p class="text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.9);">
                            Drag & drop file atau klik untuk browse
                        </p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                            Format: CSV atau Excel (.xlsx, .xls) - Max 5MB
                        </p>
                    </div>
                    <div id="uploadSuccess" class="hidden">
                        <i class="fas fa-file-csv text-4xl mb-3" style="color: rgba(52, 199, 89, 1);"></i>
                        <p class="text-sm font-medium" style="color: rgba(52, 199, 89, 1);" id="fileName"></p>
                        <button type="button" onclick="clearFile()" class="text-xs mt-2" style="color: rgba(235, 235, 245, 0.6);">
                            <i class="fas fa-times"></i> Ganti file
                        </button>
                    </div>
                </div>
                @error('bank_statement')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Format Info -->
            <div class="rounded-apple p-4 mb-6" style="background: rgba(0, 122, 255, 0.1);">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mt-0.5 mr-3" style="color: rgba(0, 122, 255, 1);"></i>
                    <div>
                        <p class="text-sm font-medium mb-2" style="color: rgba(0, 122, 255, 1);">Format Bank Statement CSV</p>
                        <p class="text-xs mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            File CSV harus memiliki kolom berikut (dengan header):
                        </p>
                        <code class="text-xs block p-2 rounded" style="background: rgba(0, 0, 0, 0.2); color: rgba(235, 235, 245, 0.9);">
                            Tanggal, Keterangan, Debet, Kredit, Saldo, Referensi
                        </code>
                        <p class="text-xs mt-2" style="color: rgba(235, 235, 245, 0.7);">
                            <strong>Contoh:</strong> Format BTN, BCA, Mandiri, BRI, BNI
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('reconciliations.index') }}" 
                   class="px-6 py-3 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                   style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 rounded-apple text-sm font-medium shadow-sm transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, rgba(0, 122, 255, 1) 0%, rgba(10, 132, 255, 1) 100%); color: white;">
                    <i class="fas fa-check mr-2"></i>
                    Mulai Rekonsiliasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// File upload handling
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('bankStatementInput');
const uploadPrompt = document.getElementById('uploadPrompt');
const uploadSuccess = document.getElementById('uploadSuccess');
const fileNameDisplay = document.getElementById('fileName');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'rgba(0, 122, 255, 1)';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'rgba(235, 235, 245, 0.2)';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'rgba(235, 235, 245, 0.2)';
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        showFileName(files[0].name);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        showFileName(e.target.files[0].name);
    }
});

function showFileName(name) {
    fileNameDisplay.textContent = name;
    uploadPrompt.classList.add('hidden');
    uploadSuccess.classList.remove('hidden');
}

function clearFile() {
    fileInput.value = '';
    uploadPrompt.classList.remove('hidden');
    uploadSuccess.classList.add('hidden');
}
</script>

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
</div>
@endif
@endsection
