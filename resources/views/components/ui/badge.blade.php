@props([
    'variant' => 'neutral',  // neutral | primary | success | warning | danger | info
    'size' => 'sm',          // sm | md
    'pill' => true,
    'dot' => false,
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center gap-1.5 font-medium';

    $variantClasses = [
        'neutral' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
        'primary' => 'bg-[var(--color-primary)]/10 dark:bg-[var(--color-primary)]/20 text-[var(--color-primary)] dark:text-[var(--color-primary-light)]',
        'success' => 'bg-[var(--color-success)]/10 dark:bg-[var(--color-success)]/20 text-green-700 dark:text-green-400',
        'warning' => 'bg-[var(--color-warning)]/10 dark:bg-[var(--color-warning)]/20 text-amber-700 dark:text-amber-400',
        'danger' => 'bg-[var(--color-error)]/10 dark:bg-[var(--color-error)]/20 text-red-700 dark:text-red-400',
        'info' => 'bg-[var(--color-info)]/10 dark:bg-[var(--color-info)]/20 text-blue-700 dark:text-blue-400',
    ];

    $sizeClasses = [
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
    ];

    $dotColors = [
        'neutral' => 'bg-gray-400',
        'primary' => 'bg-[var(--color-primary)]',
        'success' => 'bg-[var(--color-success)]',
        'warning' => 'bg-[var(--color-warning)]',
        'danger' => 'bg-[var(--color-error)]',
        'info' => 'bg-[var(--color-info)]',
    ];

    $radiusClass = $pill ? 'rounded-full' : 'rounded-md';
    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$sizeClasses[$size]} {$radiusClass} {$class}");
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$variant] }}"></span>
    @endif
    {{ $slot }}
</span>
