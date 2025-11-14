@extends('client.layouts.app')

@section('title', 'Ajukan Permohonan - ' . $permitType->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', $permitType->code) }}" class="hover:text-blue-600">{{ $permitType->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Ajukan Permohonan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $permitType->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $permitType->description }}</p>
                <div class="mt-3 flex flex-wrap gap-4 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        Estimasi: Rp {{ number_format($permitType->estimated_cost_min, 0, ',', '.') }} - Rp {{ number_format($permitType->estimated_cost_max, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        <i class="fas fa-clock mr-1"></i>
                        Proses: {{ $permitType->avg_processing_days }} hari kerja
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('client.applications.store') }}" id="applicationForm">
        @csrf
        <input type="hidden" name="permit_type_id" value="{{ $permitType->id }}">

        <!-- Company Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-building text-blue-600 mr-2"></i>
                Informasi Perusahaan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Company Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[company_name]" 
                           value="{{ old('form_data.company_name', $draft->form_data['company_name'] ?? auth('client')->user()->company_name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alamat Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="form_data[company_address]" 
                              rows="3"
                              required
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('form_data.company_address', $draft->form_data['company_address'] ?? auth('client')->user()->address) }}</textarea>
                    @error('form_data.company_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        NPWP Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[company_npwp]" 
                           value="{{ old('form_data.company_npwp', $draft->form_data['company_npwp'] ?? '') }}"
                           placeholder="00.000.000.0-000.000"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_npwp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="form_data[company_phone]" 
                           value="{{ old('form_data.company_phone', $draft->form_data['company_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- PIC Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                Penanggung Jawab (PIC)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- PIC Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[pic_name]" 
                           value="{{ old('form_data.pic_name', $draft->form_data['pic_name'] ?? auth('client')->user()->name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[pic_position]" 
                           value="{{ old('form_data.pic_position', $draft->form_data['pic_position'] ?? '') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="form_data[pic_email]" 
                           value="{{ old('form_data.pic_email', $draft->form_data['pic_email'] ?? auth('client')->user()->email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="form_data[pic_phone]" 
                           value="{{ old('form_data.pic_phone', $draft->form_data['pic_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                Catatan Tambahan (Opsional)
            </h2>
            <textarea name="form_data[notes]" 
                      rows="4"
                      placeholder="Tambahkan catatan atau informasi khusus terkait permohonan Anda..."
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('form_data.notes', $draft->form_data['notes'] ?? '') }}</textarea>
        </div>

        <!-- Form Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex flex-col md:flex-row gap-3 justify-end">
                <a href="{{ route('client.services.show', $permitType->code) }}" 
                   class="text-center px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <button type="submit" 
                        name="save_as_draft" 
                        value="1"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>
                    Simpan sebagai Draft
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Lanjutkan ke Upload Dokumen
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
