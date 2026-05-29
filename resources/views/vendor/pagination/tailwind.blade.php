@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Mobile: Previous / Next --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl cursor-not-allowed transition
                             bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-white/30">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200
                          bg-gray-100 text-gray-700 hover:bg-gray-200
                          dark:bg-white/5 dark:text-white/80 dark:hover:bg-white/10">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200
                          bg-gray-100 text-gray-700 hover:bg-gray-200
                          dark:bg-white/5 dark:text-white/80 dark:hover:bg-white/10">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl cursor-not-allowed transition
                             bg-gray-100 text-gray-400 dark:bg-white/5 dark:text-white/30">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop: Full pagination --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm leading-5 text-gray-500 dark:text-white/60">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-gray-900 dark:text-white/85">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium text-gray-900 dark:text-white/85">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium text-gray-900 dark:text-white/85">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex rtl:flex-row-reverse rounded-xl overflow-hidden
                             border border-gray-200 dark:border-white/10">

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center px-2.5 py-2 text-sm font-medium cursor-not-allowed
                                         text-gray-300 bg-gray-50 dark:text-white/25 dark:bg-white/[0.03]"
                                  aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                           class="inline-flex items-center px-2.5 py-2 text-sm font-medium transition-all duration-150
                                  text-gray-600 bg-gray-50 hover:bg-gray-100
                                  dark:text-white/70 dark:bg-white/[0.05] dark:hover:bg-white/10"
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
                                <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium cursor-default
                                             text-gray-400 bg-gray-50 border-l border-gray-200
                                             dark:text-white/40 dark:bg-white/[0.03] dark:border-white/10">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-semibold cursor-default
                                                     text-white bg-primary border-l border-gray-200
                                                     dark:border-white/10">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium transition-all duration-150
                                              text-gray-600 bg-gray-50 hover:bg-gray-100 border-l border-gray-200
                                              dark:text-white/70 dark:bg-white/[0.05] dark:hover:bg-white/10 dark:border-white/10"
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
                           class="inline-flex items-center px-2.5 py-2 -ml-px text-sm font-medium transition-all duration-150
                                  text-gray-600 bg-gray-50 hover:bg-gray-100 border-l border-gray-200
                                  dark:text-white/70 dark:bg-white/[0.05] dark:hover:bg-white/10 dark:border-white/10"
                           aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center px-2.5 py-2 -ml-px text-sm font-medium cursor-not-allowed
                                         text-gray-300 bg-gray-50 border-l border-gray-200
                                         dark:text-white/25 dark:bg-white/[0.03] dark:border-white/10"
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
