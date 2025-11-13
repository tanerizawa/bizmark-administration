@php
    $faqItems = [
        [
            'id' => 'faq-duration',
            'question' => [
                'id' => 'Berapa lama proses pengurusan perizinan?',
                'en' => 'How long does the permit process take?'
            ],
            'answer' => [
                'id' => 'Waktu pengurusan bervariasi tergantung jenis izin. OSS (NIB) biasanya 1-3 hari kerja, UKL-UPL 14-30 hari kerja, sedangkan AMDAL dapat memakan waktu 3-6 bulan. Kami memberikan estimasi setelah konsultasi awal.',
                'en' => 'Processing time depends on the permit type. OSS (NIB) usually takes 1-3 working days, UKL-UPL 14-30 days, while AMDAL can take 3-6 months. We provide a clear estimate after the initial consultation.'
            ],
        ],
        [
            'id' => 'faq-documents',
            'question' => [
                'id' => 'Apa saja dokumen yang perlu disiapkan?',
                'en' => 'Which documents need to be prepared?'
            ],
            'answer' => [
                'id' => 'Dokumen dasar meliputi KTP/NPWP Direktur, Akta Pendirian, SK Kemenkumham, NPWP Perusahaan, serta dokumen teknis tambahan sesuai jenis perizinan. Tim kami membantu menyiapkan seluruh kebutuhan tersebut.',
                'en' => 'You typically need the director’s ID/tax number, company deed, Ministry of Law decree, company tax number, plus supporting technical documents depending on the permit. Our team helps you assemble everything.'
            ],
        ],
        [
            'id' => 'faq-payment',
            'question' => [
                'id' => 'Bagaimana skema pembayaran layanan Bizmark.ID?',
                'en' => 'How is the Bizmark.ID payment schedule arranged?'
            ],
            'answer' => [
                'id' => 'Kami menerapkan pembayaran bertahap: 50% saat kick-off sebagai DP dan 50% ketika izin terbit. Untuk proyek besar kami dapat menyesuaikan skema sesuai kesepakatan.',
                'en' => 'We use staged payments: 50% at kick-off as a down payment and 50% after permit issuance. For larger mandates we can customise the schedule based on mutual agreement.'
            ],
        ],
        [
            'id' => 'faq-guarantee',
            'question' => [
                'id' => 'Apakah ada jaminan jika perizinan tidak berhasil?',
                'en' => 'Is there a guarantee if the permit process fails?'
            ],
            'answer' => [
                'id' => 'Ada. Jika kegagalan berasal dari sisi kami, biaya akan dikembalikan sebagian sesuai kesepakatan awal. Dengan tingkat keberhasilan di atas 95%, situasi tersebut jarang terjadi.',
                'en' => 'Yes. If a failure is caused by our team we refund the agreed portion of the fee. With a success rate above 95%, this scenario is extremely rare.'
            ],
        ],
        [
            'id' => 'faq-tracking',
            'question' => [
                'id' => 'Bisakah saya memantau progress perizinan?',
                'en' => 'Can I monitor the permit progress?'
            ],
            'answer' => [
                'id' => 'Kami mengirimkan laporan berkala lewat WhatsApp atau email lengkap dengan milestone dan dokumen pendukung sehingga Anda memiliki visibilitas penuh.',
                'en' => 'We send scheduled updates via WhatsApp or email with milestones and supporting documents so you maintain full visibility.'
            ],
        ],
        [
            'id' => 'faq-location',
            'question' => [
                'id' => 'Apakah melayani klien di luar Jawa Barat?',
                'en' => 'Do you handle clients outside West Java?'
            ],
            'answer' => [
                'id' => 'Ya. Walau kantor pusat di Karawang, kami memiliki jaringan konsultan di berbagai provinsi dan dapat mengurus izin di seluruh Indonesia. Konsultasi awal dapat dilakukan secara daring.',
                'en' => 'Yes. Although our headquarters is in Karawang, we have consultants across Indonesia and can handle permits nationwide. The initial consultation can be done online.'
            ],
        ],
        [
            'id' => 'faq-start',
            'question' => [
                'id' => 'Bagaimana cara memulai proses perizinan?',
                'en' => 'How do I start the permit process?'
            ],
            'answer' => [
                'id' => 'Hubungi kami via WhatsApp, telepon, atau email untuk konsultasi gratis. Kami akan melakukan asesmen kebutuhan, mengirim proposal, dan langsung menyiapkan dokumen setelah persetujuan.',
                'en' => 'Contact us via WhatsApp, phone, or email for a complimentary consultation. We will assess your needs, send a proposal, and begin preparing documents once approved.'
            ],
        ],
        [
            'id' => 'faq-renewal',
            'question' => [
                'id' => 'Apakah Bizmark.ID membantu perpanjangan izin?',
                'en' => 'Do you provide permit renewal services?'
            ],
            'answer' => [
                'id' => 'Tentu. Kami memantau masa berlaku izin, mengingatkan sebelum kedaluwarsa, dan mengurus seluruh proses perpanjangan agar operasional tidak terganggu.',
                'en' => 'Absolutely. We track expiry dates, remind you before deadlines, and manage the renewal process end-to-end so operations run without disruption.'
            ],
        ],
    ];
    $locale = app()->getLocale();
@endphp

<section id="faq" class="section bg-gray-50">
    <div class="container max-w-4xl">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">FAQ</span>
            <h2 class="section-title mb-6">
                {{ $locale === 'id' ? 'Pertanyaan yang Sering Diajukan' : 'Frequently Asked Questions' }}
            </h2>
            <p class="section-description max-w-2xl mx-auto">
                {{ $locale === 'id' ? 'Temukan jawaban atas pertanyaan umum seputar layanan perizinan kami.' : 'Find quick answers about our permitting services and engagement model.' }}
            </p>
        </div>

        <div class="space-y-4" id="faqAccordion">
            @foreach($faqItems as $index => $item)
                @php
                    $question = $locale === 'id' ? $item['question']['id'] : $item['question']['en'];
                    $answer = $locale === 'id' ? $item['answer']['id'] : $item['answer']['en'];
                @endphp
                <div class="faq-item" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                    <button type="button"
                            class="faq-trigger"
                            data-faq-target="{{ $item['id'] }}"
                            aria-expanded="false"
                            aria-controls="{{ $item['id'] }}">
                        <span class="faq-question">{{ $question }}</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div id="{{ $item['id'] }}" class="faq-content hidden">
                        <p>{{ $answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12" data-aos="fade-up">
            <p class="text-gray-600 mb-4">
                {{ $locale === 'id' ? 'Masih punya pertanyaan lain?' : 'Need help with something else?' }}
            </p>
            <a href="https://wa.me/6283879602855?text={{ $locale === 'id' ? 'Halo PT Cangah Pajaratan Mandiri, saya punya pertanyaan tentang perizinan' : 'Hello PT Cangah Pajaratan Mandiri, I have a question about permits' }}"
               target="_blank"
               class="btn btn-primary"
               data-cta="faq_whatsapp">
                <i class="fab fa-whatsapp"></i>
                {{ $locale === 'id' ? 'Tanya Sekarang' : 'Ask Now' }}
            </a>
        </div>
    </div>
</section>
