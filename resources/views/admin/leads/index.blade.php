@extends('layouts.app')

@section('title', 'Lead Management')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <x-ui.card variant="flat" padding="md" class="relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-[var(--apple-blue)]/20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0 space-y-2.5">
                <p class="text-xs uppercase tracking-[0.4em] text-gray-500 dark:text-gray-400">Lead Management</p>
                <h1 class="text-2xl md:text-xl font-bold text-gray-900 dark:text-white">Kelola Lead & Inquiry</h1>
                <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">Pantau leads dari inquiry layanan, konsultasi AI, dan permohonan biaya jasa</p>
                <div class="flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span><i class="fas fa-envelope mr-1.5"></i>{{ $serviceInquiriesCount }} inquiry</span>
                    <span><i class="fas fa-calculator mr-1.5"></i>{{ $consultationLeadsCount }} consultation</span>
                    <span><i class="fas fa-file-signature mr-1.5"></i>{{ $serviceCostRequestsCount ?? 0 }} permohonan</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($activeTab === 'service-inquiries')
                    <x-ui.button variant="ghost" size="sm" :href="route('admin.service-inquiries.export', request()->all())">
                        <i class="fas fa-download mr-1"></i>Export
                    </x-ui.button>
                @elseif($activeTab === 'consultation-leads')
                    <x-ui.button variant="ghost" size="sm" :href="route('admin.consultation-leads.export', request()->all())">
                        <i class="fas fa-download mr-1"></i>Export
                    </x-ui.button>
                @endif
            </div>
        </div>
    </x-ui.card>

    {{-- Alert Messages --}}
    @if(session('success'))
        <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Tab Navigation & Content --}}
    <x-ui.card variant="flat" padding="none">
        {{-- Tab Buttons --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                {{-- Service Inquiries tab --}}
                <button
                    @click="window.location='{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}'"
                    id="tab-service-inquiries"
                    role="tab"
                    aria-selected="{{ $activeTab === 'service-inquiries' ? 'true' : 'false' }}"
                    class="px-3.5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    :class="{
                        'text-white bg-[var(--apple-blue)]/15 border border-[var(--apple-blue)]/30': '{{ $activeTab }}' === 'service-inquiries',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white/5 border border-transparent': '{{ $activeTab }}' !== 'service-inquiries'
                    }"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-envelope"></i>Service Inquiries
                        @if($serviceInquiriesCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="'{{ $activeTab }}' === 'service-inquiries' ? 'bg-white text-[var(--apple-blue)]' : 'bg-[var(--apple-blue)]/25 text-white'">
                                {{ $serviceInquiriesCount }}
                            </span>
                        @endif
                    </span>
                </button>

                {{-- Consultation Leads tab --}}
                <button
                    @click="window.location='{{ route('admin.leads.index', ['tab' => 'consultation-leads']) }}'"
                    id="tab-consultation-leads"
                    role="tab"
                    aria-selected="{{ $activeTab === 'consultation-leads' ? 'true' : 'false' }}"
                    class="px-3.5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    :class="{
                        'text-white bg-[var(--apple-blue)]/15 border border-[var(--apple-blue)]/30': '{{ $activeTab }}' === 'consultation-leads',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white/5 border border-transparent': '{{ $activeTab }}' !== 'consultation-leads'
                    }"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-calculator"></i>Consultation Leads
                        @if($consultationLeadsCount > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="'{{ $activeTab }}' === 'consultation-leads' ? 'bg-white text-[var(--apple-blue)]' : 'bg-[rgba(255,204,0,0.25)] text-[#FFD60A]'">
                                {{ $consultationLeadsCount }}
                            </span>
                        @endif
                    </span>
                </button>

                {{-- Service Cost Requests tab --}}
                <button
                    @click="window.location='{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}'"
                    id="tab-service-cost-requests"
                    role="tab"
                    aria-selected="{{ $activeTab === 'service-cost-requests' ? 'true' : 'false' }}"
                    class="px-3.5 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
                    :class="{
                        'text-white bg-[var(--apple-blue)]/15 border border-[var(--apple-blue)]/30': '{{ $activeTab }}' === 'service-cost-requests',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white/5 border border-transparent': '{{ $activeTab }}' !== 'service-cost-requests'
                    }"
                >
                    <span class="flex items-center gap-2">
                        <i class="fas fa-file-signature"></i>Permohonan Biaya
                        @if(($serviceCostRequestsCount ?? 0) > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium rounded-full"
                                  :class="'{{ $activeTab }}' === 'service-cost-requests' ? 'bg-white text-[var(--apple-blue)]' : 'bg-[rgba(255,149,0,0.25)] text-[#FF9500]'">
                                {{ $serviceCostRequestsCount }}
                            </span>
                        @endif
                    </span>
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-3">
            <div id="content-service-inquiries" class="{{ $activeTab !== 'service-inquiries' ? 'hidden' : '' }}">
                @include('admin.leads.tabs.service-inquiries')
            </div>
            <div id="content-consultation-leads" class="{{ $activeTab !== 'consultation-leads' ? 'hidden' : '' }}">
                @include('admin.leads.tabs.consultation-leads')
            </div>
            <div id="content-service-cost-requests" class="{{ $activeTab !== 'service-cost-requests' ? 'hidden' : '' }}">
                @include('admin.leads.tabs.service-cost-requests')
            </div>
        </div>
    </x-ui.card>

    {{-- Convert to Client Modal --}}
    <x-ui.modal title="Konversi ke Klien" submit-label="Konversi">
        <x-slot:trigger>
            <template x-teleport="body">
                <div id="convertModal" x-data="{ isOpen: false }" x-show="isOpen" x-cloak style="display: none;" class="fixed inset-0 z-[60]">
                    {{-- Backdrop --}}
                    <div x-show="isOpen" x-transition.opacity.duration.300ms class="fixed inset-0 bg-black/75" @click="isOpen = false" aria-hidden="true"></div>
                    {{-- Modal panel --}}
                    <div x-show="isOpen" x-transition.duration.300ms class="relative flex items-center justify-center min-h-screen p-4">
                        <div @click.outside="isOpen = false" class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Konversi consultation lead ini menjadi akun klien terdaftar?
                            </p>
                            <form id="convertForm" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="create_client_account" value="1" checked
                                               class="rounded border-gray-300 dark:border-gray-600 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Buat akun klien</span>
                                    </label>
                                </div>
                                <x-ui.input name="password" type="password" label="Password untuk klien" required />
                                <x-ui.input name="company_name" label="Nama perusahaan (opsional)" />
                                <div class="flex justify-end gap-2 pt-2">
                                    <x-ui.button variant="ghost" size="sm" @click="isOpen = false">Batal</x-ui.button>
                                    <x-ui.button type="submit" size="sm">Konversi</x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </x-slot:trigger>
    </x-ui.modal>

    @push('scripts')
    <script>
    function showConvertModal(consultationId) {
        document.getElementById('convertForm').action = `/admin/consultation-leads/${consultationId}/convert`;
        const modal = document.getElementById('convertModal');
        if (modal && modal.__x) {
            modal.__x.$data.isOpen = true;
        }
    }
    </script>
    @endpush
    @endsection
