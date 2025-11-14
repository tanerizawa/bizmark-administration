<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy->title }} - Karir Bizmark.ID</title>
    <meta name="description" content="{{ Str::limit(strip_tags($vacancy->description), 160) }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">B</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Bizmark.ID</span>
                </a>
                
                <a href="{{ route('career.index') }}" class="text-gray-600 hover:text-blue-600 transition flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Lowongan</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Job Detail -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-start">
                    <i class="fas fa-check-circle mr-3 mt-1 text-lg"></i>
                    <div>
                        <p class="font-semibold">{{ session('success') }}</p>
                        <p class="text-sm mt-1">Tim HR kami akan meninjau aplikasi Anda dan menghubungi melalui email/WhatsApp dalam 3-5 hari kerja.</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Header Card -->
                    <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl shadow-lg p-8 text-white">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <span class="bg-white/20 backdrop-blur-sm text-white text-sm font-semibold px-3 py-1 rounded-full">
                                    {{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}
                                </span>
                                @if($vacancy->isOpen())
                                    <span class="bg-green-500 text-white text-sm font-semibold px-3 py-1 rounded-full ml-2">
                                        <i class="fas fa-circle text-xs"></i> Aktif
                                    </span>
                                @else
                                    <span class="bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded-full ml-2">
                                        Ditutup
                                    </span>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm opacity-90">Diposting</div>
                                <div class="font-semibold">{{ $vacancy->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <h1 class="text-4xl font-bold mb-3">{{ $vacancy->title }}</h1>
                        <p class="text-lg opacity-95 flex items-center">
                            <i class="fas fa-building mr-2"></i>
                            PT. Cangah Pajaratan Mandiri (Bizmark.ID)
                        </p>
                        <div class="mt-6 flex items-center space-x-4 text-sm">
                            <span class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg">
                                <i class="fas fa-users mr-2"></i>
                                {{ $vacancy->applications_count }} Pelamar
                            </span>
                            @if($vacancy->deadline)
                                <span class="flex items-center bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg">
                                    <i class="fas fa-clock mr-2"></i>
                                    {{ $vacancy->deadline->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Informasi Posisi
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Lokasi Kerja</div>
                                    <div class="font-semibold text-gray-900">{{ $vacancy->location }}</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-wallet text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Range Gaji</div>
                                    <div class="font-semibold text-gray-900">{{ $vacancy->salary_range }}</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-briefcase text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Tipe Pekerjaan</div>
                                    <div class="font-semibold text-gray-900">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}</div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="far fa-calendar-times text-red-600"></i>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Batas Pendaftaran</div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $vacancy->deadline ? $vacancy->deadline->format('d M Y') : 'Tidak terbatas' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Deskripsi Pekerjaan
                        </h2>
                        <div class="text-gray-700 leading-relaxed prose max-w-none">
                            {!! nl2br(e($vacancy->description)) !!}
                        </div>
                    </div>

                    <!-- Responsibilities -->
                    @if($responsibilities && count($responsibilities) > 0)
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-tasks text-blue-600 mr-3"></i>
                                Tanggung Jawab
                            </h2>
                            <div class="space-y-4">
                                @foreach($responsibilities as $index => $responsibility)
                                    <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center flex-shrink-0 font-semibold">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-gray-800 leading-relaxed flex-1">{{ $responsibility }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Qualifications -->
                    @if($qualifications && count($qualifications) > 0)
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-user-check text-purple-600 mr-3"></i>
                                Kualifikasi yang Dibutuhkan
                            </h2>
                            
                            <!-- Important Requirements Alert -->
                            <div class="bg-red-50 border-2 border-red-300 rounded-lg p-4 mb-6">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xl mt-0.5"></i>
                                    <div>
                                        <h3 class="font-bold text-red-900 mb-2">Syarat Wajib yang Harus Dipenuhi:</h3>
                                        <ul class="space-y-2 text-sm text-red-800">
                                            <li class="flex items-start space-x-2">
                                                <i class="fas fa-motorcycle text-red-600 mt-0.5"></i>
                                                <span><strong>Kendaraan Motor & SIM C</strong> - Untuk mobilitas meeting offline ke kantor Karawang</span>
                                            </li>
                                            <li class="flex items-start space-x-2">
                                                <i class="fas fa-laptop text-red-600 mt-0.5"></i>
                                                <span><strong>Laptop Pribadi</strong> - Dengan spesifikasi memadai untuk pekerjaan remote (belum difasilitasi perusahaan)</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach($qualifications as $qualification)
                                    @php
                                        $isRequired = str_contains(strtoupper($qualification), 'WAJIB');
                                        $iconColor = $isRequired ? 'text-red-600' : 'text-purple-600';
                                        $bgColor = $isRequired ? 'bg-red-100' : 'bg-purple-100';
                                        $textWeight = $isRequired ? 'font-semibold' : '';
                                    @endphp
                                    <div class="flex items-start space-x-4">
                                        <div class="w-6 h-6 {{ $bgColor }} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas {{ $isRequired ? 'fa-star' : 'fa-check' }} {{ $iconColor }} text-sm"></i>
                                        </div>
                                        <span class="text-gray-800 leading-relaxed flex-1 {{ $textWeight }}">{{ $qualification }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Benefits -->
                    @if($benefits && count($benefits) > 0)
                        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-md p-8 border-2 border-green-200">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-gift text-green-600 mr-3"></i>
                                Keuntungan Bergabung dengan Kami
                            </h2>
                            <p class="text-gray-600 mb-6">Benefit dan fasilitas yang akan Anda dapatkan sebagai bagian dari tim Bizmark.ID</p>
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($benefits as $benefit)
                                    <div class="flex items-start space-x-3 bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                        <span class="text-gray-800 font-medium">{{ $benefit }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Company Info -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Tentang Bizmark.ID
                        </h2>
                        <div class="space-y-4 text-gray-700 leading-relaxed">
                            <p>
                                <strong>PT. Cangah Pajaratan Mandiri (Bizmark.ID)</strong> adalah perusahaan konsultan lingkungan dan teknis terpercaya 
                                yang berkomitmen memberikan solusi profesional dalam pengelolaan lingkungan dan pembangunan berkelanjutan.
                            </p>
                            <div class="grid md:grid-cols-2 gap-4 mt-6">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-industry text-blue-600 mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Bidang Industri</div>
                                        <div class="text-gray-600">Konsultan Lingkungan & Teknis</div>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-users text-blue-600 mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Ukuran Perusahaan</div>
                                        <div class="text-gray-600">Growing Company</div>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-map-marker-alt text-blue-600 mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Lokasi Kantor</div>
                                        <div class="text-gray-600">Karawang, Jawa Barat</div>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-globe text-blue-600 mt-1"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Website</div>
                                        <a href="{{ route('landing') }}" class="text-blue-600 hover:underline">bizmark.id</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Process -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-md p-8 text-white">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-route mr-3"></i>
                            Proses Rekrutmen
                        </h2>
                        <div class="grid md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-paper-plane text-2xl"></i>
                                </div>
                                <h3 class="font-semibold mb-2">1. Kirim Lamaran</h3>
                                <p class="text-sm opacity-90">Lengkapi formulir dan upload dokumen</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-search text-2xl"></i>
                                </div>
                                <h3 class="font-semibold mb-2">2. Seleksi Berkas</h3>
                                <p class="text-sm opacity-90">Tim HR review aplikasi Anda (3-5 hari)</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-comments text-2xl"></i>
                                </div>
                                <h3 class="font-semibold mb-2">3. Interview</h3>
                                <p class="text-sm opacity-90">Interview dengan HR & User</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-handshake text-2xl"></i>
                                </div>
                                <h3 class="font-semibold mb-2">4. Offering</h3>
                                <p class="text-sm opacity-90">Penawaran kerja & onboarding</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Apply Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 sticky top-24 space-y-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Tertarik Melamar?</h3>
                            <p class="text-gray-600 text-sm">Kirim lamaran Anda sekarang dan bergabunglah dengan tim profesional kami!</p>
                        </div>
                        
                        @if($vacancy->isOpen())
                            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Total Pelamar</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ $vacancy->applications_count }}</span>
                                </div>
                                @if($vacancy->deadline)
                                    <div class="flex items-center text-sm text-gray-600 mt-2">
                                        <i class="fas fa-clock mr-2 text-red-500"></i>
                                        Ditutup {{ $vacancy->deadline->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('career.apply', $vacancy->id) }}" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-semibold text-center transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Lamar Sekarang
                            </a>

                            @if($vacancy->google_form_url)
                                <a href="{{ $vacancy->google_form_url }}" target="_blank" class="block w-full bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold text-center transition">
                                    <i class="fab fa-google mr-2"></i>
                                    Lamar via Google Form
                                </a>
                            @endif

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-info-circle text-yellow-600 mt-1"></i>
                                    <div class="text-sm text-gray-700">
                                        <p class="font-semibold text-gray-900 mb-1">Perhatian!</p>
                                        <p>Setiap email hanya bisa mendaftar <strong>satu kali</strong> untuk posisi ini.</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-3"></i>
                                <p class="text-red-700 font-semibold mb-1">Lowongan Ditutup</p>
                                <p class="text-red-600 text-sm">Pendaftaran sudah tidak tersedia</p>
                            </div>
                        @endif

                        <!-- Requirements Checklist -->
                        <div class="pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-900 mb-3">Yang Perlu Disiapkan:</h4>
                            
                            <!-- Critical Requirements -->
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                                <p class="text-xs font-semibold text-red-900 mb-2 uppercase">Syarat Wajib:</p>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start space-x-2">
                                        <i class="fas fa-motorcycle text-red-600 mt-0.5"></i>
                                        <span class="text-red-800"><strong>Motor & SIM C</strong></span>
                                    </li>
                                    <li class="flex items-start space-x-2">
                                        <i class="fas fa-laptop text-red-600 mt-0.5"></i>
                                        <span class="text-red-800"><strong>Laptop Pribadi</strong></span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Document Requirements -->
                            <p class="text-xs font-semibold text-gray-900 mb-2 uppercase">Dokumen:</p>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-file-pdf text-blue-600 mt-0.5"></i>
                                    <span>CV/Resume terbaru (PDF/DOC, max 2MB)</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-folder text-blue-600 mt-0.5"></i>
                                    <span>Portfolio kerja (opsional, max 5MB)</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-envelope text-blue-600 mt-0.5"></i>
                                    <span>Surat lamaran singkat</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-graduation-cap text-blue-600 mt-0.5"></i>
                                    <span>Data pendidikan & pengalaman</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Share -->
                        <div class="pt-6 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-900 mb-3">Bagikan Lowongan Ini:</p>
                            <div class="grid grid-cols-4 gap-2">
                                <a href="https://wa.me/?text={{ urlencode($vacancy->title . ' - ' . route('career.show', $vacancy->slug)) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg text-center transition flex items-center justify-center" title="WhatsApp">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('career.show', $vacancy->slug)) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg text-center transition flex items-center justify-center" title="Facebook">
                                    <i class="fab fa-facebook-f text-xl"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($vacancy->title) }}&url={{ urlencode(route('career.show', $vacancy->slug)) }}" target="_blank" class="bg-sky-500 hover:bg-sky-600 text-white p-3 rounded-lg text-center transition flex items-center justify-center" title="Twitter">
                                    <i class="fab fa-twitter text-xl"></i>
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('career.show', $vacancy->slug)) }}" target="_blank" class="bg-blue-700 hover:bg-blue-800 text-white p-3 rounded-lg text-center transition flex items-center justify-center" title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-xl"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="pt-6 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-900 mb-3">Butuh Informasi Lebih?</p>
                            <a href="https://wa.me/6283879602855?text=Halo, saya ingin bertanya tentang lowongan {{ $vacancy->title }}" target="_blank" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg flex items-center justify-center space-x-2 transition shadow hover:shadow-md">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span class="font-semibold">Hubungi HR via WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Bizmark.ID - PT. Cangah Pajaratan Mandiri. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
