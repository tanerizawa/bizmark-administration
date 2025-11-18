@extends('client.layouts.app')

@section('title', 'Detail Proyek - BizMark')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Mobile: Back Button Only -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('client.services.show', session('permit_selection.kbli_code')) }}" 
           class="inline-flex items-center text-[#0a66c2] hover:text-[#004182] font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Desktop: Breadcrumb -->
    <nav class="hidden sm:block mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-[#0a66c2]">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', session('permit_selection.kbli_code')) }}" class="hover:text-[#0a66c2] max-w-sm truncate inline-block">{{ session('permit_selection.kbli_description') }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Detail Proyek</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-[#0a66c2] text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1">Informasi Proyek</h1>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                    Lengkapi detail proyek Anda untuk memproses {{ count(session('permit_selection.permits', [])) }} izin yang dipilih
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('client.applications.store-package') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Bagian 1: Informasi Proyek -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-info-circle text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Detail Proyek</span>
            </h2>

            <div class="space-y-4">
                <!-- Nama Proyek -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Proyek <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="project_name" 
                           value="{{ old('project_name') }}"
                           required
                           placeholder="Contoh: Pembangunan Gedung Kantor PT ABC"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('project_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi Proyek -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Lokasi Proyek <span class="text-red-500">*</span>
                    </label>
                    <textarea name="project_location" 
                              rows="3"
                              required
                              placeholder="Alamat lengkap lokasi proyek (Jalan, Nomor, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi)"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">{{ old('project_location') }}</textarea>
                    @error('project_location')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Row: Luas Tanah & Luas Bangunan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Luas Tanah (m²)
                        </label>
                        <input type="number" 
                               name="land_area" 
                               value="{{ old('land_area') }}"
                               step="0.01"
                               placeholder="500"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        @error('land_area')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Luas Bangunan (m²)
                        </label>
                        <input type="number" 
                               name="building_area" 
                               value="{{ old('building_area') }}"
                               step="0.01"
                               placeholder="300"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        @error('building_area')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Row: Jumlah Lantai & Nilai Investasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jumlah Lantai
                        </label>
                        <input type="number" 
                               name="building_floors" 
                               value="{{ old('building_floors') }}"
                               min="1"
                               placeholder="3"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        @error('building_floors')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nilai Investasi (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="investment_value" 
                               value="{{ old('investment_value') }}"
                               required
                               step="1000000"
                               placeholder="5000000000"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        @error('investment_value')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total perkiraan biaya proyek</p>
                    </div>
                </div>

                <!-- Target Penyelesaian -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Target Penyelesaian
                    </label>
                    <input type="date" 
                           name="target_completion_date" 
                           value="{{ old('target_completion_date') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('target_completion_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Proyek -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Deskripsi Proyek <span class="text-red-500">*</span>
                    </label>
                    <textarea name="project_description" 
                              rows="4"
                              required
                              placeholder="Jelaskan detail proyek Anda, tujuan, dan informasi relevan lainnya..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">{{ old('project_description') }}</textarea>
                    @error('project_description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Bagian 2: Review Izin yang Dipilih -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-clipboard-check text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Izin yang Dipilih</span>
            </h2>

            @php
                $permits = session('permit_selection.permits', []);
                $bizmarkCount = 0;
                $ownedCount = 0;
                $selfCount = 0;
                foreach($permits as $permit) {
                    if($permit['service_type'] === 'bizmark') $bizmarkCount++;
                    elseif($permit['service_type'] === 'owned') $ownedCount++;
                    else $selfCount++;
                }
            @endphp

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-4">
                <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <i class="fas fa-handshake text-xl sm:text-2xl text-[#0a66c2] flex-shrink-0"></i>
                        <div class="min-w-0">
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $bizmarkCount }}</p>
                            <p class="text-[10px] sm:text-xs text-gray-700 dark:text-gray-300 truncate">BizMark.ID</p>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <i class="fas fa-check-circle text-xl sm:text-2xl text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                        <div class="min-w-0">
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $ownedCount }}</p>
                            <p class="text-[10px] sm:text-xs text-gray-700 dark:text-gray-300 truncate">Sudah Ada</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl p-3 sm:p-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <i class="fas fa-user-check text-xl sm:text-2xl text-gray-600 dark:text-gray-400 flex-shrink-0"></i>
                        <div class="min-w-0">
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $selfCount }}</p>
                            <p class="text-[10px] sm:text-xs text-gray-700 dark:text-gray-300 truncate">Dikerjakan Sendiri</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permit List -->
            <div class="space-y-3">
                @forelse($permits as $index => $permit)
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-3 sm:p-4 
                            @if($permit['service_type'] === 'bizmark') bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10
                            @elseif($permit['service_type'] === 'owned') bg-emerald-50 dark:bg-emerald-900/10
                            @else bg-gray-50 dark:bg-gray-700/20
                            @endif">
                    <div class="flex flex-col sm:flex-row items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm">
                                    {{ $permit['name'] }}
                                </h3>
                                <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full flex-shrink-0
                                    @if($permit['type'] === 'mandatory') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                    @else bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-[#0a66c2]/20
                                    @endif">
                                    {{ $permit['type'] === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                </span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-[10px] sm:text-xs text-gray-600 dark:text-gray-400">
                                @if(!empty($permit['category']))
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-folder flex-shrink-0"></i>
                                    <span class="truncate">{{ $permit['category'] }}</span>
                                </span>
                                @endif
                                
                                @if($permit['service_type'] === 'bizmark')
                                    @if(!empty($permit['estimated_days']))
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clock flex-shrink-0"></i>~{{ $permit['estimated_days'] }} hari
                                    </span>
                                    @endif
                                    
                                    @if(!empty($permit['estimated_cost_min']) && !empty($permit['estimated_cost_max']))
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-money-bill-wave flex-shrink-0"></i>
                                        <span class="truncate">
                                        Rp {{ number_format($permit['estimated_cost_min'], 0, ',', '.') }} - 
                                        Rp {{ number_format($permit['estimated_cost_max'], 0, ',', '.') }}
                                        </span>
                                    </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <span class="flex-shrink-0 px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full
                            @if($permit['service_type'] === 'bizmark') bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-[#0a66c2]/20
                            @elseif($permit['service_type'] === 'owned') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                            @endif">
                            @if($permit['service_type'] === 'bizmark')
                                <i class="fas fa-handshake mr-1"></i><span class="hidden sm:inline">BizMark.ID</span><span class="sm:hidden">BizMark</span>
                            @elseif($permit['service_type'] === 'owned')
                                <i class="fas fa-check-circle mr-1"></i>Sudah Ada
                            @else
                                <i class="fas fa-user-check mr-1"></i><span class="hidden sm:inline">Dikerjakan Sendiri</span><span class="sm:hidden">Sendiri</span>
                            @endif
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-2xl sm:text-3xl mb-2"></i>
                    <p class="text-sm">Tidak ada izin yang dipilih</p>
                </div>
                @endforelse
            </div>

            @if($bizmarkCount > 0)
            <div class="mt-4 p-3 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl">
                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 flex items-start gap-2">
                    <i class="fas fa-info-circle text-[#0a66c2] flex-shrink-0 mt-0.5"></i>
                    <span>
                        <strong>{{ $bizmarkCount }} izin</strong> akan dikelola oleh tim BizMark.ID. Anda akan menerima quotation untuk paket izin ini.
                    </span>
                </p>
            </div>
            @endif
        </div>

        <!-- Bagian 3: Dokumen Pendukung (Optional) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-paperclip text-gray-600 mr-2 flex-shrink-0"></i>
                <span>Dokumen Pendukung (Opsional)</span>
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload Dokumen
                </label>
                <input type="file" 
                       name="supporting_documents[]" 
                       multiple
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#0a66c2] file:text-white hover:file:bg-[#004182] file:cursor-pointer">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-start gap-1">
                    <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                    <span>Format yang didukung: PDF, JPG, PNG, DOC, DOCX (Maks. 10MB per file)</span>
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <a href="{{ route('client.applications.create', ['kbli_code' => session('permit_selection.kbli_code')]) }}" 
               class="order-2 sm:order-1 text-center px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors inline-flex items-center justify-center gap-2 font-medium">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <button type="submit" 
                    class="order-1 sm:order-2 px-6 py-3 bg-[#0a66c2] text-white rounded-xl hover:bg-[#004182] transition-colors inline-flex items-center justify-center gap-2 font-semibold shadow-sm">
                <i class="fas fa-paper-plane"></i>
                <span class="hidden sm:inline">Ajukan Permohonan Paket Izin</span>
                <span class="sm:hidden">Ajukan Permohonan</span>
            </button>
        </div>
    </form>
</div>
@endsection
