@extends('layouts.app')

@section('title', 'Manajemen Permohonan Izin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manajemen Permohonan Izin</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Review dan kelola permohonan izin dari client</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg shadow-sm p-4">
                <div class="text-sm text-blue-600 dark:text-blue-400">Submitted</div>
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $stats['submitted'] }}</div>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg shadow-sm p-4">
                <div class="text-sm text-yellow-600 dark:text-yellow-400">Under Review</div>
                <div class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $stats['under_review'] }}</div>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg shadow-sm p-4">
                <div class="text-sm text-purple-600 dark:text-purple-400">Quoted</div>
                <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $stats['quoted'] }}</div>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg shadow-sm p-4">
                <div class="text-sm text-indigo-600 dark:text-indigo-400">In Progress</div>
                <div class="text-2xl font-bold text-indigo-900 dark:text-indigo-100">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg shadow-sm p-4">
                <div class="text-sm text-green-600 dark:text-green-400">Completed</div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $stats['completed'] }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300 p-4 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-600 text-red-700 dark:text-red-300 p-4 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6 p-6">
            <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>Cari
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="No. aplikasi atau nama client"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        >
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-filter mr-1"></i>Status
                        </label>
                        <select 
                            name="status" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Permit Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-certificate mr-1"></i>Jenis Izin
                        </label>
                        <select 
                            name="permit_type_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            <option value="">Semua Jenis</option>
                            @foreach($permitTypes as $type)
                                <option value="{{ $type->id }}" {{ request('permit_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Client Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i>Client
                        </label>
                        <select 
                            name="client_id" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                            <option value="">Semua Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Dari Tanggal
                        </label>
                        <input 
                            type="date" 
                            name="date_from" 
                            value="{{ request('date_from') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                        >
                    </div>

                </div>

                <div class="flex gap-2">
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-purple-600 dark:bg-purple-700 text-white rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition"
                    >
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a 
                        href="{{ route('admin.permit-applications.index') }}" 
                        class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                    >
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Applications Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                No. Aplikasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Jenis Izin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tanggal Submit
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Harga
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($applications as $app)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $app->application_number }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $app->client->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $app->client->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $formData = is_string($app->form_data) 
                                            ? json_decode($app->form_data, true) 
                                            : $app->form_data;
                                        $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                                    @endphp
                                    @if($isPackage)
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <i class="fas fa-box-open text-purple-600 dark:text-purple-400 mr-1"></i>
                                            {{ $formData['project_name'] ?? 'Paket Izin' }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ count($formData['selected_permits'] ?? []) }} izin 
                                            ({{ $formData['permits_by_service']['bizmark'] ?? 0 }} BizMark.ID)
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $app->permitType ? $app->permitType->name : ($formData['permit_name'] ?? 'N/A') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                            'submitted' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                            'under_review' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                            'document_incomplete' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300',
                                            'quoted' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
                                            'quotation_accepted' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300',
                                            'quotation_rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                            'payment_pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                            'payment_verified' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-800 dark:text-teal-300',
                                            'in_progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                            'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                            'cancelled' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                        ];
                                        $colorClass = $statusColors[$app->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucwords(str_replace('_', ' ', $app->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($app->submitted_at)
                                        {{ $app->submitted_at->format('d M Y') }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($app->quoted_price)
                                        Rp {{ number_format($app->quoted_price, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Belum ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a 
                                        href="{{ route('admin.permit-applications.show', $app->id) }}" 
                                        class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300"
                                    >
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 dark:text-gray-500">
                                        <i class="fas fa-folder-open text-4xl mb-3"></i>
                                        <p>Tidak ada aplikasi ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
