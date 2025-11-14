@extends('client.layouts.app')

@section('title', 'Katalog Layanan')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Katalog Layanan Perizinan</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Telusuri dan ajukan permohonan izin yang Anda butuhkan</p>
            </div>

            <!-- Search & Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('client.services.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cari Layanan
                            </label>
                            <input 
                                type="text" 
                                name="search" 
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="Cari berdasarkan nama atau kode izin..."
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kategori
                            </label>
                            <select 
                                name="category" 
                                id="category"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Sort & Action Buttons -->
                    <div class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Urutkan Berdasarkan
                            </label>
                            <select 
                                name="sort" 
                                id="sort"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="estimated_cost_min" {{ request('sort') == 'estimated_cost_min' ? 'selected' : '' }}>Harga</option>
                                <option value="avg_processing_days" {{ request('sort') == 'avg_processing_days' ? 'selected' : '' }}>Durasi Proses</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                <i class="fas fa-search mr-2"></i>Cari
                            </button>
                            <a href="{{ route('client.services.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                                <i class="fas fa-redo mr-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Services Grid -->
            @if($services->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    @foreach($services as $service)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                            <!-- Category Badge -->
                            <div class="px-6 pt-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($service->category === 'business') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($service->category === 'building') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($service->category === 'environmental') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                    @elseif($service->category === 'land') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($service->category === 'transportation') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    {{ $service->name }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                                    {{ $service->description }}
                                </p>

                                <!-- Info Grid -->
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-money-bill-wave w-5 text-gray-500 dark:text-gray-400"></i>
                                        <span class="text-gray-700 dark:text-gray-300 ml-2">
                                            Rp {{ number_format($service->estimated_cost_min, 0, ',', '.') }} - 
                                            Rp {{ number_format($service->estimated_cost_max, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-clock w-5 text-gray-500 dark:text-gray-400"></i>
                                        <span class="text-gray-700 dark:text-gray-300 ml-2">
                                            {{ $service->avg_processing_days }} hari kerja
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-file-alt w-5 text-gray-500 dark:text-gray-400"></i>
                                        <span class="text-gray-700 dark:text-gray-300 ml-2">
                                            {{ count($service->required_documents ?? []) }} dokumen diperlukan
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <a href="{{ route('client.services.show', $service->code) }}" 
                                   class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    Lihat Detail & Ajukan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    {{ $services->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Tidak Ada Layanan Ditemukan</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Coba ubah filter pencarian Anda</p>
                    <a href="{{ route('client.services.index') }}" class="inline-block px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

