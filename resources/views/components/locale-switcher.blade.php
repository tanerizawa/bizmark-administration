{{-- Locale Switcher Component (vanilla JS - no Alpine dependency) --}}
@php
    $currentLocale = app()->getLocale();
    $availableLocales = [
        'id' => [
            'name' => 'Bahasa Indonesia',
            'short' => 'ID',
            'flag' => '🇮🇩',
        ],
        'en' => [
            'name' => 'English',
            'short' => 'EN',
            'flag' => '🇬🇧',
        ],
    ];
    $switcherId = 'locale-switcher-' . Str::random(6);
@endphp

<div class="relative inline-block text-left" id="{{ $switcherId }}">
    <button type="button" 
            onclick="this.nextElementSibling.classList.toggle('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            aria-expanded="false" 
            aria-haspopup="true">
        <span class="text-lg">{{ $availableLocales[$currentLocale]['flag'] }}</span>
        <span class="hidden sm:inline">{{ $availableLocales[$currentLocale]['short'] }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div class="hidden absolute right-0 z-50 w-56 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         role="menu" 
         aria-orientation="vertical">
        
        <div class="py-1">
            @foreach($availableLocales as $locale => $info)
                <a href="{{ route('locale.set', $locale) }}" 
                   class="group flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 transition-colors {{ $currentLocale === $locale ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}"
                   role="menuitem">
                    <span class="text-2xl">{{ $info['flag'] }}</span>
                    <div class="flex-1">
                        <div class="font-medium {{ $currentLocale === $locale ? 'text-blue-600' : 'text-gray-900' }}">
                            {{ $info['name'] }}
                        </div>
                        @if($currentLocale === $locale)
                            <div class="text-xs text-blue-500 font-medium">
                                <i class="fas fa-check mr-1"></i>Active
                            </div>
                        @endif
                    </div>
                    @if($currentLocale === $locale)
                        <i class="fas fa-check text-blue-600"></i>
                    @endif
                </a>
            @endforeach
        </div>
        
        <div class="px-4 py-3 bg-gray-50">
            <p class="text-xs text-gray-500">
                <i class="fas fa-globe mr-1"></i>
                @if($currentLocale === 'en')
                    For foreign investors
                @else
                    Untuk pasar lokal Indonesia
                @endif
            </p>
        </div>
    </div>
</div>

<script>
(function() {
    var container = document.getElementById('{{ $switcherId }}');
    if (!container) return;
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            var dd = container.querySelector('[role="menu"]');
            if (dd) dd.classList.add('hidden');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var dd = container.querySelector('[role="menu"]');
            if (dd) dd.classList.add('hidden');
        }
    });
})();
</script>
