<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="Dapatkan analisis AI gratis untuk kebutuhan perizinan usaha Anda. Cepat, akurat, dan tanpa biaya.">
    <title>Analisis Perizinan | Bizmark.ID</title>
    
    <!-- LinkedIn Blue Color Palette -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        linkedin: {
                            50: '#e7f3f8',
                            100: '#cce7f1',
                            200: '#99cfe3',
                            500: '#0077B5',
                            600: '#005582',
                            700: '#004161',
                            900: '#001820',
                        },
                        gold: {
                            400: '#F2CD49',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh2JtHeS4ZL8SaJIs54IVqVdPXgeSrxlL1YgM7GkL4Z3+5eZ5Pg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Smooth transitions */
        .transition-all {
            transition: all 0.3s ease;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Progress bar animation */
        @keyframes progress {
            from { width: 0%; }
        }
        
        .animate-progress {
            animation: progress 0.5s ease forwards;
        }
        
        /* Loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-linkedin-50 via-white to-linkedin-100 min-h-screen">
    @php
        $contact = config('landing_metrics.contact');
        $experience = config('landing_metrics.experience');
        $benefits = [
            'AI biz-process memetakan izin prioritas hanya dalam 30 detik',
            'Tim konsultan senior memvalidasi hasil sebelum dikirim',
            'Termasuk rekomendasi timeline, instansi, dan estimasi biaya'
        ];
        $documentTips = [
            'OSS RBA (NIB, NIB Perizinan Berusaha)',
            'UKL-UPL / AMDAL & perizinan lingkungan',
            'PBG, SLF, TDG, penetapan KBLI, dan izin sektoral lain'
        ];
    @endphp
    
    <div x-data="inquiryForm()" x-init="init()" x-cloak class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="max-w-4xl mx-auto mb-10 animate-fadeIn text-center">
            <div class="text-center">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-4">
                    <svg class="w-10 h-10 text-linkedin-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span class="text-2xl font-bold text-linkedin-700">
                        Bizmark<span class="text-gold-400">.ID</span>
                    </span>
                </a>
                
                <!-- Title -->
                <h1 class="text-3xl sm:text-4xl font-bold text-linkedin-900 mb-3">
                    Analisis Perizinan
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Dapatkan rekomendasi izin usaha yang Anda butuhkan dari database kami dalam <strong>30 detik</strong>. 
                    Gratis, cepat, dan handal!
                </p>
                
                <!-- Progress Bar -->
                <div class="mt-6 max-w-md mx-auto">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Langkah <span x-text="step"></span> dari 2</span>
                        <span class="text-sm font-medium text-linkedin-500" x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-linkedin-500 to-linkedin-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto grid lg:grid-cols-3 gap-6 items-start">
            <!-- Insight Sidebar -->
            <aside class="space-y-6">
                <div class="bg-white/90 backdrop-blur-md border border-linkedin-100 rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-linkedin-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-check-circle text-linkedin-500"></i>
                        Kenapa Analisis Ini?
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        @foreach($benefits as $benefit)
                            <li class="flex items-start gap-3">
                                <span class="w-2.5 h-2.5 mt-2 rounded-full bg-linkedin-500"></span>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 rounded-xl bg-linkedin-50 border border-linkedin-100 px-4 py-3 text-sm text-linkedin-700">
                        <strong>{{ $experience['years'] ?? '10+' }} tahun</strong> pengalaman lintas industri • <strong>{{ $contact['hours'] ?? 'Portal 24/7' }}</strong>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-linkedin-600 to-linkedin-700 text-white rounded-2xl p-6 shadow-xl">
                    <p class="text-sm uppercase tracking-[0.2em] text-white/70 mb-2">Hubungi Konsultan</p>
                    <h3 class="text-2xl font-semibold mb-3">Butuh Jawaban Cepat?</h3>
                    <p class="text-white/80 text-sm mb-5">Tim kami siap merespons dalam 5 menit melalui WhatsApp. Anda juga dapat mengirim dokumen awal untuk review.</p>
                    <div class="space-y-3">
                        <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-3 bg-white text-linkedin-700 font-semibold rounded-xl py-3 shadow-lg hover:-translate-y-0.5 transition">
                            <i class="fab fa-whatsapp text-xl text-green-500"></i> Chat WhatsApp
                        </a>
                        <a href="tel:{{ $contact['phone'] ?? '+6283879602855' }}" class="flex items-center justify-center gap-3 bg-white/10 border border-white/30 text-white font-semibold rounded-xl py-3 hover:bg-white/15 transition">
                            <i class="fas fa-phone-alt"></i> {{ $contact['phone_display'] ?? 'Hubungi Kami' }}
                        </a>
                    </div>
                    <p class="text-xs text-white/70 mt-4">{{ $contact['hours'] ?? 'Portal 24/7' }} • Email: {{ $contact['email'] ?? 'info@bizmark.id' }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-lg">
                    <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-500 mb-3">Siapkan Informasi</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        @foreach($documentTips as $tip)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-folder-open text-linkedin-500 mt-0.5"></i>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 text-xs text-gray-500">
                        Data Anda disimpan terenkripsi dan hanya digunakan untuk asesmen awal.
                    </div>
                </div>
            </aside>
            
            <!-- Form Container -->
            <div class="lg:col-span-2 space-y-6">
                <form @submit.prevent="submitForm" @input.debounce.500ms="saveDraft()" class="bg-white rounded-2xl shadow-xl border border-linkedin-100 overflow-hidden">
                
                <!-- Step 1: Contact & Company Info -->
                <div x-show="step === 1" x-transition class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-linkedin-900 mb-2 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-linkedin-500 text-white text-sm font-bold">1</span>
                            Informasi Kontak & Perusahaan
                        </h2>
                        <p class="text-gray-600">Kami butuh info ini untuk mengirimkan hasil analisis ke Anda.</p>
                    </div>
                    <div x-show="draftLoaded || lastSavedAt" class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex flex-col gap-2">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <div>
                                <p>Data sebelumnya dipulihkan otomatis. Anda dapat melanjutkan tanpa mengulang dari awal.</p>
                                <p x-show="lastSavedAt" class="text-xs text-amber-700" x-text="'Terakhir tersimpan: ' + savedAtLabel"></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" @click="clearDraft" class="text-xs font-semibold text-amber-800 underline hover:text-amber-900">Reset Form</button>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   x-model="formData.email" 
                                   @blur="checkRateLimit"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="email@perusahaan.com">
                            <p x-show="rateLimitWarning" x-text="rateLimitWarning" class="mt-1 text-sm text-amber-600" role="status" aria-live="polite"></p>
                        </div>

                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="formData.company_name" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="PT/CV/Nama Perusahaan Anda">
                        </div>

                        <!-- Company Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jenis Badan Usaha
                            </label>
                            <select x-model="formData.company_type" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih jenis badan usaha...</option>
                                <option value="PT">PT (Perseroan Terbatas)</option>
                                <option value="CV">CV (Commanditaire Vennootschap)</option>
                                <option value="Individual">Perorangan</option>
                                <option value="Koperasi">Koperasi</option>
                                <option value="Yayasan">Yayasan</option>
                                <option value="Belum Terdaftar">Belum Terdaftar</option>
                            </select>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Telepon (WhatsApp) <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   x-model="formData.phone" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="08xx-xxxx-xxxx atau +62xxx">
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kontak Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="formData.contact_person" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Nama lengkap Anda">
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jabatan
                            </label>
                            <input type="text" 
                                   x-model="formData.position" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Direktur, Owner, Manager, dll">
                        </div>
                    </div>

                    <!-- Next Button -->
                    <div class="mt-8 flex gap-4">
                        <a href="{{ route('landing') }}" 
                           class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-center">
                            Kembali
                        </a>
                        <button type="button" 
                                @click="nextStep"
                                :disabled="!isStep1Valid"
                                :class="isStep1Valid ? 'bg-gradient-to-r from-linkedin-500 to-linkedin-600 hover:from-linkedin-600 hover:to-linkedin-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="flex-1 px-6 py-3 text-white font-semibold rounded-lg transition shadow-lg">
                            Lanjut →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Business Info -->
                <div x-show="step === 2" x-transition class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-linkedin-900 mb-2 flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-linkedin-500 text-white text-sm font-bold">2</span>
                            Informasi Usaha & Proyek
                        </h2>
                        <p class="text-gray-600">Ceritakan tentang usaha Anda agar AI dapat memberikan rekomendasi terbaik.</p>
                    </div>

                    <div class="space-y-5">
                        <!-- Business Activity -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jenis Usaha / Aktivitas Bisnis <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="formData.business_activity" 
                                      required
                                      rows="3"
                                      maxlength="1000"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition resize-none"
                                      placeholder="Contoh: Produksi makanan ringan, Cafe & Restoran, Jasa Konstruksi, dll"></textarea>
                            <p class="mt-1 text-xs text-gray-500" x-text="formData.business_activity.length + '/1000 karakter'"></p>
                        </div>

                        <!-- Business Scale -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Skala Usaha <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'micro' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="micro" class="sr-only" required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Mikro</div>
                                        <div class="text-sm text-gray-600">< 10 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'small' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="small" class="sr-only" required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Kecil</div>
                                        <div class="text-sm text-gray-600">10-50 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'medium' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="medium" class="sr-only" required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Menengah</div>
                                        <div class="text-sm text-gray-600">50-100 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'large' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="large" class="sr-only" required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Besar</div>
                                        <div class="text-sm text-gray-600">> 100 karyawan</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Location Province -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Provinsi Lokasi Usaha <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.location_province" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih provinsi...</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}">{{ $province }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Location City -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kota/Kabupaten <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   x-model="formData.location_city" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Nama kota/kabupaten">
                        </div>

                        <!-- Location Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kategori Lokasi <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.location_category === 'industrial' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.location_category" value="industrial" class="sr-only" required>
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900">Kawasan Industri</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.location_category === 'commercial' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.location_category" value="commercial" class="sr-only" required>
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900">Area Komersial</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.location_category === 'residential' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.location_category" value="residential" class="sr-only" required>
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900">Area Residensial</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.location_category === 'rural' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-300 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.location_category" value="rural" class="sr-only" required>
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900">Pedesaan</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Estimated Investment -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Investasi <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.estimated_investment" 
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih range investasi...</option>
                                <option value="under_100m">< Rp 100 juta</option>
                                <option value="100m_500m">Rp 100 - 500 juta</option>
                                <option value="500m_2b">Rp 500 juta - 2 miliar</option>
                                <option value="over_2b">> Rp 2 miliar</option>
                            </select>
                        </div>

                        <!-- Timeline -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Target Timeline
                            </label>
                            <select x-model="formData.timeline" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih timeline...</option>
                                <option value="urgent">Urgent (< 1 bulan)</option>
                                <option value="1-3_months">1-3 bulan</option>
                                <option value="3-6_months">3-6 bulan</option>
                                <option value="6plus_months">> 6 bulan</option>
                                <option value="not_sure">Belum pasti</option>
                            </select>
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea x-model="formData.additional_notes" 
                                      rows="3"
                                      maxlength="2000"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition resize-none"
                                      placeholder="Informasi tambahan yang perlu kami ketahui..."></textarea>
                            <p class="mt-1 text-xs text-gray-500" x-text="formData.additional_notes.length + '/2000 karakter'"></p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex gap-4">
                        <button type="button" 
                                @click="prevStep"
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                            ← Kembali
                        </button>
                        <button type="submit" 
                                :disabled="isSubmitting || !isStep2Valid"
                                :class="(isSubmitting || !isStep2Valid) ? 'bg-gray-300 cursor-not-allowed' : 'bg-gradient-to-r from-linkedin-500 to-linkedin-600 hover:from-linkedin-600 hover:to-linkedin-700'"
                                class="flex-1 px-6 py-3 text-white font-semibold rounded-lg transition shadow-lg flex items-center justify-center gap-2">
                            <span x-show="!isSubmitting">🤖 Analisis dengan AI</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                    <p x-show="errorMessage" x-text="errorMessage" class="mt-4 text-sm text-red-600 text-center" role="alert" aria-live="assertive"></p>
                </div>

            </form>

            <!-- Trust Indicators -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-3">✨ Gratis · 🔒 Data Aman · ⚡ Hasil dalam 30 detik</p>
                <p class="text-xs text-gray-500">
                    Dengan mengirim form ini, Anda setuju dengan 
                    <a href="{{ route('privacy.policy') }}" class="text-linkedin-500 hover:underline" target="_blank">Kebijakan Privasi</a> dan 
                    <a href="{{ route('terms.conditions') }}" class="text-linkedin-500 hover:underline" target="_blank">Syarat & Ketentuan</a> kami.
                </p>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isSubmitting" 
         x-cloak
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="animate-spin text-linkedin-500" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-linkedin-900 mb-2">AI Sedang Menganalisis...</h3>
            <p class="text-gray-600 mb-4">Memproses data Anda dan menyiapkan rekomendasi perizinan.</p>
            <div class="flex items-center justify-center gap-1">
                <div class="w-2 h-2 bg-linkedin-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-linkedin-500 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-linkedin-500 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
            </div>
            <p class="text-sm text-gray-500 mt-4">Estimasi: 10-30 detik</p>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        function inquiryForm() {
            return {
                step: 1,
                isSubmitting: false,
                rateLimitWarning: '',
                draftLoaded: false,
                lastSavedAt: null,
                errorMessage: '',
                storageKey: 'bizmark_inquiry_draft',
                formData: {
                    // Step 1
                    email: '',
                    company_name: '',
                    company_type: '',
                    phone: '',
                    contact_person: '',
                    position: '',
                    // Step 2
                    business_activity: '',
                    business_scale: '',
                    location_province: '',
                    location_city: '',
                    location_category: '',
                    estimated_investment: '',
                    timeline: '',
                    additional_notes: '',
                    // UTM (auto-captured)
                    utm_source: urlParams.get('utm_source') || '',
                    utm_medium: urlParams.get('utm_medium') || '',
                    utm_campaign: urlParams.get('utm_campaign') || '',
                },
                
                init() {
                    this.loadDraft();
                },
                
                get progress() {
                    return this.step === 1 ? 50 : 100;
                },

                get savedAtLabel() {
                    if (!this.lastSavedAt) return '';
                    try {
                        const date = new Date(this.lastSavedAt);
                        return date.toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' });
                    } catch (e) {
                        return '';
                    }
                },
                
                get isStep1Valid() {
                    return this.formData.email && 
                           this.formData.company_name && 
                           this.formData.phone && 
                           this.formData.contact_person;
                },
                
                get isStep2Valid() {
                    return this.formData.business_activity && 
                           this.formData.business_scale && 
                           this.formData.location_province && 
                           this.formData.location_city && 
                           this.formData.location_category && 
                           this.formData.estimated_investment;
                },
                
                nextStep() {
                    if (this.isStep1Valid) {
                        this.step = 2;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                
                prevStep() {
                    this.step = 1;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                getDefaultFormData() {
                    return {
                        email: '',
                        company_name: '',
                        company_type: '',
                        phone: '',
                        contact_person: '',
                        position: '',
                        business_activity: '',
                        business_scale: '',
                        location_province: '',
                        location_city: '',
                        location_category: '',
                        estimated_investment: '',
                        timeline: '',
                        additional_notes: '',
                        utm_source: urlParams.get('utm_source') || '',
                        utm_medium: urlParams.get('utm_medium') || '',
                        utm_campaign: urlParams.get('utm_campaign') || '',
                    };
                },

                saveDraft() {
                    const payload = { ...this.formData, _savedAt: new Date().toISOString() };
                    localStorage.setItem(this.storageKey, JSON.stringify(payload));
                    this.lastSavedAt = payload._savedAt;
                },

                loadDraft() {
                    const saved = localStorage.getItem(this.storageKey);
                    if (!saved) return;
                    try {
                        const parsed = JSON.parse(saved);
                        const { _savedAt, ...data } = parsed;
                        this.formData = { ...this.formData, ...data };
                        this.lastSavedAt = _savedAt || null;
                        this.draftLoaded = true;
                    } catch (error) {
                        console.warn('Failed to load draft', error);
                    }
                },

                clearDraft(resetFields = true) {
                    localStorage.removeItem(this.storageKey);
                    this.draftLoaded = false;
                    this.lastSavedAt = null;
                    this.errorMessage = '';
                    this.rateLimitWarning = '';
                    if (resetFields) {
                        this.formData = this.getDefaultFormData();
                        this.step = 1;
                    }
                },
                
                async checkRateLimit() {
                    if (!this.formData.email) return;
                    
                    try {
                        const response = await fetch('{{ route("landing.service-inquiry.check-rate-limit") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email: this.formData.email })
                        });
                        
                        const data = await response.json();
                        
                        if (!data.allowed) {
                            this.rateLimitWarning = '⚠️ ' + data.limit_info.message;
                        } else if (data.stats.email_remaining <= 2) {
                            this.rateLimitWarning = `ℹ️ Anda memiliki ${data.stats.email_remaining} analisis gratis tersisa hari ini.`;
                        } else {
                            this.rateLimitWarning = '';
                        }
                    } catch (error) {
                        console.error('Rate limit check failed:', error);
                    }
                },
                
                async submitForm() {
                    if (!this.isStep2Valid) return;
                    
                    this.isSubmitting = true;
                    this.errorMessage = '';
                    
                    try {
                        const response = await fetch('{{ route("landing.service-inquiry.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.formData)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.clearDraft(false);
                            // Poll for result
                            this.pollResult(data.inquiry_number);
                        } else if (data.error === 'rate_limit') {
                            this.errorMessage = data.message || 'Batas analisis gratis tercapai untuk hari ini.';
                            this.isSubmitting = false;
                        } else {
                            this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                            this.isSubmitting = false;
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        this.errorMessage = 'Gagal mengirim data. Periksa koneksi internet Anda.';
                        this.isSubmitting = false;
                    }
                },
                
                async pollResult(inquiryNumber, attempts = 0) {
                    const maxAttempts = 20; // 20 x 2 seconds = 40 seconds max
                    
                    if (attempts >= maxAttempts) {
                        this.errorMessage = 'Analisis membutuhkan waktu lebih lama. Hasil lengkap akan dikirim ke email Anda.';
                        this.isSubmitting = false;
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/konsultasi-gratis/api/status/${inquiryNumber}`);
                        const data = await response.json();
                        
                        if (data.status === 'completed') {
                            // Redirect to result page
                            window.location.href = `/konsultasi-gratis/hasil/${inquiryNumber}`;
                        } else {
                            // Retry after 2 seconds
                            setTimeout(() => this.pollResult(inquiryNumber, attempts + 1), 2000);
                        }
                    } catch (error) {
                        console.error('Poll error:', error);
                        setTimeout(() => this.pollResult(inquiryNumber, attempts + 1), 2000);
                    }
                }
            }
        }
    </script>
</body>
</html>
