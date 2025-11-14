@extends('layouts.app')

@section('title', 'Pengaturan KBLI')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-industry mr-2"></i>Pengaturan KBLI
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Kelola data Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm opacity-90">Total KBLI</div>
                        <div class="text-3xl font-bold mt-1">{{ number_format($kbliStats['total']) }}</div>
                    </div>
                    <div class="text-4xl opacity-75">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm opacity-90">Total Sektor</div>
                        <div class="text-3xl font-bold mt-1">
                            {{ $kbliStats['by_sector']->count() }}
                        </div>
                    </div>
                    <div class="text-4xl opacity-75">
                        <i class="fas fa-th"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Import Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-upload mr-2 text-blue-500"></i>Import Data KBLI
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Upload file CSV untuk mengimport data KBLI ke dalam database
                </p>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.settings.kbli.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- CSV Format Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Format CSV
                        </h3>
                        <p class="text-sm text-blue-800 dark:text-blue-300 mb-3">
                            File CSV harus memiliki header di baris pertama (nama kolom bisa bahasa Indonesia atau Inggris):
                        </p>
                        <div class="bg-white dark:bg-gray-800 rounded border border-blue-200 dark:border-blue-700 p-3 mb-3">
                            <div class="text-xs text-gray-800 dark:text-gray-200 space-y-1">
                                <div><strong>Kode/Code</strong> - Kode KBLI 4-5 digit (contoh: 0111, 62010)</div>
                                <div><strong>Judul/Description</strong> - Nama/deskripsi singkat kegiatan usaha</div>
                                <div><strong>Kategori/Sector</strong> - Sektor usaha (A, B, C, dst)</div>
                                <div><strong>Deskripsi/Notes</strong> - Penjelasan detail (opsional)</div>
                            </div>
                        </div>
                        <p class="text-xs text-blue-700 dark:text-blue-400">
                            <strong>Contoh header Excel Anda:</strong><br>
                            <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded">Kategori, Kode, Judul, Deskripsi</code><br>
                            <span class="text-green-600 dark:text-green-400">✓ Urutan kolom bebas, sistem akan otomatis mapping berdasarkan nama header</span>
                        </p>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-file-csv mr-1"></i>File CSV
                        </label>
                        <input 
                            type="file" 
                            name="csv_file" 
                            accept=".csv,.txt"
                            required
                            class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/50 dark:file:text-blue-200
                                dark:hover:file:bg-blue-900"
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Format: CSV atau TXT (Max: 10MB)
                        </p>
                    </div>

                    <!-- Clear Existing Data Option -->
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="clear_existing" 
                            id="clear_existing"
                            value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="clear_existing" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Hapus semua data KBLI yang ada sebelum import (gunakan dengan hati-hati!)
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3">
                        <button 
                            type="submit"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition duration-150"
                        >
                            <i class="fas fa-upload mr-2"></i>
                            Import Data
                        </button>

                        <a 
                            href="{{ route('admin.settings.kbli.template') }}"
                            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition duration-150"
                        >
                            <i class="fas fa-download mr-2"></i>
                            Download Template CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export and Clear Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Export -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-download mr-2 text-green-500"></i>Export Data
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Download semua data KBLI dalam format CSV
                    </p>
                    <a 
                        href="{{ route('admin.settings.kbli.export') }}"
                        class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition duration-150"
                    >
                        <i class="fas fa-file-download mr-2"></i>
                        Export ke CSV
                    </a>
                </div>
            </div>

            <!-- Clear Data -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-trash-alt mr-2 text-red-500"></i>Hapus Semua Data
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Hapus seluruh data KBLI dari database (tidak dapat dikembalikan!)
                    </p>
                    <form action="{{ route('admin.settings.kbli.clear') }}" method="POST" onsubmit="return confirm('PERHATIAN: Anda akan menghapus SEMUA data KBLI. Tindakan ini tidak dapat dibatalkan! Lanjutkan?')">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit"
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition duration-150"
                        >
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Hapus Semua Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- KBLI by Sector Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Distribusi per Sektor
                </h2>
            </div>
            <div class="p-6">
                @if($kbliStats['by_sector']->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($kbliStats['by_sector'] as $sector)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">
                                            Sektor {{ $sector->sector }}
                                        </div>
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                                            {{ number_format($sector->count) }}
                                        </div>
                                    </div>
                                    <div class="text-3xl text-gray-400 dark:text-gray-500">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-database text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">
                            Belum ada data KBLI. Silakan import data terlebih dahulu.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
