@props([
    'icon' => null,             // Font Awesome class
    'title' => 'No data',
    'description' => '',
    'action' => null,           // ['label' => '...', 'url' => '...', 'onclick' => '...']
    'actionVariant' => 'primary',
    'size' => 'md',             // sm | md | lg
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => [
            'icon' => 'text-3xl',
            'title' => 'text-base',
            'description' => 'text-sm',
            'spacing' => 'py-8 px-4',
        ],
        'md' => [
            'icon' => 'text-5xl',
            'title' => 'text-xl',
            'description' => 'text-sm',
            'spacing' => 'py-12 px-6',
        ],
        'lg' => [
            'icon' => 'text-6xl',
            'title' => 'text-2xl',
            'description' => 'text-base',
            'spacing' => 'py-16 px-8',
        ],
    ];
@endphp

<div class="flex flex-col items-center justify-center text-center {{ $sizeClasses[$size]['spacing'] }} {{ $class }}">
    {{-- Icon --}}
    @if($icon)
        <div class="{{ $sizeClasses[$size]['icon'] }} text-gray-300 dark:text-gray-600 mb-4">
            <i class="{{ $icon }}"></i>
        </div>
    @else
        <div class="{{ $sizeClasses[$size]['icon'] }} text-gray-300 dark:text-gray-600 mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
    @endif

    {{-- Title --}}
    <h3 class="{{ $sizeClasses[$size]['title'] }} font-semibold text-gray-900 dark:text-white mb-2">
        {{ $title }}
    </h3>

    {{-- Description --}}
    @if($description)
        <p class="{{ $sizeClasses[$size]['description'] }} text-gray-500 dark:text-gray-400 max-w-sm">
            {{ $description }}
        </p>
    @endif

    {{-- Action button --}}
    @if($action)
        <div class="mt-6">
            @if(isset($action['url']))
                <x-ui.button
                    :href="$action['url']"
                    :variant="$actionVariant"
                    size="md"
                >
                    {{ $action['label'] }}
                </x-ui.button>
            @else
                <x-ui.button
                    :variant="$actionVariant"
                    size="md"
                    x-data
                    @click="{{ $action['onclick'] ?? '' }}"
                >
                    {{ $action['label'] }}
                </x-ui.button>
            @endif
        </div>
    @endif

    {{-- Custom slot --}}
    {{ $slot }}
</div>
