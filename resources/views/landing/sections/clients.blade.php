{{-- Trusted Clients Section - Enhanced from Easybiz.id --}}
<section class="section bg-gradient-to-b from-white to-gray-50/30">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">Dipercaya Oleh</span>
            <h2 class="section-title mb-4">Klien-Klien Kami</h2>
            <p class="section-description">
                Lebih dari 100+ perusahaan dan organisasi telah mempercayai Bizmark.ID sebagai partner bisnis mereka
            </p>
        </div>

        {{-- Client Logos Grid --}}
        <div class="relative" data-aos="fade-up" data-aos-delay="100">
            {{-- Enhanced grid with better spacing and hover effects --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 lg:gap-8 xl:gap-10">
                @php
                    $clients = [
                        'PT. Global Mandiri',
                        'Yayasan Pendidikan Nusantara',
                        'CV. Kreasi Digital',
                        'PT. Sejahtera Abadi',
                        'Koperasi Maju Bersama',
                        'PT. Teknologi Inovasi',
                        'Yayasan Kesehatan Sehat',
                        'CV. Solusi Bisnis',
                        'PT. Mitra Usaha',
                        'Perkumpulan Pengusaha',
                        'PT. Berkah Sejahtera',
                        'Yayasan Sosial Indonesia',
                    ];
                @endphp

                @foreach($clients as $index => $clientName)
                <div class="client-logo-card group" data-aos="zoom-in" data-aos-delay="{{ 100 + ($index * 50) }}">
                    <div class="relative aspect-[3/2] flex items-center justify-center p-6 bg-white rounded-2xl border border-gray-200/60 
                                transition-all duration-400 ease-out hover:-translate-y-1 hover:border-primary/25 hover:bg-primary/5">
                        {{-- Placeholder for logo - will be replaced with actual logos --}}
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="text-center grayscale group-hover:grayscale-0 transition-all duration-500">
                                <div class="text-3xl font-bold text-gray-400 group-hover:text-primary mb-1">
                                    {{ strtoupper(substr($clientName, 0, 2)) }}
                                </div>
                                <div class="text-sm text-gray-500 group-hover:text-gray-700 font-medium leading-tight">
                                    {{ $clientName }}
                                </div>
                            </div>
                        </div>
                        
                        {{-- Hover overlay effect --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent rounded-2xl 
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Decorative elements --}}
            <div class="absolute -top-8 -left-8 w-32 h-32 bg-primary/5 rounded-full blur-3xl -z-10"></div>
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-secondary/5 rounded-full blur-3xl -z-10"></div>
        </div>

        {{-- Bottom CTA --}}
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
            <p class="text-gray-600 mb-4">Bergabunglah dengan klien-klien terpercaya kami</p>
            <a href="#contact" class="btn btn-primary">
                <span>Mulai Konsultasi</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</section>

<style>
    .client-logo-card {
        animation: fadeInUp 0.6s ease-out backwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
