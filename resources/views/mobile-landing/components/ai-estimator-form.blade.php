<!-- AI Estimator Form (Multi-Step) -->
@php
    $currentLocale = app()->getLocale();
    $services = [
        ['id' => 'nib', 'name_id' => 'NIB (Nomor Induk Berusaha)', 'name_en' => 'NIB (Business Identification Number)'],
        ['id' => 'siup', 'name_id' => 'SIUP (Surat Izin Usaha Perdagangan)', 'name_en' => 'SIUP (Trading Business License)'],
        ['id' => 'pbg', 'name_id' => 'PBG (Persetujuan Bangunan Gedung)', 'name_en' => 'PBG (Building Approval)'],
        ['id' => 'ukl-upl', 'name_id' => 'UKL-UPL (Lingkungan)', 'name_en' => 'UKL-UPL (Environmental)'],
        ['id' => 'sertifikat-halal', 'name_id' => 'Sertifikat Halal', 'name_en' => 'Halal Certificate'],
    ];
    
    $businessTypes = [
        ['id' => 'pt', 'name_id' => 'PT (Perseroan Terbatas)', 'name_en' => 'PT (Limited Company)'],
        ['id' => 'cv', 'name_id' => 'CV (Comanditaire Vennootschap)', 'name_en' => 'CV (Limited Partnership)'],
        ['id' => 'ud', 'name_id' => 'UD (Usaha Dagang)', 'name_en' => 'UD (Trading Business)'],
        ['id' => 'koperasi', 'name_id' => 'Koperasi', 'name_en' => 'Cooperative'],
        ['id' => 'yayasan', 'name_id' => 'Yayasan', 'name_en' => 'Foundation'],
    ];
@endphp

