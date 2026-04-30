@props(['items' => []])

<nav class="mb-4" aria-label="breadcrumb">
    <ol class="flex items-center gap-2 text-sm" style="color: rgba(235,235,245,0.6);">
        <li>
            <a href="{{ route('dashboard') }}" class="hover:text-white transition-apple inline-flex items-center gap-2">
                <i class="fas fa-home text-xs"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        @foreach($items as $index => $item)
            <li class="flex items-center gap-2">
                <span class="text-dark-text-tertiary">/</span>
                
                @if(isset($item['url']) && $index < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="hover:text-white transition-apple">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-white font-medium">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
