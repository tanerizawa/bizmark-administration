<!-- WhatsApp Floating Action Button (FAB) -->
@php
    $whatsappNumber = '6283879602855'; // Your WhatsApp number
    $currentLocale = app()->getLocale();
    $isEnglish = $currentLocale === 'en';
    $message = $isEnglish 
        ? 'Hello! I need help with business licensing services.' 
        : 'Halo! Saya butuh bantuan untuk layanan perizinan usaha.';
    $whatsappUrl = 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode($message);
@endphp

<!-- Main FAB Button -->
<div class="whatsapp-fab-container">
    <!-- Primary FAB -->
    <a href="{{ $whatsappUrl }}" 
       target="_blank"
       rel="noopener noreferrer"
       class="whatsapp-fab group"
       aria-label="{{ __('mobile.whatsapp.chat_now') }}"
       x-data="{ tooltip: false }"
       @mouseenter="tooltip = true"
       @mouseleave="tooltip = false">
        
        <!-- Green Circle with WhatsApp Icon -->
        <div class="fab-circle">
            <!-- WhatsApp Icon (SVG for better quality) -->
            <svg class="w-7 h-7 text-white" 
                 fill="currentColor" 
                 viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            
            <!-- Pulse animation rings -->
            <span class="fab-pulse-ring"></span>
            <span class="fab-pulse-ring" style="animation-delay: 0.5s;"></span>
        </div>
        
        <!-- Notification Badge (optional) -->
        <span class="fab-badge">1</span>
        
        <!-- Tooltip (appears on hover for desktop) -->
        <div x-show="tooltip" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-x-2"
             x-transition:enter-end="opacity-100 translate-x-0"
             class="fab-tooltip hidden md:block">
            <div class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap shadow-xl">
                {{ __('mobile.whatsapp.chat_now') }}
                <div class="text-xs text-gray-300 mt-1">
                    {{ __('mobile.whatsapp.typical_reply') }}
                </div>
            </div>
            <!-- Arrow -->
            <div class="fab-tooltip-arrow"></div>
        </div>
    </a>
    
    <!-- "We're Online" Indicator (optional, shows during business hours) -->
    @if(now()->hour >= 8 && now()->hour < 18)
    <div class="fab-online-indicator">
        <span class="online-dot"></span>
        <span class="text-xs font-medium text-gray-700">
            {{ __('mobile.whatsapp.online') }}
        </span>
    </div>
    @endif
</div>

<style>
/* ===== WhatsApp FAB Container ===== */
.whatsapp-fab-container {
    position: fixed;
    bottom: 80px; /* Above bottom nav if exists */
    right: 16px;
    z-index: 999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

/* ===== Main FAB Button ===== */
.whatsapp-fab {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

/* Hover effect (desktop) */
.whatsapp-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(37, 211, 102, 0.5);
}

/* Active (press) effect - Neuroscience: Tactile feedback */
.whatsapp-fab:active {
    transform: scale(0.95);
}

/* ===== Inner Circle ===== */
.fab-circle {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

/* ===== Pulse Animation Rings ===== */
/* Neuroscience: Motion attracts attention without distraction */
.fab-pulse-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid #25D366;
    border-radius: 50%;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    opacity: 0;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.3);
        opacity: 0.5;
    }
    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

/* ===== Notification Badge ===== */
/* Neuroscience: Red = urgency, triggers action */
.fab-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: #DC2626;
    color: white;
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* ===== Tooltip ===== */
.fab-tooltip {
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin-right: 12px;
    pointer-events: none;
}

.fab-tooltip-arrow {
    position: absolute;
    right: -6px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-left: 6px solid #111827;
}

/* ===== Online Indicator ===== */
.fab-online-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.online-dot {
    width: 8px;
    height: 8px;
    background: #10B981;
    border-radius: 50%;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* ===== Responsive Adjustments ===== */
@media (max-width: 640px) {
    .whatsapp-fab-container {
        bottom: 24px;
        right: 16px;
    }
    
    /* Thumb zone optimization for right-handed users */
    .whatsapp-fab {
        width: 56px;
        height: 56px;
    }
}

/* Safe area for iPhone notch */
@supports (padding: max(0px)) {
    .whatsapp-fab-container {
        bottom: max(24px, env(safe-area-inset-bottom));
        right: max(16px, env(safe-area-inset-right));
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .fab-online-indicator {
        background: #1F2937;
        color: #F3F4F6;
    }
}

/* Accessibility: Focus state for keyboard navigation */
.whatsapp-fab:focus {
    outline: 3px solid #60A5FA;
    outline-offset: 2px;
}

/* Reduce motion for users with vestibular disorders */
@media (prefers-reduced-motion: reduce) {
    .fab-pulse-ring,
    .online-dot {
        animation: none;
    }
    
    .whatsapp-fab {
        transition: none;
    }
}
</style>

<!-- Alpine.js for tooltip (if not already included) -->
@push('scripts')
<script>
// Track WhatsApp FAB clicks
document.querySelector('.whatsapp-fab')?.addEventListener('click', function() {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'whatsapp_fab_click', {
            'event_category': 'engagement',
            'event_label': 'mobile_fab',
            'value': 1
        });
    }
});

// Show badge based on unread messages (if you have backend integration)
function updateWhatsAppBadge(count) {
    const badge = document.querySelector('.fab-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
    }
}

// Example: updateWhatsAppBadge(3);
</script>
@endpush
