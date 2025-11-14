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

                <!-- KBLI Code with Autocomplete -->
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
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        
                        <!-- Hidden inputs for actual data -->
                        <input type="hidden" name="kbli_code" id="kbli_code" value="{{ old('kbli_code', $draft->kbli_code ?? '') }}">
                        <input type="hidden" name="kbli_description" id="kbli_description" value="{{ old('kbli_description', $draft->kbli_description ?? '') }}">
                        <input type="hidden" name="kbli_category" id="kbli_category" value="{{ old('kbli_category', $draft->kbli_category ?? '') }}">
                        
                        <!-- Autocomplete dropdown -->
                        <div id="kbli_dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>

                        <!-- Loading indicator -->
                        <div id="kbli_loading" class="hidden absolute right-3 top-3">
                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                        </div>
                    </div>

                    <!-- Selected KBLI display -->
                    <div id="kbli_selected" class="hidden mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-blue-900 dark:text-blue-300" id="selected_code"></span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" id="selected_category_badge"></span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300" id="selected_description"></p>
                            </div>
                            <button type="button" onclick="clearKBLI()" class="ml-2 text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        KBLI membantu kami memahami bidang usaha Anda sesuai dengan standar OSS (Online Single Submission)
                    </p>
                    @error('kbli_code')
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

<script>
// KBLI Autocomplete
let kbliSearchTimeout = null;
let selectedKBLI = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const kbliCode = document.getElementById('kbli_code').value;
    if (kbliCode) {
        // Load existing KBLI data
        showSelectedKBLI({
            code: document.getElementById('kbli_code').value,
            description: document.getElementById('kbli_description').value,
            category: document.getElementById('kbli_category').value
        });
    }
});

document.getElementById('kbli_search').addEventListener('input', function(e) {
    const keyword = e.target.value.trim();
    
    // Clear previous timeout
    if (kbliSearchTimeout) {
        clearTimeout(kbliSearchTimeout);
    }
    
    // Hide dropdown if less than 2 characters
    if (keyword.length < 2) {
        document.getElementById('kbli_dropdown').classList.add('hidden');
        return;
    }
    
    // Show loading
    document.getElementById('kbli_loading').classList.remove('hidden');
    
    // Debounce search
    kbliSearchTimeout = setTimeout(() => {
        searchKBLI(keyword);
    }, 300);
});

function searchKBLI(keyword) {
    fetch(`/api/kbli/search?q=${encodeURIComponent(keyword)}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('kbli_loading').classList.add('hidden');
            
            if (data.success && data.data.length > 0) {
                displayKBLIResults(data.data);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('kbli_loading').classList.add('hidden');
            displayNoResults();
        });
}

function displayKBLIResults(results) {
    const dropdown = document.getElementById('kbli_dropdown');
    let html = '<div class="py-2">';
    
    results.forEach(item => {
        const categoryColor = getCategoryColor(item.category);
        html += `
            <button type="button" 
                    onclick='selectKBLI(${JSON.stringify(item)})' 
                    class="w-full px-4 py-3 text-left hover:bg-gray-100 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">${item.code}</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full ${categoryColor}">${item.category}</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">${item.description}</p>
                        <p class="text-xs text-gray-500 mt-1">Sektor: ${item.sector}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs mt-1"></i>
                </div>
            </button>
        `;
    });
    
    html += '</div>';
    dropdown.innerHTML = html;
    dropdown.classList.remove('hidden');
}

function displayNoResults() {
    const dropdown = document.getElementById('kbli_dropdown');
    dropdown.innerHTML = `
        <div class="p-4 text-center text-gray-500">
            <i class="fas fa-search mb-2 text-2xl"></i>
            <p class="text-sm">Tidak ada hasil ditemukan</p>
        </div>
    `;
    dropdown.classList.remove('hidden');
}

function selectKBLI(kbli) {
    selectedKBLI = kbli;
    
    // Set hidden inputs
    document.getElementById('kbli_code').value = kbli.code;
    document.getElementById('kbli_description').value = kbli.description;
    document.getElementById('kbli_category').value = kbli.category;
    
    // Clear search input
    document.getElementById('kbli_search').value = '';
    
    // Hide dropdown
    document.getElementById('kbli_dropdown').classList.add('hidden');
    
    // Show selected KBLI
    showSelectedKBLI(kbli);
}

function showSelectedKBLI(kbli) {
    const selectedDiv = document.getElementById('kbli_selected');
    const categoryColor = getCategoryColor(kbli.category);
    
    document.getElementById('selected_code').textContent = kbli.code;
    document.getElementById('selected_description').textContent = kbli.description;
    
    const categoryBadge = document.getElementById('selected_category_badge');
    categoryBadge.textContent = kbli.category;
    categoryBadge.className = `px-2 py-0.5 text-xs font-semibold rounded-full ${categoryColor}`;
    
    selectedDiv.classList.remove('hidden');
}

function clearKBLI() {
    selectedKBLI = null;
    document.getElementById('kbli_code').value = '';
    document.getElementById('kbli_description').value = '';
    document.getElementById('kbli_category').value = '';
    document.getElementById('kbli_selected').classList.add('hidden');
    document.getElementById('kbli_search').value = '';
    document.getElementById('kbli_search').focus();
}

function getCategoryColor(category) {
    const colors = {
        'Rendah': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'Menengah Rendah': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Menengah Tinggi': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'Tinggi': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    };
    return colors[category] || 'bg-gray-100 text-gray-800';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('kbli_dropdown');
    const searchInput = document.getElementById('kbli_search');
    
    if (!dropdown.contains(e.target) && e.target !== searchInput) {
        dropdown.classList.add('hidden');
    }
});
</script>

@endsection
