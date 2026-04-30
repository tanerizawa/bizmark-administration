@props([
    'value' => 0,               // 0-100
    'size' => 'md',             // sm | md | lg
    'variant' => 'primary',     // primary | success | warning | danger | info
    'showLabel' => false,
    'labelPosition' => 'inside', // inside | outside
    'animated' => false,
    'indeterminate' => false,
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'h-1.5',
        'md' => 'h-2.5',
        'lg' => 'h-4',
    ];

    $colorClasses = [
        'primary' => 'bg-[var(--color-primary)]',
        'success' => 'bg-[var(--color-success)]',
        'warning' => 'bg-[var(--color-warning)]',
        'danger' => 'bg-[var(--color-error)]',
        'info' => 'bg-[var(--color-info)]',
    ];

    $animatedClass = $animated ? 'animate-pulse' : '';
    $barClasses = "h-full rounded-full {$colorClasses[$variant]} transition-all duration-500 ease-out {$animatedClass}";
@endphp

<div class="w-full {{ $class }}">
    @if($showLabel && $labelPosition === 'outside')
        <div class="flex justify-between items-center mb-1">
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label ?? '' }}</span>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $value }}%</span>
        </div>
    @endif

    <div
        class="w-full {{ $sizeClasses[$size] }} bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden"
        role="progressbar"
        aria-valuenow="{{ $indeterminate ? null : $value }}"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-label="{{ $label ?? 'Progress' }}"
    >
        @if($indeterminate)
            {{-- Indeterminate progress bar --}}
            <div class="{{ $barClasses }} w-1/2 animate-progress-indeterminate"
                 style="background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);">
            </div>
        @else
            <div class="{{ $barClasses }}" style="width: {{ min(100, max(0, $value)) }}%">
                @if($showLabel && $labelPosition === 'inside' && $size !== 'sm')
                    <span class="flex items-center justify-center h-full text-xs font-medium text-white px-2">
                        {{ $value }}%
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            @keyframes progress-indeterminate {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(400%); }
            }
            .animate-progress-indeterminate {
                animation: progress-indeterminate 1.5s ease-in-out infinite;
            }
        </style>
    @endpush
@endonce
