@props([
    'size' => 'md',  // sm | md | lg
])

@php
    $sizeClasses = [
        'sm' => 'h-3 w-3',
        'md' => 'h-4 w-4',
        'lg' => 'h-5 w-5',
    ];
@endphp

<svg class="animate-spin {{ $sizeClasses[$size] }} flex-shrink-0" viewBox="0 0 24 24" fill="none">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
</svg>
