@props([
    'variant' => 'elevated',  // elevated | bordered | flat
    'padding' => 'md',        // none | sm | md | lg
    'class' => '',
])

@php
    $baseClasses = 'bg-white dark:bg-gray-800';

    $variantClasses = [
        'elevated' => 'rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 dark:border-gray-700',
        'bordered' => 'rounded-2xl border-2 border-gray-200 dark:border-gray-700',
        'flat' => 'rounded-2xl bg-gray-50 dark:bg-gray-800/50',
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
