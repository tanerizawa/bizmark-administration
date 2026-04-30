@props([
    'variant' => 'text',        // text | circle | rect | card
    'lines' => 3,               // For text variant
    'width' => 'full',          // For rect variant
    'height' => '4',            // For rect variant (in Tailwind spacing units)
    'class' => '',
])

@php
    $baseClasses = 'animate-pulse bg-gray-200 dark:bg-gray-700';
@endphp

<div class="{{ $class }}" {{ $attributes }}>
    @if($variant === 'text')
        <div class="space-y-2.5">
            @for($i = 0; $i < $lines; $i++)
                <div
                    class="{{ $baseClasses }} rounded h-4"
                    style="width: {{ $i === $lines - 1 ? '60%' : '100' }}%"
                ></div>
            @endfor
        </div>

    @elseif($variant === 'circle')
        <div class="{{ $baseClasses }} rounded-full {{ $width ? 'w-'.$width : 'w-10' }} {{ $height ? 'h-'.$height : 'h-10' }}"></div>

    @elseif($variant === 'rect')
        <div class="{{ $baseClasses }} rounded-xl {{ $width ? 'w-'.$width : 'w-full' }} {{ $height ? 'h-'.$height : 'h-32' }}"></div>

    @elseif($variant === 'card')
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 space-y-4">
            {{-- Card header --}}
            <div class="flex items-center gap-3">
                <div class="{{ $baseClasses }} rounded-full w-10 h-10"></div>
                <div class="space-y-2 flex-1">
                    <div class="{{ $baseClasses }} rounded h-4 w-1/3"></div>
                    <div class="{{ $baseClasses }} rounded h-3 w-1/4"></div>
                </div>
            </div>

            {{-- Card body --}}
            <div class="space-y-2.5">
                <div class="{{ $baseClasses }} rounded h-3 w-full"></div>
                <div class="{{ $baseClasses }} rounded h-3 w-5/6"></div>
                <div class="{{ $baseClasses }} rounded h-3 w-2/3"></div>
            </div>

            {{-- Card footer --}}
            <div class="flex gap-2">
                <div class="{{ $baseClasses }} rounded-lg h-9 w-24"></div>
                <div class="{{ $baseClasses }} rounded-lg h-9 w-24"></div>
            </div>
        </div>

    @elseif($variant === 'table')
        <div class="space-y-3">
            {{-- Table header --}}
            <div class="flex gap-4">
                @for($i = 0; $i < 4; $i++)
                    <div class="{{ $baseClasses }} rounded h-5 w-1/4"></div>
                @endfor
            </div>

            {{-- Table rows --}}
            @for($row = 0; $row < $lines; $row++)
                <div class="flex gap-4">
                    @for($col = 0; $col < 4; $col++)
                        <div class="{{ $baseClasses }} rounded h-4 w-1/4"></div>
                    @endfor
                </div>
            @endfor
        </div>
    @endif
</div>
