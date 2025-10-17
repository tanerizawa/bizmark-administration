<!-- Contact Section -->
<section id="contact" class="section bg-white">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">
                {{ app()->getLocale() == 'id' ? 'Hubungi Kami' : 'Contact Us' }}
            </span>
            <h2 class="section-title mb-6">
                {{ app()->getLocale() == 'id' ? 'Mulai Konsultasi Gratis' : 'Start Free Consultation' }}
            </h2>
            <p class="section-description max-w-2xl mx-auto">
                {{ app()->getLocale() == 'id' ? 'Tim kami siap membantu kebutuhan perizinan industri Anda' : 'Our team is ready to help with your industrial permit needs' }}
            </p>
        </div>
        
        <div class="card p-8 md:p-12 max-w-5xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div>
                    <h3 class="text-2xl font-bold mb-6 text-gray-900">
                        {{ app()->getLocale() == 'id' ? 'Informasi Kontak' : 'Contact Information' }}
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start group">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-primary group-hover:scale-110 transition-all">
                                <i class="fas fa-phone text-primary text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">
                                    {{ app()->getLocale() == 'id' ? 'Telepon' : 'Phone' }}
                                </p>
                                <a href="tel:+6281382605030" class="text-gray-600 hover:text-primary transition">
                                    +62 813 8260 5030
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start group">
                            <div class="w-12 h-12 bg-secondary/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-secondary group-hover:scale-110 transition-all">
                                <i class="fas fa-envelope text-secondary text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">Email</p>
                                <a href="mailto:headoffice.cpm@gmail.com" class="text-gray-600 hover:text-primary transition break-all">
                                    headoffice.cpm@gmail.com
                                </a>
                            </div>
                        </div>
                        <div class="flex items-start group">
                            <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center mr-4 group-hover:bg-accent group-hover:scale-110 transition-all">
                                <i class="fas fa-map-marker-alt text-accent text-xl group-hover:text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 mb-1">
                                    {{ app()->getLocale() == 'id' ? 'Alamat' : 'Address' }}
                                </p>
                                <p class="text-gray-600">
                                    Karawang, Jawa Barat<br>
                                    Indonesia 41361
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 space-y-3">
                        <a href="https://wa.me/6281382605030?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi" 
                           target="_blank"
                           class="btn btn-primary w-full justify-center">
                            <i class="fab fa-whatsapp"></i>
                            {{ app()->getLocale() == 'id' ? 'Chat via WhatsApp' : 'Chat via WhatsApp' }}
                        </a>
                        <a href="tel:+6281382605030"
                           class="btn btn-secondary w-full justify-center">
                            <i class="fas fa-phone-alt"></i>
                            {{ app()->getLocale() == 'id' ? 'Telepon Sekarang' : 'Call Now' }}
                        </a>
                    </div>
                </div>
                
                <!-- Quick Form -->
                <div>
                    <h3 class="text-2xl font-bold mb-6 text-gray-900">
                        {{ app()->getLocale() == 'id' ? 'Kirim Pesan' : 'Send Message' }}
                    </h3>
                    <form class="space-y-4" onsubmit="alert('{{ app()->getLocale() == 'id' ? 'Pengiriman form akan segera diimplementasikan' : 'Form submission will be implemented soon' }}'); return false;">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-500 mb-2">
                                {{ app()->getLocale() == 'id' ? 'Nama Lengkap' : 'Full Name' }}
                            </label>
                            <input type="text" 
                                   id="name"
                                   placeholder="{{ app()->getLocale() == 'id' ? 'Masukkan nama lengkap' : 'Enter your full name' }}" 
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition text-gray-900 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-500 mb-2">Email</label>
                            <input type="email" 
                                   id="email"
                                   placeholder="{{ app()->getLocale() == 'id' ? 'nama@email.com' : 'name@email.com' }}" 
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition text-gray-900 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-500 mb-2">
                                {{ app()->getLocale() == 'id' ? 'Nomor Telepon' : 'Phone Number' }}
                            </label>
                            <input type="tel" 
                                   id="phone"
                                   placeholder="+62 812 3456 7890" 
                                   required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition text-gray-900 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-500 mb-2">
                                {{ app()->getLocale() == 'id' ? 'Pesan' : 'Message' }}
                            </label>
                            <textarea id="message"
                                      placeholder="{{ app()->getLocale() == 'id' ? 'Ceritakan kebutuhan perizinan Anda...' : 'Tell us about your permit needs...' }}" 
                                      rows="4" 
                                      required
                                      class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition text-gray-900 placeholder:text-gray-400 resize-none"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <i class="fas fa-paper-plane"></i>
                            {{ app()->getLocale() == 'id' ? 'Kirim Pesan' : 'Send Message' }}
                        </button>
                        <p class="text-xs text-gray-400 text-center mt-2">
                            {{ app()->getLocale() == 'id' ? 'Kami akan merespon dalam 1x24 jam' : 'We will respond within 24 hours' }}
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
