{{--
    Client Empty State Component
    Usage: @include('client.components.empty-state', [
        'icon'    => 'fa-inbox',          // Font Awesome class (required)
        'title'   => 'Belum Ada Data',    // required
        'message' => 'Penjelasan...',     // optional
        'ctaLabel'=> 'Tambah Sekarang',   // optional
        'ctaHref' => route('...'),        // optional
        'ctaIcon' => 'fa-plus',           // optional, default fa-arrow-right
        'secondary'=> true,               // optional: show reset filter button
        'secondaryLabel' => 'Reset',      // optional
        'secondaryHref'  => route('...'), // optional
        'size'    => 'md',                // 'sm' | 'md' (default) | 'lg'
        'color'   => 'gray',              // 'gray' (default) | 'blue' | 'green' | 'amber'
    ])
--}}
@php
    $size   = $size ?? 'md';
    $color  = $color ?? 'gray';
    $ctaIcon = $ctaIcon ?? 'fa-arrow-right';

    $paddingClass = match($size) {
        'sm'  => 'py-8',
        'lg'  => 'py-20',
        default => 'py-14',
    };

    $iconSizeClass = match($size) {
        'sm'  => 'w-12 h-12 text-xl',
        'lg'  => 'w-20 h-20 text-3xl',
        default => 'w-16 h-16 text-2xl',
    };

    $colorClasses = match($color) {
        'blue'  => ['bg' => 'bg-[#0a66c2]/10', 'text' => 'text-[#0a66c2]'],
        'green' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-500'],
        'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-500'],
        default => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-400 dark:text-gray-500'],
    };
@endphp

<div class="text-center {{ $paddingClass }} px-4" role="status" aria-live="polite">
    <div class="inline-flex items-center justify-center {{ $iconSizeClass }} {{ $colorClasses['bg'] }} rounded-full mx-auto mb-4">
        <i class="fas {{ $icon }} {{ $colorClasses['text'] }}" aria-hidden="true"></i>
    </div>

    <p class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-1 leading-tight">
        {{ $title }}
    </p>

    @if(!empty($message))
    <p class="text-sm text-gray-500 dark:text-gray-400 leading-normal max-w-xs mx-auto mb-5">
        {{ $message }}
    </p>
    @else
    <div class="mb-5"></div>
    @endif

    @if(!empty($ctaHref) && !empty($ctaLabel))
    <a href="{{ $ctaHref }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-lg active:scale-95 transition-all">
        <i class="fas {{ $ctaIcon }} text-xs" aria-hidden="true"></i>
        {{ $ctaLabel }}
    </a>
    @endif

    @if(!empty($secondary) && !empty($secondaryHref))
    <a href="{{ $secondaryHref }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold text-sm rounded-lg active:scale-95 transition-all {{ !empty($ctaHref) ? 'ml-2' : '' }}">
        <i class="fas fa-redo text-xs" aria-hidden="true"></i>
        {{ $secondaryLabel ?? 'Reset Filter' }}
    </a>
    @endif
</div>
