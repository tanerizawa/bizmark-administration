@php
    $stats = config('landing.stats', []);
@endphp

{{-- Statistics Section - Enhanced with Counter Animation --}}
<section class="section bg-gradient-to-br from-primary/5 via-white to-secondary/5 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-0 left-1/4 w-64 h-64 bg-primary rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-secondary rounded-full blur-3xl"></div>
    </div>

    <div class="container relative z-10">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">Pencapaian Kami</span>
            <h2 class="section-title mb-4">Dipercaya & Berpengalaman</h2>
            <p class="section-description max-w-2xl mx-auto">
                Angka-angka yang membuktikan komitmen kami dalam memberikan layanan terbaik
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
            @foreach($stats as $index => $stat)
                <article class="stat-card" data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}" style="--stat-accent: {{ $stat['color'] }}; --stat-accent-soft: {{ $stat['soft'] }};">
                    <div class="stat-surface text-center">
                        <div class="stat-icon mx-auto mb-6">
                            <i class="{{ $stat['icon'] }} text-xl text-white"></i>
                        </div>
                        <div class="stat-value">
                            <span class="counter-animation" data-target="{{ $stat['target'] }}">0</span>
                            @if($stat['suffix'])
                                <span class="stat-suffix">{{ $stat['suffix'] }}</span>
                            @endif
                        </div>
                        <p class="stat-label">{{ $stat['label'] }}</p>
                        <p class="stat-description">{{ $stat['description'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="300">
            <p class="text-lg text-gray-700 font-medium">
                Ratusan bisnis telah berkembang bersama kami.
                <a href="#contact" class="text-primary hover:text-primary-dark underline-link">
                    Saatnya giliran Anda!
                </a>
            </p>
        </div>
    </div>
</section>

{{-- Counter Animation Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter-animation');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                animateCounter(entry.target);
                entry.target.classList.add('counted');
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));

    function animateCounter(element) {
        const target = parseFloat(element.dataset.target || '0');
        const duration = parseInt(element.dataset.duration || '2000', 10);
        const decimals = parseInt(element.dataset.decimals || '0', 10);
        const increment = target / (duration / 16);
        let current = 0;

        const formatValue = (value) => {
            if (decimals > 0) {
                return Math.min(value, target).toFixed(decimals);
            }
            return Math.floor(Math.min(value, target)).toString();
        };

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = formatValue(current);
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = formatValue(target);
            }
        };

        updateCounter();
    }
});
</script>
