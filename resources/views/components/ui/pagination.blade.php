@props([
    'paginator' => null,
    'variant' => 'full',        // simple | full
    'showInfo' => true,
    'showPages' => true,
    'size' => 'sm',             // sm | md
    'class' => '',
])

@php
    if (!$paginator || $paginator->isEmpty()) {
        return;
    }

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
    ];

    $buttonBase = 'inline-flex items-center justify-center font-medium rounded-lg border transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-1 dark:focus:ring-offset-gray-900';
    $buttonActive = 'bg-[var(--color-primary)] text-white border-[var(--color-primary)]';
    $buttonInactive = 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700';
    $buttonDisabled = 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-900 text-gray-400 dark:text-gray-600 border-gray-200 dark:border-gray-700';
@endphp

<div class="flex items-center justify-between {{ $class }}">
    {{-- Info text --}}
    @if($showInfo && $variant === 'full')
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->total() }}</span>
        </div>
    @elseif($showInfo && $variant === 'simple')
        <div class="text-sm text-gray-500 dark:text-gray-400">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </div>
    @else
        <div></div>
    @endif

    {{-- Pagination buttons --}}
    @if($showPages)
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($paginator->onFirstPage())
                <span class="{{ $buttonBase . ' ' . $buttonDisabled . ' ' . $sizeClasses[$size] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    class="{{ $buttonBase . ' ' . $buttonInactive . ' ' . $sizeClasses[$size] }}"
                    rel="prev"
                    aria-label="Sebelumnya"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Page numbers --}}
            @if($variant === 'full')
                @php
                    $start = max(1, $paginator->currentPage() - 2);
                    $end = min($paginator->lastPage(), $paginator->currentPage() + 2);

                    if ($start > 1) {
                        echo '<a href="' . $paginator->url(1) . '" class="' . $buttonBase . ' ' . $buttonInactive . ' ' . $sizeClasses[$size] . '">1</a>';
                        if ($start > 2) {
                            echo '<span class="px-2 text-gray-400 dark:text-gray-500">...</span>';
                        }
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        if ($i === $paginator->currentPage()) {
                            echo '<span class="' . $buttonBase . ' ' . $buttonActive . ' ' . $sizeClasses[$size] . '">' . $i . '</span>';
                        } else {
                            echo '<a href="' . $paginator->url($i) . '" class="' . $buttonBase . ' ' . $buttonInactive . ' ' . $sizeClasses[$size] . '">' . $i . '</a>';
                        }
                    }

                    if ($end < $paginator->lastPage()) {
                        if ($end < $paginator->lastPage() - 1) {
                            echo '<span class="px-2 text-gray-400 dark:text-gray-500">...</span>';
                        }
                        echo '<a href="' . $paginator->url($paginator->lastPage()) . '" class="' . $buttonBase . ' ' . $buttonInactive . ' ' . $sizeClasses[$size] . '">' . $paginator->lastPage() . '</a>';
                    }
                @endphp
            @endif

            {{-- Next --}}
            @if($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    class="{{ $buttonBase . ' ' . $buttonInactive . ' ' . $sizeClasses[$size] }}"
                    rel="next"
                    aria-label="Selanjutnya"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="{{ $buttonBase . ' ' . $buttonDisabled . ' ' . $sizeClasses[$size] }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    @endif
</div>
