@extends('client.layouts.app')

@section('title', 'Edit Permohonan - ' . $application->application_number)

@section('content')
@php
    $formData = is_array($application->form_data) ? $application->form_data : (json_decode($application->form_data, true) ?? []);
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Mobile: Back Button Only -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center text-[#0a66c2] hover:text-[#004182] font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Desktop: Breadcrumb -->
    <nav class="hidden sm:block mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.applications.index') }}" class="hover:text-[#0a66c2]">Permohonan Saya</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.applications.show', $application->id) }}" class="hover:text-[#0a66c2]">{{ $application->application_number }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Edit</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-amber-600 dark:text-amber-400 text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-1">
                    Edit Permohonan
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $application->application_number }}
                    @if($permitType)
                        &mdash; {{ $permitType->name }}
                    @endif
                </p>
                <div class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="fas fa-pencil-alt mr-1.5"></i>
                    Draft &mdash; belum diajukan
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Info Box -->
    <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border-l-4 border-[#0a66c2] rounded-r-xl p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-info-circle text-[#0a66c2]"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">Perbarui Data Permohonan Anda</h4>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Anda dapat mengubah data permohonan ini selama masih berstatus <strong>Draft</strong>.
                    Simpan sebagai draft atau langsung ajukan setelah selesai.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('client.applications.update', $application->id) }}" id="applicationForm">
        @csrf
        @method('PUT')

        <!-- Company Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-building text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Informasi Perusahaan</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Company Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="form_data[company_name]"
                           value="{{ old('form_data.company_name', $formData['company_name'] ?? auth('client')->user()->company_name) }}"
                           required
                           autocomplete="organization"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
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
                              autocomplete="street-address"
                              class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">{{ old('form_data.company_address', $formData['company_address'] ?? auth('client')->user()->address) }}</textarea>
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
                           value="{{ old('form_data.company_npwp', $formData['company_npwp'] ?? '') }}"
                           placeholder="00.000.000.0-000.000"
                           required
                           inputmode="numeric"
                           autocomplete="off"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
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
                           value="{{ old('form_data.company_phone', $formData['company_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           inputmode="tel"
                           autocomplete="tel"
                           pattern="[0-9+\s\-\(\)]+"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- KBLI Code -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kode KBLI (Klasifikasi Baku Lapangan Usaha Indonesia)
                        <span class="text-xs text-gray-500 font-normal ml-1">- Opsional</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="kbli_search"
                               placeholder="Ketik untuk mencari KBLI (min. 2 karakter)..."
                               autocomplete="off"
                               class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">

                        <input type="hidden" name="kbli_code" id="kbli_code" value="{{ old('kbli_code', $application->kbli_code ?? '') }}">
                        <input type="hidden" name="kbli_description" id="kbli_description" value="{{ old('kbli_description', $application->kbli_description ?? '') }}">

                        <div id="kbli_dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-60 overflow-y-auto"></div>
                        <div id="kbli_loading" class="hidden absolute right-3 top-3">
                            <i class="fas fa-spinner fa-spin text-[#0a66c2]"></i>
                        </div>
                    </div>

                    <div id="kbli_selected" class="hidden mt-2 p-2.5 sm:p-3 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/20 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs sm:text-sm font-bold text-[#0a66c2]" id="selected_code"></span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300" id="selected_description"></p>
                            </div>
                            <button type="button" onclick="clearKBLI()" class="ml-2 text-red-600 hover:text-red-800 flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        KBLI membantu kami memahami bidang usaha Anda sesuai dengan standar OSS
                    </p>
                </div>
            </div>
        </div>

        <!-- PIC Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Penanggung Jawab (PIC)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- PIC Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="form_data[pic_name]"
                           value="{{ old('form_data.pic_name', $formData['pic_name'] ?? auth('client')->user()->name) }}"
                           required
                           autocomplete="name"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
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
                           value="{{ old('form_data.pic_position', $formData['pic_position'] ?? '') }}"
                           required
                           autocomplete="organization-title"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
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
                           value="{{ old('form_data.pic_email', $formData['pic_email'] ?? auth('client')->user()->email) }}"
                           required
                           inputmode="email"
                           autocomplete="email"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
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
                           value="{{ old('form_data.pic_phone', $formData['pic_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           inputmode="tel"
                           autocomplete="tel"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-sticky-note text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Catatan Tambahan (Opsional)</span>
            </h2>
            <textarea name="form_data[notes]"
                      rows="4"
                      placeholder="Tambahkan catatan atau informasi khusus terkait permohonan Anda..."
                      class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2] dark:bg-gray-700 dark:text-white transition-colors">{{ old('form_data.notes', $formData['notes'] ?? '') }}</textarea>
        </div>

        <!-- Form Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 sticky bottom-0 sm:static">
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                <a href="{{ route('client.applications.show', $application->id) }}"
                   class="order-3 sm:order-1 text-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit"
                        name="save_as_draft"
                        value="1"
                        class="order-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors font-medium shadow-sm">
                    <i class="fas fa-save mr-2"></i>
                    Simpan sebagai Draft
                </button>
                <button type="submit"
                        class="order-1 sm:order-3 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition-colors font-medium shadow-sm">
                    <i class="fas fa-paper-plane mr-2"></i>
                    <span class="hidden sm:inline">Simpan &amp; Ajukan Permohonan</span>
                    <span class="sm:hidden">Ajukan</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let kbliSearchTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    const kbliCode = document.getElementById('kbli_code').value;
    if (kbliCode) {
        showSelectedKBLI({
            code: kbliCode,
            description: document.getElementById('kbli_description').value
        });
    }
});

