<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Mailing List</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Subscribers</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Kelola daftar subscriber untuk kampanye email marketing</p>
        </div>
        <a href="{{ route('admin.subscribers.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-purple);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>Add Subscriber
        </a>
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form method="GET" action="{{ route('admin.email-management.index') }}">
            <input type="hidden" name="tab" value="subscribers">
            <div style="display:grid;grid-template-columns:2fr 1fr auto auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Pencarian</label>
                    <input type="text" name="search" value="{{ request('tab')==='subscribers' ? request('search') : '' }}" placeholder="Nama atau email subscriber..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="status" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Status</option>
                        @foreach(($statuses ?? []) as $status)
                            <option value="{{ $status }}" {{ request('tab')==='subscribers' && request('status')===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="padding:8px 20px;background:var(--apple-purple);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:5px"></i>Filter
                </button>
                <a href="{{ route('admin.email-management.index', ['tab' => 'subscribers']) }}"
                   style="padding:8px 14px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    @php
    $subStatusMap = [
        'active'       => ['bg'=>'color-mix(in srgb,var(--apple-green) 15%,transparent)',  'color'=>'var(--apple-green)'],
        'unsubscribed' => ['bg'=>'color-mix(in srgb,var(--apple-red) 15%,transparent)',    'color'=>'var(--apple-red)'],
        'pending'      => ['bg'=>'color-mix(in srgb,var(--apple-yellow) 15%,transparent)', 'color'=>'var(--apple-yellow)'],
        'bounced'      => ['bg'=>'color-mix(in srgb,var(--apple-orange) 15%,transparent)', 'color'=>'var(--apple-orange)'],
    ];
    @endphp
    @if(isset($subscribers) && $subscribers->count() > 0)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead style="background:var(--dark-bg-secondary)">
                        <tr>
                            @foreach(['Nama','Email','Status','Tags','Subscribed','Aksi'] as $col)
                            <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscribers as $subscriber)
                        @php $ss = $subStatusMap[$subscriber->status] ?? ['bg'=>'color-mix(in srgb,var(--dark-text-secondary) 10%,transparent)','color'=>'var(--dark-text-secondary)']; @endphp
                        <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:11px 14px">
                                <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary)">{{ $subscriber->name }}</span>
                            </td>
                            <td style="padding:11px 14px">
                                <span style="font-size:0.82rem;color:var(--dark-text-secondary)">{{ $subscriber->email }}</span>
                            </td>
                            <td style="padding:11px 14px">
                                <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">{{ ucfirst($subscriber->status) }}</span>
                            </td>
                            <td style="padding:11px 14px">
                                @if($subscriber->tags && count($subscriber->tags) > 0)
                                <div style="display:flex;flex-wrap:wrap;gap:4px">
                                    @foreach(array_slice($subscriber->tags,0,2) as $tag)
                                    <span style="padding:2px 8px;border-radius:20px;font-size:0.7rem;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal)">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($subscriber->tags) > 2)
                                    <span style="font-size:0.7rem;color:var(--dark-text-secondary)">+{{ count($subscriber->tags)-2 }}</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td style="padding:11px 14px">
                                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $subscriber->subscribed_at?->format('d M Y') ?? $subscriber->created_at->format('d M Y') }}</span>
                            </td>
                            <td style="padding:11px 14px">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <a href="{{ route('admin.subscribers.show', $subscriber->id) }}"
                                       style="color:var(--apple-teal);font-size:0.8rem" title="Lihat"
                                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.subscribers.edit', $subscriber->id) }}"
                                       style="color:var(--apple-blue);font-size:0.8rem" title="Edit"
                                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.subscribers.destroy', $subscriber->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus subscriber ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:none;border:none;color:var(--apple-red);font-size:0.8rem;cursor:pointer;padding:0" title="Hapus"
                                                onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($subscribers,'hasPages') && $subscribers->hasPages())
            <div style="padding:12px 16px;border-top:1px solid var(--dark-separator);background:var(--dark-bg-secondary)">
                {{ $subscribers->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    @else
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:48px;text-align:center">
            <div style="width:48px;height:48px;border-radius:50%;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <i class="fas fa-users" style="font-size:1.2rem;color:var(--apple-purple)"></i>
            </div>
            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum Ada Subscriber</p>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Mulai bangun mailing list untuk mengirim campaign</p>
            <a href="{{ route('admin.subscribers.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-purple);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus" style="font-size:0.75rem"></i>Add Subscriber
            </a>
        </div>
    @endif
</div>
