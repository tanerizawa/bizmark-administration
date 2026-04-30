@props([
    'name' => '',
    'type' => 'text',         // text | email | number | password | tel | url
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'size' => 'md',           // sm | md | lg
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'leadingIcon' => null,    // Font Awesome class, e.g. 'fa-solid fa-envelope'
    'trailingIcon' => null,
    'class' => '',
])

@php
    $inputClasses = 'block w-full rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0';

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3.5 text-base',
    ];

    $stateClasses = $error
        ? 'border-red-300 dark:border-red-500 text-red-900 dark:text-red-400 focus:ring-red-500 focus:border-red-500 placeholder-red-300'
        : 'border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] placeholder-gray-400 dark:placeholder-gray-500';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900' : '';

    $leadingPadding = $leadingIcon ? ($size === 'sm' ? 'pl-8' : ($size === 'lg' ? 'pl-11' : 'pl-9')) : '';
    $trailingPadding = $trailingIcon ? ($size === 'sm' ? 'pr-8' : ($size === 'lg' ? 'pr-11' : 'pr-9')) : '';

    $classes = trim("{$inputClasses} {$sizeClasses[$size]} {$stateClasses} {$disabledClasses} {$leadingPadding} {$trailingPadding} {$class}");
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($leadingIcon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <i class="{{ $leadingIcon }}"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $classes]) }}
        />

        @if($trailingIcon)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                <i class="{{ $trailingIcon }}"></i>
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helperText }}</p>
    @endif
</div>
