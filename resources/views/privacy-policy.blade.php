@extends('landing.layout')

@section('title', app()->getLocale() == 'id' ? 'Kebijakan Privasi - Bizmark.ID' : 'Privacy Policy - Bizmark.ID')

@section('content')
<div class="min-h-screen bg-dark-bg py-20">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-12" data-aos="fade-down">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white">
                {{ app()->getLocale() == 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' }}
            </h1>
            <p class="text-gray-400">
                {{ app()->getLocale() == 'id' 
                    ? 'Terakhir diperbarui: 11 Januari 2025'
                    : 'Last updated: January 11, 2025' }}
            </p>
        </div>
        
        <!-- Content -->
        <div class="glass rounded-2xl p-8 md:p-12 space-y-8 text-gray-300" data-aos="fade-up">
            
            @if(app()->getLocale() == 'id')
            
            <!-- Indonesian Version -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">1. Informasi yang Kami Kumpulkan</h2>
                <p class="mb-4">Kami mengumpulkan informasi berikut saat Anda menggunakan website kami:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Informasi yang Anda berikan secara langsung (nama, email, nomor telepon)</li>
                    <li>Data penggunaan website (halaman yang dikunjungi, waktu kunjungan, durasi)</li>
                    <li>Informasi teknis (jenis browser, alamat IP, tipe perangkat)</li>
                    <li>Cookie dan teknologi pelacakan serupa</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">2. Penggunaan Cookie</h2>
                <p class="mb-4">Kami menggunakan cookie untuk meningkatkan pengalaman Anda:</p>
                
                <div class="bg-white/5 rounded-xl p-6 mb-4">
                    <h3 class="font-bold text-white mb-3">📌 Cookie Esensial</h3>
                    <p>Cookie yang diperlukan untuk fungsi dasar website, seperti menjaga preferensi bahasa Anda dan menyimpan pilihan consent cookie.</p>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6 mb-4">
                    <h3 class="font-bold text-white mb-3">📊 Cookie Analitik</h3>
                    <p>Kami menggunakan Google Analytics untuk memahami bagaimana pengunjung menggunakan website kami. Data ini membantu kami meningkatkan layanan.</p>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6">
                    <h3 class="font-bold text-white mb-3">⚙️ Cookie Fungsional</h3>
                    <p>Cookie yang mengingat pilihan Anda (seperti bahasa pilihan) dan memberikan fitur yang lebih personal.</p>
                </div>
                
                <p class="mt-4">Anda dapat menolak cookie kapan saja melalui banner consent di website atau pengaturan browser Anda.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">3. Bagaimana Kami Menggunakan Informasi Anda</h2>
                <p class="mb-4">Informasi yang kami kumpulkan digunakan untuk:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Merespons pertanyaan dan permintaan konsultasi Anda</li>
                    <li>Meningkatkan kualitas layanan dan pengalaman pengguna</li>
                    <li>Mengirimkan informasi relevan tentang layanan perizinan lingkungan kami</li>
                    <li>Menganalisis traffic website dan perilaku pengguna</li>
                    <li>Mematuhi kewajiban hukum dan peraturan yang berlaku</li>
                    <li>Mencegah aktivitas penipuan dan melindungi keamanan website</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">4. Berbagi Informasi</h2>
                <p class="mb-4 text-lg font-semibold text-apple-blue">Kami TIDAK menjual informasi pribadi Anda kepada pihak ketiga.</p>
                <p class="mb-4">Kami hanya berbagi informasi dengan:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Penyedia layanan pihak ketiga:</strong> Seperti Google Analytics untuk analisis website</li>
                    <li><strong>Kewajiban hukum:</strong> Jika diwajibkan oleh hukum atau proses hukum yang sah</li>
                    <li><strong>Dengan persetujuan Anda:</strong> Dengan persetujuan eksplisit dari Anda untuk tujuan tertentu</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">5. Keamanan Data</h2>
                <p class="mb-4">Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang sesuai untuk melindungi data pribadi Anda:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Enkripsi HTTPS untuk semua transmisi data</li>
                    <li>Security headers untuk melindungi dari serangan web</li>
                    <li>Akses terbatas ke data pribadi hanya untuk karyawan yang berwenang</li>
                    <li>Pemantauan keamanan secara berkala</li>
                    <li>Backup data teratur untuk mencegah kehilangan data</li>
                </ul>
                <p class="mt-4">Namun, tidak ada metode transmisi melalui Internet yang 100% aman. Kami berkomitmen untuk melindungi data Anda dengan standar industri terbaik.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">6. Hak Anda</h2>
                <p class="mb-4">Sesuai dengan peraturan perlindungan data, Anda memiliki hak untuk:</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">📋 Akses Data</h3>
                        <p class="text-sm">Mengakses informasi pribadi yang kami miliki tentang Anda</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">✏️ Koreksi Data</h3>
                        <p class="text-sm">Memperbaiki informasi yang tidak akurat atau tidak lengkap</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🗑️ Penghapusan Data</h3>
                        <p class="text-sm">Meminta penghapusan data pribadi Anda</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">⛔ Objeksi</h3>
                        <p class="text-sm">Menolak pemrosesan data Anda untuk tujuan tertentu</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🔄 Portabilitas</h3>
                        <p class="text-sm">Menerima salinan data Anda dalam format terstruktur</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🚫 Penarikan Consent</h3>
                        <p class="text-sm">Menarik persetujuan Anda kapan saja</p>
                    </div>
                </div>
                <p class="mt-4">Untuk menggunakan hak-hak Anda, silakan hubungi kami melalui informasi kontak di bawah.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">7. Google Analytics</h2>
                <p class="mb-4">Website ini menggunakan Google Analytics untuk menganalisis penggunaan website. Google Analytics menggunakan cookie untuk membantu kami menganalisis bagaimana pengguna menggunakan situs ini.</p>
                <p class="mb-4">Informasi yang dihasilkan oleh cookie tentang penggunaan website Anda (termasuk alamat IP Anda) akan dikirim dan disimpan oleh Google di server mereka.</p>
                <p class="mb-4">Google akan menggunakan informasi ini untuk:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Mengevaluasi penggunaan website Anda</li>
                    <li>Menyusun laporan tentang aktivitas website</li>
                    <li>Menyediakan layanan lain yang berkaitan dengan aktivitas website dan penggunaan internet</li>
                </ul>
                <p class="mt-4">Anda dapat menolak penggunaan cookie Google Analytics dengan memilih "Tolak" pada banner cookie consent kami.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">8. Penyimpanan Data</h2>
                <p>Kami menyimpan data pribadi Anda hanya selama diperlukan untuk tujuan yang dijelaskan dalam kebijakan ini, atau sesuai dengan persyaratan hukum. Setelah periode penyimpanan berakhir, data akan dihapus atau dianonimkan dengan aman.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">9. Perubahan Kebijakan</h2>
                <p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu untuk mencerminkan perubahan dalam praktik kami atau untuk alasan operasional, hukum, atau peraturan lainnya. Perubahan akan dipublikasikan di halaman ini dengan tanggal "Terakhir diperbarui" yang baru. Kami mendorong Anda untuk meninjau kebijakan ini secara berkala.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">10. Hubungi Kami</h2>
                <p class="mb-4">Jika Anda memiliki pertanyaan, kekhawatiran, atau permintaan terkait kebijakan privasi ini atau praktik data kami, silakan hubungi kami:</p>
                <div class="bg-gradient-to-br from-apple-blue/10 to-apple-green/10 rounded-xl p-6 border border-white/10">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-bold text-white mb-2">📧 Email</p>
                            <a href="mailto:cs@bizmark.id" class="text-apple-blue hover:underline">cs@bizmark.id</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">💬 WhatsApp</p>
                            <a href="https://wa.me/6281382605030" target="_blank" class="text-apple-blue hover:underline">+62 813-8260-5030</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">🌐 Website</p>
                            <a href="https://bizmark.id" class="text-apple-blue hover:underline">bizmark.id</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">📍 Alamat</p>
                            <p class="text-sm">Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
            </section>
            
            @else
            
            <!-- English Version -->
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">1. Information We Collect</h2>
                <p class="mb-4">We collect the following information when you use our website:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Information you provide directly (name, email, phone number)</li>
                    <li>Website usage data (pages visited, visit time, duration)</li>
                    <li>Technical information (browser type, IP address, device type)</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">2. Use of Cookies</h2>
                <p class="mb-4">We use cookies to enhance your experience:</p>
                
                <div class="bg-white/5 rounded-xl p-6 mb-4">
                    <h3 class="font-bold text-white mb-3">📌 Essential Cookies</h3>
                    <p>Cookies required for basic website functions, such as maintaining your language preferences and storing cookie consent choices.</p>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6 mb-4">
                    <h3 class="font-bold text-white mb-3">📊 Analytics Cookies</h3>
                    <p>We use Google Analytics to understand how visitors use our website. This data helps us improve our services.</p>
                </div>
                
                <div class="bg-white/5 rounded-xl p-6">
                    <h3 class="font-bold text-white mb-3">⚙️ Functional Cookies</h3>
                    <p>Cookies that remember your choices (such as language preference) and provide more personalized features.</p>
                </div>
                
                <p class="mt-4">You can decline cookies at any time through the consent banner on our website or your browser settings.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">3. How We Use Your Information</h2>
                <p class="mb-4">The information we collect is used to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Respond to your inquiries and consultation requests</li>
                    <li>Improve service quality and user experience</li>
                    <li>Send relevant information about our environmental permitting services</li>
                    <li>Analyze website traffic and user behavior</li>
                    <li>Comply with legal obligations and applicable regulations</li>
                    <li>Prevent fraudulent activities and protect website security</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">4. Information Sharing</h2>
                <p class="mb-4 text-lg font-semibold text-apple-blue">We DO NOT sell your personal information to third parties.</p>
                <p class="mb-4">We only share information with:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Third-party service providers:</strong> Such as Google Analytics for website analysis</li>
                    <li><strong>Legal obligations:</strong> When required by law or valid legal process</li>
                    <li><strong>With your consent:</strong> With your explicit consent for specific purposes</li>
                </ul>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">5. Data Security</h2>
                <p class="mb-4">We implement appropriate technical and organizational security measures to protect your personal data:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>HTTPS encryption for all data transmission</li>
                    <li>Security headers to protect against web attacks</li>
                    <li>Limited access to personal data only for authorized employees</li>
                    <li>Regular security monitoring</li>
                    <li>Regular data backups to prevent data loss</li>
                </ul>
                <p class="mt-4">However, no method of transmission over the Internet is 100% secure. We are committed to protecting your data with industry best standards.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">6. Your Rights</h2>
                <p class="mb-4">In accordance with data protection regulations, you have the right to:</p>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">📋 Data Access</h3>
                        <p class="text-sm">Access personal information we hold about you</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">✏️ Data Correction</h3>
                        <p class="text-sm">Correct inaccurate or incomplete information</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🗑️ Data Deletion</h3>
                        <p class="text-sm">Request deletion of your personal data</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">⛔ Objection</h3>
                        <p class="text-sm">Object to processing your data for certain purposes</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🔄 Portability</h3>
                        <p class="text-sm">Receive a copy of your data in structured format</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-4">
                        <h3 class="font-bold text-white mb-2">🚫 Withdraw Consent</h3>
                        <p class="text-sm">Withdraw your consent at any time</p>
                    </div>
                </div>
                <p class="mt-4">To exercise your rights, please contact us using the information below.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">7. Google Analytics</h2>
                <p class="mb-4">This website uses Google Analytics to analyze website usage. Google Analytics uses cookies to help us analyze how users use this site.</p>
                <p class="mb-4">The information generated by the cookie about your use of the website (including your IP address) will be sent and stored by Google on their servers.</p>
                <p class="mb-4">Google will use this information to:</p>
                <ul class="list-disc pl-6 space-y-2">
                    <li>Evaluate your use of the website</li>
                    <li>Compile reports on website activity</li>
                    <li>Provide other services relating to website activity and internet usage</li>
                </ul>
                <p class="mt-4">You can decline the use of Google Analytics cookies by selecting "Decline" on our cookie consent banner.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">8. Data Retention</h2>
                <p>We retain your personal data only as long as necessary for the purposes described in this policy, or as required by legal requirements. After the retention period ends, data will be securely deleted or anonymized.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">9. Policy Changes</h2>
                <p>We may update this privacy policy from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. Changes will be published on this page with a new "Last updated" date. We encourage you to review this policy periodically.</p>
            </section>
            
            <section>
                <h2 class="text-2xl font-bold text-white mb-4">10. Contact Us</h2>
                <p class="mb-4">If you have questions, concerns, or requests related to this privacy policy or our data practices, please contact us:</p>
                <div class="bg-gradient-to-br from-apple-blue/10 to-apple-green/10 rounded-xl p-6 border border-white/10">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="font-bold text-white mb-2">📧 Email</p>
                            <a href="mailto:cs@bizmark.id" class="text-apple-blue hover:underline">cs@bizmark.id</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">💬 WhatsApp</p>
                            <a href="https://wa.me/6281382605030" target="_blank" class="text-apple-blue hover:underline">+62 813-8260-5030</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">🌐 Website</p>
                            <a href="https://bizmark.id" class="text-apple-blue hover:underline">bizmark.id</a>
                        </div>
                        <div>
                            <p class="font-bold text-white mb-2">📍 Address</p>
                            <p class="text-sm">Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
            </section>
            
            @endif
            
        </div>
        
        <!-- Back Button -->
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="200">
            <a href="/" class="btn-secondary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ app()->getLocale() == 'id' ? 'Kembali ke Beranda' : 'Back to Home' }}
            </a>
        </div>
    </div>
</div>
@endsection
