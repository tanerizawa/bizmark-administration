<div class="space-y-4">
    {{-- Header Section --}}
    <div class="email-panel-header">
        <div>
            <h2 class="text-base font-semibold text-white">Email Templates</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Buat dan kelola template email untuk kampanye marketing
            </p>
        </div>
        <a href="{{ route('admin.templates.create') ?? '#' }}" class="btn-apple-primary-sm px-3 py-2">
            <i class="fas fa-plus mr-2"></i>New Template
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.email-management.index') }}" class="email-toolbar">
        <input type="hidden" name="tab" value="templates">
        <div class="email-toolbar-grid compact-4">
            <div class="email-filter">
                <label for="template-search">Pencarian</label>
                <input id="template-search" type="text" placeholder="Nama template atau subject..." name="search"
                       class="input-apple w-full" value="{{ request('tab') === 'templates' ? request('search') : '' }}">
            </div>
            <div class="email-filter">
                <label for="template-category">Kategori</label>
                <select id="template-category" name="category" class="input-apple w-full">
                    <option value="">Semua Kategori</option>
                    @foreach(($categories ?? []) as $category)
                        <option value="{{ $category }}" {{ request('tab') === 'templates' && request('category') === $category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.email-management.index', ['tab' => 'templates']) }}" class="btn-apple-sm px-4 py-2">
                Reset
            </a>
        </div>
    </form>

    {{-- Template Grid --}}
    @if(isset($templates) && $templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($templates as $template)
            <div class="card-elevated rounded-apple-lg overflow-hidden hover:bg-opacity-80 transition-apple email-table-shell">
                    {{-- Thumbnail --}}
                        <div class="h-32 overflow-hidden relative" style="background: linear-gradient(135deg, rgba(10,132,255,0.2), rgba(30,86,172,0.2));">
                        @if($template->thumbnail)
                            <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <i class="fas fa-file-code text-3xl" style="color: rgba(235,235,245,0.3);"></i>
                            </div>
                        @endif
                        
                        {{-- Status Badge --}}
                        @if($template->is_active)
                            <span class="absolute top-3 right-3 px-2 py-1 text-xs font-semibold rounded-full bg-green-500/80 text-white">
                                Active
                            </span>
                        @else
                            <span class="absolute top-3 right-3 px-2 py-1 text-xs font-semibold rounded-full bg-gray-500/80 text-white">
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-base font-semibold text-white flex-1">
                                {{ $template->name }}
                            </h3>
                            @if($template->category)
                                <span class="px-2 py-0.5 text-xs rounded-full ml-2" 
                                      style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">
                                    {{ ucfirst($template->category) }}
                                </span>
                            @endif
                        </div>

                        <p class="text-sm mb-3 line-clamp-2" style="color: rgba(235,235,245,0.6);">
                            {{ $template->subject }}
                        </p>

                        {{-- Variables --}}
                        @if($template->variables && count($template->variables) > 0)
                            <div class="mb-3">
                                <p class="text-xs mb-1" style="color: rgba(235,235,245,0.5);">Variables:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($template->variables, 0, 3) as $variable)
                                        <code class="px-2 py-0.5 text-xs rounded" 
                                              style="background: rgba(255,255,255,0.05); color: rgba(90,200,250,1);">
                                            @{{ $variable }}
                                        </code>
                                    @endforeach
                                    @if(count($template->variables) > 3)
                                        <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                                            +{{ count($template->variables) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-3 border-t" style="border-color: rgba(235,235,245,0.1);">
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                {{ $template->campaigns_count ?? 0 }} campaigns
                            </p>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.templates.edit', $template->id) ?? '#' }}" 
                                   class="text-sm" style="color: rgba(10,132,255,1);">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.templates.show', $template->id) ?? '#' }}" 
                                   class="text-sm" style="color: rgba(10,132,255,1);">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm" style="color: rgba(255,69,58,1);">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($templates, 'hasPages') && $templates->hasPages())
            <div class="mt-6">
                {{ $templates->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="email-empty-state">
            <i class="fas fa-file-code"></i>
            <p class="text-base font-medium text-white mb-2">No Templates Yet</p>
            <p class="text-sm mb-4" style="color: rgba(235,235,245,0.6);">
                Create email templates to streamline your campaigns
            </p>
            <a href="{{ route('admin.templates.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Create Template
            </a>
        </div>
    @endif
</div>
