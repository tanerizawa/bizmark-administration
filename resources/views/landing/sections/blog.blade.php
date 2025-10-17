{{-- Blog Articles Section - Enhanced Horizontal Carousel --}}
<section id="blog" class="section bg-gradient-to-b from-white to-gray-50/30">
    <div class="container">
        {{-- Section Header --}}
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">Artikel & Insight</span>
            <h2 class="section-title mb-4">Panduan & Tips Perizinan</h2>
            <p class="section-description max-w-2xl mx-auto">
                Pelajari lebih lanjut tentang proses perizinan industri dan regulasi terbaru dari expert kami
            </p>
        </div>

        @if(isset($latestArticles) && $latestArticles->count() > 0)
            {{-- Articles Carousel --}}
            <div class="relative" x-data="blogCarousel()" data-aos="fade-up" data-aos-delay="100">
                {{-- Carousel Wrapper --}}
                <div class="overflow-hidden -mx-4">
                    <div class="flex gap-6 px-4 transition-transform duration-500 ease-out"
                         :style="`transform: translateX(-${currentScroll}px)`"
                         x-ref="track">
                        
                        @foreach($latestArticles as $index => $article)
                        <article class="flex-shrink-0 w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]">
                            <div class="card h-full group hover:shadow-2xl transition-all duration-300 overflow-hidden">
                                {{-- Article Image --}}
                                <div class="relative overflow-hidden bg-gradient-to-br from-primary/10 to-secondary/10 aspect-[16/10]">
                                    @if($article->featured_image)
                                        <img src="{{ Storage::url($article->featured_image) }}" 
                                             alt="{{ $article->title }}" 
                                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <i class="fas fa-newspaper text-5xl text-primary/20"></i>
                                        </div>
                                    @endif
                                    
                                    {{-- Category Badge --}}
                                    @if($article->category)
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-1 bg-primary/90 backdrop-blur-sm text-white text-xs font-bold rounded-lg">
                                            {{ is_object($article->category) ? $article->category->name : $article->category }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Featured Badge --}}
                                    @if($article->is_featured)
                                    <div class="absolute top-3 right-3">
                                        <span class="px-2.5 py-1 bg-secondary/90 backdrop-blur-sm text-white text-xs font-bold rounded-lg">
                                            <i class="fas fa-star text-xs mr-1"></i>
                                            Unggulan
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Article Content --}}
                                <div class="p-5">
                                    {{-- Meta Info --}}
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-3">
                                        <span class="flex items-center">
                                            <i class="far fa-calendar mr-1.5"></i>
                                            {{ $article->published_at->format('d M Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-clock mr-1.5"></i>
                                            {{ $article->reading_time ?? 5 }} min
                                        </span>
                                        @if($article->views_count)
                                        <span class="flex items-center">
                                            <i class="far fa-eye mr-1.5"></i>
                                            {{ $article->views_count }}
                                        </span>
                                        @endif
                                    </div>

                                    {{-- Title --}}
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-primary transition-colors duration-300">
                                        {{ $article->title }}
                                    </h3>

                                    {{-- Excerpt --}}
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                                        {{ $article->excerpt }}
                                    </p>

                                    {{-- Read More Link --}}
                                    <a href="{{ route('blog.article', $article->slug) }}" 
                                       class="inline-flex items-center gap-2 text-primary hover:text-primary-dark font-semibold text-sm group/link">
                                        <span class="underline-link">Baca Selengkapnya</span>
                                        <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-300"></i>
                                    </a>

                                    {{-- Author (if available) --}}
                                    @if($article->author)
                                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($article->author->name, 0, 1)) }}
                                        </div>
                                        <div class="text-xs">
                                            <div class="font-medium text-gray-900">{{ $article->author->name }}</div>
                                            <div class="text-gray-500">{{ $article->author->position ?? 'Author' }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                        @endforeach

                        {{-- View All Articles Card --}}
                        <div class="flex-shrink-0 w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]">
                            <div class="card h-full bg-gradient-to-br from-primary/5 to-secondary/5 border-2 border-dashed border-primary/30
                                        flex flex-col items-center justify-center text-center p-8 hover:border-primary hover:shadow-xl
                                        transition-all duration-300 group cursor-pointer">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-primary-dark 
                                            flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-th-large text-3xl text-white"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Lihat Semua Artikel</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Jelajahi {{ $latestArticles->count() }}+ artikel lainnya tentang perizinan & regulasi
                                </p>
                                <a href="{{ route('blog.index') }}" class="btn btn-primary inline-flex items-center gap-2">
                                    <span>Selengkapnya</span>
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Arrows --}}
                <button @click="scrollPrev()" 
                        x-show="canScrollPrev"
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 lg:-translate-x-6
                               w-12 h-12 rounded-full bg-white border-2 border-gray-200 shadow-lg
                               flex items-center justify-center text-gray-700
                               hover:bg-primary hover:text-white hover:border-primary
                               transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-primary/20 z-10">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button @click="scrollNext()" 
                        x-show="canScrollNext"
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 lg:translate-x-6
                               w-12 h-12 rounded-full bg-white border-2 border-gray-200 shadow-lg
                               flex items-center justify-center text-gray-700
                               hover:bg-primary hover:text-white hover:border-primary
                               transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-primary/20 z-10">
                    <i class="fas fa-chevron-right"></i>
                </button>

                {{-- Progress Bar --}}
                <div class="mt-8 max-w-md mx-auto">
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-primary to-secondary rounded-full transition-all duration-300"
                             :style="`width: ${scrollProgress}%`"></div>
                    </div>
                </div>
            </div>

        @else
            {{-- Empty State --}}
            <div class="text-center py-16" data-aos="fade-up">
                <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-newspaper text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Belum Ada Artikel</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Artikel dan panduan perizinan akan segera hadir. Pantau terus untuk mendapatkan insight terbaru!
                </p>
                <a href="#contact" class="btn btn-primary inline-flex items-center gap-2">
                    <span>Hubungi Kami</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        @endif

        {{-- Bottom CTA --}}
        @if(isset($latestArticles) && $latestArticles->count() > 0)
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
            <p class="text-gray-600 mb-4">Butuh panduan khusus untuk perizinan Anda?</p>
            <a href="#contact" class="btn btn-secondary inline-flex items-center gap-2">
                <span>Konsultasi Gratis</span>
                <i class="fas fa-comments text-sm"></i>
            </a>
        </div>
        @endif
    </div>
</section>

{{-- Alpine.js Blog Carousel Component --}}
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

            // Recalculate on window resize
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
            this.canScrollNext = this.currentScroll < this.maxScroll - 10; // 10px threshold
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