<div x-data="aiEstimatorForm()">
    <!-- Step 1: Service Selection -->
    <div x-show="$parent.currentStep === 1" class="step-content">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('mobile.ai_estimator.service_type') }}
        </h4>
        
        <div class="space-y-3">
            @foreach($services as $service)
            <label class="service-option-card" :class="{ 'selected': formData.service === '{{ $service['id'] }}' }">
                <input type="radio" 
                       name="service" 
                       value="{{ $service['id'] }}" 
                       x-model="formData.service"
                       class="sr-only">
                <div class="flex items-center">
                    <div class="radio-custom">
                        <div class="radio-dot"></div>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">
                            {{ $currentLocale === 'en' ? $service['name_en'] : $service['name_id'] }}
                        </div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>
        
        <button @click="$parent.nextStep()" 
                :disabled="!formData.service"
                class="btn-primary-full mt-6"
                :class="{ 'opacity-50 cursor-not-allowed': !formData.service }">
            {{ __('mobile.ai_estimator.next') }}
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
    
    <!-- Step 2: Project Details -->
    <div x-show="$parent.currentStep === 2" class="step-content">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">
            {{ __('mobile.ai_estimator.step_2_title') }}
        </h4>
        
        <div class="space-y-4">
            <!-- Business Type -->
            <div>
                <label class="form-label">
                    {{ __('mobile.ai_estimator.business_type') }}
                </label>
                <select x-model="formData.businessType" class="form-select">
                    <option value="">{{ __('mobile.ai_estimator.select_business') }}</option>
                    @foreach($businessTypes as $type)
                    <option value="{{ $type['id'] }}">
                        {{ $currentLocale === 'en' ? $type['name_en'] : $type['name_id'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Location -->
            <div>
                <label class="form-label">
                    {{ __('mobile.ai_estimator.location') }}
                </label>
                <select x-model="formData.location" class="form-select">
                    <option value="">{{ __('mobile.ai_estimator.select_location') }}</option>
                    <option value="banten">Banten</option>
                    <option value="jakarta">DKI Jakarta</option>
                    <option value="jabar">Jawa Barat</option>
                    <option value="jateng">Jawa Tengah</option>
                    <option value="jatim">Jawa Timur</option>
                </select>
            </div>
            
            <!-- Urgency -->
            <div>
                <label class="form-label">
                    {{ __('mobile.ai_estimator.urgency') }}
                </label>
                <div class="space-y-2">
                    <label class="urgency-option" :class="{ 'selected': formData.urgency === 'normal' }">
                        <input type="radio" name="urgency" value="normal" x-model="formData.urgency" class="sr-only">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ __('mobile.ai_estimator.normal') }}</div>
                                <div class="text-xs text-gray-500">+ Rp 0</div>
                            </div>
                            <div class="radio-custom">
                                <div class="radio-dot"></div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="urgency-option" :class="{ 'selected': formData.urgency === 'fast' }">
                        <input type="radio" name="urgency" value="fast" x-model="formData.urgency" class="sr-only">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ __('mobile.ai_estimator.fast') }}</div>
                                <div class="text-xs text-gray-500">+ Rp 1.500.000</div>
                            </div>
                            <div class="radio-custom">
                                <div class="radio-dot"></div>
                            </div>
                        </div>
                    </label>
                    
                    <label class="urgency-option" :class="{ 'selected': formData.urgency === 'express' }">
                        <input type="radio" name="urgency" value="express" x-model="formData.urgency" class="sr-only">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ __('mobile.ai_estimator.express') }}</div>
                                <div class="text-xs text-yellow-600 font-semibold">+ Rp 3.000.000</div>
                            </div>
                            <div class="radio-custom">
                                <div class="radio-dot"></div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button @click="$parent.prevStep()" class="btn-secondary-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('mobile.ai_estimator.previous') }}
            </button>
            
            <button @click="calculateEstimate()" 
                    :disabled="!isStep2Valid()"
                    class="btn-primary-full"
                    :class="{ 'opacity-50 cursor-not-allowed': !isStep2Valid() }">
                {{ __('mobile.ai_estimator.calculate') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Step 3: Results -->
    <div x-show="$parent.currentStep === 3" class="step-content">
        <!-- Loading State -->
        <div x-show="isCalculating" class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
                <svg class="animate-spin w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">{{ __('mobile.ai_estimator.calculating') }}</p>
        </div>
        
        <!-- Results -->
        <div x-show="!isCalculating" class="space-y-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900">
                    {{ __('mobile.ai_estimator.estimated_cost') }}
                </h4>
            </div>
            
            <!-- Cost Breakdown -->
            <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('mobile.ai_estimator.base_fee') }}</span>
                    <span class="font-semibold text-gray-900" x-text="formatCurrency(estimate.baseFee)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('mobile.ai_estimator.processing_fee') }}</span>
                    <span class="font-semibold text-gray-900" x-text="formatCurrency(estimate.processingFee)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">{{ __('mobile.ai_estimator.urgency_fee') }}</span>
                    <span class="font-semibold text-gray-900" x-text="formatCurrency(estimate.urgencyFee)"></span>
                </div>
                
                <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                    <span class="font-bold text-gray-900">{{ __('mobile.ai_estimator.total') }}</span>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600" x-text="formatCurrency(estimate.total)"></div>
                    </div>
                </div>
            </div>
            
            <!-- Disclaimer -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-yellow-800">
                        {{ __('mobile.ai_estimator.disclaimer') }}
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                     <a :href="`${waBase}?text=${getWhatsAppMessage()}`"
                   target="_blank"
                   class="btn-primary-full">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    {{ __('mobile.ai_estimator.get_consultation') }}
                </a>
                
                <button @click="resetEstimator()" class="btn-secondary-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('mobile.ai_estimator.start_over') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== Form Styles ===== */
.step-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    font-size: 14px;
    color: #111827;
    background: white;
    transition: all 0.2s;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
}

.form-select:focus {
    outline: none;
    border-color: #0077B5;
    box-shadow: 0 0 0 3px rgba(0, 119, 181, 0.1);
}

