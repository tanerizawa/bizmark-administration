@props([
    'name' => '',
    'label' => '',
    'accept' => '',             // e.g. '.pdf,.doc,.docx'
    'multiple' => false,
    'maxSize' => '2MB',         // Human readable
    'maxFiles' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'size' => 'md',             // sm | md | lg
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'p-4 text-sm',
        'md' => 'p-6 text-sm',
        'lg' => 'p-8 text-base',
    ];

    $iconSizeClasses = [
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
    ];

    $stateClasses = $error
        ? 'border-red-300 dark:border-red-500 bg-red-50 dark:bg-red-900/10'
        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
@endphp

<div
    x-data="{
        fileName: '',
        fileSize: '',
        dragOver: false,
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = file.size > 1024 * 1024
                    ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                    : (file.size / 1024).toFixed(1) + ' KB';
            }
        },
        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                this.fileName = file.name;
                this.fileSize = file.size > 1024 * 1024
                    ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                    : (file.size / 1024).toFixed(1) + ' KB';
                $refs.fileInput.files = event.dataTransfer.files;
            }
        }
    }"
    class="w-full {{ $class }}"
>
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    {{-- Drop zone --}}
    <div
        class="relative {{ $sizeClasses[$size] }} {{ $stateClasses }} {{ $disabledClasses }} border-2 border-dashed rounded-xl transition-all duration-200"
        :class="{ 'border-[var(--color-primary)] bg-[var(--color-primary)]/5': dragOver }"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="handleDrop"
    >
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $name }}"
            x-ref="fileInput"
            @change="handleFileSelect"
            accept="{{ $accept }}"
            @if($multiple) multiple @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            class="absolute inset-0 w-full h-full opacity-0 {{ $disabledClasses }}"
        />

        <div class="flex flex-col items-center justify-center text-center">
            {{-- Upload icon --}}
            <div class="{{ $iconSizeClasses[$size] }} mb-3 text-gray-400 dark:text-gray-500">
                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>

            {{-- File selected state --}}
            <template x-if="!fileName">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-[var(--color-primary)]">Klik untuk upload</span>
                        atau drag & drop
                    </p>
                    @if($accept)
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                            Format: {{ $accept }}
                        </p>
                    @endif
                    @if($maxSize)
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Maks. {{ $maxSize }}
                            @if($maxFiles)
                                , {{ $maxFiles }} file
                            @endif
                        </p>
                    @endif
                </div>
            </template>

            {{-- Selected file display --}}
            <template x-if="fileName">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-[var(--color-primary)]/10 text-[var(--color-primary)]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="fileName"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="fileSize"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ $helperText }}</p>
    @endif
</div>
