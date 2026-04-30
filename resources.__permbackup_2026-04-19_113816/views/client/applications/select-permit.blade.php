@extends('client.layouts.app')

@section('title', 'Pilih Jenis Izin - BizMark')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Mobile: Back Button Only -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('client.services.show', $kbli->code) }}" 
           class="inline-flex items-center text-[#0a66c2] hover:text-[#004182] font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Desktop: Breadcrumb -->
    <nav class="hidden sm:block mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-[#0a66c2]">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs flex-shrink-0"></i></li>
            <li><a href="{{ route('client.services.show', $kbli->code) }}" class="hover:text-[#0a66c2] max-w-md truncate inline-block">{{ $kbli->description }}</a></li>
            <li><i class="fas fa-chevron-right text-xs flex-shrink-0"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Pilih Jenis Izin</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-[#0a66c2] text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1">Pilih Izin yang Akan Diajukan</h1>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                    KBLI {{ $kbli->code }}: {{ $kbli->description }}
                </p>
                <p class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-start gap-1">
                    <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                    <span>Pilih izin dan tentukan mana yang akan dikerjakan oleh BizMark.ID, sudah dimiliki, atau dikerjakan sendiri.</span>
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('client.applications.select-permits') }}" method="POST" x-data="permitSelection()">
        @csrf
        <input type="hidden" name="kbli_code" value="{{ $kbli->code }}">
        <input type="hidden" name="kbli_description" value="{{ $kbli->description }}">

    @if($recommendation && !empty($recommendation->recommended_permits))
    <!-- Recommended Permits from Analysis -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-star text-amber-500 flex-shrink-0"></i>
                <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Izin yang Direkomendasikan</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">({{ count($recommendation->recommended_permits) }} izin)</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="selectAll()" class="text-xs text-[#0a66c2] hover:text-[#004182] hover:underline">
                    <i class="fas fa-check-square mr-1"></i>Pilih Semua
                </button>
                <button type="button" @click="deselectAll()" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                    <i class="fas fa-square mr-1"></i>Hapus Semua
                </button>
            </div>
        </div>
        
        <div class="space-y-3">
            @foreach($recommendation->recommended_permits as $index => $permit)
                <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 p-3 sm:p-4 transition-all"
                     :class="selectedPermits.includes({{ $index }}) ? 'border-[#0a66c2] bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10' : ''">
                    <div class="flex items-start gap-2 sm:gap-3">
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" 
                                   :checked="selectedPermits.includes({{ $index }})"
                                   @change="togglePermit({{ $index }})"
                                   class="w-5 h-5 text-[#0a66c2] border-gray-300 rounded focus:ring-[#0a66c2]">
                        </div>

                        <!-- Permit Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-xs sm:text-sm mb-1">
                                        {{ $permit['name'] }}
                                        @if(($permit['type'] ?? '') === 'mandatory')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-[10px] sm:text-xs font-semibold rounded ml-1 sm:ml-2">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            WAJIB
                                        </span>
                                        @endif
                                    </h3>
                                    <p class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 mb-2">
                                        {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-money-bill-wave flex-shrink-0"></i>
                                            <span class="truncate">
                                            @if(isset($permit['estimated_cost_range']))
                                                Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                            @else
                                                N/A
                                            @endif
                                            </span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-clock flex-shrink-0"></i>
                                            {{ $permit['estimated_days'] ?? 'N/A' }} hari
                                        </span>
                                        @if(!empty($permit['issuing_authority']))
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-building flex-shrink-0"></i>
                                            <span class="truncate">{{ $permit['issuing_authority'] }}</span>
                                        </span>
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
                                <p class="text-[10px] sm:text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih layanan:</p>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <label class="inline-flex items-start cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="bizmark"
                                               :checked="getServiceType({{ $index }}) === 'bizmark'"
                                               @change="setServiceType({{ $index }}, 'bizmark')"
                                               class="w-4 h-4 text-[#0a66c2] border-gray-300 focus:ring-[#0a66c2] flex-shrink-0 mt-0.5">
                                        <span class="ml-2 text-[10px] sm:text-xs text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-check-circle text-[#0a66c2]"></i> <strong>BizMark.ID</strong> (Kami bantu urus)
                                        </span>
                                    </label>
                                    <label class="inline-flex items-start cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="owned"
                                               :checked="getServiceType({{ $index }}) === 'owned'"
                                               @change="setServiceType({{ $index }}, 'owned')"
                                               class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500 flex-shrink-0 mt-0.5">
                                        <span class="ml-2 text-[10px] sm:text-xs text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-check text-emerald-600"></i> Sudah Ada
                                        </span>
                                    </label>
                                    <label class="inline-flex items-start cursor-pointer">
                                        <input type="radio" 
                                               :name="'permits[' + {{ $index }} + '][service_type]'"
                                               value="self"
                                               :checked="getServiceType({{ $index }}) === 'self'"
                                               @change="setServiceType({{ $index }}, 'self')"
                                               class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500 flex-shrink-0 mt-0.5">
                                        <span class="ml-2 text-[10px] sm:text-xs text-gray-700 dark:text-gray-300">
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
    <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
        <p class="text-xs sm:text-sm text-amber-800 dark:text-amber-200 flex items-start gap-2">
            <i class="fas fa-exclamation-triangle flex-shrink-0 mt-0.5"></i>
            <span>Belum ada rekomendasi izin untuk KBLI ini. Silakan hubungi admin untuk mendapatkan analisis.</span>
        </p>
    </div>
    @endif

    <!-- Summary & Submit -->
    <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow-lg mt-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    <span x-text="selectedPermits.length"></span> izin dipilih
                </p>
                <p class="text-[10px] sm:text-xs text-gray-600 dark:text-gray-400 mt-1">
                    <span x-text="countByServiceType('bizmark')"></span> BizMark.ID • 
                    <span x-text="countByServiceType('owned')"></span> Sudah Ada • 
                    <span x-text="countByServiceType('self')"></span> Dikerjakan Sendiri
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('client.services.show', $kbli->code) }}" 
                   class="order-2 sm:order-1 text-center px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs sm:text-sm rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <button type="submit" 
                        :disabled="selectedPermits.length === 0"
                        :class="selectedPermits.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#004182]'"
                        class="order-1 sm:order-2 px-6 py-2.5 bg-[#0a66c2] text-white text-xs sm:text-sm rounded-xl transition-colors inline-flex items-center justify-center gap-2 font-medium shadow-sm">
                    <i class="fas fa-paper-plane"></i>
                    <span class="hidden sm:inline">Lanjutkan (<span x-text="selectedPermits.length"></span> izin)</span>
                    <span class="sm:hidden">Lanjutkan (<span x-text="selectedPermits.length"></span>)</span>
                </button>
            </div>
        </div>
    </div>

    </form>

    <!-- Help Section -->
    <div class="mt-6 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/20 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl p-4">
        <div class="flex flex-col sm:flex-row items-start gap-3">
            <i class="fas fa-question-circle text-[#0a66c2] text-lg mt-0.5 flex-shrink-0"></i>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Butuh Bantuan?</h3>
                <p class="text-xs text-gray-700 dark:text-gray-300 mb-2">
                    Tidak yakin izin mana yang harus dipilih? Tim ahli kami siap membantu Anda memilih dan mengurus izin yang tepat.
                </p>
                <a href="{{ route('client.applications.create') }}" 
                   class="inline-flex items-center text-xs text-[#0a66c2] font-semibold hover:text-[#004182] hover:underline">
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
