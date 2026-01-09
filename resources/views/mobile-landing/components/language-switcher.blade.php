<!-- Language Switcher Component -->
<div class="language-switcher">
    @php
        $currentLocale = app()->getLocale();
        $isEnglish = $currentLocale === 'en';
        $switchRoute = $isEnglish ? route('mobile.landing.id') : route('mobile.landing.en');
        $switchFlag = $isEnglish ? '🇮🇩' : '🇬🇧';
        $switchText = $isEnglish ? __('mobile.language.switch_to_id') : __('mobile.language.switch_to_en');
    @endphp
    
    <!-- Mobile-optimized toggle button (top-right) -->
    <a href="{{ $switchRoute }}" 
       class="fixed top-4 right-4 z-50 
              flex items-center gap-2 
              bg-white dark:bg-gray-800 
              px-3 py-2 rounded-full 
              shadow-lg hover:shadow-xl 
              transition-all duration-300
              border border-gray-200 dark:border-gray-700
              group hover:scale-105">
        <!-- Flag icon -->
        <span class="text-xl leading-none">{{ $switchFlag }}</span>
        
        <!-- Text (hidden on small screens) -->
        <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-200">
            {{ $switchText }}
        </span>
        
        <!-- Chevron icon -->
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 group-hover:rotate-180 transition-transform" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M19 9l-7 7-7-7" />
        </svg>
    </a>
</div>

<!-- Alternative: Dropdown style (for navigation menu) -->
<div class="language-dropdown relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
        <span class="text-lg">{{ $isEnglish ? '🇬🇧' : '🇮🇩' }}</span>
        <span class="text-sm font-medium">{{ $isEnglish ? 'EN' : 'ID' }}</span>
        <svg class="w-4 h-4 transition-transform" 
             :class="{ 'rotate-180': open }"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    
    <!-- Dropdown menu -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">
        
        <!-- Indonesian Option -->
        <a href="{{ route('mobile.landing.id') }}" 
           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ !$isEnglish ? 'bg-blue-50 dark:bg-gray-700' : '' }}">
            <span class="text-xl">🇮🇩</span>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    Bahasa Indonesia
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Indonesia
                </div>
            </div>
            @if(!$isEnglish)
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            @endif
        </a>
        
        <!-- English Option -->
        <a href="{{ route('mobile.landing.en') }}" 
           class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors {{ $isEnglish ? 'bg-blue-50 dark:bg-gray-700' : '' }}">
            <span class="text-xl">🇬🇧</span>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    English
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    United Kingdom
                </div>
            </div>
            @if($isEnglish)
            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            @endif
        </a>
    </div>
</div>

<style>
/* Custom styles for language switcher */
.language-switcher a {
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

/* Neuroscience: Smooth transitions reduce cognitive load */
.language-switcher a,
.language-dropdown button,
.language-dropdown a {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Fitts's Law: Minimum 48x48px touch target */
.language-switcher a {
    min-width: 48px;
    min-height: 48px;
}

/* Active state feedback (tactile feel) */
.language-switcher a:active,
.language-dropdown button:active {
    transform: scale(0.95);
}
</style>

<!-- Alpine.js for dropdown (if not already included) -->
@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
