@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'options' => [],            // [['value' => '1', 'label' => 'Option 1'], ...] OR ['value' => 'label']
    'value' => '',
    'error' => null,
    'size' => 'md',             // sm | md | lg
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'leadingIcon' => null,      // Font Awesome class
    'multiple' => false,
    'searchable' => false,      // Will add Alpine.js filter
    'class' => '',
])

@php
    $selectClasses = 'block w-full rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0 appearance-none bg-no-repeat';
    $selectClasses .= " bg-[url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e\")]";

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm pr-8 bg-[length:16px_12px] bg-[right_0.5rem_center]',
        'md' => 'px-4 py-2.5 text-sm pr-10 bg-[length:16px_12px] bg-[right_0.75rem_center]',
        'lg' => 'px-5 py-3.5 text-base pr-12 bg-[length:16px_12px] bg-[right_1rem_center]',
    ];

    $stateClasses = $error
        ? 'border-red-300 dark:border-red-500 text-red-900 dark:text-red-400 focus:ring-red-500 focus:border-red-500'
        : 'border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)]';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-gray-900' : '';

    $classes = trim("{$selectClasses} {$sizeClasses[$size]} {$stateClasses} {$disabledClasses} {$class}");

    // Normalize options: if simple array ['val' => 'label'], convert to [['value' => 'val', 'label' => 'label']]
    $normalizedOptions = [];
    foreach ($options as $key => $option) {
        if (is_array($option) && isset($option['value'])) {
            $normalizedOptions[] = $option;
        } elseif (is_array($option) && isset($option['label'])) {
            $normalizedOptions[] = $option;
        } else {
            $normalizedOptions[] = ['value' => $key, 'label' => $option];
        }
    }
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
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 z-10">
                <i class="{{ $leadingIcon }}"></i>
            </div>
        @endif

        @if($searchable)
            {{-- Searchable select with Alpine.js --}}
            <div
                x-data="{
                    open: false,
                    query: '',
                    selectedValue: '{{ $value }}',
                    get filteredOptions() {
                        return {{ json_encode($normalizedOptions) }}.filter(o =>
                            o.label.toLowerCase().includes(this.query.toLowerCase())
                        );
                    },
                    select(val) {
                        this.selectedValue = val;
                        this.open = false;
                        this.query = '';
                        $refs.hiddenInput.value = val;
                        $refs.hiddenInput.dispatchEvent(new Event('change'));
                    }
                }"
                class="relative"
            >
                <button
                    type="button"
                    @click="open = !open"
                    @keydown.escape="open = false"
                    :aria-expanded="open"
                    aria-haspopup="listbox"
                    :class="{
                        'ring-2 ring-[var(--color-primary)] border-[var(--color-primary)]': open,
                    }"
                    class="{{ $classes }} w-full text-left"
                >
                    <span x-text="selectedValue ? ({{ json_encode($normalizedOptions) }}.find(o => o.value == selectedValue)?.label || '{{ $placeholder }}') : '{{ $placeholder }}'"
                          :class="{'text-gray-400': !selectedValue}"
                          class="{{ $value ? '' : 'text-gray-400' }}"
                    >{{ $value ? (collect($normalizedOptions)->firstWhere('value', $value)['label'] ?? $placeholder) : $placeholder }}</span>
                </button>

                <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ $value }}" />

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute z-50 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-lg"
                    role="listbox"
                >
                    <div class="p-2">
                        <input
                            type="text"
                            x-model="query"
                            placeholder="Cari..."
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                            @click.stop
                        />
                    </div>

                    <template x-if="filteredOptions.length === 0">
                        <div class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                            Tidak ada hasil
                        </div>
                    </template>

                    <template x-for="(option, index) in filteredOptions" :key="index">
                        <button
                            type="button"
                            @click="select(option.value)"
                            :class="{
                                'bg-[var(--color-primary)]/10 text-[var(--color-primary)]': selectedValue == option.value,
                                'hover:bg-gray-100 dark:hover:bg-gray-700': selectedValue != option.value,
                            }"
                            class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors duration-150"
                            role="option"
                            :aria-selected="selectedValue == option.value"
                            x-text="option.label"
                        ></button>
                    </template>
                </div>
            </div>
        @else
            <select
                name="{{ $name }}"
                id="{{ $name }}"
                @if($multiple) multiple @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => $classes]) }}
            >
                @if($placeholder)
                    <option value="" disabled selected>{{ $placeholder }}</option>
                @endif
                @foreach($normalizedOptions as $option)
                    <option
                        value="{{ $option['value'] }}"
                        {{ old($name, $value) == $option['value'] ? 'selected' : '' }}
                    >
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helperText }}</p>
    @endif
</div>
