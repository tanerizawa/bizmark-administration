<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karir - Bergabung dengan Tim Bizmark.ID</title>
    <meta name="description" content="Jelajahi peluang karir di Bizmark.ID. Bergabunglah dengan tim profesional kami dalam layanan perizinan dan konsultasi bisnis.">
    
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
                
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('landing') }}" class="text-gray-600 hover:text-blue-600 transition">Beranda</a>
                    <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-blue-600 transition">Layanan</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-blue-600 transition">Blog</a>
                    <a href="{{ route('career.index') }}" class="text-blue-600 font-semibold">Karir</a>
                </nav>
                
                <a href="https://wa.me/6283879602855" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
                    <i class="fab fa-whatsapp"></i>
                    <span class="hidden sm:inline">Hubungi Kami</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 py-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Bergabung dengan Tim Kami</h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Wujudkan karir impian Anda bersama Bizmark.ID. Berkembang bersama tim profesional dalam industri perizinan dan konsultasi bisnis.
            </p>
            <div class="flex flex-wrap justify-center gap-6 mt-8">
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold">5+</div>
                    <div class="text-blue-100">Tahun Berpengalaman</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold">500+</div>
                    <div class="text-blue-100">Klien Terlayani</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold">20+</div>
                    <div class="text-blue-100">Tim Profesional</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Job Listings -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Lowongan Tersedia</h2>
                <p class="text-gray-600">Temukan posisi yang sesuai dengan keahlian dan minat Anda</p>
            </div>

            @if($vacancies->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($vacancies as $vacancy)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group">
                            <div class="p-6">
                                <!-- Job Type Badge -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        {{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}
                                    </span>
                                    @if($vacancy->deadline)
                                        <span class="text-xs text-gray-500">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $vacancy->deadline->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Job Title -->
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition">
                                    {{ $vacancy->title }}
                                </h3>

                                <!-- Company Info -->
                                <p class="text-gray-600 mb-4">
                                    <i class="fas fa-building mr-2 text-gray-400"></i>
                                    PT. Cangah Pajaratan Mandiri
                                </p>

                                <!-- Location & Salary -->
                                <div class="space-y-2 mb-6">
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                        {{ $vacancy->location }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-wallet mr-2 text-gray-400"></i>
                                        {{ $vacancy->salary_range }}
                                    </p>
                                </div>

                                <!-- Applications Count -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $vacancy->applications_count }} pelamar
                                    </span>
                                    <a href="{{ route('career.show', $vacancy->slug) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm flex items-center space-x-1">
                                        <span>Lihat Detail</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $vacancies->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-briefcase text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Lowongan Tersedia</h3>
                    <p class="text-gray-600 mb-6">Saat ini belum ada posisi yang dibuka. Silakan cek kembali nanti.</p>
                    <a href="{{ route('landing') }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-home"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Why Join Us -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Mengapa Bergabung dengan Kami?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Kami menawarkan lingkungan kerja yang mendukung pengembangan karir dan kesejahteraan tim</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Pengembangan Karir</h3>
                    <p class="text-gray-600">Pelatihan berkelanjutan dan kesempatan untuk berkembang di industri perizinan</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tim Solid</h3>
                    <p class="text-gray-600">Bekerja dengan tim profesional yang suportif dan kolaboratif</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-award text-3xl text-pink-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Kompensasi Kompetitif</h3>
                    <p class="text-gray-600">Gaji dan benefit yang sesuai dengan pengalaman dan kontribusi Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Bizmark.ID - PT. Cangah Pajaratan Mandiri. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
