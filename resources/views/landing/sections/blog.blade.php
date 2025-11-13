@php
    $articles = isset($latestArticles) ? $latestArticles : collect();
@endphp

<section id="blog" class="section bg-gradient-to-b from-white to-gray-50/30">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">Artikel & Insight</span>
            <h2 class="section-title mb-4">Panduan & Tips Perizinan</h2>
            <p class="section-description max-w-2xl mx-auto">
                Pelajari proses perizinan industri, pembaruan regulasi, dan studi kasus langsung dari tim Bizmark.ID.
            </p>
        </div>

        @if($articles->count() > 0)
            <div class="relative" x-data="blogCarousel()" data-aos="fade-up" data-aos-delay="100">
                <div class="overflow-hidden -mx-4">
                    <div class="flex gap-6 px-4 transition-transform duration-500 ease-out"
                         :style="`transform: translateX(-${currentScroll}px)`"
                         x-ref="track">
                        @foreach($articles as $index => $article)
                            @php
                                $cover = $article->featured_image ? Storage::url($article->featured_image) : null;
                                $publishedDate = $article->published_at;
                            @endphp
                            <article class="flex-shrink-0 w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]">
                                <div class="card h-full group overflow-hidden flex flex-col">
                                    <div class="relative overflow-hidden bg-gradient-to-br from-primary/10 to-secondary/10 aspect-[16/10]">
                                        @if($cover)
                                            <img src="{{ $cover }}"
                                                 alt="{{ $article->title }}"
                                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                 loading="lazy"
                                                 decoding="async"
                                                 sizes="(min-width: 1024px) 360px, 100vw">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center text-primary/20">
                                                <i class="fas fa-newspaper text-5xl"></i>
                                            </div>
                                        @endif

                                        @if($article->category)
                                            <span class="pill pill-brand absolute top-3 left-3 !text-[0.6rem] !tracking-[0.28em]">
                                                {{ is_object($article->category) ? $article->category->name : $article->category }}
                                            </span>
                                        @endif

                                        @if($article->is_featured)
                                            <span class="pill pill-neutral absolute top-3 right-3 !text-[0.6rem] !tracking-[0.28em]">
                                                <i class="fas fa-star text-xs mr-1"></i> Unggulan
                                            </span>
                                        @endif
                                    </div>

                                    <div class="p-5 flex flex-col gap-4 flex-1">
                                        <div class="flex items-center flex-wrap gap-3 text-xs text-gray-500">
                                            <span class="flex items-center gap-1.5">
                                                <i class="far fa-calendar"></i>
                                                {{ $publishedDate ? $publishedDate->format('d M Y') : 'Belum terbit' }}
                                            </span>
                                            <span class="flex items-center gap-1.5">
                                                <i class="far fa-clock"></i>
                                                {{ $article->reading_time ?? 5 }} min
                                            </span>
                                            @if($article->views_count)
                                                <span class="flex items-center gap-1.5">
                                                    <i class="far fa-eye"></i>
                                                    {{ number_format($article->views_count) }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="text-lg font-semibold text-slate-900 leading-tight line-clamp-2">
                                            {{ $article->title }}
                                        </h3>
                                        <p class="text-sm text-slate-600 line-clamp-3">
                                            {{ $article->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($article->content ?? ''), 120) }}
                                        </p>

                                        <div class="mt-auto">
                                            <a href="{{ route('blog.show', $article->slug) }}" class="link-primary inline-flex items-center gap-2">
                                                {{ __('Baca Selengkapnya') }}
                                                <i class="fas fa-arrow-right text-sm"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <button @click="scrollPrev()"
                        x-show="canScrollPrev"
                        class="carousel-nav left"
                        :class="{ 'opacity-50 cursor-not-allowed': !canScrollPrev }"
                        :disabled="!canScrollPrev">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button @click="scrollNext()"
                        x-show="canScrollNext"
                        class="carousel-nav right"
                        :class="{ 'opacity-50 cursor-not-allowed': !canScrollNext }"
                        :disabled="!canScrollNext">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="mt-8 max-w-md mx-auto">
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary to-secondary rounded-full transition-all duration-300"
                             :style="`width: ${scrollProgress}%`"></div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
                <p class="text-gray-600 mb-4">Butuh insight khusus untuk kasus Anda?</p>
                <a href="#contact" class="btn btn-secondary" data-cta="blog_consultation">
                    <span>Konsultasi Gratis</span>
                    <i class="fas fa-comments text-sm"></i>
                </a>
            </div>
        @else
            <div class="text-center py-16" data-aos="fade-up">
                <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-newspaper text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Artikel</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Artikel dan panduan perizinan akan segera hadir. Pantau terus untuk mendapatkan insight terbaru!
                </p>
                <a href="#contact" class="btn btn-primary" data-cta="blog_empty_contact">
                    <span>Hubungi Kami</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        @endif
    </div>
</section>

<script>
function blogCarousel() {
    return {
        currentScroll: 0,
        maxScroll: 0,
        scrollProgress: 0,
        canScrollPrev: false,
        canScrollNext: true,

        init() {
            this.$nextTick(() => {
                this.calculateMaxScroll();
                this.updateScrollState();
            });

            window.addEventListener('resize', () => {
                this.calculateMaxScroll();
                this.updateScrollState();
            });
        },

        calculateMaxScroll() {
            const track = this.$refs.track;
            if (track) {
                this.maxScroll = track.scrollWidth - track.parentElement.clientWidth;
            }
        },

        scrollNext() {
            const scrollAmount = this.$refs.track.parentElement.clientWidth * 0.8;
            this.currentScroll = Math.min(this.currentScroll + scrollAmount, this.maxScroll);
            this.updateScrollState();
        },

        scrollPrev() {
            const scrollAmount = this.$refs.track.parentElement.clientWidth * 0.8;
            this.currentScroll = Math.max(this.currentScroll - scrollAmount, 0);
            this.updateScrollState();
        },

        updateScrollState() {
            this.canScrollPrev = this.currentScroll > 0;
            this.canScrollNext = this.currentScroll < this.maxScroll - 10;
            this.scrollProgress = this.maxScroll > 0 ? (this.currentScroll / this.maxScroll) * 100 : 0;
        }
    }
}
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
