{{-- Locale Switcher Component --}}
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
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }">
    <button @click="open = !open" type="button" 
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            aria-expanded="false" 
            aria-haspopup="true">
        <span class="text-lg">{{ $availableLocales[$currentLocale]['flag'] }}</span>
        <span class="hidden sm:inline">{{ $availableLocales[$currentLocale]['short'] }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 w-56 mt-2 origin-top-right bg-white border border-gray-200 divide-y divide-gray-100 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="locale-menu"
         style="display: none;">
        
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
