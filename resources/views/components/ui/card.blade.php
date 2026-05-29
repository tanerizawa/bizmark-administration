@props([
    'variant' => 'elevated',  // elevated | bordered | flat
    'padding' => 'md',        // none | sm | md | lg
    'class' => '',
])

@php
    $baseClasses = 'rounded-2xl';

    $variantClasses = [
        'elevated' => 'bg-[var(--dark-bg-tertiary)] shadow-sm hover:shadow-md transition-shadow duration-200 border border-[var(--dark-separator)]',
        'bordered' => 'bg-[var(--dark-bg-secondary)] border-2 border-[var(--dark-separator)]',
        'flat' => 'bg-[var(--dark-bg-secondary)] border border-[var(--dark-separator)]',
    ];

    $paddingClasses = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];

    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$paddingClasses[$padding]} {$class}");
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="mb-4">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            {{ $footer }}
        </div>
    @endisset
</div>
