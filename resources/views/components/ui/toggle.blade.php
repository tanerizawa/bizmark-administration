@props([
    'name' => '',
    'label' => '',
    'description' => '',
    'checked' => false,
    'value' => '1',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'size' => 'md',             // sm | md | lg
    'color' => 'primary',       // primary | success | warning | danger
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => [
            'toggle' => 'h-5 w-9',
            'circle' => 'h-3.5 w-3.5',
            'translate' => 'translate-x-4',
        ],
        'md' => [
            'toggle' => 'h-6 w-11',
            'circle' => 'h-5 w-5',
            'translate' => 'translate-x-5',
        ],
        'lg' => [
            'toggle' => 'h-7 w-14',
            'circle' => 'h-6 w-6',
            'translate' => 'translate-x-7',
        ],
    ];

    $colorClasses = [
        'primary' => 'bg-[var(--color-primary)]',
        'success' => 'bg-[var(--color-success)]',
        'warning' => 'bg-[var(--color-warning)]',
        'danger' => 'bg-[var(--color-error)]',
    ];

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
@endphp

<div x-data="{ on: {{ $checked ? 'true' : 'false' }} }" class="flex items-start">
    <button
        type="button"
        role="switch"
        :aria-checked="on"
        @if($disabled) disabled @endif
        :class="{
            '{{ $colorClasses[$color] }}': on,
            'bg-gray-300 dark:bg-gray-600': !on,
        }"
        class="relative inline-flex shrink-0 {{ $sizeClasses[$size]['toggle'] }} rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-1 dark:focus:ring-offset-gray-900 {{ $disabledClasses }} {{ $class }}"
        @click="if(!{{ $disabled ? 'true' : 'false' }}) { on = !on; $refs.hiddenInput.value = on ? '{{ $value }}' : ''; $refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true })); }"
        @keydown.space.prevent="on = !on; $refs.hiddenInput.value = on ? '{{ $value }}' : ''; $refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));"
        aria-label="{{ $label ?: $name }}"
    >
        <span
            :class="{ '{{ $sizeClasses[$size]['translate'] }}': on, 'translate-x-0': !on }"
            class="pointer-events-none inline-block {{ $sizeClasses[$size]['circle'] }} rounded-full bg-white shadow transform ring-0 transition-transform duration-200 ease-in-out"
        ></span>
    </button>

    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ $checked ? $value : '' }}" />

    @if($label || $description)
        <div class="ml-3">
            @if($label)
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 {{ $disabledClasses }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500 ml-0.5">*</span>
                    @endif
                </label>
            @endif
            @if($description)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
            @endif
            @if($helperText && !$error)
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $helperText }}</p>
            @endif
        </div>
    @endif

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 ml-3">{{ $error }}</p>
    @endif
</div>
