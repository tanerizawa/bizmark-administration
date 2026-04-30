@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'size' => 'md',             // sm | md | lg
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helperText' => '',
    'showCharCount' => false,
    'maxLength' => null,
    'resize' => 'vertical',     // none | vertical | both
    'class' => '',
])

@php
    $textareaClasses = 'block w-full rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0';

    $resizeClasses = [
        'none' => 'resize-none',
        'vertical' => 'resize-y',
        'both' => 'resize',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3.5 text-base',
    ];

    $stateClasses = $error
        ? 'border-red-300 dark:border-red-500 text-red-900 dark:text-red-400 focus:ring-red-500 focus:border-red-500 placeholder-red-300'
        : 'border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] placeholder-gray-400 dark:placeholder-gray-500';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900' : '';
    $readonlyClasses = $readonly ? 'bg-gray-50 dark:bg-gray-900 cursor-default' : '';

    $classes = trim("{$textareaClasses} {$resizeClasses[$resize]} {$sizeClasses[$size]} {$stateClasses} {$disabledClasses} {$readonlyClasses} {$class}");
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
            @if($maxLength)
                <span class="text-xs text-gray-400 font-normal ml-2">(max. {{ $maxLength }} karakter)</span>
            @endif
        </label>
    @endif

    @if($showCharCount)
        {{-- Textarea with character count using Alpine.js --}}
        <div
            x-data="{
                value: '{{ str_replace(["'", "\n"], ["\\'", '\\n'], $value) }}',
                maxLength: {{ $maxLength ?? 'null' }},
                get count() { return this.value.length; }
            }"
            class="relative"
        >
            <textarea
                name="{{ $name }}"
                id="{{ $name }}"
                rows="{{ $rows }}"
                placeholder="{{ $placeholder }}"
                x-model="value"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                @if($maxLength) maxlength="{{ $maxLength }}" @endif
                {{ $attributes->merge(['class' => $classes . ' pb-8']) }}
            >{{ $value }}</textarea>

            <div class="absolute bottom-2 right-3 text-xs text-gray-400 dark:text-gray-500">
                <span x-text="count"></span>
                @if($maxLength)
                    <span>/ {{ $maxLength }}</span>
                @endif
            </div>
        </div>
    @else
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($maxLength) maxlength="{{ $maxLength }}" @endif
            {{ $attributes->merge(['class' => $classes]) }}
        >{{ $value }}</textarea>
    @endif

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helperText }}</p>
    @endif
</div>
