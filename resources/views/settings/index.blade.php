@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-2xl font-bold" style="color: #FFFFFF;">
            <i class="fas fa-cog mr-2" style="color: rgba(235, 235, 245, 0.4);"></i>
            Settings
        </h1>
        <p class="text-sm mt-1" style="color: rgba(235, 235, 245, 0.6);">
            Manage application settings, users, and permissions
        </p>
    </div>

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
                    <i class="fas fa-building mr-2"></i>General
                </a>
                <a href="{{ route('settings.index', ['tab' => 'users']) }}" 
                   class="tab-button {{ $activeTab === 'users' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i>Users
                </a>
                <a href="{{ route('settings.index', ['tab' => 'roles']) }}" 
                   class="tab-button {{ $activeTab === 'roles' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-user-shield mr-2"></i>Roles & Permissions
                </a>
                <a href="{{ route('settings.index', ['tab' => 'financial']) }}" 
                   class="tab-button {{ $activeTab === 'financial' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-wallet mr-2"></i>Financial
                </a>
                <a href="{{ route('settings.index', ['tab' => 'project']) }}" 
                   class="tab-button {{ $activeTab === 'project' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-project-diagram mr-2"></i>Project
                </a>
                <a href="{{ route('settings.index', ['tab' => 'security']) }}" 
                   class="tab-button {{ $activeTab === 'security' ? 'active' : '' }} px-4 py-2 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-2"></i>Security
                </a>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            @if($activeTab === 'general')
                @include('settings.tabs.general')
            @elseif($activeTab === 'users')
                @include('settings.tabs.users', ['users' => $users, 'roles' => $roles])
            @elseif($activeTab === 'roles')
                @include('settings.tabs.roles', ['roles' => $roles, 'permissions' => $permissions])
            @elseif($activeTab === 'financial')
                @include('settings.tabs.financial')
            @elseif($activeTab === 'project')
                @include('settings.tabs.project')
            @elseif($activeTab === 'security')
                @include('settings.tabs.security')
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
