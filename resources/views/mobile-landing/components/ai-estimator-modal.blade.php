<!-- AI Cost Estimator Modal (Bottom Sheet) -->
@php
    $currentLocale = app()->getLocale();
@endphp

<div id="ai-estimator-modal" 
     class="ai-estimator-modal"
     x-data="aiEstimatorModal()"
     x-cloak>
    
    <!-- Backdrop Overlay -->
    <div x-show="isOpen" 
         @click="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-backdrop">
    </div>
    
    <!-- Bottom Sheet -->
    <div x-show="isOpen"
         @click.away="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="modal-content">
        
        <!-- Drag Handle -->
        <div class="modal-handle-container">
            <div class="modal-handle"></div>
        </div>
        
        <!-- Header -->
        <div class="modal-header">
            <div>
                <h3 class="text-xl font-bold text-gray-900">
                    {{ __('mobile.ai_estimator.title') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('mobile.ai_estimator.subtitle') }}
                </p>
            </div>
            <button @click="close()" 
                    class="close-button"
                    aria-label="{{ __('mobile.ui.close') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-item" :class="{ 'active': currentStep === 1, 'completed': currentStep > 1 }">
                <div class="step-circle">
                    <span x-show="currentStep === 1">1</span>
                    <svg x-show="currentStep > 1" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="step-label">{{ __('mobile.ai_estimator.step_1_title') }}</span>
            </div>
            
            <div class="step-line" :class="{ 'active': currentStep >= 2 }"></div>
            
            <div class="step-item" :class="{ 'active': currentStep === 2, 'completed': currentStep > 2 }">
                <div class="step-circle">
                    <span x-show="currentStep <= 2">2</span>
                    <svg x-show="currentStep > 2" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="step-label">{{ __('mobile.ai_estimator.step_2_title') }}</span>
            </div>
            
            <div class="step-line" :class="{ 'active': currentStep >= 3 }"></div>
            
            <div class="step-item" :class="{ 'active': currentStep === 3 }">
                <div class="step-circle">3</div>
                <span class="step-label">{{ __('mobile.ai_estimator.step_3_title') }}</span>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="modal-body">
            <!-- Include the AI Estimator Form -->
            @include('mobile-landing.components.ai-estimator-form')
        </div>
    </div>
</div>

<!-- Trigger Button (to be placed in hero section) -->
<button @click="$dispatch('open-ai-estimator')" 
        class="btn-ai-estimator">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
    </svg>
    {{ __('mobile.hero.cta_primary') }}
</button>

<style>
/* ===== Modal Styles ===== */
[x-cloak] { 
    display: none !important; 
}

.ai-estimator-modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    pointer-events: none;
}

.ai-estimator-modal > * {
    pointer-events: auto;
}

/* Backdrop */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

/* Bottom Sheet */
.modal-content {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    max-height: 90vh;
    background: white;
    border-radius: 24px 24px 0 0;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* Drag Handle */
.modal-handle-container {
    padding: 12px 0 8px;
    display: flex;
    justify-content: center;
    cursor: grab;
}

.modal-handle {
    width: 48px;
    height: 4px;
    background: #D1D5DB;
    border-radius: 2px;
}

/* Header */
.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 16px 24px;
    border-bottom: 1px solid #E5E7EB;
}

.close-button {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: #6B7280;
    transition: all 0.2s;
}

.close-button:hover {
    background: #F3F4F6;
    color: #111827;
}

.close-button:active {
    transform: scale(0.95);
}

/* Step Indicator */
.step-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 24px;
    gap: 8px;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    background: #F3F4F6;
    color: #9CA3AF;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.step-item.active .step-circle {
    background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
    color: white;
    border-color: #0077B5;
    box-shadow: 0 4px 12px rgba(0, 119, 181, 0.3);
}

.step-item.completed .step-circle {
    background: #10B981;
    color: white;
}

.step-label {
    font-size: 11px;
    color: #6B7280;
    text-align: center;
    max-width: 80px;
}

.step-item.active .step-label {
    color: #0077B5;
    font-weight: 600;
}

.step-line {
    flex: 1;
    height: 2px;
    background: #E5E7EB;
    transition: all 0.3s;
}

.step-line.active {
    background: #0077B5;
}

/* Modal Body */
.modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    -webkit-overflow-scrolling: touch;
}

/* AI Estimator Button */
.btn-ai-estimator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 24px;
    background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
    color: white;
    font-weight: 600;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 119, 181, 0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    min-width: 48px;
    min-height: 48px;
}

.btn-ai-estimator:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 119, 181, 0.4);
}

.btn-ai-estimator:active {
    transform: scale(0.98);
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    .modal-content {
        background: #1F2937;
    }
    
    .modal-header {
        border-bottom-color: #374151;
    }
    
    .modal-handle {
        background: #4B5563;
    }
    
    .close-button:hover {
        background: #374151;
    }
}

/* Safe area (iPhone notch) */
@supports (padding: max(0px)) {
    .modal-content {
        padding-bottom: env(safe-area-inset-bottom);
    }
}
</style>

<script>
function aiEstimatorModal() {
    return {
        isOpen: false,
        currentStep: 1,
        
        init() {
            // Listen for open event
            this.$watch('isOpen', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
            
            // Alpine.js custom event listener
            document.addEventListener('open-ai-estimator', () => {
                this.open();
            });
            
            // Also listen to window custom event (fallback)
            window.addEventListener('open-ai-estimator', () => {
                this.open();
            });
        },
        
        open() {
            this.isOpen = true;
            this.currentStep = 1;
            
            // Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'ai_estimator_open', {
                    'event_category': 'engagement',
                    'event_label': 'mobile_modal'
                });
            }
        },
        
        close() {
            this.isOpen = false;
        },
        
        nextStep() {
            if (this.currentStep < 3) {
                this.currentStep++;
            }
        },
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        }
    }
}
</script>
