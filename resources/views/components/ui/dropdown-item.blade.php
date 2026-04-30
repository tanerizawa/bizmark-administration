@props([
    'href' => '#',
    'icon' => null,
    'variant' => 'default',     // default | danger
    'method' => null,           // GET | POST | PUT | DELETE
    'disabled' => false,
    'class' => '',
])

@php
    $variantClasses = [
        'default' => 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white',
        'danger' => 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300',
    ];

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
    $classes = "group flex items-center w-full px-4 py-2 text-sm transition-colors duration-150 {$variantClasses[$variant]} {$disabledClasses} {$class}";
@endphp

@if($method && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE']))
    {{-- Form method dropdown item --}}
    <form method="POST" action="{{ $href }}" class="w-full">
        @csrf
        @method($method)
        <button
            type="submit"
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $classes]) }}
            role="menuitem"
        >
            @if($icon)
                <i class="{{ $icon }} w-5 h-5 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-current transition-colors duration-150"></i>
            @endif
            {{ $slot }}
        </button>
    </form>
@else
    {{-- Link dropdown item --}}
    <a
        href="{{ $disabled ? '#' : $href }}"
        @if($disabled) aria-disabled="true" @endif
        {{ $attributes->merge(['class' => $classes]) }}
        role="menuitem"
    >
        @if($icon)
            <i class="{{ $icon }} w-5 h-5 mr-3 text-gray-400 dark:text-gray-500 group-hover:text-current transition-colors duration-150"></i>
        @endif
        {{ $slot }}
    </a>
@endif
