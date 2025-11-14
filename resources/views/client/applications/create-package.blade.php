@extends('client.layouts.app')

@section('title', 'Detail Proyek - BizMark')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', session('permit_selection.kbli_code')) }}" class="hover:text-blue-600">{{ session('permit_selection.kbli_description') }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Detail Proyek</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Informasi Proyek</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Lengkapi detail proyek Anda untuk memproses {{ count(session('permit_selection.permits', [])) }} izin yang dipilih
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('client.applications.store-package') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Bagian 1: Informasi Proyek -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Detail Proyek
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
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('project_location') }}</textarea>
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
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
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
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">{{ old('project_description') }}</textarea>
                    @error('project_description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Bagian 2: Review Izin yang Dipilih -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-clipboard-check text-green-600 mr-2"></i>
                Izin yang Dipilih
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-handshake text-2xl text-blue-600 dark:text-blue-400"></i>
                        <div>
                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $bizmarkCount }}</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300">BizMark.ID</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                        <div>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $ownedCount }}</p>
                            <p class="text-xs text-green-700 dark:text-green-300">Sudah Ada</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-user-check text-2xl text-purple-600 dark:text-purple-400"></i>
                        <div>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $selfCount }}</p>
                            <p class="text-xs text-purple-700 dark:text-purple-300">Dikerjakan Sendiri</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permit List -->
            <div class="space-y-3">
                @forelse($permits as $index => $permit)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 
                            @if($permit['service_type'] === 'bizmark') bg-blue-50/50 dark:bg-blue-900/10
                            @elseif($permit['service_type'] === 'owned') bg-green-50/50 dark:bg-green-900/10
                            @else bg-purple-50/50 dark:bg-purple-900/10
                            @endif">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                                    {{ $permit['name'] }}
                                </h3>
                                <span class="text-xs px-2 py-0.5 rounded-full 
                                    @if($permit['type'] === 'mandatory') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                    @else bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                    @endif">
                                    {{ $permit['type'] === 'mandatory' ? 'Wajib' : 'Opsional' }}
                                </span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-600 dark:text-gray-400">
                                @if(!empty($permit['category']))
                                <span>
                                    <i class="fas fa-folder mr-1"></i>{{ $permit['category'] }}
                                </span>
                                @endif
                                
                                @if($permit['service_type'] === 'bizmark')
                                    @if(!empty($permit['estimated_days']))
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>~{{ $permit['estimated_days'] }} hari
                                    </span>
                                    @endif
                                    
                                    @if(!empty($permit['estimated_cost_min']) && !empty($permit['estimated_cost_max']))
                                    <span>
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        Rp {{ number_format($permit['estimated_cost_min'], 0, ',', '.') }} - 
                                        Rp {{ number_format($permit['estimated_cost_max'], 0, ',', '.') }}
                                    </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <span class="flex-shrink-0 px-3 py-1 text-xs font-semibold rounded-full
                            @if($permit['service_type'] === 'bizmark') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                            @elseif($permit['service_type'] === 'owned') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                            @else bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                            @endif">
                            @if($permit['service_type'] === 'bizmark')
                                <i class="fas fa-handshake mr-1"></i>BizMark.ID
                            @elseif($permit['service_type'] === 'owned')
                                <i class="fas fa-check-circle mr-1"></i>Sudah Ada
                            @else
                                <i class="fas fa-user-check mr-1"></i>Dikerjakan Sendiri
                            @endif
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>Tidak ada izin yang dipilih</p>
                </div>
                @endforelse
            </div>

            @if($bizmarkCount > 0)
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>{{ $bizmarkCount }} izin</strong> akan dikelola oleh tim BizMark.ID. Anda akan menerima quotation untuk paket izin ini.
                </p>
            </div>
            @endif
        </div>

        <!-- Bagian 3: Dokumen Pendukung (Optional) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-paperclip text-gray-600 mr-2"></i>
                Dokumen Pendukung (Opsional)
            </h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload Dokumen
                </label>
                <input type="file" 
                       name="supporting_documents[]" 
                       multiple
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Format yang didukung: PDF, JPG, PNG, DOC, DOCX (Maks. 10MB per file)
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('client.applications.create', ['kbli_code' => session('permit_selection.kbli_code')]) }}" 
               class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>

            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors inline-flex items-center gap-2 font-semibold">
                <i class="fas fa-paper-plane"></i>
                Ajukan Permohonan Paket Izin
            </button>
        </div>
    </form>
</div>
@endsection
