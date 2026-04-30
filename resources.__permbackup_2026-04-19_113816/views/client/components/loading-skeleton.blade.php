@props(['type' => 'card', 'count' => 1])

@if($type === 'card')
    @for($i = 0; $i < $count; $i++)
    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
        <div class="flex items-center justify-between mb-4">
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton w-12 h-8"></div>
        </div>
        <div class="skeleton skeleton-text w-3/4"></div>
        <div class="skeleton skeleton-text w-1/2"></div>
    </div>
    @endfor
@elseif($type === 'metric')
    @for($i = 0; $i < $count; $i++)
    <div class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
        <div class="flex items-center justify-between">
            <div>
                <div class="skeleton skeleton-text w-24 mb-2"></div>
                <div class="skeleton h-8 w-16 mb-1"></div>
                <div class="skeleton skeleton-text w-20"></div>
            </div>
            <div class="skeleton skeleton-avatar"></div>
        </div>
    </div>
    @endfor
@elseif($type === 'list')
    @for($i = 0; $i < $count; $i++)
    <div class="flex items-center gap-4 p-4 bg-white rounded-lg animate-pulse">
        <div class="skeleton skeleton-avatar flex-shrink-0"></div>
        <div class="flex-1">
            <div class="skeleton skeleton-text w-3/4 mb-2"></div>
            <div class="skeleton skeleton-text w-1/2"></div>
        </div>
    </div>
    @endfor
@elseif($type === 'table')
    <div class="bg-white rounded-lg shadow-sm overflow-hidden animate-pulse">
        <div class="p-6 border-b border-gray-200">
            <div class="skeleton skeleton-title w-48"></div>
        </div>
        <div class="divide-y divide-gray-200">
            @for($i = 0; $i < $count; $i++)
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="skeleton skeleton-text w-1/3"></div>
                    <div class="skeleton w-20 h-6"></div>
                </div>
                <div class="skeleton skeleton-text w-1/2"></div>
            </div>
            @endfor
        </div>
    </div>
@endif
