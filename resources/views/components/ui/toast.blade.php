@props([
    'position' => 'top-right',  // top-right | top-left | bottom-right | bottom-left | top-center | bottom-center
    'maxVisible' => 5,
    'duration' => 3000,         // Default duration in ms
    'class' => '',
])

@php
    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 -translate-x-1/2',
    ];

    $typeClasses = [
        'success' => 'bg-[var(--color-success)] text-white',
        'error' => 'bg-[var(--color-error)] text-white',
        'warning' => 'bg-[var(--color-warning)] text-white',
        'info' => 'bg-[var(--color-info)] text-white',
    ];

    $typeIcons = [
        'success' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'error' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'warning' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
        'info' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];
@endphp

{{-- Toast Container — Place once at layout level --}}
<div
    x-data="{
        toasts: [],
        addToast(event) {
            const detail = event.detail || {};
            this.toasts.push({
                id: Date.now() + Math.random(),
                message: detail.message || '',
                type: detail.type || 'info',
                duration: detail.duration || {{ $duration }},
                title: detail.title || null,
            });

            // Limit visible toasts
            if (this.toasts.length > {{ $maxVisible }}) {
                this.toasts.shift();
            }

            // Auto-remove
            const toast = this.toasts[this.toasts.length - 1];
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== toast.id);
            }, toast.duration);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="addToast"
    class="fixed {{ $positionClasses[$position] }} z-[9999] flex flex-col gap-2 w-full max-w-sm {{ $class }}"
    aria-live="polite"
    aria-atomic="true"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 translate-x-2"
            x-transition:enter-end="transform opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform opacity-100 translate-x-0"
            x-transition:leave-end="transform opacity-0 translate-x-2"
            :class="{
                'bg-[var(--color-success)] text-white': toast.type === 'success',
                'bg-[var(--color-error)] text-white': toast.type === 'error',
                'bg-[var(--color-warning)] text-white': toast.type === 'warning',
                'bg-[var(--color-info)] text-white': toast.type === 'info',
            }"
            class="flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg"
            role="alert"
        >
            {{-- Icon --}}
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <template x-if="toast.title">
                    <p class="text-sm font-semibold" x-text="toast.title"></p>
                </template>
                <p class="text-sm opacity-90" x-text="toast.message"></p>
            </div>

            {{-- Close button --}}
            <button
                @click="removeToast(toast.id)"
                class="shrink-0 opacity-70 hover:opacity-100 transition-opacity"
                aria-label="Close"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
