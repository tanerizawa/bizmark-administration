    <!-- Contact Section -->
    <section id="contact" class="py-20 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">Hubungi Kami</h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">Kami siap membantu Anda dengan solusi terbaik</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <div class="section p-8 mb-8">
                        <h3 class="text-2xl font-bold mb-6">Informasi Kontak</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-2xl mr-4 mt-1" style="color: var(--color-primary);"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Alamat</h4>
                                    <p style="color: var(--dark-text-secondary);">Jl. Sudirman No. 123, Jakarta Selatan<br>DKI Jakarta 12190, Indonesia</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-phone text-2xl mr-4 mt-1" style="color: var(--color-primary);"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Telepon</h4>
                                    <p style="color: var(--dark-text-secondary);">+62 21 1234 5678<br>+62 838 7960 2855</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-2xl mr-4 mt-1" style="color: var(--color-primary);"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Email</h4>
                                    <p style="color: var(--dark-text-secondary);">cs@bizmark.id<br>cs@bizmark.id</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <i class="fas fa-clock text-2xl mr-4 mt-1" style="color: var(--color-primary);"></i>
                                <div>
                                    <h4 class="font-bold mb-1">Jam Operasional</h4>
                                    <p style="color: var(--dark-text-secondary);">Senin - Jumat: 08:00 - 17:00 WIB<br>Sabtu: 08:00 - 12:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="section p-8">
                        <h3 class="text-2xl font-bold mb-6">Ikuti Kami</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="social-icon" style="background-color: var(--color-primary);" aria-label="Facebook">
                                <i class="fab fa-facebook-f text-white text-xl" aria-hidden="true"></i>
                            </a>
                            <a href="#" class="social-icon" style="background-color: var(--apple-blue);" aria-label="Twitter">
                                <i class="fab fa-twitter text-white text-xl" aria-hidden="true"></i>
                            </a>
                            <a href="#" class="social-icon" style="background: linear-gradient(45deg, #405DE6, #E1306C);" aria-label="Instagram">
                                <i class="fab fa-instagram text-white text-xl" aria-hidden="true"></i>
                            </a>
                            <a href="#" class="social-icon" style="background-color: var(--color-primary);" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in text-white text-xl" aria-hidden="true"></i>
                            </a>
                            <a href="#" class="social-icon" style="background-color: var(--apple-green);" aria-label="WhatsApp">
                                <i class="fab fa-whatsapp text-white text-xl" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- ============================================
                     CONTACT FORM - COGNITIVE LOAD OPTIMIZATION
                     Neuroscience Principles:
                     - Multi-step form (max 3-4 fields per step)
                     - Progressive disclosure (Step 1 → 2 → 3)
                     - Visual progress indicator
                     - Real-time validation feedback
                     - Miller's Law: 3 steps max
                     ============================================ -->
                <div class="section p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold">Kirim Pesan</h3>
                        <!-- Progress Indicator -->
                        <div class="flex items-center gap-2" id="formProgress">
                            <div class="progress-step active" data-step="1">
                                <span class="text-xs font-bold">1</span>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step" data-step="2">
                                <span class="text-xs font-bold">2</span>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step" data-step="3">
                                <span class="text-xs font-bold">3</span>
                            </div>
                        </div>
                    </div>
                    
                    <form id="contactForm" class="space-y-6" data-neural-priority="highest">
                        @csrf
                        <!-- Step 1: Basic Information (3 fields - Miller's Law) -->
                        <div class="form-step active" data-step="1">
                            <p class="text-sm mb-4" style="color: var(--dark-text-secondary);">
                                <i class="fas fa-user-circle mr-2" style="color: var(--color-primary);"></i>
                                <strong>Langkah 1:</strong> Informasi Dasar Anda
                            </p>
                            
                            <div class="space-y-4">
                                <div class="form-field">
                                    <label class="block mb-2 font-medium">
                                        Nama Lengkap <span style="color: var(--color-error);">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           required 
                                           autocomplete="name"
                                           class="form-input" 
                                           placeholder="Contoh: Budi Santoso"
                                           data-validation="required|min:3">
                                    <span class="validation-message"></span>
                                </div>
                                
                                <div class="form-field">
                                    <label class="block mb-2 font-medium">
                                        Email <span style="color: var(--color-error);">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           required 
                                           inputmode="email"
                                           autocomplete="email"
                                           class="form-input" 
                                           placeholder="email@perusahaan.com"
                                           data-validation="required|email">
                                    <span class="validation-message"></span>
                                </div>
                                
                                <div class="form-field">
                                    <label class="block mb-2 font-medium">
                                        WhatsApp <span style="color: var(--color-error);">*</span>
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           required 
                                           inputmode="tel"
                                           autocomplete="tel"
                                           pattern="[0-9+\s-]+"
                                           class="form-input" 
                                           placeholder="08123456789"
                                           data-validation="required|phone">
                                    <span class="validation-message"></span>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    class="btn-primary w-full mt-6" 
                                    onclick="nextFormStep(2)"
                                    data-neural-priority="highest">
                                Lanjut ke Layanan
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                        
                        <!-- Step 2: Service Selection (2 fields - Miller's Law) -->
                        <div class="form-step" data-step="2" style="display: none;">
                            <p class="text-sm mb-4" style="color: var(--dark-text-secondary);">
                                <i class="fas fa-briefcase mr-2" style="color: var(--color-primary);"></i>
                                <strong>Langkah 2:</strong> Layanan yang Dibutuhkan
                            </p>
                            
                            <div class="space-y-4">
                                <div class="form-field">
                                    <label class="block mb-2 font-medium">
                                        Jenis Layanan <span style="color: var(--color-error);">*</span>
                                    </label>
                                    <select name="service" 
                                            required 
                                            class="form-input"
                                            data-validation="required">
                                        <option value="">Pilih layanan...</option>
                                        <option value="perizinan">Manajemen Perizinan (OSS, AMDAL, PBG, SLF)</option>
                                        <option value="konsultasi">Konsultasi Bisnis & Legal</option>
                                        <option value="digitalisasi">Digitalisasi Administrasi</option>
                                        <option value="legalitas">Legalitas Perusahaan (PT, CV)</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                    <span class="validation-message"></span>
                                </div>
                                
                                <div class="form-field">
                                    <label class="block mb-2 font-medium">
                                        Detail Kebutuhan <span style="color: var(--color-error);">*</span>
                                    </label>
                                    <textarea name="message" 
                                              required 
                                              class="form-textarea" 
                                              rows="4" 
                                              placeholder="Ceritakan kebutuhan Anda secara singkat..."
                                              data-validation="required|min:20"></textarea>
                                    <span class="validation-message"></span>
                                    <p class="text-xs mt-1" style="color: var(--dark-text-secondary);">
                                        <i class="fas fa-info-circle mr-1"></i>Minimal 20 karakter
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4 mt-6">
                                <button type="button" 
                                        class="btn-secondary flex-1" 
                                        onclick="prevFormStep(1)">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </button>
                                <button type="button" 
                                        class="btn-primary flex-1" 
                                        onclick="nextFormStep(3)"
                                        data-neural-priority="highest">
                                    Lanjut ke Review
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Review & Submit (Progressive Disclosure) -->
                        <div class="form-step" data-step="3" style="display: none;">
                            <p class="text-sm mb-4" style="color: var(--dark-text-secondary);">
                                <i class="fas fa-check-circle mr-2" style="color: var(--color-success);"></i>
                                <strong>Langkah 3:</strong> Review & Kirim
                            </p>
                            
                            <div class="section p-6 mb-6" style="background: var(--dark-bg-tertiary);">
                                <h4 class="font-bold mb-4 text-lg">Ringkasan Pesan Anda</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between pb-2" style="border-bottom: 1px solid var(--color-border);">
                                        <span style="color: var(--dark-text-secondary);">Nama:</span>
                                        <span class="font-medium" id="reviewName">-</span>
                                    </div>
                                    <div class="flex justify-between pb-2" style="border-bottom: 1px solid var(--color-border);">
                                        <span style="color: var(--dark-text-secondary);">Email:</span>
                                        <span class="font-medium" id="reviewEmail">-</span>
                                    </div>
                                    <div class="flex justify-between pb-2" style="border-bottom: 1px solid var(--color-border);">
                                        <span style="color: var(--dark-text-secondary);">WhatsApp:</span>
                                        <span class="font-medium" id="reviewPhone">-</span>
                                    </div>
                                    <div class="flex justify-between pb-2" style="border-bottom: 1px solid var(--color-border);">
                                        <span style="color: var(--dark-text-secondary);">Layanan:</span>
                                        <span class="font-medium" id="reviewService">-</span>
                                    </div>
                                    <div class="pt-2">
                                        <span class="block mb-2" style="color: var(--dark-text-secondary);">Pesan:</span>
                                        <p class="font-medium" id="reviewMessage">-</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3 mb-6 p-4 rounded-lg" style="background: rgba(10, 102, 194, 0.1); border: 1px solid rgba(10, 102, 194, 0.3);">
                                <i class="fas fa-shield-alt mt-1" style="color: var(--color-primary);"></i>
                                <p class="text-sm" style="color: var(--dark-text-secondary);">
                                    Data Anda aman dan akan kami gunakan hanya untuk keperluan komunikasi layanan.
                                </p>
                            </div>
                            
                            <div class="flex gap-4">
                                <button type="button" 
                                        class="btn-secondary flex-1" 
                                        onclick="prevFormStep(2)">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </button>
                                <button type="submit" 
                                        class="btn-primary flex-1"
                                        data-neural-priority="highest">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Kirim Pesan
                                </button>
                            </div>
                        </div>
                        
                        <!-- Success Message (Hidden) -->
                        <div class="form-success" style="display: none;">
                            <div class="text-center p-8">
                                <div class="mb-4">
                                    <i class="fas fa-check-circle text-6xl" style="color: var(--color-success);"></i>
                                </div>
                                <h4 class="text-2xl font-bold mb-3">Pesan Terkirim!</h4>
                                <p class="text-lg mb-6" style="color: var(--dark-text-secondary);">
                                    Terima kasih telah menghubungi kami. Tim kami akan segera merespons dalam 1x24 jam.
                                </p>
                                <button type="button" 
                                        class="btn-primary" 
                                        onclick="resetContactForm()">
                                    <i class="fas fa-plus mr-2"></i>
                                    Kirim Pesan Lain
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

