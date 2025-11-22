@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-orange opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-red opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-2.5 max-w-3xl">
            <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Konfigurasi Sistem</p>
            <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                Pengaturan Aplikasi
            </h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                Kelola konfigurasi bisnis, pengguna, keamanan, dan preferensi sistem secara terpusat.
            </p>
        </div>
    </section>

    @if(session('success'))
        <div class="mb-4 p-3 rounded-apple" style="background: rgba(52, 199, 89, 0.1); border-left: 3px solid rgba(52, 199, 89, 1);">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2" style="color: rgba(52, 199, 89, 1);"></i>
                <span style="color: rgba(52, 199, 89, 1);">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 rounded-apple" style="background: rgba(255, 59, 48, 0.1); border-left: 3px solid rgba(255, 59, 48, 1);">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2" style="color: rgba(255, 59, 48, 1);"></i>
                <span style="color: rgba(255, 59, 48, 1);">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabs Container -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b" style="border-color: rgba(84, 84, 88, 0.65);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <a href="{{ route('settings.index', ['tab' => 'general']) }}" 
                   class="tab-button {{ $activeTab === 'general' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-building mr-2"></i>Umum
                </a>
                <a href="{{ route('settings.index', ['tab' => 'users']) }}" 
                   class="tab-button {{ $activeTab === 'users' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i>Pengguna
                </a>
                <a href="{{ route('settings.index', ['tab' => 'roles']) }}" 
                   class="tab-button {{ $activeTab === 'roles' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-user-shield mr-2"></i>Peran & Akses
                </a>
                <a href="{{ route('settings.index', ['tab' => 'financial']) }}" 
                   class="tab-button {{ $activeTab === 'financial' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-wallet mr-2"></i>Keuangan
                </a>
                <a href="{{ route('settings.index', ['tab' => 'project']) }}" 
                   class="tab-button {{ $activeTab === 'project' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-project-diagram mr-2"></i>Proyek
                </a>
                <a href="{{ route('settings.index', ['tab' => 'security']) }}" 
                   class="tab-button {{ $activeTab === 'security' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-2"></i>Keamanan
                </a>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'general')
                @include('settings.tabs.general', ['setting' => $businessSetting])
            @elseif($activeTab === 'users')
                @include('settings.tabs.users', ['users' => $users, 'roles' => $roles])
            @elseif($activeTab === 'roles')
                @include('settings.tabs.roles', ['roles' => $roles, 'permissions' => $permissions])
            @elseif($activeTab === 'financial')
                @include('settings.tabs.financial', [
                    'expenseCategories' => $expenseCategories,
                    'paymentMethods' => $paymentMethods,
                    'taxRates' => $taxRates,
                ])
            @elseif($activeTab === 'project')
                @include('settings.tabs.project', ['statuses' => $projectStatuses])
            @elseif($activeTab === 'security')
                @include('settings.tabs.security', ['securitySetting' => $securitySetting])
            @endif
        </div>
    </div>
</div>

<style>
.tab-button {
    color: rgba(235, 235, 245, 0.6);
    background-color: transparent;
}

.tab-button:hover {
    color: rgba(235, 235, 245, 0.9);
    background-color: rgba(255, 255, 255, 0.05);
}

.tab-button.active {
    color: #FFFFFF;
    background-color: rgba(0, 122, 255, 0.15);
    border: 1px solid rgba(0, 122, 255, 0.3);
}
</style>
@endsection
