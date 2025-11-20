@extends('mobile-landing.layouts.content')

@section('title', 'Karir - Bergabung dengan Tim Bizmark.ID')
@section('meta_description', 'Jelajahi peluang karir di Bizmark.ID. Bergabunglah dengan tim profesional kami dalam layanan perizinan dan konsultasi bisnis.')

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4 text-3xl">
            <i class="fas fa-briefcase"></i>
        </div>
        <h1 class="headline text-4xl mb-4">
            Bergabung dengan Tim Kami
        </h1>
        <p class="text-base text-white/90 leading-relaxed mb-8">
            Wujudkan karir impian Anda bersama Bizmark.ID. Berkembang bersama tim profesional dalam industri perizinan dan konsultasi bisnis.
        </p>
        
        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4">
                <div class="text-3xl font-bold mb-1">5+</div>
                <div class="text-xs text-white/80">Tahun Pengalaman</div>
            </div>
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4">
                <div class="text-3xl font-bold mb-1">500+</div>
                <div class="text-xs text-white/80">Klien Terlayani</div>
            </div>
            <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4">
                <div class="text-3xl font-bold mb-1">20+</div>
                <div class="text-xs text-white/80">Tim Profesional</div>
            </div>
        </div>
    </div>
</section>

<!-- Job Listings -->
<section class="magazine-section bg-white">
    <div class="content-container">
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl mb-6">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl flex-shrink-0"></i>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="mb-6">
            <h2 class="headline text-2xl text-gray-900 mb-2">
                <i class="fas fa-briefcase text-[#0077B5] mr-2"></i>
                Posisi Tersedia
            </h2>
            <p class="text-sm text-gray-600">
                {{ $vacancies->total() }} posisi terbuka
            </p>
        </div>

        @if($vacancies->count() > 0)
        <div class="space-y-4">
            @foreach($vacancies as $vacancy)
            <a href="{{ route('career.show', $vacancy->slug) }}" 
               class="block bg-gradient-to-br from-white to-gray-50 rounded-2xl p-5 shadow-md hover:shadow-xl transition-all border border-gray-100">
                <!-- Header -->
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900 mb-1">
                            {{ $vacancy->title }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-building text-[#0077B5] mr-1"></i>
                            {{ $vacancy->department }}
                        </p>
                    </div>
                    @if($vacancy->is_urgent)
                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0">
                        <i class="fas fa-fire mr-1"></i>Urgent
                    </span>
                    @endif
                </div>

                <!-- Details -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-map-marker-alt text-[#0077B5]"></i>
                        <span>{{ $vacancy->location }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-briefcase text-[#0077B5]"></i>
                        <span>{{ $vacancy->type }}</span>
                    </div>
                    @if($vacancy->experience_required)
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-chart-line text-[#0077B5]"></i>
                        <span>{{ $vacancy->experience_required }}</span>
                    </div>
                    @endif
                    @if($vacancy->salary_range)
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-money-bill-wave text-green-500"></i>
                        <span>{{ $vacancy->salary_range }}</span>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                @if($vacancy->summary)
                <p class="text-sm text-gray-700 leading-relaxed mb-4 line-clamp-2">
                    {{ $vacancy->summary }}
                </p>
                @endif

                <!-- CTA -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <span class="text-sm font-semibold text-[#0077B5]">
                        Lihat Detail & Lamar
                    </span>
                    <i class="fas fa-arrow-right text-[#0077B5]"></i>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($vacancies->hasPages())
        <div class="mt-8">
            {{ $vacancies->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-briefcase text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">
                Tidak Ada Lowongan Saat Ini
            </h3>
            <p class="text-sm text-gray-600 mb-6">
                Saat ini tidak ada posisi yang tersedia. Silakan cek kembali nanti.
            </p>
            <a href="{{ route('landing') }}" class="inline-block bg-[#0077B5] text-white font-semibold px-6 py-3 rounded-xl">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Why Join Us Section -->
<section class="magazine-section bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-6 text-center">
            Mengapa Bergabung dengan Kami?
        </h2>
        
        <div class="grid gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-rocket text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Pengembangan Karir</h3>
                        <p class="text-sm text-gray-600">Peluang berkembang dan belajar dari para ahli di industri perizinan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-hand-holding-usd text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Kompensasi Kompetitif</h3>
                        <p class="text-sm text-gray-600">Gaji dan benefit yang menarik sesuai dengan pengalaman Anda</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Tim Profesional</h3>
                        <p class="text-sm text-gray-600">Bekerja dengan tim yang solid dan mendukung pertumbuhan bersama</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-balance-scale text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">Work-Life Balance</h3>
                        <p class="text-sm text-gray-600">Lingkungan kerja yang sehat dan mendukung keseimbangan hidup</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-question-circle text-3xl"></i>
        </div>
        <h2 class="headline text-3xl mb-3">
            Ada Pertanyaan?
        </h2>
        <p class="text-base text-white/90 mb-6 leading-relaxed">
            Tim HR kami siap membantu menjawab pertanyaan Anda tentang peluang karir di Bizmark.ID
        </p>
        
        <div class="space-y-3">
            <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20bertanya%20tentang%20lowongan%20pekerjaan" 
               target="_blank"
               class="block w-full bg-white text-[#0077B5] font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transition-all">
                <i class="fab fa-whatsapp mr-2"></i> Chat via WhatsApp
            </a>
            <a href="mailto:hr@bizmark.id" 
               class="block w-full bg-white/10 backdrop-blur text-white font-semibold py-4 px-6 rounded-xl border-2 border-white/30">
                <i class="fas fa-envelope mr-2"></i> Email: hr@bizmark.id
            </a>
        </div>
    </div>
</section>

@endsection
