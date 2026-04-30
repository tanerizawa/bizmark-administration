@props([
    'variant' => 'primary',   // primary | secondary | outline | ghost | danger
    'size' => 'md',           // sm | md | lg
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading...',
    'type' => 'button',       // button | submit | reset
    'href' => null,           // jika diisi render <a> bukan <button>
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] focus:ring-[var(--color-primary)] shadow-sm hover:shadow-md',
        'secondary' => 'bg-[var(--color-secondary)] text-white hover:bg-[var(--color-secondary-dark)] focus:ring-[var(--color-secondary)] shadow-sm hover:shadow-md',
        'outline' => 'border-2 border-[var(--color-primary)] text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white focus:ring-[var(--color-primary)]',
        'ghost' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-gray-400',
        'danger' => 'bg-[var(--color-error)] text-white hover:bg-red-600 focus:ring-red-500 shadow-sm hover:shadow-md',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-7 py-3.5 text-base',
    ];

    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$sizeClasses[$size]} {$class}");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <x-ui.button-spinner />
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled || $loading) disabled @endif
    >
        @if($loading)
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
