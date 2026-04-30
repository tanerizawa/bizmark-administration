@props([
    'label' => '',
    'value' => '',
    'trend' => null,          // null (no trend) | positive number | negative number
    'trendLabel' => '',
    'icon' => null,           // Font Awesome class
    'variant' => 'primary',   // primary | success | warning | danger | info
    'class' => '',
])

@php
    $baseClasses = 'rounded-2xl p-6 border transition-all duration-200 hover:shadow-md';

    $variantClasses = [
        'primary' => 'bg-white dark:bg-gray-800 border-gray-100 dark:border-gray-700',
        'success' => 'bg-white dark:bg-gray-800 border-l-4 border-l-[var(--color-success)] border-gray-100 dark:border-gray-700',
        'warning' => 'bg-white dark:bg-gray-800 border-l-4 border-l-[var(--color-warning)] border-gray-100 dark:border-gray-700',
        'danger' => 'bg-white dark:bg-gray-800 border-l-4 border-l-[var(--color-error)] border-gray-100 dark:border-gray-700',
        'info' => 'bg-white dark:bg-gray-800 border-l-4 border-l-[var(--color-info)] border-gray-100 dark:border-gray-700',
    ];

    $iconBgClasses = [
        'primary' => 'bg-[var(--color-primary)]/10 text-[var(--color-primary)]',
        'success' => 'bg-[var(--color-success)]/10 text-green-600',
        'warning' => 'bg-[var(--color-warning)]/10 text-amber-600',
        'danger' => 'bg-[var(--color-error)]/10 text-red-600',
        'info' => 'bg-[var(--color-info)]/10 text-blue-600',
    ];

    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$class}");

    $trendUp = $trend !== null && $trend >= 0;
    $trendDown = $trend !== null && $trend < 0;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                {{ $label }}
            </p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $value }}
            </p>

            @if($trend !== null)
                <div class="mt-2 flex items-center gap-1.5">
                    @if($trendUp)
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ number_format($trend, 1) }}%
                        </span>
                    @else
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                            {{ number_format(abs($trend), 1) }}%
                        </span>
                    @endif

                    @if($trendLabel)
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $trendLabel }}</span>
                    @endif
                </div>
            @endif
        </div>

        @if($icon)
            <div class="flex-shrink-0 ml-4">
                <div class="w-12 h-12 rounded-xl {{ $iconBgClasses[$variant] }} flex items-center justify-center">
                    <i class="{{ $icon }} text-lg"></i>
                </div>
            </div>
        @endif
    </div>
</div>
