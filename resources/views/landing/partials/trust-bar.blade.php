{{-- ============================================
     TRUST BAR COMPONENT
     Neuroscience-Optimized Social Proof
     ============================================ --}}

<div class="bg-white/95 backdrop-blur-xl border-y border-primary/10 py-8 md:py-10 relative overflow-hidden">
    {{-- Subtle background glow --}}
    <div class="absolute inset-0 bg-gradient-to-r from-primary/3 via-transparent to-secondary/3"></div>

    <div class="container relative z-10">
        <div class="grid md:grid-cols-3 gap-8 md:gap-12 items-center">

            {{-- 1. CLIENT LOGOS SHOWCASE --}}
            <div class="space-y-4">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold text-center md:text-left">
                    Dipercaya Oleh
                </p>

                {{-- Logo Grid with Avatar Style --}}
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    {{-- Client Logo Avatars --}}
                    <div class="flex -space-x-3">
                        <div class="h-12 w-12 rounded-full bg-white border-2 border-gray-200 shadow-md flex items-center justify-center overflow-hidden hover:scale-110 transition-transform" title="PT Indofood">
                            <span class="text-xs font-bold text-primary">IF</span>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white border-2 border-gray-200 shadow-md flex items-center justify-center overflow-hidden hover:scale-110 transition-transform" title="PT Astra">
                            <span class="text-xs font-bold text-primary">AS</span>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white border-2 border-gray-200 shadow-md flex items-center justify-center overflow-hidden hover:scale-110 transition-transform" title="PT Unilever">
                            <span class="text-xs font-bold text-primary">UN</span>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-white border-2 border-gray-200 shadow-md flex items-center justify-center overflow-hidden hover:scale-110 transition-transform" title="PT Pertamina">
                            <span class="text-xs font-bold text-primary">PT</span>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-primary/10 border-2 border-primary/30 shadow-md flex items-center justify-center hover:scale-110 transition-transform">
                            <span class="text-xs font-bold text-primary">+243</span>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-600 text-center md:text-left">
                    <strong class="text-attention-high font-semibold">247+ perusahaan</strong> mempercayai kami
                </p>
            </div>

            {{-- 2. ANIMATED RATING DISPLAY --}}
            <div class="flex flex-col items-center justify-center space-y-3 p-6 bg-gradient-to-br from-primary/5 to-secondary/5 rounded-2xl border border-primary/10">
                {{-- Star Rating --}}
                <div class="flex items-center gap-1.5" id="trust-rating">
                    <i class="fas fa-star text-2xl text-yellow-400 star-icon" style="animation-delay: 0ms"></i>
                    <i class="fas fa-star text-2xl text-yellow-400 star-icon" style="animation-delay: 100ms"></i>
                    <i class="fas fa-star text-2xl text-yellow-400 star-icon" style="animation-delay: 200ms"></i>
                    <i class="fas fa-star text-2xl text-yellow-400 star-icon" style="animation-delay: 300ms"></i>
                    <i class="fas fa-star text-2xl text-yellow-400 star-icon" style="animation-delay: 400ms"></i>
                </div>

                {{-- Rating Score --}}
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-attention-max"
                          data-counter="4.9"
                          data-counter-decimals="1"
                          data-counter-duration="1500">
                        0
                    </span>
                    <span class="text-lg text-gray-500 font-medium">/ 5.0</span>
                </div>

                {{-- Review Count --}}
                <p class="text-sm text-gray-600">
                    Berdasarkan <strong class="text-attention-high font-semibold"
                                         data-counter="127"
                                         data-counter-duration="2000">0</strong> review
                </p>

                {{-- Verified Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 border border-green-200 rounded-full">
                    <i class="fas fa-shield-check text-green-600"></i>
                    <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">Verified Reviews</span>
                </div>
            </div>

            {{-- 3. CERTIFICATION BADGES --}}
            <div class="space-y-4">
                <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold text-center md:text-left">
                    Sertifikasi & Compliance
                </p>

                {{-- Badge Grid --}}
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                    {{-- ISO Badge --}}
                    <div class="flex flex-col items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/30 hover:shadow-lg transition-all group">
                        <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-certificate text-xl text-blue-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">ISO 9001</span>
                    </div>

                    {{-- OSS Partner Badge --}}
                    <div class="flex flex-col items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/30 hover:shadow-lg transition-all group">
                        <div class="h-10 w-10 rounded-lg bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-handshake text-xl text-green-600"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">OSS Partner</span>
                    </div>

                    {{-- Verified Badge --}}
                    <div class="flex flex-col items-center gap-2 p-3 bg-white border border-gray-200 rounded-xl hover:border-primary/30 hover:shadow-lg transition-all group">
                        <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-check-circle text-xl text-primary"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Verified</span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 text-center md:text-left">
                    Konsultan <strong class="text-attention-high font-semibold">bersertifikat resmi</strong>
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Star Animation Styles --}}
<style>
@keyframes star-pop {
    0% {
        transform: scale(0) rotate(0deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(10deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

.star-icon {
    display: inline-block;
    animation: star-pop 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    opacity: 0;
}

/* Hover effect for rating */
#trust-rating:hover .star-icon {
    animation: star-pulse 0.6s ease-in-out infinite alternate;
}

@keyframes star-pulse {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(1.1);
    }
}
</style>