document.getElementById('kbli_search').addEventListener('input', function(e) {
    const keyword = e.target.value.trim();
    if (kbliSearchTimeout) clearTimeout(kbliSearchTimeout);
    if (keyword.length < 2) {
        document.getElementById('kbli_dropdown').classList.add('hidden');
        return;
    }
    document.getElementById('kbli_loading').classList.remove('hidden');
    kbliSearchTimeout = setTimeout(() => searchKBLI(keyword), 300);
});

function searchKBLI(keyword) {
    fetch(`/api/kbli/search?q=${encodeURIComponent(keyword)}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('kbli_loading').classList.add('hidden');
            data.success && data.data.length > 0 ? displayKBLIResults(data.data) : displayNoResults();
        })
        .catch(() => {
            document.getElementById('kbli_loading').classList.add('hidden');
            displayNoResults();
        });
}

function displayKBLIResults(results) {
    const dropdown = document.getElementById('kbli_dropdown');
    let html = '<div class="py-1 sm:py-2">';
    results.forEach(item => {
        html += `<button type="button" onclick='selectKBLI(${JSON.stringify(item)})'
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-left hover:bg-gray-100 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <span class="text-xs font-bold text-[#0a66c2]">${item.code}</span>
                    <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2 mt-0.5">${item.description}</p>
                    <p class="text-[10px] text-gray-500 mt-1">Sektor: ${item.sector}</p>
                </div>
                <i class="fas fa-chevron-right text-gray-400 text-xs mt-1 flex-shrink-0"></i>
            </div></button>`;
    });
    html += '</div>';
    dropdown.innerHTML = html;
    dropdown.classList.remove('hidden');
}

function displayNoResults() {
    const dropdown = document.getElementById('kbli_dropdown');
    dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">Tidak ditemukan hasil</div>';
    dropdown.classList.remove('hidden');
}

function selectKBLI(item) {
    document.getElementById('kbli_code').value = item.code;
    document.getElementById('kbli_description').value = item.description;
    showSelectedKBLI(item);
    document.getElementById('kbli_dropdown').classList.add('hidden');
    document.getElementById('kbli_search').value = '';
}

function showSelectedKBLI(item) {
    document.getElementById('selected_code').textContent = item.code;
    document.getElementById('selected_description').textContent = item.description;
    document.getElementById('kbli_selected').classList.remove('hidden');
}

function clearKBLI() {
    document.getElementById('kbli_code').value = '';
    document.getElementById('kbli_description').value = '';
    document.getElementById('kbli_search').value = '';
    document.getElementById('kbli_selected').classList.add('hidden');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#kbli_search') && !e.target.closest('#kbli_dropdown')) {
        document.getElementById('kbli_dropdown').classList.add('hidden');
    }
});
</script>
@endsection
