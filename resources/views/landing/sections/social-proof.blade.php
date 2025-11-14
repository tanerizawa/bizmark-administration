@php
    $clientList = config('landing.clients', []);
    $testimonials = collect(config('landing.testimonials', []));
@endphp

{{-- Social Proof Section: Clients + Testimonials --}}
<section class="py-24 lg:py-32 bg-gradient-to-b from-white to-gray-50">
    <div class="container-wide space-y-20">
        
        {{-- Part 1: Trusted Clients --}}
        <div class="space-y-14">
            <div class="text-center max-w-3xl mx-auto space-y-4" data-aos="fade-up">
                <div class="pill pill-brand mx-auto justify-center">
                    Dipercaya Oleh
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                    Dipercaya perusahaan manufaktur, infrastruktur, dan energi.
                </h2>
                <p class="text-lg text-gray-600">
                    100+ organisasi menyerahkan audit izin, AMDAL, dan pengelolaan legalitasnya kepada tim Bizmark.ID untuk menjaga tata kelola tetap rapi.
                </p>
            </div>

            {{-- Client Logos Grid --}}
            <div class="relative" data-aos="fade-up" data-aos-delay="80">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 lg:gap-8 xl:gap-10" role="list" aria-label="Daftar klien Bizmark.ID">
                    @foreach($clientList as $index => $clientName)
                    <article class="client-logo-card group" role="listitem" data-aos="zoom-in" data-aos-delay="{{ 80 + ($index * 40) }}">
                        <div class="relative aspect-[3/2] flex items-center justify-center p-6 bg-white rounded-2xl border border-slate-100 transition-all duration-400 hover:-translate-y-1 hover:border-primary/30 hover:bg-primary/5">
                            <div class="text-center grayscale group-hover:grayscale-0 transition-all duration-500">
                                <div class="monogram mb-1">
                                    {{ strtoupper(substr($clientName, 0, 2)) }}
                                </div>
                                <div class="text-sm text-slate-500 group-hover:text-slate-700 font-medium leading-tight">
                                    {{ $clientName }}
                                </div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </article>
                    @endforeach
                </div>
                <div class="absolute -top-8 -left-8 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10"></div>
                <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-secondary/10 rounded-full blur-3xl -z-10"></div>
            </div>
        </div>

        {{-- Part 2: Client Testimonials --}}
        <div class="space-y-14">
            <div class="text-center max-w-3xl mx-auto space-y-4" data-aos="fade-up">
                <span class="section-label">Testimoni Klien</span>
                <h2 class="section-title">Cerita Singkat Mitra Kami</h2>
                <p class="section-description">
                    Lihat bagaimana para pemimpin menata perizinan dan dokumen dengan dukungan Bizmark.ID.
                </p>
            </div>

            <div class="relative" data-aos="fade-up" data-aos-delay="100">
                @if($testimonials->isNotEmpty())
                <div class="testimonials-wrapper" x-data="testimonialCarousel({{ max(1, $testimonials->count()) }})">
                    <div class="overflow-hidden">
                        <div class="testimonials-track flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${currentSlide * 100}%)`">
                            @foreach($testimonials as $testimonial)
                                <div class="testimonial-slide flex-shrink-0 w-full px-4">
                                    <div class="max-w-4xl mx-auto">
                                        <div class="testimonial-card">
                                            <div class="testimonial-quote" aria-hidden="true">
                                                <i class="fas fa-quote-right"></i>
                                            </div>

                                            <div class="relative z-10">
                                                <div class="flex justify-center gap-1 mb-6">
                                                    @for($i = 0; $i < $testimonial['rating']; $i++)
                                                        <i class="fas fa-star text-yellow-400 text-xl"></i>
                                                    @endfor
                                                </div>

                                                <blockquote class="text-center mb-8">
                                                    <p class="text-lg lg:text-xl text-gray-700 leading-relaxed font-medium italic">"{{ $testimonial['text'] }}"</p>
                                                </blockquote>

                                                <div class="flex items-center justify-center gap-4">
                                                    <div class="relative">
                                                        <div class="testimonial-avatar">
                                                            {{ strtoupper(substr($testimonial['name'], 0, 1)) }}
                                                        </div>
                                                        <div class="testimonial-verified" aria-hidden="true">
                                                            <i class="fas fa-check text-white text-xs"></i>
                                                        </div>
                                                    </div>
                                                    <div class="text-left">
                                                        <div class="font-bold text-gray-900 text-lg">{{ $testimonial['name'] }}</div>
                                                        <div class="text-sm text-gray-600">
                                                            {{ $testimonial['position'] }} · <span class="text-primary">{{ $testimonial['company'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button @click="prevSlide()"
                            class="carousel-nav left"
                            :class="{ 'opacity-50 cursor-not-allowed': currentSlide === 0 }"
                            :disabled="currentSlide === 0"
                            aria-label="Testimoni sebelumnya">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <button @click="nextSlide()"
                            class="carousel-nav right"
                            :class="{ 'opacity-50 cursor-not-allowed': currentSlide === totalSlides - 1 }"
                            :disabled="currentSlide === totalSlides - 1"
                            aria-label="Testimoni berikutnya">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="flex justify-center gap-2 mt-8">
                        <template x-for="index in totalSlides" :key="index">
                            <button @click="goToSlide(index - 1)"
                                    class="testimonial-dot"
                                    :class="currentSlide === (index - 1) ? 'active' : ''"
                                    :aria-label="`Loncat ke testimoni ${index}`"></button>
                        </template>
                    </div>
                </div>
                @else
                <div class="card text-center" data-aos="fade-up">
                    <p class="text-lg text-gray-600">Testimoni akan segera tersedia.</p>
                </div>
                @endif
            </div>

            <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                <p class="text-gray-600 mb-4">Ingin berbagi pengalaman Anda?</p>
                <a href="#contact" class="btn btn-secondary" data-cta="testimonials_contact">
                    <span>Hubungi Kami</span>
                    <i class="fas fa-comment-dots text-sm"></i>
                </a>
            </div>
        </div>

        {{-- Bottom Note --}}
        <div class="text-center space-y-3" data-aos="fade-up">
            <p class="text-sm uppercase tracking-[0.4em] text-gray-400">Reference Projects</p>
            <p class="text-base text-gray-600">Studi kasus lengkap tersedia via NDA - hubungi tim kami untuk menjadwalkan review.</p>
        </div>
    </div>
</section>

<script>
function testimonialCarousel(totalSlides) {
    return {
        currentSlide: 0,
        totalSlides,
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
                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            }, 5000);
        },

        resetAutoplay() {
            clearInterval(this.autoplayInterval);
            this.startAutoplay();
        }
    }
}
</script>
