<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Tim Email</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Email Accounts</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Kelola akun email tim untuk sistem multi-user email management</p>
        </div>
        <a href="{{ route('admin.email-accounts.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>Add Account
        </a>
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form method="GET" action="{{ route('admin.email-management.index') }}">
            <input type="hidden" name="tab" value="accounts">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Pencarian</label>
                    <input type="text" name="search" value="{{ request('tab')==='accounts' ? request('search') : '' }}" placeholder="Nama akun atau email..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Tipe</label>
                    <select name="type" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua</option>
                        <option value="shared"   {{ request('tab')==='accounts' && request('type')==='shared'   ? 'selected' : '' }}>Shared</option>
                        <option value="personal" {{ request('tab')==='accounts' && request('type')==='personal' ? 'selected' : '' }}>Personal</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Departemen</label>
                    <select name="department" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua</option>
                        @foreach(['general'=>'General','cs'=>'Customer Service','sales'=>'Sales','support'=>'Tech Support','finance'=>'Finance'] as $val => $lbl)
                        <option value="{{ $val }}" {{ request('tab')==='accounts' && request('department')===$val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="is_active" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua</option>
                        <option value="1" {{ request('tab')==='accounts' && request('is_active')==='1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('tab')==='accounts' && request('is_active')==='0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" style="padding:8px 20px;background:var(--apple-teal);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:5px"></i>Filter
                </button>
                <a href="{{ route('admin.email-management.index', ['tab' => 'accounts']) }}"
                   style="padding:8px 14px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Reset</a>
            </div>
        </form>
    </div>

    {{-- Account List --}}
    @if(isset($accounts) && $accounts->count() > 0)
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($accounts as $account)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px">

                    {{-- Avatar --}}
                    <div style="width:44px;height:44px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#fff;background:{{ $account->type === 'shared' ? 'linear-gradient(135deg,var(--apple-teal),var(--apple-blue))' : 'linear-gradient(135deg,var(--apple-green),var(--apple-teal))' }}">
                        {{ strtoupper(substr($account->email, 0, 1)) }}
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px">
                            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $account->name }}</h3>
                            <span style="padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:{{ $account->type === 'shared' ? 'color-mix(in srgb,var(--apple-teal) 15%,transparent)' : 'color-mix(in srgb,var(--apple-green) 15%,transparent)' }};color:{{ $account->type === 'shared' ? 'var(--apple-teal)' : 'var(--apple-green)' }}">
                                {{ ucfirst($account->type) }}
                            </span>
                            <span style="padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:{{ $account->is_active ? 'color-mix(in srgb,var(--apple-green) 15%,transparent)' : 'color-mix(in srgb,var(--apple-red) 15%,transparent)' }};color:{{ $account->is_active ? 'var(--apple-green)' : 'var(--apple-red)' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span style="padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">
                                {{ $account->department_label }}
                            </span>
                        </div>
                        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 2px">
                            <i class="fas fa-envelope" style="font-size:0.7rem;margin-right:4px"></i>{{ $account->email }}
                        </p>
                        @if($account->description)
                        <p style="font-size:0.75rem;color:var(--dark-text-secondary);opacity:.7;margin:0">{{ $account->description }}</p>
                        @endif
                        @if($account->activeUsers && $account->activeUsers->count() > 0)
                        <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary)">Assigned to:</span>
                            <div style="display:flex;margin-left:-4px">
                                @foreach($account->activeUsers->take(3) as $user)
                                <div style="width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--apple-green),var(--apple-teal));border:2px solid var(--dark-bg-tertiary);margin-left:-4px" title="{{ $user->name }}">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                @endforeach
                                @if($account->activeUsers->count() > 3)
                                <div style="width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-secondary);border:2px solid var(--dark-bg-tertiary);margin-left:-4px">
                                    +{{ $account->activeUsers->count()-3 }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;text-align:center;min-width:200px">
                        @foreach([['Received','total_received','var(--dark-text-primary)'],['Sent','total_sent','var(--dark-text-primary)'],['Unread',null,'var(--apple-orange)']] as [$lbl,$field,$clr])
                        <div>
                            <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px">{{ $lbl }}</p>
                            <p style="font-size:0.95rem;font-weight:700;color:{{ $clr }};margin:0">
                                @if($lbl==='Unread'){{ number_format($account->getUnreadCount()??0) }}@else{{ number_format($account->$field??0) }}@endif
                            </p>
                        </div>
                        @endforeach
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:8px;flex-shrink:0">
                        <a href="{{ route('admin.email-accounts.edit', $account->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.78rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-edit" style="font-size:0.7rem"></i>Edit
                        </a>
                        <a href="{{ route('admin.email-accounts.show', $account->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-teal) 30%,var(--dark-separator));color:var(--apple-teal);font-size:0.78rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-eye" style="font-size:0.7rem"></i>View
                        </a>
                    </div>
                </div>

                @if($account->auto_reply_enabled)
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--dark-separator);display:flex;align-items:center;gap:6px">
                    <i class="fas fa-reply-all" style="font-size:0.75rem;color:var(--apple-teal)"></i>
                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)">Auto-reply enabled</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @if(method_exists($accounts,'hasPages') && $accounts->hasPages())
        <div style="padding:4px 0">{{ $accounts->appends(request()->query())->links() }}</div>
        @endif
    @else
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:48px;text-align:center">
            <div style="width:48px;height:48px;border-radius:50%;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <i class="fas fa-at" style="font-size:1.2rem;color:var(--apple-teal)"></i>
            </div>
            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum Ada Email Account</p>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Tambah akun email untuk memulai manajemen email tim</p>
            <a href="{{ route('admin.email-accounts.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus" style="font-size:0.75rem"></i>Add Account
            </a>
        </div>
    @endif
</div>
