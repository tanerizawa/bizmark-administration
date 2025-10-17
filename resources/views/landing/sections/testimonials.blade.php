{{-- Testimonials Section - Enhanced Carousel Design --}}
<section class="section bg-white">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="section-label">Testimoni Klien</span>
            <h2 class="section-title mb-4">Kisah Sukses Bersama Kami</h2>
            <p class="section-description max-w-2xl mx-auto">
                Dengarkan langsung pengalaman klien-klien kami yang telah merasakan dampak positif dari layanan kami
            </p>
        </div>

        {{-- Testimonials Carousel --}}
        <div class="relative" data-aos="fade-up" data-aos-delay="100">
            <div class="testimonials-wrapper" x-data="testimonialCarousel()">
                {{-- Carousel Container --}}
                <div class="overflow-hidden">
                    <div class="testimonials-track flex transition-transform duration-500 ease-out" 
                         :style="`transform: translateX(-${currentSlide * 100}%)`">
                        
                        @php
                            $testimonials = [
                                [
                                    'name' => 'Budi Santoso',
                                    'position' => 'CEO',
                                    'company' => 'PT. Maju Sejahtera',
                                    'image' => 'testimonial-1.jpg',
                                    'rating' => 5,
                                    'text' => 'Bizmark.ID sangat membantu kami dalam mengurus semua dokumen legalitas perusahaan. Prosesnya cepat, transparan, dan tim mereka sangat profesional. Kami sangat merekomendasikan layanan mereka untuk perusahaan yang ingin fokus ke bisnis tanpa pusing urusan administrasi.',
                                ],
                                [
                                    'name' => 'Siti Rahayu',
                                    'position' => 'Founder',
                                    'company' => 'CV. Kreasi Digital Indonesia',
                                    'image' => 'testimonial-2.jpg',
                                    'rating' => 5,
                                    'text' => 'Sebagai startup baru, kami sangat terbantu dengan panduan lengkap dari Bizmark.ID. Mereka tidak hanya mengurus dokumen, tapi juga memberikan konsultasi yang sangat valuable. Harga transparan dan tidak ada biaya tersembunyi. Highly recommended!',
                                ],
                                [
                                    'name' => 'Ahmad Hidayat',
                                    'position' => 'Direktur',
                                    'company' => 'Yayasan Pendidikan Nusantara',
                                    'image' => 'testimonial-3.jpg',
                                    'rating' => 5,
                                    'text' => 'Pengalaman yang luar biasa! Tim Bizmark.ID sangat responsif dan membantu kami menyelesaikan semua keperluan legal yayasan dengan cepat. Proses yang rumit menjadi mudah berkat bantuan mereka. Terima kasih Bizmark.ID!',
                                ],
                                [
                                    'name' => 'Linda Wijaya',
                                    'position' => 'Managing Partner',
                                    'company' => 'Koperasi Sejahtera Bersama',
                                    'image' => 'testimonial-4.jpg',
                                    'rating' => 5,
                                    'text' => 'Sudah 3 tahun kami menggunakan jasa Bizmark.ID untuk berbagai keperluan legal dan administrasi. Konsistensi kualitas layanan mereka sangat baik, dan tim support-nya selalu siap membantu. Partner yang dapat diandalkan!',
                                ],
                                [
                                    'name' => 'Rudi Hermawan',
                                    'position' => 'Owner',
                                    'company' => 'PT. Teknologi Maju',
                                    'image' => 'testimonial-5.jpg',
                                    'rating' => 5,
                                    'text' => 'Proses pendirian PT kami selesai lebih cepat dari yang diperkirakan! Bizmark.ID benar-benar ahli di bidangnya. Dokumentasi lengkap dan mereka selalu update progress secara berkala. Sangat puas dengan layanan mereka.',
                                ],
                            ];
                        @endphp

                        @foreach($testimonials as $index => $testimonial)
                        <div class="testimonial-slide flex-shrink-0 w-full px-4">
                            <div class="max-w-4xl mx-auto">
                                <div class="bg-white/90 rounded-3xl p-8 lg:p-10 
                                            border border-primary/10 
                                            transition-all duration-400 relative overflow-hidden group hover:-translate-y-1">
                                    
                                    {{-- Quote Icon Background --}}
                                    <div class="absolute top-8 right-8 text-8xl text-primary/5 leading-none 
                                                group-hover:text-primary/10 transition-colors duration-500">
                                        <i class="fas fa-quote-right"></i>
                                    </div>

                                    {{-- Content --}}
                                    <div class="relative z-10">
                                        {{-- Rating Stars --}}
                                        <div class="flex justify-center gap-1 mb-6">
                                            @for($i = 0; $i < $testimonial['rating']; $i++)
                                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                                            @endfor
                                        </div>

                                        {{-- Testimonial Text --}}
                                        <blockquote class="text-center mb-8">
                                            <p class="text-lg lg:text-xl text-gray-700 leading-relaxed font-medium italic">
                                                "{{ $testimonial['text'] }}"
                                            </p>
                                        </blockquote>

                                        {{-- Client Info --}}
                                        <div class="flex items-center justify-center gap-4">
                                            {{-- Avatar --}}
                                            <div class="relative">
                                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary to-primary-dark 
                                                            flex items-center justify-center text-white text-lg font-bold
                                                            ring-4 ring-white">
                                                    {{ strtoupper(substr($testimonial['name'], 0, 1)) }}
                                                </div>
                                                {{-- Verified Badge --}}
                                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full 
                                                            border-2 border-white flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </div>

                                            {{-- Name & Position --}}
                                            <div class="text-left">
                                                <div class="font-bold text-gray-900 text-lg">{{ $testimonial['name'] }}</div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $testimonial['position'] }} - 
                                                    <span class="text-primary">{{ $testimonial['company'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Decorative corner --}}
                                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-primary/5 to-transparent rounded-tr-full">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Arrows --}}
                <button @click="prevSlide()" 
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 lg:-translate-x-12
                               w-11 h-11 rounded-full bg-white border border-gray-200
                               flex items-center justify-center text-gray-700
                               hover:bg-primary hover:text-white hover:border-primary
                               transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-primary/20"
                        :class="{ 'opacity-50 cursor-not-allowed': currentSlide === 0 }"
                        :disabled="currentSlide === 0">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button @click="nextSlide()" 
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 lg:translate-x-12
                               w-11 h-11 rounded-full bg-white border border-gray-200
                               flex items-center justify-center text-gray-700
                               hover:bg-primary hover:text-white hover:border-primary
                               transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-primary/20"
                        :class="{ 'opacity-50 cursor-not-allowed': currentSlide === totalSlides - 1 }"
                        :disabled="currentSlide === totalSlides - 1">
                    <i class="fas fa-chevron-right"></i>
                </button>

                {{-- Pagination Dots --}}
                <div class="flex justify-center gap-2 mt-8">
                    <template x-for="(slide, index) in totalSlides" :key="index">
                        <button @click="goToSlide(index)"
                                class="w-3 h-3 rounded-full transition-all duration-300"
                                :class="currentSlide === index ? 'bg-primary w-8' : 'bg-gray-300 hover:bg-gray-400'">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- CTA Button --}}
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
            <p class="text-gray-600 mb-4">Ingin berbagi pengalaman Anda?</p>
            <a href="#contact" class="btn btn-secondary">
                <span>Hubungi Kami</span>
                <i class="fas fa-comment-dots text-sm"></i>
            </a>
        </div>
    </div>
</section>

{{-- Alpine.js Carousel Component --}}
<script>
function testimonialCarousel() {
    return {
        currentSlide: 0,
        totalSlides: {{ count($testimonials) }},
        autoplayInterval: null,

        init() {
            this.startAutoplay();
        },

        nextSlide() {
            if (this.currentSlide < this.totalSlides - 1) {
                this.currentSlide++;
                this.resetAutoplay();
            }
        },

        prevSlide() {
            if (this.currentSlide > 0) {
                this.currentSlide--;
                this.resetAutoplay();
            }
        },

        goToSlide(index) {
            this.currentSlide = index;
            this.resetAutoplay();
        },

        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                if (this.currentSlide < this.totalSlides - 1) {
                    this.currentSlide++;
                } else {
                    this.currentSlide = 0;
                }
            }, 5000); // Change slide every 5 seconds
        },

        resetAutoplay() {
            clearInterval(this.autoplayInterval);
            this.startAutoplay();
        }
    }
}
</script>

<style>
    .testimonial-slide {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
</style>
