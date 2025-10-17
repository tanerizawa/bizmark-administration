<!-- FAQ Section -->
<section id="faq" class="section bg-gray-50">
    <div class="container max-w-4xl">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">
                {{ app()->getLocale() == 'id' ? 'FAQ' : 'FAQ' }}
            </span>
            <h2 class="section-title mb-6">
                {{ app()->getLocale() == 'id' ? 'Pertanyaan yang Sering Diajukan' : 'Frequently Asked Questions' }}
            </h2>
            <p class="section-description max-w-2xl mx-auto">
                {{ app()->getLocale() == 'id' ? 'Temukan jawaban atas pertanyaan umum seputar layanan perizinan kami' : 'Find answers to common questions about our permit services' }}
            </p>
        </div>

        <!-- FAQ Accordion -->
        <div class="space-y-4" id="faqAccordion">
            <div class="faq-item bg-white rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="0">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-gray-50 transition"
                        data-faq-target="faq-answer-1"
                        aria-expanded="false"
                        aria-controls="faq-answer-1">
                    <span class="text-lg font-semibold pr-4 text-gray-900">
                        {{ app()->getLocale() == 'id' ? 'Berapa lama proses pengurusan perizinan?' : 'How long does the permit process take?' }}
                    </span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-primary"></i>
                </button>
                <div id="faq-answer-1" class="faq-content hidden px-6 pb-5 text-gray-600 border-t border-gray-200">
                    <p class="pt-4">
                        @if(app()->getLocale() == 'id')
                            Waktu pengurusan bervariasi tergantung jenis izin yang diajukan. Untuk OSS (NIB) biasanya 1-3 hari kerja, UKL-UPL sekitar 14-30 hari kerja, sedangkan AMDAL bisa memakan waktu 3-6 bulan. Kami akan memberikan estimasi waktu yang akurat setelah konsultasi awal.
                        @else
                            Processing time varies depending on the type of permit. OSS (NIB) usually takes 1-3 working days, UKL-UPL around 14-30 working days, while AMDAL can take 3-6 months. We will provide an accurate time estimate after initial consultation.
                        @endif
                    </p>
                </div>
            </div>

            <div class="faq-item bg-white rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="100">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-gray-50 transition"
                        data-faq-target="faq-answer-2"
                        aria-expanded="false"
                        aria-controls="faq-answer-2">
                    <span class="text-lg font-semibold pr-4 text-gray-900">
                        {{ app()->getLocale() == 'id' ? 'Apa saja dokumen yang perlu disiapkan?' : 'What documents need to be prepared?' }}
                    </span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-primary"></i>
                </button>
                <div id="faq-answer-2" class="faq-content hidden px-6 pb-5 text-gray-600 border-t border-gray-200">
                    <p class="pt-4">
                        @if(app()->getLocale() == 'id')
                            Dokumen dasar yang umumnya diperlukan meliputi: KTP/NPWP Direktur, Akta Pendirian Perusahaan, SK Kemenkumham, NPWP Perusahaan, dan dokumen pendukung lainnya sesuai jenis perizinan. Tim kami akan membantu Anda mempersiapkan seluruh dokumen yang diperlukan.
                        @else
                            Basic documents generally required include: Director's ID/Tax Number, Company Deed, Ministry of Law Decree, Company Tax Number, and other supporting documents according to permit type. Our team will help you prepare all required documents.
                        @endif
                    </p>
                </div>
            </div>

            <div class="faq-item bg-white rounded-2xl border border-gray-200" data-aos="fade-up" data-aos-delay="200">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-gray-50 transition"
                        data-faq-target="faq-answer-3"
                        aria-expanded="false"
                        aria-controls="faq-answer-3">
                    <span class="text-lg font-semibold pr-4 text-gray-900">
                        {{ app()->getLocale() == 'id' ? 'Bagaimana sistem pembayaran yang diterapkan?' : 'What payment system is applied?' }}
                    </span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-primary"></i>
                </button>
                <div id="faq-answer-3" class="faq-content hidden px-6 pb-5 text-gray-600 border-t border-gray-200">
                    <p class="pt-4">
                        @if(app()->getLocale() == 'id')
                            Kami menerapkan sistem pembayaran bertahap untuk memudahkan klien. Biasanya dibagi menjadi: 50% di awal sebagai down payment, dan 50% setelah izin terbit. Untuk proyek besar, kami bisa menyesuaikan skema pembayaran sesuai kesepakatan.
                        @else
                            We apply a staged payment system to facilitate clients. Usually divided into: 50% upfront as down payment, and 50% after permit issuance. For large projects, we can adjust the payment scheme as agreed.
                        @endif
                    </p>
                </div>
            </div>

            <div class="faq-item glass rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="300">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-white/10 transition"
                        data-faq-target="faq-answer-4"
                        aria-expanded="false"
                        aria-controls="faq-answer-4">
                    <span class="text-lg font-semibold pr-4 text-white/90">Apakah ada jaminan jika perizinan tidak berhasil?</span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-white/80"></i>
                </button>
                <div id="faq-answer-4" class="faq-content hidden px-6 pb-5 text-white/70 border-t border-white/10">
                    <p class="pt-4">Ya, kami memberikan jaminan kepuasan pelanggan. Jika perizinan tidak berhasil karena kesalahan dari pihak kami, kami akan mengembalikan sebagian biaya sesuai kesepakatan awal. Namun, dengan track record kami yang 98% sukses, hal ini sangat jarang terjadi.</p>
                </div>
            </div>

            <div class="faq-item glass rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="400">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-white/10 transition"
                        data-faq-target="faq-answer-5"
                        aria-expanded="false"
                        aria-controls="faq-answer-5">
                    <span class="text-lg font-semibold pr-4 text-white/90">Apakah bisa memantau progress perizinan?</span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-white/80"></i>
                </button>
                <div id="faq-answer-5" class="faq-content hidden px-6 pb-5 text-white/70 border-t border-white/10">
                    <p class="pt-4">Ya, kami memberikan update progress secara berkala melalui WhatsApp atau email. Anda akan mendapatkan informasi lengkap tentang setiap tahap proses perizinan yang sedang berjalan.</p>
                </div>
            </div>

            <div class="faq-item glass rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="500">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-white/10 transition"
                        data-faq-target="faq-answer-6"
                        aria-expanded="false"
                        aria-controls="faq-answer-6">
                    <span class="text-lg font-semibold pr-4 text-white/90">Apakah melayani klien dari luar Jawa Barat?</span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-white/80"></i>
                </button>
                <div id="faq-answer-6" class="faq-content hidden px-6 pb-5 text-white/70 border-t border-white/10">
                    <p class="pt-4">Ya, kami melayani klien dari seluruh Indonesia. Meskipun kantor pusat kami di Karawang, Jawa Barat, kami memiliki jaringan di berbagai kota dan dapat mengurus perizinan di berbagai lokasi. Konsultasi awal bisa dilakukan secara online.</p>
                </div>
            </div>

            <div class="faq-item glass rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="600">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-white/10 transition"
                        data-faq-target="faq-answer-7"
                        aria-expanded="false"
                        aria-controls="faq-answer-7">
                    <span class="text-lg font-semibold pr-4 text-white/90">Bagaimana cara memulai proses perizinan?</span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-white/80"></i>
                </button>
                <div id="faq-answer-7" class="faq-content hidden px-6 pb-5 text-white/70 border-t border-white/10">
                    <p class="pt-4">Sangat mudah! Cukup hubungi kami melalui WhatsApp, telepon, atau email untuk konsultasi gratis. Tim kami akan melakukan analisis kebutuhan, memberikan penawaran, dan setelah deal, kami akan segera memulai proses persiapan dokumen.</p>
                </div>
            </div>

            <div class="faq-item glass rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="700">
                <button type="button"
                        class="faq-trigger w-full text-left px-6 py-5 flex items-center justify-between hover:bg-white/10 transition"
                        data-faq-target="faq-answer-8"
                        aria-expanded="false"
                        aria-controls="faq-answer-8">
                    <span class="text-lg font-semibold pr-4 text-white/90">Apakah ada layanan perpanjangan izin?</span>
                    <i class="fas fa-chevron-down faq-icon transition-transform duration-300 text-white/80"></i>
                </button>
                <div id="faq-answer-8" class="faq-content hidden px-6 pb-5 text-white/70 border-t border-white/10">
                    <p class="pt-4">Ya, kami tidak hanya membantu pengurusan izin baru, tetapi juga perpanjangan izin yang sudah ada. Kami akan mengingatkan Anda sebelum masa berlaku habis dan membantu proses perpanjangan agar tidak terjadi gangguan operasional.</p>
                </div>
            </div>
        </div>

        <!-- CTA Button -->
        <div class="text-center mt-12" data-aos="fade-up">
            <p class="text-gray-600 mb-4">
                {{ app()->getLocale() == 'id' ? 'Masih punya pertanyaan lain?' : 'Have more questions?' }}
            </p>
            <a href="https://wa.me/6281382605030?text={{ app()->getLocale() == 'id' ? 'Halo PT Cangah Pajaratan Mandiri, saya punya pertanyaan tentang perizinan' : 'Hello PT Cangah Pajaratan Mandiri, I have a question about permits' }}" 
               target="_blank"
               class="btn btn-primary">
                <i class="fab fa-whatsapp"></i>
                {{ app()->getLocale() == 'id' ? 'Tanya Sekarang' : 'Ask Now' }}
            </a>
        </div>
    </div>
</section>
