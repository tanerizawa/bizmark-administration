@php
    $footerMetrics = config('landing.footer_metrics', []);
    $footerLinks = config('landing.footer_links', []);

    $resolveHref = function ($link) {
        if (!empty($link['type']) && $link['type'] === 'route') {
            return route($link['href']);
        }
        return $link['href'] ?? '#';
    };
@endphp

<footer class="bg-slate-950 text-slate-200">
    <!-- Newsletter Section -->
    <div class="border-b border-slate-800">
        <div class="container-wide py-12">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 mb-4">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">
                    Dapatkan Update Terbaru
                </h3>
                <p class="text-slate-400 mb-6 max-w-xl mx-auto">
                    Berlangganan newsletter kami untuk mendapatkan informasi terbaru tentang perizinan, regulasi, dan tips bisnis langsung ke email Anda.
                </p>
                
                <form id="newsletterForm" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input 
                        type="email" 
                        id="subscriberEmail"
                        name="email"
                        placeholder="Email Anda" 
                        required
                        class="flex-1 px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    >
                    <button 
                        type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            Subscribe
                        </span>
                    </button>
                </form>
                
                <div id="subscribeMessage" class="mt-4 hidden"></div>
                
                <p class="text-xs text-slate-500 mt-4">
                    <i class="fas fa-lock mr-1"></i>
                    Kami menghargai privasi Anda. Data email tidak akan dibagikan ke pihak ketiga.
                </p>
            </div>
        </div>
    </div>

    <div class="container-wide py-16 lg:py-20 grid lg:grid-cols-[1.2fr_0.8fr_0.8fr_0.7fr] gap-10">
        <div class="space-y-5">
            <div>
                <span class="text-xs uppercase tracking-[0.4em] text-slate-500">Bizmark.ID</span>
                <p class="text-2xl font-semibold text-white">PT Cangah Pajaratan Mandiri</p>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed">
                Konsultan perizinan industri. Kami menyelaraskan dokumen, instansi, dan jadwal agar seluruh izin berjalan presisi.
            </p>
            <div class="grid grid-cols-2 gap-4 text-xs uppercase tracking-[0.35em] text-slate-500">
                @foreach($footerMetrics as $metric)
                    <div>
                        <p class="text-slate-200 text-lg font-semibold">{{ $metric['value'] }}</p>
                        {{ $metric['label'] }}
                    </div>
                @endforeach
            </div>
        </div>

        @foreach($footerLinks as $section => $links)
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-500">{{ $section }}</p>
                <ul class="space-y-2 text-sm text-slate-300">
                    @foreach($links as $link)
                        <li>
                            @if(!empty($link['href']))
                                <a href="{{ $resolveHref($link) }}" class="hover:text-white transition">{{ $link['label'] }}</a>
                            @else
                                {{ $link['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <div class="border-t border-slate-800 py-6">
        <div class="container-wide flex flex-col lg:flex-row items-center justify-between gap-3 text-xs text-slate-500">
            <p>&copy; {{ now()->year }} Bizmark.ID • PT Cangah Pajaratan Mandiri</p>
            <p>Made in Indonesia</p>
        </div>
    </div>
</footer>

<script>
document.getElementById('newsletterForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('subscriberEmail').value;
    const submitBtn = this.querySelector('button[type="submit"]');
    const messageDiv = document.getElementById('subscribeMessage');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
    
    try {
        const response = await fetch('{{ route("newsletter.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            messageDiv.className = 'mt-4 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400';
            messageDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
            this.reset();
            
            // Track subscription
            if (typeof gtag !== 'undefined') {
                gtag('event', 'newsletter_subscription', {
                    'event_category': 'Engagement',
                    'event_label': email
                });
            }
        } else {
            throw new Error(data.message || 'Subscription failed');
        }
    } catch (error) {
        messageDiv.className = 'mt-4 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400';
        
        if (error.message.includes('already')) {
            messageDiv.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Email ini sudah terdaftar di newsletter kami.';
        } else {
            messageDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan. Silakan coba lagi.';
        }
    } finally {
        messageDiv.classList.remove('hidden');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><i class="fas fa-paper-plane"></i>Subscribe</span>';
        
        // Hide message after 5 seconds
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
    }
});
</script>