/* Service Option Cards */
.service-option-card {
    display: block;
    padding: 16px;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.service-option-card:hover {
    border-color: #0077B5;
    background: #F0F9FF;
}

.service-option-card.selected {
    border-color: #0077B5;
    background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
    box-shadow: 0 4px 12px rgba(0, 119, 181, 0.15);
}

/* Urgency Options */
.urgency-option {
    display: block;
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.urgency-option:hover {
    border-color: #0077B5;
}

.urgency-option.selected {
    border-color: #0077B5;
    background: #F0F9FF;
}

/* Custom Radio */
.radio-custom {
    width: 24px;
    height: 24px;
    border: 2px solid #D1D5DB;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    margin-right: 12px;
}

.selected .radio-custom {
    border-color: #0077B5;
    background: #0077B5;
}

.radio-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
    opacity: 0;
    transition: opacity 0.2s;
}

.selected .radio-dot {
    opacity: 1;
}

/* Buttons */
.btn-primary-full {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 24px;
    background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
    color: white;
    font-weight: 600;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 119, 181, 0.3);
    transition: all 0.3s;
    min-height: 48px;
}

.btn-primary-full:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 119, 181, 0.4);
}

.btn-primary-full:active:not(:disabled) {
    transform: scale(0.98);
}

.btn-secondary-full {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 24px;
    background: white;
    color: #0077B5;
    font-weight: 600;
    border: 2px solid #0077B5;
    border-radius: 12px;
    transition: all 0.3s;
    min-height: 48px;
}

.btn-secondary-full:hover {
    background: #F0F9FF;
}

.btn-secondary-full:active {
    transform: scale(0.98);
}

/* Screen reader only */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
</style>

<script>
function aiEstimatorForm() {
    return {
        waBase: @js(config('landing_metrics.contact.whatsapp_link')),
        formData: {
            service: '',
            businessType: '',
            location: '',
            urgency: 'normal'
        },
        estimate: {
            baseFee: 0,
            processingFee: 0,
            urgencyFee: 0,
            total: 0
        },
        isCalculating: false,
        
        isStep2Valid() {
            return this.formData.businessType && this.formData.location && this.formData.urgency;
        },
        
        calculateEstimate() {
            this.isCalculating = true;
            this.$parent.nextStep();
            
            // Simulate AI calculation delay
            setTimeout(() => {
                // Base fees by service type
                const baseFees = {
                    'nib': 2500000,
                    'siup': 3500000,
                    'pbg': 5000000,
                    'ukl-upl': 4500000,
                    'sertifikat-halal': 3000000
                };
                
                // Processing fees by business type
                const processingFees = {
                    'pt': 1500000,
                    'cv': 1000000,
                    'ud': 750000,
                    'koperasi': 1200000,
                    'yayasan': 1300000
                };
                
                // Urgency fees
                const urgencyFees = {
                    'normal': 0,
                    'fast': 1500000,
                    'express': 3000000
                };
                
                this.estimate.baseFee = baseFees[this.formData.service] || 0;
                this.estimate.processingFee = processingFees[this.formData.businessType] || 0;
                this.estimate.urgencyFee = urgencyFees[this.formData.urgency] || 0;
                this.estimate.total = this.estimate.baseFee + this.estimate.processingFee + this.estimate.urgencyFee;
                
                this.isCalculating = false;
                
                // Analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'ai_estimate_calculated', {
                        'event_category': 'conversion',
                        'event_label': this.formData.service,
                        'value': this.estimate.total
                    });
                }
            }, 2000);
        },
        
        formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        },
        
        getWhatsAppMessage() {
            const locale = '{{ $currentLocale }}';
            const message = locale === 'en' 
                ? `Hello! I got an estimate of ${this.formatCurrency(this.estimate.total)} for ${this.formData.service}. I want to discuss further.`
                : `Halo! Saya mendapat estimasi ${this.formatCurrency(this.estimate.total)} untuk layanan ${this.formData.service}. Saya ingin konsultasi lebih lanjut.`;
            return encodeURIComponent(message);
        },
        
        resetEstimator() {
            this.formData = {
                service: '',
                businessType: '',
                location: '',
                urgency: 'normal'
            };
            this.estimate = {
                baseFee: 0,
                processingFee: 0,
                urgencyFee: 0,
                total: 0
            };
            this.$parent.currentStep = 1;
        }
    }
}
</script>
