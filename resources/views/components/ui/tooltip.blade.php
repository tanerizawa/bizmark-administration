@props([
    'content' => '',
    'position' => 'top',        // top | bottom | left | right
    'delay' => 200,             // ms
    'size' => 'sm',             // sm | md
    'class' => '',
])

@php
    $positionClasses = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];

    $arrowClasses = [
        'top' => 'top-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-b-transparent border-t-gray-900 dark:border-t-gray-700',
        'bottom' => 'bottom-full left-1/2 -translate-x-1/2 border-l-transparent border-r-transparent border-t-transparent border-b-gray-900 dark:border-b-gray-700',
        'left' => 'left-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-r-transparent border-l-gray-900 dark:border-l-gray-700',
        'right' => 'right-full top-1/2 -translate-y-1/2 border-t-transparent border-b-transparent border-l-transparent border-r-gray-900 dark:border-r-gray-700',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1.5 text-sm',
    ];
@endphp

<div
    x-data="{ show: false }"
    @mouseenter="setTimeout(() => show = true, {{ $delay }})"
    @mouseleave="show = false"
    @focusin="show = true"
    @focusout="show = false"
    class="relative inline-flex {{ $class }}"
>
    {{-- Trigger element --}}
    {{ $slot }}

    {{-- Tooltip --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 {{ $positionClasses[$position] }} {{ $sizeClasses[$size] }} bg-gray-900 dark:bg-gray-700 text-white rounded-lg shadow-lg whitespace-nowrap pointer-events-none"
        role="tooltip"
        style="display: none;"
    >
        {{ $content }}

        {{-- Arrow --}}
        <div class="absolute w-0 h-0 border-4 {{ $arrowClasses[$position] }}"></div>
    </div>
</div>
