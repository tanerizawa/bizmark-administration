@props([
    'src' => null,              // Image URL
    'alt' => '',
    'name' => '',               // For initials fallback
    'size' => 'md',             // xs | sm | md | lg | xl
    'variant' => 'circular',    // circular | rounded | square
    'status' => null,           // online | offline | away | busy
    'class' => '',
])

@php
    $sizeClasses = [
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
    ];

    $radiusClasses = [
        'circular' => 'rounded-full',
        'rounded' => 'rounded-lg',
        'square' => 'rounded-none',
    ];

    $statusClasses = [
        'online' => 'bg-[var(--color-success)]',
        'offline' => 'bg-gray-400 dark:bg-gray-500',
        'away' => 'bg-[var(--color-warning)]',
        'busy' => 'bg-[var(--color-error)]',
    ];

    $statusSizes = [
        'xs' => 'w-1.5 h-1.5 ring-1',
        'sm' => 'w-2 h-2 ring-1',
        'md' => 'w-2.5 h-2.5 ring-1.5',
        'lg' => 'w-3 h-3 ring-1.5',
        'xl' => 'w-3.5 h-3.5 ring-2',
    ];

    $initials = '';
    if ($name) {
        $parts = explode(' ', trim($name));
        if (count($parts) >= 2) {
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }
    }
@endphp

<div class="relative inline-flex shrink-0 {{ $class }}">
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $alt ?: $name }}"
            class="{{ $sizeClasses[$size] }} {{ $radiusClasses[$variant] }} object-cover ring-2 ring-white dark:ring-gray-800"
            onerror="this.parentElement.innerHTML = '<span class=\'{{ $sizeClasses[$size] }} {{ $radiusClasses[$variant] }} bg-[var(--color-primary)] text-white flex items-center justify-center font-medium ring-2 ring-white dark:ring-gray-800\'>{{ $initials }}</span>'"
        />
    @elseif($initials)
        <span class="{{ $sizeClasses[$size] }} {{ $radiusClasses[$variant] }} bg-[var(--color-primary)]/10 text-[var(--color-primary)] dark:bg-[var(--color-primary)]/20 dark:text-[var(--color-primary-light)] flex items-center justify-center font-medium ring-2 ring-white dark:ring-gray-800">
            {{ $initials }}
        </span>
    @else
        <span class="{{ $sizeClasses[$size] }} {{ $radiusClasses[$variant] }} bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 flex items-center justify-center ring-2 ring-white dark:ring-gray-800">
            <svg class="w-1/2 h-1/2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
        </span>
    @endif

    @if($status)
        <span class="absolute bottom-0 right-0 block {{ $statusSizes[$size] }} {{ $statusClasses[$status] }} rounded-full ring-white dark:ring-gray-800" aria-hidden="true">
            <span class="sr-only">{{ ucfirst($status) }}</span>
        </span>
    @endif
</div>
