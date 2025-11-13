@php
    $testimonials = collect(config('landing.testimonials', []));
@endphp

<section class="section bg-white">
    <div class="container">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="section-label">Testimoni Klien</span>
            <h2 class="section-title mb-4">Kisah Sukses Bersama Kami</h2>
            <p class="section-description max-w-2xl mx-auto">
                Dengarkan pengalaman para pemimpin yang mempercayakan proses perizinan dan tata kelola dokumen kepada Bizmark.ID.
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
                                                <p class="text-lg lg:text-xl text-gray-700 leading-relaxed font-medium italic">“{{ $testimonial['text'] }}”</p>
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
                        :disabled="currentSlide === 0">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button @click="nextSlide()"
                        class="carousel-nav right"
                        :class="{ 'opacity-50 cursor-not-allowed': currentSlide === totalSlides - 1 }"
                        :disabled="currentSlide === totalSlides - 1">
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

        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
            <p class="text-gray-600 mb-4">Ingin berbagi pengalaman Anda?</p>
            <a href="#contact" class="btn btn-secondary" data-cta="testimonials_contact">
                <span>Hubungi Kami</span>
                <i class="fas fa-comment-dots text-sm"></i>
            </a>
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
