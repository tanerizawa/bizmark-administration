@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Mobile: Previous / Next --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-apple cursor-not-allowed transition"
                      style="color: rgba(235,235,245,0.3); background: rgba(255,255,255,0.04); border: 1px solid rgba(84,84,88,0.36);">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-apple transition-all duration-200"
                   style="color: rgba(235,235,245,0.8); background: rgba(255,255,255,0.06); border: 1px solid rgba(84,84,88,0.36);">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-apple transition-all duration-200"
                   style="color: rgba(235,235,245,0.8); background: rgba(255,255,255,0.06); border: 1px solid rgba(84,84,88,0.36);">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-apple cursor-not-allowed transition"
                      style="color: rgba(235,235,245,0.3); background: rgba(255,255,255,0.04); border: 1px solid rgba(84,84,88,0.36);">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop: Full pagination --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm leading-5" style="color: rgba(235,235,245,0.6);">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium" style="color: rgba(235,235,245,0.85);">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium" style="color: rgba(235,235,245,0.85);">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium" style="color: rgba(235,235,245,0.85);">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex rtl:flex-row-reverse rounded-apple overflow-hidden" style="border: 1px solid rgba(84,84,88,0.36);">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-2.5 py-2 text-sm font-medium cursor-not-allowed"
                                  style="color: rgba(235,235,245,0.25); background: rgba(255,255,255,0.03);"
                                  aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                           class="inline-flex items-center px-2.5 py-2 text-sm font-medium transition-all duration-150"
                           style="color: rgba(235,235,245,0.7); background: rgba(255,255,255,0.05);"
                           onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                           aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium cursor-default"
                                      style="color: rgba(235,235,245,0.4); background: rgba(255,255,255,0.03); border-left: 1px solid rgba(84,84,88,0.36);">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-semibold cursor-default"
                                              style="color: #FFFFFF; background: rgba(0,122,255,0.35); border-left: 1px solid rgba(84,84,88,0.36);">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium transition-all duration-150"
                                       style="color: rgba(235,235,245,0.7); background: rgba(255,255,255,0.05); border-left: 1px solid rgba(84,84,88,0.36);"
                                       onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                                       aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                           class="inline-flex items-center px-2.5 py-2 -ml-px text-sm font-medium transition-all duration-150"
                           style="color: rgba(235,235,245,0.7); background: rgba(255,255,255,0.05); border-left: 1px solid rgba(84,84,88,0.36);"
                           onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                           aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-2.5 py-2 -ml-px text-sm font-medium cursor-not-allowed"
                                  style="color: rgba(235,235,245,0.25); background: rgba(255,255,255,0.03); border-left: 1px solid rgba(84,84,88,0.36);"
                                  aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
