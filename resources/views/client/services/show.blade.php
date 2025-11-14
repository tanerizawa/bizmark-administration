@extends('client.layouts.app')

@section('title', $service->name)

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm">
                <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-900 dark:text-white font-medium">{{ $service->name }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Header -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2
                                    @if($service->category === 'business') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($service->category === 'building') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($service->category === 'environmental') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                    @elseif($service->category === 'land') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($service->category === 'transportation') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                                </span>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $service->name }}</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $service->code }}</p>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $service->description }}</p>
                    </div>

                    <!-- Service Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Detail Layanan
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Estimasi Biaya</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Rp {{ number_format($service->estimated_cost_min, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        s/d Rp {{ number_format($service->estimated_cost_max, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Waktu Proses</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $service->avg_processing_days }} hari kerja
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Sejak pembayaran diverifikasi
                                    </p>
                                </div>
                            </div>

                            @if($service->institution)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Instansi Terkait</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $service->institution->name }}
                                    </p>
                                </div>
                            </div>
                            @endif

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-yellow-600 dark:text-yellow-400 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Dokumen Diperlukan</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ count($service->required_documents ?? []) }} dokumen
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Required Documents -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                            Dokumen yang Diperlukan
                        </h2>
                        <div class="space-y-3">
                            @forelse($service->required_documents ?? [] as $index => $doc)
                                <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $doc }}</p>
                                    </div>
                                    <i class="fas fa-check-circle text-green-500 ml-2"></i>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada dokumen khusus yang diperlukan</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Process Steps -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-list-ol text-blue-600 mr-2"></i>
                            Alur Proses Permohonan
                        </h2>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">1</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Isi Formulir Permohonan</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lengkapi data perusahaan dan informasi yang diperlukan</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">2</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Upload Dokumen</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Unggah semua dokumen pendukung yang diperlukan</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">3</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Review Admin</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tim kami akan mereview kelengkapan dokumen Anda</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">4</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Terima Quotation</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Anda akan menerima penawaran harga resmi</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">5</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Pembayaran</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Lakukan pembayaran uang muka melalui transfer bank atau payment gateway</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">6</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Proses Perizinan</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kami akan memproses perizinan Anda ke instansi terkait</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Selesai</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Izin Anda siap dan dapat diunduh</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Services -->
                    @if($relatedServices->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                            Layanan Terkait
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($relatedServices as $related)
                                <a href="{{ route('client.services.show', $related->code) }}" 
                                   class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">{{ $related->name }}</h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $related->description }}</p>
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span><i class="fas fa-clock mr-1"></i>{{ $related->avg_processing_days }}d</span>
                                        <span class="text-blue-600 dark:text-blue-400">Lihat →</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- CTA Card -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white sticky top-6">
                        <h3 class="text-2xl font-bold mb-2">Siap Mengajukan?</h3>
                        <p class="text-blue-100 mb-6">Mulai proses permohonan izin Anda sekarang dengan mengisi formulir online kami</p>
                        
                        <a href="{{ route('client.applications.create', ['permit_type' => $service->id]) }}" 
                           class="block w-full text-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition mb-4">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Ajukan Permohonan
                        </a>

                        <div class="space-y-3 pt-4 border-t border-blue-500">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-shield-alt text-blue-200 mr-3"></i>
                                <span class="text-blue-100">Data Anda dijamin aman</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-headset text-blue-200 mr-3"></i>
                                <span class="text-blue-100">Support 24/7</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-certificate text-blue-200 mr-3"></i>
                                <span class="text-blue-100">Garansi legal & resmi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mt-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-question-circle text-blue-600 mr-2"></i>
                            Butuh Bantuan?
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Tim kami siap membantu menjawab pertanyaan Anda tentang layanan ini
                        </p>
                        <a href="https://wa.me/6281234567890" target="_blank" 
                           class="block w-full text-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Chat via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

