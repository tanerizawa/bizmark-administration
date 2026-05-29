@extends('layouts.app')

@section('title', 'Lead Management')
@section('page-title', 'Kelola Lead & Inquiry')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen Prospek</p>
        <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Lead & Inquiry</h1>
        <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:4px 0 0">Kelola semua masukan prospek, inquiry layanan, dan permohonan biaya dari calon klien.</p>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
                <span style="font-size:0.82rem;color:var(--apple-green);font-weight:600">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-green);opacity:.7"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-exclamation-circle" style="color:var(--apple-red)"></i>
                <span style="font-size:0.82rem;color:var(--apple-red);font-weight:600">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);opacity:.7"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Tab Navigation + Content --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

        {{-- Tab Bar --}}
        <div style="display:flex;align-items:stretch;justify-content:space-between;padding:0 20px;border-bottom:1px solid var(--dark-separator);overflow-x:auto" role="tablist">
            <div style="display:flex;align-items:stretch">
            @php
                $tabs = [
                    'service-inquiries'    => ['icon'=>'fa-envelope',      'label'=>'Service Inquiries',    'color'=>'var(--apple-blue)',   'count'=>$serviceInquiriesCount],
                    'consultation-leads'   => ['icon'=>'fa-calculator',    'label'=>'Consultation Leads',   'color'=>'var(--apple-yellow)', 'count'=>$consultationLeadsCount],
                    'service-cost-requests'=> ['icon'=>'fa-file-signature','label'=>'Permohonan Biaya',     'color'=>'var(--apple-orange)', 'count'=>$serviceCostRequestsCount ?? 0],
                ];
            @endphp
            @foreach($tabs as $tabKey => $tab)
                @php $isActive = $activeTab === $tabKey; @endphp
                <a href="{{ route('admin.leads.index', ['tab' => $tabKey]) }}"
                   role="tab"
                   aria-selected="{{ $isActive ? 'true' : 'false' }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:14px 6px;margin-right:24px;font-size:0.85rem;font-weight:{{ $isActive ? '700' : '500' }};color:{{ $isActive ? $tab['color'] : 'var(--dark-text-secondary)' }};text-decoration:none;border-bottom:2px solid {{ $isActive ? $tab['color'] : 'transparent' }};margin-bottom:-1px;transition:color .2s,border-color .2s;white-space:nowrap"
                   onmouseover="if(!this.classList.contains('active-tab'))this.style.color='var(--dark-text-primary)'"
                   onmouseout="if(!this.classList.contains('active-tab'))this.style.color='var(--dark-text-secondary)'"
                   {{ $isActive ? 'class=active-tab' : '' }}>
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:{{ $isActive ? 'color-mix(in srgb,'.$tab['color'].' 18%,transparent)' : 'var(--dark-bg-tertiary)' }};color:{{ $isActive ? $tab['color'] : 'var(--dark-text-secondary)' }};font-size:0.75rem;flex-shrink:0">
                        <i class="fas {{ $tab['icon'] }}"></i>
                    </span>
                    {{ $tab['label'] }}
                    @if($tab['count'] > 0)
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 5px;border-radius:10px;font-size:0.7rem;font-weight:700;background:{{ $isActive ? $tab['color'] : 'var(--dark-bg-tertiary)' }};color:{{ $isActive ? ($tab['color'] === 'var(--apple-yellow)' ? '#000' : '#fff') : 'var(--dark-text-secondary)' }}">
                            {{ $tab['count'] > 99 ? '99+' : $tab['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
            </div>
            {{-- Export button aligned right in tab bar --}}
            <div style="display:flex;align-items:center;padding:10px 0 10px 16px;flex-shrink:0">
                @if($activeTab === 'service-inquiries')
                    <a href="{{ route('admin.service-inquiries.export', request()->all()) }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none;transition:all .2s" onmouseover="this.style.color='var(--dark-text-primary)';this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.color='var(--dark-text-secondary)';this.style.borderColor='var(--dark-separator)'">
                        <i class="fas fa-download"></i>Export CSV
                    </a>
                @elseif($activeTab === 'consultation-leads')
                    <a href="{{ route('admin.consultation-leads.export', request()->all()) }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none;transition:all .2s" onmouseover="this.style.color='var(--dark-text-primary)';this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.color='var(--dark-text-secondary)';this.style.borderColor='var(--dark-separator)'">
                        <i class="fas fa-download"></i>Export CSV
                    </a>
                @endif
            </div>
        </div>

        {{-- Active Tab Content --}}
        <div style="padding:20px">
            @if($activeTab === 'service-inquiries')
                @include('admin.leads.tabs.service-inquiries')
            @elseif($activeTab === 'consultation-leads')
                @include('admin.leads.tabs.consultation-leads')
            @elseif($activeTab === 'service-cost-requests')
                @include('admin.leads.tabs.service-cost-requests')
            @endif
        </div>
    </div>

</div>

{{-- Convert to Client Modal — Alpine v3 + x-teleport --}}
<template x-teleport="body">
    <div
        x-data="{ isOpen: false, convertUrl: '' }"
        @open-convert-modal.window="isOpen = true; convertUrl = $event.detail.url"
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-[100]"
        style="display:none;"
    >
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"
             @click="isOpen = false"
             aria-hidden="true"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="isOpen = false"
                 @keydown.escape.window="isOpen = false"
                 style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator)" class="relative rounded-2xl shadow-2xl w-full max-w-md"
                 role="dialog" aria-modal="true" aria-labelledby="convert-modal-title">

                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid var(--dark-separator)">
                    <div>
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Lead Management</p>
                        <h2 id="convert-modal-title" style="font-size:1rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">
                            Konversi ke Klien
                        </h2>
                    </div>
                    <button @click="isOpen = false"
                            style="padding:8px;border-radius:10px;background:none;border:none;color:var(--dark-text-secondary);cursor:pointer;transition:all .2s"
                            onmouseover="this.style.background='var(--dark-bg-tertiary)';this.style.color='var(--dark-text-primary)'"
                            onmouseout="this.style.background='none';this.style.color='var(--dark-text-secondary)'"
                            aria-label="Tutup">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="px-6 py-5">
                    <p style="font-size:0.875rem;color:var(--dark-text-secondary);margin-bottom:20px">
                        Konversi consultation lead ini menjadi akun klien terdaftar. Sistem akan membuat akun klien dan proyek perizinan secara otomatis.
                    </p>
                    <form id="convertForm" method="POST" :action="convertUrl" class="space-y-4">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="create_client_account" value="1" checked
                                   style="width:16px;height:16px;border-radius:4px;border:1px solid var(--dark-separator);background:var(--dark-bg-tertiary);accent-color:var(--color-primary);cursor:pointer">
                            <span style="font-size:0.875rem;font-weight:500;color:var(--dark-text-primary)">
                                Buat akun klien baru
                            </span>
                        </label>
                        <x-ui.input name="password" type="password" label="Password untuk klien" required />
                        <x-ui.input name="company_name" label="Nama perusahaan (opsional)" />
                    </form>
                </div>

                <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding:14px 24px;border-top:1px solid var(--dark-separator)">
                    <x-ui.button variant="ghost" size="sm" @click="isOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit" size="sm" form="convertForm">
                        <i class="fas fa-user-plus mr-1.5"></i>Konversi
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
function showConvertModal(convertUrl) {
    window.dispatchEvent(new CustomEvent('open-convert-modal', { detail: { url: convertUrl } }));
}
</script>
@endpush
@endsection
