<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Marketing</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Campaigns</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Buat dan kelola kampanye email marketing untuk pelanggan</p>
        </div>
        <a href="{{ route('admin.campaigns.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none;border:none">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>New Campaign
        </a>
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form method="GET" action="{{ route('admin.email-management.index') }}">
            <input type="hidden" name="tab" value="campaigns">
            <div style="display:grid;grid-template-columns:2fr 1fr auto auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Pencarian</label>
                    <input type="text" name="search" value="{{ request('tab')==='campaigns' ? request('search') : '' }}" placeholder="Nama campaign atau subject..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="status" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Status</option>
                        @foreach(($statuses ?? []) as $status)
                            <option value="{{ $status }}" {{ request('tab')==='campaigns' && request('status')===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="padding:8px 20px;background:var(--apple-green);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:5px"></i>Filter
                </button>
                <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}"
                   style="padding:8px 14px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Reset</a>
            </div>
        </form>
    </div>

    {{-- Campaign Cards --}}
    @if(isset($campaigns) && $campaigns->count() > 0)
        @php
        $statusStyles = [
            'sent'      => ['bg'=>'color-mix(in srgb,var(--apple-green) 15%,transparent)',  'color'=>'var(--apple-green)'],
            'scheduled' => ['bg'=>'color-mix(in srgb,var(--apple-blue) 15%,transparent)',   'color'=>'var(--apple-blue)'],
            'sending'   => ['bg'=>'color-mix(in srgb,var(--apple-yellow) 15%,transparent)', 'color'=>'var(--apple-yellow)'],
            'draft'     => ['bg'=>'color-mix(in srgb,var(--dark-text-secondary) 15%,transparent)', 'color'=>'var(--dark-text-secondary)'],
            'cancelled' => ['bg'=>'color-mix(in srgb,var(--apple-red) 15%,transparent)',    'color'=>'var(--apple-red)'],
        ];
        @endphp
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px">
            @foreach($campaigns as $campaign)
            @php $ss = $statusStyles[$campaign->status] ?? $statusStyles['draft']; @endphp
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:{{ $ss['bg'] }};color:{{ $ss['color'] }}">{{ ucfirst($campaign->status) }}</span>
                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)">#{{ $campaign->id }}</span>
                </div>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 4px">{{ $campaign->name }}</h3>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 12px">{{ $campaign->subject }}</p>
                @if($campaign->status === 'sent')
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;padding:10px 0;margin-bottom:12px;border-top:1px solid var(--dark-separator);border-bottom:1px solid var(--dark-separator)">
                    @foreach([['Sent','sent_count',null,'var(--dark-text-primary)'],['Opened','open_rate','%','var(--apple-green)'],['Clicked','click_rate','%','var(--apple-blue)'],['Bounced','bounce_rate','%','var(--apple-red)']] as [$lbl,$field,$sfx,$clr])
                    <div style="text-align:center">
                        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0 0 2px">{{ $lbl }}</p>
                        <p style="font-size:0.88rem;font-weight:600;color:{{ $clr }};margin:0">{{ $lbl==='Sent'?number_format($campaign->$field):$campaign->$field }}{{ $sfx }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">
                        <i class="far fa-calendar" style="margin-right:4px"></i>
                        {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('d M Y H:i') : $campaign->created_at->format('d M Y') }}
                    </p>
                    <div style="display:flex;gap:8px">
                        @if($campaign->status === 'draft')
                        <a href="{{ route('admin.campaigns.edit', $campaign->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.78rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-edit" style="font-size:0.7rem"></i>Edit
                        </a>
                        @endif
                        <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,var(--dark-separator));color:var(--apple-green);font-size:0.78rem;font-weight:600;text-decoration:none">
                            <i class="fas fa-eye" style="font-size:0.7rem"></i>View
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if(method_exists($campaigns,'hasPages') && $campaigns->hasPages())
        <div style="padding:4px 0">{{ $campaigns->appends(request()->query())->links() }}</div>
        @endif
    @else
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:48px;text-align:center">
            <div style="width:48px;height:48px;border-radius:50%;background:color-mix(in srgb,var(--apple-green) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <i class="fas fa-bullhorn" style="font-size:1.2rem;color:var(--apple-green)"></i>
            </div>
            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum Ada Campaign</p>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Buat kampanye email pertama untuk menjangkau subscriber</p>
            <a href="{{ route('admin.campaigns.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus" style="font-size:0.75rem"></i>Buat Campaign
            </a>
        </div>
    @endif
</div>
