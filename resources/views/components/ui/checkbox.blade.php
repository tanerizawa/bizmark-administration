@props([
    'name' => '',
    'label' => '',
    'value' => '1',
    'checked' => false,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'description' => '',        // Longer description text below label
    'indeterminate' => false,
    'size' => 'md',             // sm | md | lg
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
    ];

    $checkboxClasses = 'rounded border-gray-300 dark:border-gray-600 text-[var(--color-primary)] focus:ring-[var(--color-primary)] focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-gray-900 transition-all duration-150';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';

    $classes = trim("{$checkboxClasses} {$sizeClasses[$size]} {$disabledClasses} {$class}");
@endphp

<div class="relative flex items-start">
    <div class="flex items-center h-5">
        @if($indeterminate)
            {{-- Indeterminate checkbox using Alpine.js --}}
            <div x-data="{ checked: {{ $checked ? 'true' : 'false' }}, indeterminate: true }"
                 x-init="$refs.checkbox.indeterminate = indeterminate"
                 class="relative">
                <input
                    type="checkbox"
                    name="{{ $name }}"
                    id="{{ $name }}"
                    value="{{ $value }}"
                    x-ref="checkbox"
                    x-model="checked"
                    @change="$refs.checkbox.indeterminate = false"
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    {{ $attributes->merge(['class' => $classes . ' rounded']) }}
                />
            </div>
        @else
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $name }}"
                value="{{ $value }}"
                @if($checked) checked @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => $classes]) }}
            />
        @endif
    </div>

    @if($label || $description)
        <div class="ml-3 text-sm">
            @if($label)
                <label for="{{ $name }}" class="font-medium text-gray-700 dark:text-gray-300 {{ $disabledClasses }}">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500 ml-0.5">*</span>
                    @endif
                </label>
            @endif
            @if($description)
                <p class="text-gray-500 dark:text-gray-400 {{ $label ? 'mt-0.5' : '' }}">{{ $description }}</p>
            @endif
            @if($helperText && !$error)
                <p class="text-gray-400 dark:text-gray-500 mt-0.5">{{ $helperText }}</p>
            @endif
        </div>
    @endif

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 ml-8">{{ $error }}</p>
    @endif
</div>
