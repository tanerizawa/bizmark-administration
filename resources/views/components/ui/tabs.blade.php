@props([
    'tabs' => [],               // [['id' => 'tab1', 'label' => 'Tab 1', 'icon' => null, 'badge' => null], ...]
    'defaultTab' => '',
    'variant' => 'underline',   // underline | pills
    'size' => 'md',             // sm | md | lg
    'class' => '',
    'contentClass' => '',
])

@php
    $firstTab = collect($tabs)->first()['id'] ?? '';
    $activeTab = $defaultTab ?: $firstTab;

    $sizeClasses = [
        'sm' => 'text-sm gap-1',
        'md' => 'text-sm gap-1',
        'lg' => 'text-base gap-2',
    ];

    $tabSizeClasses = [
        'sm' => 'px-3 py-1.5',
        'md' => 'px-4 py-2',
        'lg' => 'px-5 py-2.5',
    ];
@endphp

<div
    x-data="{ activeTab: '{{ $activeTab }}' }"
    x-id="['tab']"
    class="w-full {{ $class }}"
>
    {{-- Tab List --}}
    <div
        role="tablist"
        aria-label="Tabs"
        class="flex {{ $sizeClasses[$size] }} {{ $variant === 'pills' ? 'bg-gray-100 dark:bg-gray-800 rounded-xl p-1' : 'border-b border-gray-200 dark:border-gray-700' }}"
    >
        @foreach($tabs as $tab)
            <button
                @click="activeTab = '{{ $tab['id'] }}'"
                :aria-selected="activeTab === '{{ $tab['id'] }}'"
                :tabindex="activeTab === '{{ $tab['id'] }}' ? '0' : '-1'"
                role="tab"
                :id="$id('tab', '{{ $tab['id'] }}')"
                :aria-controls="$id('tab', '{{ $tab['id'] }}-panel')"
                class="{{ $tabSizeClasses[$size] }} font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-1 dark:focus:ring-offset-gray-900"
                :class="{
                    @if($variant === 'pills')
                        'bg-white dark:bg-gray-700 text-[var(--color-primary)] shadow-sm': activeTab === '{{ $tab['id'] }}',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== '{{ $tab['id'] }}'
                    @else
                        'text-[var(--color-primary)] border-b-2 border-[var(--color-primary)] -mb-px': activeTab === '{{ $tab['id'] }}',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 border-b-2 border-transparent -mb-px': activeTab !== '{{ $tab['id'] }}'
                    @endif
                }"
            >
                <span class="flex items-center gap-2">
                    @if($tab['icon'] ?? null)
                        <i class="{{ $tab['icon'] }}"></i>
                    @endif
                    {{ $tab['label'] }}
                    @if($tab['badge'] ?? null)
                        <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                            {{ $tab['badge'] }}
                        </span>
                    @endif
                </span>
            </button>
        @endforeach
    </div>

    {{-- Tab Panels --}}
    <div class="mt-4 {{ $contentClass }}">
        {{ $slot }}
    </div>
</div>
