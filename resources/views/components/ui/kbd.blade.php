@props([
    'keys' => [],          // array of keys, e.g. ['Cmd', 'K']
    'separator' => '+',
])

@php
    $list = is_array($keys) ? $keys : preg_split('/\s*\+\s*/', (string) $keys);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }}>
    @foreach($list as $i => $k)
        @if($i > 0)
            <span class="text-[10px] text-[var(--text-tertiary)]">{{ $separator }}</span>
        @endif
        <kbd>{{ $k }}</kbd>
    @endforeach
</span>
