@props([
    'items' => [],   // [['title' => '...', 'description' => '...', 'date' => '...', 'status' => 'completed|current|pending', 'icon' => 'fas fa-check']]
])

<ol class="portal-timeline relative space-y-4">
    @foreach($items as $i => $item)
        @php
            $status = $item['status'] ?? 'pending';
            $isLast = $loop->last;
            $dotClass = match($status) {
                'completed' => 'bg-[var(--client-primary)] text-white border-[var(--client-primary)]',
                'current'   => 'bg-[var(--client-primary-light)] text-[var(--client-primary)] border-[var(--client-primary)] ring-4 ring-[var(--client-primary)]/15',
                default     => 'bg-[var(--surface)] text-[var(--text-tertiary)] border-[var(--border-default)]',
            };
            $lineClass = $status === 'completed' ? 'bg-[var(--client-primary)]' : 'bg-[var(--border-subtle)]';
        @endphp
        <li class="relative pl-10">
            {{-- Vertical connector line --}}
            @if(!$isLast)
                <span class="absolute left-[14px] top-7 bottom-[-1rem] w-px {{ $lineClass }}" aria-hidden="true"></span>
            @endif

            {{-- Dot / icon --}}
            <span class="absolute left-0 top-0.5 w-7 h-7 rounded-full border-2 inline-flex items-center justify-center text-[10px] {{ $dotClass }}">
                @if(!empty($item['icon']))
                    <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                @elseif($status === 'completed')
                    <i class="fas fa-check" aria-hidden="true"></i>
                @else
                    <span class="font-semibold text-[10px]">{{ $i + 1 }}</span>
                @endif
            </span>

            <div class="min-w-0">
                <div class="flex items-baseline justify-between gap-2 flex-wrap">
                    <p class="text-sm font-semibold text-[var(--text-primary)] leading-tight">
                        {{ $item['title'] ?? '' }}
                    </p>
                    @if(!empty($item['date']))
                        <time class="text-xs text-[var(--text-tertiary)] tabular-nums">{{ $item['date'] }}</time>
                    @endif
                </div>
                @if(!empty($item['description']))
                    <p class="text-xs text-[var(--text-secondary)] mt-1 leading-relaxed">
                        {{ $item['description'] }}
                    </p>
                @endif
            </div>
        </li>
    @endforeach
</ol>
