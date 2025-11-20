<!-- FOOTER: Magazine Credits Style - Mobile Optimized -->
<footer class="bg-gray-900 text-white py-6 px-6">
    <div class="max-w-4xl mx-auto">
        
        <!-- Brand Section (Compact) -->
        <div class="text-center mb-5">
            <div class="mb-2">
                <div class="text-white font-bold text-xl flex items-center justify-center gap-2">
                    <i class="fas fa-building text-yellow-400"></i>
                    <span>Bizmark<span class="text-yellow-400">.ID</span></span>
                </div>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed max-w-md mx-auto mb-3">
                Solusi perizinan usaha terpercaya untuk pertumbuhan bisnis Anda. 
                Proses cepat, transparan, dan 100% legal.
            </p>
            
            <!-- Social Media -->
            <div class="flex items-center justify-center gap-3 mb-3">
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                    <i class="fab fa-facebook text-sm"></i>
                </a>
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                    <i class="fab fa-instagram text-sm"></i>
                </a>
                <a href="#" class="w-9 h-9 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:text-white hover:bg-gray-700 transition-colors">
                    <i class="fab fa-linkedin text-sm"></i>
                </a>
            </div>
            <p class="text-xs text-gray-400">
                Email resmi: 
                <a href="mailto:cs@bizmark.id" class="text-white font-semibold hover:underline">
                    cs@bizmark.id
                </a>
            </p>
        </div>
        
        <!-- Quick Links (2 Columns) -->
        <div class="grid grid-cols-2 gap-6 mb-5 text-center">
            <!-- Layanan -->
            <div>
                <h4 class="font-bold text-white text-sm mb-2">Layanan</h4>
                <ul class="space-y-1.5 text-xs text-gray-400">
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Semua Layanan</a></li>
                    <li><a href="{{ route('services.index') }}#oss" class="hover:text-white transition-colors">OSS & NIB</a></li>
                    <li><a href="{{ route('services.index') }}#amdal" class="hover:text-white transition-colors">AMDAL</a></li>
                    <li><a href="{{ route('services.index') }}#pbg" class="hover:text-white transition-colors">PBG & SLF</a></li>
                </ul>
            </div>
            
            <!-- Perusahaan -->
            <div>
                <h4 class="font-bold text-white text-sm mb-2">Perusahaan</h4>
                <ul class="space-y-1.5 text-xs text-gray-400">
                    <li><a href="#why-us" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Artikel</a></li>
                    <li><a href="{{ route('career.index') }}" class="hover:text-white transition-colors">Karir</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Portal Klien</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Divider -->
        <div class="border-t border-gray-800 pt-4 mb-3"></div>
        
        <!-- Footer Bottom -->
        <div class="text-center space-y-2">
            <div class="flex items-center justify-center gap-3 text-xs text-gray-500">
                <a href="{{ route('privacy.policy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                <span>•</span>
                <a href="{{ route('terms.conditions') }}" class="hover:text-white transition-colors">Syarat & Ketentuan</a>
                <span>•</span>
                <a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap</a>
            </div>
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} Bizmark.ID. Seluruh hak cipta dilindungi.
            </p>
        </div>
        
        <!-- Back to Top (Compact) -->
        <div class="text-center mt-4">
            <a href="#" 
               onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;"
               class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-arrow-up"></i>
                <span>Kembali ke Atas</span>
            </a>
        </div>
        
        <!-- Desktop View Toggle -->
        <div class="text-center mt-3 pt-3 border-t border-gray-800">
            <a href="/?desktop=1" 
               onclick="sessionStorage.setItem('device_preference', 'desktop');"
               class="inline-flex items-center gap-2 text-xs text-gray-500 hover:text-gray-300 transition-colors">
                <i class="fas fa-desktop"></i>
                <span>Lihat Versi Desktop</span>
            </a>
        </div>
        
    </div>
</footer>
