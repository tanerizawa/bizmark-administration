@props([
    'id' => 'modal-'.uniqid(),
    'title' => '',
    'size' => 'md',          // sm | md | lg | xl | full
    'submitLabel' => 'Simpan',
    'cancelLabel' => 'Batal',
    'submitAction' => null,  // Alpine.js expression
    'class' => '',
])

@php
    $sizeClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-full mx-4',
    ];

    $classes = trim("bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full {$sizeClasses[$size]} {$class}");
@endphp

<div
    x-data="{ open: false }"
    x-id="['modal-title']"
    @keydown.escape.window="open = false"
    {{ $attributes->whereDoesntStartWith('x-') }}
>
    {{ $trigger }}

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                @click="open = false"
                aria-hidden="true"
            >
            </div>

            {{-- Modal Panel --}}
            <div
                x-show="open"
                x-transition.duration.300ms
                class="relative flex items-center justify-center min-h-screen p-4"
            >
                <div
                    @click.outside="open = false"
                    :aria-labelledby="$id('modal-title')"
                    role="dialog"
                    aria-modal="true"
                    class="{{ $classes }}"
                >
                    {{-- Header --}}
                    @if($title)
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h2
                                :id="$id('modal-title')"
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                {{ $title }}
                            </h2>
                            <button
                                @click="open = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                aria-label="Tutup"
                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- Body --}}
                    <div class="px-6 py-4">
                        {{ $slot }}
                    </div>

                    {{-- Footer --}}
                    @if(!empty($footer) || $submitLabel)
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                            @isset($footer)
                                {{ $footer }}
                            @else
                                <x-ui.button variant="ghost" @click="open = false" size="sm">
                                    {{ $cancelLabel }}
                                </x-ui.button>
                                <x-ui.button
                                    variant="primary"
                                    size="sm"
                                    @if($submitAction) @click="{{ $submitAction }}" @endif
                                >
                                    {{ $submitLabel }}
                                </x-ui.button>
                            @endisset
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
