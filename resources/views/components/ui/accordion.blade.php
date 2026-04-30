@props([
    'items' => [],              // [['title' => '...', 'content' => '...'], ...]
    'allowMultiple' => false,
    'defaultOpen' => [],        // Array of indices to open by default
    'variant' => 'default',     // default | bordered | ghost
    'size' => 'md',             // sm | md | lg
    'class' => '',
])

@php
    $variantClasses = [
        'default' => 'divide-y divide-gray-200 dark:divide-gray-700 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900',
        'bordered' => 'space-y-2',
        'ghost' => 'divide-y divide-gray-100 dark:divide-gray-800',
    ];

    $itemBordered = $variant === 'bordered' ? 'rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden' : '';

    $sizeClasses = [
        'sm' => [
            'button' => 'px-3 py-2 text-sm',
            'content' => 'px-3 pb-3 text-xs',
        ],
        'md' => [
            'button' => 'px-4 py-3 text-sm',
            'content' => 'px-4 pb-4 text-sm',
        ],
        'lg' => [
            'button' => 'px-5 py-4 text-base',
            'content' => 'px-5 pb-5 text-base',
        ],
    ];
@endphp

<div
    x-data="{
        activeItems: {{ json_encode($defaultOpen) }},
        toggle(index) {
            if ({{ $allowMultiple ? 'true' : 'false' }}) {
                if (this.activeItems.includes(index)) {
                    this.activeItems = this.activeItems.filter(i => i !== index);
                } else {
                    this.activeItems = [...this.activeItems, index];
                }
            } else {
                this.activeItems = this.activeItems.includes(index) ? [] : [index];
            }
        },
        isOpen(index) {
            return this.activeItems.includes(index);
        }
    }"
    class="{{ $variantClasses[$variant] }} {{ $class }}"
>
    @foreach($items as $index => $item)
        <div class="{{ $itemBordered }}">
            <button
                type="button"
                @click="toggle({{ $index }})"
                :aria-expanded="isOpen({{ $index }})"
                :aria-controls="'accordion-content-{{ $index }}'"
                class="flex items-center justify-between w-full text-left font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150 {{ $sizeClasses[$size]['button'] }}"
            >
                <span>{{ $item['title'] }}</span>
                <svg
                    class="w-4 h-4 shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': isOpen({{ $index }}) }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="isOpen({{ $index }})"
                x-collapse
                :id="'accordion-content-{{ $index }}'"
                role="region"
                class="text-gray-600 dark:text-gray-400 {{ $sizeClasses[$size]['content'] }}"
            >
                {{ $item['content'] ?? '' }}
            </div>
        </div>
    @endforeach

    {{-- Fallback slot for custom content --}}
    @if(count($items) === 0)
        {{ $slot }}
    @endif
</div>
