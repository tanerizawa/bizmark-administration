<footer class="bg-gray-950 border-t border-gray-900 text-gray-300">
    <!-- Main Footer Content -->
    <div class="py-16 px-4">
        <div class="container">
            <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-8 mb-12">
                
                <!-- Column 1: Company Info (Wider - 2 cols) -->
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold mb-2 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            Bizmark.ID
                        </h3>
                        <p class="text-sm text-gray-400">
                            {{ app()->getLocale() == 'id' ? 'Perizinan Industri Terpercaya' : 'Trusted Industrial Permit Consultant' }}
                        </p>
                    </div>
                    
                    <p class="text-gray-400 text-sm mb-6 leading-relaxed">
                        @if(app()->getLocale() == 'id')
                            PT Cangah Pajaratan Mandiri - Spesialis perizinan Limbah B3, AMDAL, UKL-UPL untuk industri manufaktur dengan pendampingan transparan dan terpercaya.
                        @else
                            PT Cangah Pajaratan Mandiri - Specialist in B3 Waste, AMDAL, UKL-UPL permits for the manufacturing industry with transparent and trusted guidance.
                        @endif
                    </p>
                    
                    <!-- Newsletter Signup -->
                    <div class="bg-white/5 rounded-2xl p-6 mb-6 border border-white/10 shadow-soft">
                        <h4 class="font-bold mb-2 flex items-center text-white">
                            <i class="fas fa-envelope-open-text text-secondary mr-2"></i>
                            {{ app()->getLocale() == 'id' ? 'Newsletter Perizinan' : 'Permit Newsletter' }}
                        </h4>
                        <p class="text-sm text-gray-400 mb-4">
                            {{ app()->getLocale() == 'id' ? 'Dapatkan update regulasi & tips perizinan terbaru' : 'Get latest regulation updates & permit tips' }}
                        </p>
                        <form class="flex gap-2" onsubmit="alert('Newsletter feature coming soon!'); return false;">
                            <input type="email" 
                                   placeholder="{{ app()->getLocale() == 'id' ? 'Email Anda' : 'Your Email' }}" 
                                   required 
                                   class="flex-1 px-4 py-2 bg-white/10 border border-white/10 rounded-xl text-sm text-gray-200 focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/30 transition placeholder:text-gray-400">
                            <button type="submit" class="btn btn-secondary bg-secondary text-white px-6 py-2 text-sm">
                                Subscribe
                            </button>
                        </form>
                    </div>
                    
                    <!-- Social Media -->
                    <div>
                        <h5 class="font-semibold mb-3 text-sm text-white">
                            {{ app()->getLocale() == 'id' ? 'Ikuti Kami:' : 'Follow Us:' }}
                        </h5>
                        <div class="flex gap-3">
                            <a href="https://facebook.com/bizmarkid" target="_blank" rel="noopener" class="w-10 h-10 bg-primary/10 hover:bg-primary hover:text-white rounded-full flex items-center justify-center transition group text-primary" title="Facebook" aria-label="Facebook">
                                <i class="fab fa-facebook-f group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="https://instagram.com/bizmark.id" target="_blank" rel="noopener" class="w-10 h-10 bg-pink-500/10 hover:bg-pink-500 hover:text-white rounded-full flex items-center justify-center transition group text-pink-500" title="Instagram" aria-label="Instagram">
                                <i class="fab fa-instagram group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="https://linkedin.com/company/bizmarkid" target="_blank" rel="noopener" class="w-10 h-10 bg-blue-600/10 hover:bg-blue-600 hover:text-white rounded-full flex items-center justify-center transition group text-blue-600" title="LinkedIn" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="https://youtube.com/@bizmarkid" target="_blank" rel="noopener" class="w-10 h-10 bg-red-600/10 hover:bg-red-600 hover:text-white rounded-full flex items-center justify-center transition group text-red-600" title="YouTube" aria-label="YouTube">
                                <i class="fab fa-youtube group-hover:scale-110 transition-transform"></i>
                            </a>
                            <a href="https://wa.me/6281382605030" target="_blank" rel="noopener" class="w-10 h-10 bg-secondary/10 hover:bg-secondary hover:text-white rounded-full flex items-center justify-center transition group text-secondary" title="WhatsApp" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp group-hover:scale-110 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Column 2: Layanan Lengkap -->
                <div>
                    <h4 class="font-bold mb-4 text-lg text-white">
                        {{ app()->getLocale() == 'id' ? 'Layanan Kami' : 'Our Services' }}
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#services" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-biohazard text-xs mr-2"></i>{{ app()->getLocale() == 'id' ? 'Perizinan LB3' : 'B3 Waste Permit' }}
                        </a></li>
                        <li><a href="#services" class="hover:text-secondary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-leaf text-xs mr-2"></i>AMDAL
                        </a></li>
                        <li><a href="#services" class="hover:text-purple-600 transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-file-alt text-xs mr-2"></i>UKL-UPL
                        </a></li>
                        <li><a href="#services" class="hover:text-accent transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-globe text-xs mr-2"></i>OSS (NIB)
                        </a></li>
                        <li><a href="#services" class="hover:text-cyan-600 transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-building text-xs mr-2"></i>PBG / SLF
                        </a></li>
                        <li><a href="#services" class="hover:text-pink-600 transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-industry text-xs mr-2"></i>Izin Operasional
                        </a></li>
                        <li><a href="#services" class="hover:text-teal-400 transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-user-tie text-xs mr-2"></i>Konsultan Lingkungan
                        </a></li>
                        <li><a href="#services" class="hover:text-indigo-400 transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-chart-line text-xs mr-2"></i>Monitoring Digital
                        </a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Perusahaan -->
                <div>
                    <h4 class="font-bold mb-4 text-lg text-white">Perusahaan</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#about" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-info-circle text-xs mr-2"></i>Tentang Kami
                        </a></li>
                        <li><a href="#about" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-users text-xs mr-2"></i>Tim Kami
                        </a></li>
                        <li><a href="#about" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-briefcase text-xs mr-2"></i>Portofolio
                        </a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-newspaper text-xs mr-2"></i>Blog & Artikel
                        </a></li>
                        <li><a href="#contact" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-envelope text-xs mr-2"></i>Kontak
                        </a></li>
                        <li><a href="#about" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-handshake text-xs mr-2"></i>Mitra
                        </a></li>
                        <li><a href="#contact" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-question-circle text-xs mr-2"></i>FAQ
                        </a></li>
                        <li><a href="#contact" class="hover:text-primary transition hover:pl-2 inline-block duration-200">
                            <i class="fas fa-user-plus text-xs mr-2"></i>Karir
                        </a></li>
                    </ul>
                </div>
                
                <!-- Column 4: Kontak & Legal -->
                <div>
                    <h4 class="font-bold mb-4 text-lg text-white">Kontak Kami</h4>
                    <ul class="space-y-3 text-sm text-gray-400 mb-6">
                        <li class="flex items-start">
                            <i class="fas fa-phone text-primary mr-3 mt-1"></i>
                            <div>
                                <a href="tel:+6281382605030" class="hover:text-primary transition">
                                    +62 813 8260 5030
                                </a>
                                <p class="text-xs text-gray-500">Senin - Jumat, 08:00 - 17:00</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-primary mr-3 mt-1"></i>
                            <a href="mailto:headoffice.cpm@gmail.com" class="hover:text-primary transition break-all">
                                headoffice.cpm@gmail.com
                            </a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary mr-3 mt-1"></i>
                            <span>Karawang, Jawa Barat<br>Indonesia 41361</span>
                        </li>
                    </ul>
                    
                    <h5 class="font-semibold mb-3 text-sm text-white">Legal & Kebijakan</h5>
                    <ul class="space-y-2 text-xs text-gray-400">
                        <li><a href="#" class="hover:text-primary transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-primary transition">Sitemap</a></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Bottom Footer -->
    <div class="border-t border-white/10 py-6 px-4">
        <div class="container">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-400">
                <!-- Copyright -->
                <div class="text-center md:text-left">
                    <p>&copy; 2025 <strong class="text-white">PT Cangah Pajaratan Mandiri</strong> (Bizmark.ID)</p>
                    <p class="text-xs mt-1">All rights reserved. Made with ❤️ in Indonesia</p>
                </div>
                
                <!-- Certifications Badge -->
                <div class="flex items-center gap-3">
                    <div class="px-3 py-1 bg-green-500/10 border border-green-500/30 rounded-full flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span class="text-xs font-semibold text-green-400">Verified Company</span>
                    </div>
                    <div class="px-3 py-1 bg-blue-500/10 border border-blue-500/30 rounded-full flex items-center gap-2">
                        <i class="fas fa-certificate text-blue-400"></i>
                        <span class="text-xs font-semibold text-blue-400">ISO Certified</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
