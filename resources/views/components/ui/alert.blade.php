@props([
    'variant' => 'info',      // info | success | warning | danger
    'dismissible' => false,
    'icon' => true,
    'title' => '',
    'class' => '',
])

@php
    $baseClasses = 'relative rounded-xl p-4 border';

    $variantClasses = [
        'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200',
        'success' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200',
        'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200',
        'danger' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200',
    ];

    $iconClasses = [
        'info' => 'fa-solid fa-circle-info',
        'success' => 'fa-solid fa-circle-check',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'danger' => 'fa-solid fa-circle-exclamation',
    ];

    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$class}");

    $dismissId = 'alert-'.uniqid();
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    role="alert"
    {{ $attributes->merge(['class' => $classes]) }}
>
    <div class="flex gap-3">
        @if($icon)
            <div class="flex-shrink-0 mt-0.5">
                <i class="{{ $iconClasses[$variant] }}"></i>
            </div>
        @endif

        <div class="flex-1">
            @if($title)
                <h3 class="text-sm font-semibold mb-1">{{ $title }}</h3>
            @endif
            <div class="text-sm">
                {{ $slot }}
            </div>
        </div>

        @if($dismissible)
            <button
                @click="show = false"
                class="flex-shrink-0 ml-auto -mx-1 -my-1 rounded-lg p-1.5 inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2 opacity-60 hover:opacity-100 transition-opacity"
                aria-label="Dismiss"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        @endif
    </div>
</div>
