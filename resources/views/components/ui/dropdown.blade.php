@props([
    'align' => 'right',         // left | right
    'width' => '48',            // 48 (192px) | 56 (224px) | 64 (256px) | 72 (288px)
    'contentClasses' => '',
    'dropdownId' => 'dropdown-'.uniqid(),
    'class' => '',
])

@php
    $alignmentClasses = [
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'right' => 'ltr:origin-top-right rtl:origin-top-left end-0',
    ];

    $widthClasses = [
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        '72' => 'w-72',
    ];
@endphp

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
    class="relative inline-block text-left {{ $class }}"
>
    {{-- Trigger --}}
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Dropdown menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $alignmentClasses[$align] }} {{ $widthClasses[$width] }} rounded-xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 {{ $contentClasses }}"
        role="menu"
        :aria-labelledby="$id('dropdown-button')"
        style="display: none;"
    >
        <div class="py-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            {{ $slot }}
        </div>
    </div>
</div>
