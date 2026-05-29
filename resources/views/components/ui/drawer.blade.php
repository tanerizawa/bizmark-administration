@props([
    'name' => 'drawer',           // unique name for x-data scope (Alpine $store key)
    'title' => null,
    'subtitle' => null,
    'size' => 'md',               // sm(360) | md(480) | lg(640)
])

@php
    $widths = [
        'sm' => 'sm:w-[360px]',
        'md' => 'sm:w-[480px]',
        'lg' => 'sm:w-[640px]',
    ];
    $widthClass = $widths[$size] ?? $widths['md'];
@endphp

{{--
    Usage:
      <x-ui.drawer name="appDetail" title="Permohonan #234">
          <x-slot:header>...</x-slot:header>
          ...content...
          <x-slot:footer>
              <x-ui.button>Action</x-ui.button>
          </x-slot:footer>
      </x-ui.drawer>

    Open via Alpine: $dispatch('drawer-open', { name: 'appDetail' })
    Close via:       $dispatch('drawer-close', { name: 'appDetail' })
--}}

<div
    x-data="{
        open: false,
        name: @js($name),
        openDrawer(detail) { if (detail?.name === this.name) { this.open = true; document.body.style.overflow = 'hidden'; } },
        closeDrawer(detail) { if (!detail || detail.name === this.name) { this.open = false; document.body.style.overflow = ''; } },
    }"
    @drawer-open.window="openDrawer($event.detail)"
    @drawer-close.window="closeDrawer($event.detail)"
    @keydown.escape.window="closeDrawer()"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        class="portal-drawer-backdrop"
        :data-open="open"
        @click="closeDrawer()"
        aria-hidden="true"
    ></div>

    {{-- Drawer --}}
    <aside
        class="portal-drawer w-full {{ $widthClass }}"
        :data-open="open"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="`${name}-title`"
        x-trap.noscroll="open"
    >
        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 px-5 py-4 border-b border-[var(--border-subtle)] flex-shrink-0">
            <div class="min-w-0">
                @isset($header)
                    {{ $header }}
                @else
                    @if($title)
                        <h2 :id="`${name}-title`" class="text-base font-semibold text-[var(--text-primary)] leading-tight truncate">
                            {{ $title }}
                        </h2>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $subtitle }}</p>
                    @endif
                @endisset
            </div>
            <button
                type="button"
                @click="closeDrawer()"
                class="flex-shrink-0 w-8 h-8 inline-flex items-center justify-center rounded-md text-[var(--text-tertiary)] hover:bg-[var(--surface-sunken)] hover:text-[var(--text-primary)] transition-colors"
                aria-label="Tutup"
            >
                <i class="fas fa-xmark text-sm" aria-hidden="true"></i>
            </button>
        </div>

        {{-- Body (scrollable) --}}
        <div class="flex-1 overflow-y-auto px-5 py-4">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-[var(--border-subtle)] bg-[var(--surface-cool)] flex-shrink-0">
                {{ $footer }}
            </div>
        @endisset
    </aside>
</div>
