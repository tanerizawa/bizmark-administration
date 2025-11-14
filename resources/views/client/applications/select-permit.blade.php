@extends('client.layouts.app')

@section('title', 'Pilih Jenis Izin - BizMark')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', $kbli->code) }}" class="hover:text-blue-600">{{ $kbli->description }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Pilih Jenis Izin</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Pilih Izin yang Akan Diajukan</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    KBLI {{ $kbli->code }}: {{ $kbli->description }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pilih izin dan tentukan mana yang akan dikerjakan oleh BizMark.ID, sudah dimiliki, atau dikerjakan sendiri.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('client.applications.store-multiple') }}" method="POST" x-data="permitSelection()">
        @csrf
        <input type="hidden" name="kbli_code" value="{{ $kbli->code }}">
        <input type="hidden" name="kbli_description" value="{{ $kbli->description }}">

    @if($recommendation && !empty($recommendation->recommended_permits))
    <!-- Recommended Permits from Analysis -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-star text-yellow-500"></i>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Izin yang Direkomendasikan</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">({{ count($recommendation->recommended_permits) }} izin)</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="selectAll()" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    <i class="fas fa-check-square mr-1"></i>Pilih Semua
                </button>
                <button type="button" @click="deselectAll()" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                    <i class="fas fa-square mr-1"></i>Hapus Semua
                </button>
            </div>
        </div>
        
        <div class="space-y-3">
            @foreach($recommendation->recommended_permits as $index => $permit)
                <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-700 p-4 transition-all"
                     :class="selectedPermits.includes({{ $index }}) ? 'border-blue-400 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/10' : ''">
                    <div class="flex items-start gap-3">
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" 
                                   :checked="selectedPermits.includes({{ $index }})"
                                   @change="togglePermit({{ $index }})"
                                   class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>

                        <!-- Permit Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">
                                        {{ $permit['name'] }}
                                        @if(($permit['type'] ?? '') === 'mandatory')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded ml-2">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                                            WAJIB
                                        </span>
                                        @endif
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                                    </p>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span><i class="fas fa-money-bill-wave mr-1"></i>
                                            @if(isset($permit['estimated_cost_range']))
                                                Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                        <span><i class="fas fa-clock mr-1"></i>{{ $permit['estimated_days'] ?? 'N/A' }} hari</span>
                                        @if(!empty($permit['issuing_authority']))
                                        <span><i class="fas fa-building mr-1"></i>{{ $permit['issuing_authority'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Service Options -->
                            <div x-show="selectedPermits.includes({{ $index }})" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih layanan:</p>
                                <div class="flex flex-wrap gap-2">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="bizmark"
                                               :checked="getServiceType({{ $index }}) === 'bizmark'"
                                               @change="setServiceType({{ $index }}, 'bizmark')"
                                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-check-circle text-blue-600"></i> <strong>BizMark.ID</strong> (Kami bantu urus)
                                        </span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="owned"
                                               :checked="getServiceType({{ $index }}) === 'owned'"
                                               @change="setServiceType({{ $index }}, 'owned')"
                                               class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-check text-green-600"></i> Sudah Ada
                                        </span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="self"
                                               :checked="getServiceType({{ $index }}) === 'self'"
                                               @change="setServiceType({{ $index }}, 'self')"
                                               class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                        <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-user text-gray-600"></i> Dikerjakan Sendiri
                                        </span>
                                    </label>
                                </div>
                                
                                <!-- Hidden inputs for form submission -->
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][name]'" value="{{ $permit['name'] }}">
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][type]'" value="{{ $permit['type'] ?? 'mandatory' }}">
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][category]'" value="{{ $permit['category'] ?? 'general' }}">
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][estimated_cost_min]'" value="{{ $permit['estimated_cost_range']['min'] ?? 0 }}">
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][estimated_cost_max]'" value="{{ $permit['estimated_cost_range']['max'] ?? 0 }}">
                                <input type="hidden" :name="'permits[' + {{ $index }} + '][estimated_days]'" value="{{ $permit['estimated_days'] ?? 0 }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- No recommendations -->
    <div class="mb-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
        <p class="text-sm text-yellow-800 dark:text-yellow-200">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Belum ada rekomendasi izin untuk KBLI ini. Silakan hubungi admin untuk mendapatkan analisis.
        </p>
    </div>
    @endif

    <!-- Summary & Submit -->
    <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 rounded-lg shadow-lg mt-4">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    <span x-text="selectedPermits.length"></span> izin dipilih
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    <span x-text="countByServiceType('bizmark')"></span> BizMark.ID • 
                    <span x-text="countByServiceType('owned')"></span> Sudah Ada • 
                    <span x-text="countByServiceType('self')"></span> Dikerjakan Sendiri
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('client.services.show', $kbli->code) }}" 
                   class="px-4 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" 
                        :disabled="selectedPermits.length === 0"
                        :class="selectedPermits.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                        class="px-6 py-2.5 bg-blue-600 text-white text-sm rounded-lg transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Lanjutkan (<span x-text="selectedPermits.length"></span> izin)
                </button>
            </div>
        </div>
    </div>

    </form>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 dark:text-blue-100 text-sm mb-1">Butuh Bantuan?</h3>
                <p class="text-xs text-blue-800 dark:text-blue-200 mb-2">
                    Tidak yakin izin mana yang harus dipilih? Tim ahli kami siap membantu Anda memilih dan mengurus izin yang tepat.
                </p>
                <a href="#" onclick="alert('Fitur konsultasi akan segera hadir. Silakan hubungi admin untuk bantuan.'); return false;" 
                   class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                    <i class="fas fa-comments mr-1"></i>
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function permitSelection() {
    return {
        selectedPermits: [],
        serviceTypes: {},
        
        init() {
            // Default: select all and set to 'bizmark'
            @if($recommendation && !empty($recommendation->recommended_permits))
            @foreach($recommendation->recommended_permits as $index => $permit)
            this.selectedPermits.push({{ $index }});
            this.serviceTypes[{{ $index }}] = 'bizmark';
            @endforeach
            @endif
        },
        
        togglePermit(index) {
            const idx = this.selectedPermits.indexOf(index);
            if (idx > -1) {
                this.selectedPermits.splice(idx, 1);
                delete this.serviceTypes[index];
            } else {
                this.selectedPermits.push(index);
                this.serviceTypes[index] = 'bizmark'; // Default to bizmark
            }
        },
        
        selectAll() {
            @if($recommendation && !empty($recommendation->recommended_permits))
            @foreach($recommendation->recommended_permits as $index => $permit)
            if (!this.selectedPermits.includes({{ $index }})) {
                this.selectedPermits.push({{ $index }});
                this.serviceTypes[{{ $index }}] = 'bizmark';
            }
            @endforeach
            @endif
        },
        
        deselectAll() {
            this.selectedPermits = [];
            this.serviceTypes = {};
        },
        
        setServiceType(index, type) {
            this.serviceTypes[index] = type;
        },
        
        getServiceType(index) {
            return this.serviceTypes[index] || 'bizmark';
        },
        
        countByServiceType(type) {
            return Object.values(this.serviceTypes).filter(t => t === type).length;
        }
    }
}
</script>
@endpush
@endsection
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', $kbli->code) }}" class="hover:text-blue-600">{{ $kbli->description }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Pilih Jenis Izin</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Pilih Jenis Izin yang Akan Diajukan</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    KBLI {{ $kbli->code }}: {{ $kbli->description }}
                </p>
            </div>
        </div>
    </div>

    @if($recommendation && !empty($recommendation->recommended_permits))
    <!-- Recommended Permits from AI Analysis -->
    <div class="mb-4">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-star text-yellow-500"></i>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Izin yang Direkomendasikan</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">(Berdasarkan analisis KBLI Anda)</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($recommendation->recommended_permits as $permit)
                @php
                    // Try to match with actual permit types
                    $matchedPermitType = $permitTypes->first(function($pt) use ($permit) {
                        return stripos($pt->name, $permit['name']) !== false 
                            || stripos($permit['name'], $pt->name) !== false;
                    });
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-lg border-2 {{ $matchedPermitType ? 'border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 cursor-pointer' : 'border-gray-200 dark:border-gray-700' }} p-4 transition-all">
                    @if($matchedPermitType)
                    <a href="{{ route('client.applications.create', ['permit_type' => $matchedPermitType->id, 'kbli_code' => $kbli->code]) }}" class="block">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-certificate text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ $permit['name'] }}
                                    </h3>
                                    @if(($permit['type'] ?? '') === 'mandatory')
                                    <span class="inline-flex items-center px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded flex-shrink-0">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                        WAJIB
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span><i class="fas fa-money-bill-wave mr-1"></i>
                                        @if(isset($permit['estimated_cost_range']))
                                            Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $permit['estimated_days'] ?? 'N/A' }} hari</span>
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 font-medium">
                                        Ajukan Sekarang <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @else
                    <!-- No matching permit type in system -->
                    <div class="flex items-start gap-3 opacity-60">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-certificate text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-1">
                                {{ $permit['name'] }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                            </p>
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                <i class="fas fa-info-circle mr-1"></i>
                                Hubungi admin untuk izin ini
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- All Available Permit Types -->
    <div>
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-list text-gray-600 dark:text-gray-400"></i>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Semua Jenis Izin Tersedia</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($permitTypes as $permitType)
            <a href="{{ route('client.applications.create', ['permit_type' => $permitType->id, 'kbli_code' => $kbli->code]) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-600 p-4 transition-all group">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-file-alt text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $permitType->name }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">
                            {{ $permitType->description }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span><i class="fas fa-clock mr-1"></i>{{ $permitType->avg_processing_days }} hari</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 dark:text-blue-100 text-sm mb-1">Butuh Bantuan?</h3>
                <p class="text-xs text-blue-800 dark:text-blue-200 mb-2">
                    Tidak yakin izin mana yang harus diajukan? Tim ahli kami siap membantu Anda memilih izin yang tepat.
                </p>
                <a href="#" onclick="alert('Fitur konsultasi akan segera hadir. Silakan hubungi admin untuk bantuan.'); return false;" 
                   class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                    <i class="fas fa-comments mr-1"></i>
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
