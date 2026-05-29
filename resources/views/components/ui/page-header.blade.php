@props([
    'title' => '',
    'description' => null,
    'eyebrow' => null,        // small caps label above title
    'icon' => null,           // FA class for eyebrow
    'breadcrumbs' => [],      // [['label' => 'Beranda', 'url' => '/'], ['label' => 'Permohonan']]
    'sticky' => false,
])

@php
    $stickyClass = $sticky ? 'sticky top-0 z-30 bg-[var(--surface)]/85 backdrop-blur supports-[backdrop-filter]:bg-[var(--surface)]/70' : '';
@endphp

<header class="portal-page-header {{ $stickyClass }} border-b border-[var(--border-subtle)]">
    <div class="px-4 lg:px-8 py-4 lg:py-5">

        {{-- Breadcrumbs --}}
        @if(!empty($breadcrumbs))
            <nav aria-label="Breadcrumb" class="mb-2">
                <ol class="flex items-center flex-wrap gap-x-1.5 gap-y-1 text-xs text-[var(--text-tertiary)]">
                    @foreach($breadcrumbs as $i => $crumb)
                        <li class="flex items-center gap-x-1.5">
                            @if($i > 0)
                                <i class="fas fa-chevron-right text-[10px] text-[var(--text-tertiary)]/60" aria-hidden="true"></i>
                            @endif

                            @if(!empty($crumb['url']) && !$loop->last)
                                <a href="{{ $crumb['url'] }}"
                                   class="hover:text-[var(--text-primary)] transition-colors duration-150 inline-flex items-center gap-1">
                                    @if(!empty($crumb['icon']))
                                        <i class="{{ $crumb['icon'] }} text-[10px]" aria-hidden="true"></i>
                                    @endif
                                    {{ $crumb['label'] }}
                                </a>
                            @else
                                <span class="text-[var(--text-secondary)] font-medium" @if($loop->last) aria-current="page" @endif>
                                    {{ $crumb['label'] }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="min-w-0 flex-1">
                {{-- Eyebrow chip --}}
                @if($eyebrow)
                    <div class="mb-2">
                        <span class="portal-eyebrow">
                            @if($icon)<i class="{{ $icon }} text-[9px]" aria-hidden="true"></i>@endif
                            {{ $eyebrow }}
                        </span>
                    </div>
                @endif

                {{-- Title --}}
                <h1 class="text-xl lg:text-2xl font-semibold text-[var(--text-primary)] leading-tight tracking-tight">
                    {{ $title ?: $slot }}
                </h1>

                {{-- Description --}}
                @if($description)
                    <p class="mt-1.5 text-sm text-[var(--text-secondary)] leading-relaxed max-w-2xl">
                        {{ $description }}
                    </p>
                @endif
            </div>

            {{-- Actions slot (right side) --}}
            @isset($actions)
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        {{-- Optional below-title slot (filters, tabs, etc) --}}
        @isset($below)
            <div class="mt-4">
                {{ $below }}
            </div>
        @endisset
    </div>
</header>
