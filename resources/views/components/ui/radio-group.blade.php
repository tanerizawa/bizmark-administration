@props([
    'name' => '',
    'label' => '',
    'options' => [],            // [['value' => '1', 'label' => 'Option 1', 'description' => ''], ...]
    'value' => '',
    'error' => null,
    'required' => false,
    'disabled' => false,
    'variant' => 'default',     // default | card
    'helperText' => '',
    'class' => '',
])

@php
    $radioClasses = 'border-gray-300 dark:border-gray-600 text-[var(--color-primary)] focus:ring-[var(--color-primary)] focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-gray-900 transition-all duration-150';
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
@endphp

<fieldset class="{{ $class }}">
    @if($label)
        <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </legend>
    @endif

    <div class="space-y-3">
        @foreach($options as $option)
            @php
                $optionValue = is_array($option) ? ($option['value'] ?? '') : '';
                $optionLabel = is_array($option) ? ($option['label'] ?? '') : $option;
                $optionDescription = is_array($option) ? ($option['description'] ?? '') : '';
                $optionDisabled = is_array($option) ? ($option['disabled'] ?? false) : false;
                $isDisabled = $disabled || $optionDisabled;
                $isSelected = old($name, $value) == $optionValue;
            @endphp

            @if($variant === 'card')
                {{-- Card-style radio option --}}
                <label class="relative flex cursor-pointer rounded-xl border p-4 transition-all duration-150
                    {{ $isSelected ? 'border-[var(--color-primary)] ring-1 ring-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}
                    {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    <div class="flex items-start gap-3 w-full">
                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $optionValue }}"
                            {{ $isSelected ? 'checked' : '' }}
                            {{ $isDisabled ? 'disabled' : '' }}
                            {{ $required ? 'required' : '' }}
                            class="mt-0.5 {{ $radioClasses }}"
                        />
                        <div class="flex-1">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                {{ $optionLabel }}
                            </span>
                            @if($optionDescription)
                                <span class="block mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $optionDescription }}
                                </span>
                            @endif
                        </div>
                    </div>
                </label>
            @else
                {{-- Default inline radio option --}}
                <label class="relative flex items-start {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                    <div class="flex items-center h-5">
                        <input
                            type="radio"
                            name="{{ $name }}"
                            value="{{ $optionValue }}"
                            {{ $isSelected ? 'checked' : '' }}
                            {{ $isDisabled ? 'disabled' : '' }}
                            {{ $required ? 'required' : '' }}
                            class="{{ $radioClasses }} {{ $disabledClasses }}"
                        />
                    </div>
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $optionLabel }}
                        </span>
                        @if($optionDescription)
                            <span class="block mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $optionDescription }}
                            </span>
                        @endif
                    </div>
                </label>
            @endif
        @endforeach
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helperText }}</p>
    @endif
</fieldset>
