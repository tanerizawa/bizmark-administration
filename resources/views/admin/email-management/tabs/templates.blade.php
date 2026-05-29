<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Design</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Templates</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Buat dan kelola template email untuk kampanye marketing</p>
        </div>
        <a href="{{ route('admin.templates.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-red);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>New Template
        </a>
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form method="GET" action="{{ route('admin.email-management.index') }}">
            <input type="hidden" name="tab" value="templates">
            <div style="display:grid;grid-template-columns:2fr 1fr auto auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Pencarian</label>
                    <input type="text" name="search" value="{{ request('tab')==='templates' ? request('search') : '' }}" placeholder="Nama template atau subject..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Kategori</label>
                    <select name="category" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Kategori</option>
                        @foreach(($categories ?? []) as $category)
                            <option value="{{ $category }}" {{ request('tab')==='templates' && request('category')===$category ? 'selected' : '' }}>{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="padding:8px 20px;background:var(--apple-red);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:5px"></i>Filter
                </button>
                <a href="{{ route('admin.email-management.index', ['tab' => 'templates']) }}"
                   style="padding:8px 14px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Reset</a>
            </div>
        </form>
    </div>

    {{-- Template Grid --}}
    @if(isset($templates) && $templates->count() > 0)
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
            @foreach($templates as $template)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                {{-- Thumbnail --}}
                <div style="height:120px;background:linear-gradient(135deg,color-mix(in srgb,var(--apple-red) 25%,var(--dark-bg-secondary)),var(--dark-bg-secondary));position:relative;display:flex;align-items:center;justify-content:center">
                    @if($template->thumbnail)
                        <img src="{{ asset('storage/'.$template->thumbnail) }}" alt="{{ $template->name }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                        <i class="fas fa-file-code" style="font-size:2rem;color:var(--apple-red);opacity:.4"></i>
                    @endif
                    {{-- Status badge --}}
                    @php
                        $statusBadgeBackground = $template->is_active ? 'var(--apple-green)' : 'var(--dark-bg-tertiary)';
                        $statusBadgeColor = $template->is_active ? '#fff' : 'var(--dark-text-secondary)';
                    @endphp
                    <span style="position:absolute;top:10px;right:10px;padding:3px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:{{ $statusBadgeBackground }};color:{{ $statusBadgeColor }}">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div style="padding:14px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px">
                        <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0;flex:1">{{ $template->name }}</h3>
                        @if($template->category)
                        <span style="flex-shrink:0;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">{{ ucfirst($template->category) }}</span>
                        @endif
                    </div>
                    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 10px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">{{ $template->subject }}</p>
                    @if($template->variables && count($template->variables) > 0)
                    <div style="margin-bottom:10px">
                        <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 4px">Variables:</p>
                        <div style="display:flex;flex-wrap:wrap;gap:4px">
                            @foreach(array_slice($template->variables,0,3) as $variable)
                            <code style="padding:2px 6px;border-radius:6px;font-size:0.7rem;background:var(--dark-bg-secondary);color:var(--apple-teal)">&#123;&#123; {{ $variable }} &#125;&#125;</code>
                            @endforeach
                            @if(count($template->variables) > 3)
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary)">+{{ count($template->variables)-3 }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid var(--dark-separator)">
                        <span style="font-size:0.72rem;color:var(--dark-text-secondary)">{{ $template->campaigns_count ?? 0 }} campaign</span>
                        <div style="display:flex;gap:10px">
                            <a href="{{ route('admin.templates.edit', $template->id) }}"
                               style="color:var(--apple-blue);font-size:0.82rem" title="Edit"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.templates.show', $template->id) }}"
                               style="color:var(--apple-teal);font-size:0.82rem" title="Lihat"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:var(--apple-red);font-size:0.82rem;cursor:pointer;padding:0" title="Hapus"
                                        onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if(method_exists($templates,'hasPages') && $templates->hasPages())
        <div style="padding:4px 0">{{ $templates->appends(request()->query())->links() }}</div>
        @endif
    @else
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:48px;text-align:center">
            <div style="width:48px;height:48px;border-radius:50%;background:color-mix(in srgb,var(--apple-red) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <i class="fas fa-file-code" style="font-size:1.2rem;color:var(--apple-red)"></i>
            </div>
            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum Ada Template</p>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Buat template email untuk mempercepat campaign</p>
            <a href="{{ route('admin.templates.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-red);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus" style="font-size:0.75rem"></i>Buat Template
            </a>
        </div>
    @endif
</div>
