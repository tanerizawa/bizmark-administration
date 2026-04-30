@props([
    'items' => [],              // [['label' => 'Home', 'url' => '/'], ['label' => 'Users', 'url' => '/users'], ...]
    'homeIcon' => null,         // Font Awesome class for home icon
    'separator' => 'chevron',   // chevron | slash | dot
    'size' => 'sm',             // sm | md
    'class' => '',
])

@php
    $separatorIcons = [
        'chevron' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
        'slash' => '<span class="text-gray-300 dark:text-gray-600">/</span>',
        'dot' => '<span class="text-gray-300 dark:text-gray-600">•</span>',
    ];

    $sizeClasses = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
    ];
@endphp

<nav aria-label="Breadcrumb" class="{{ $class }}">
    <ol class="flex items-center flex-wrap gap-1 {{ $sizeClasses[$size] }}">
        @foreach($items as $index => $item)
            <li class="flex items-center gap-1">
                @if($index > 0)
                    <span class="mx-1 text-gray-400 dark:text-gray-500 flex items-center" aria-hidden="true">
                        {!! $separatorIcons[$separator] !!}
                    </span>
                @endif

                @if(isset($item['url']) && $index < count($items) - 1)
                    <a
                        href="{{ $item['url'] }}"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors duration-150"
                    >
                        @if($index === 0 && $homeIcon)
                            <i class="{{ $homeIcon }} mr-1"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @else
                    <span
                        class="text-gray-900 dark:text-white font-medium"
                        aria-current="page"
                    >
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
