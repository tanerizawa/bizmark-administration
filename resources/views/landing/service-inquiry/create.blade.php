<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analisis Perizinan Gratis - Bizmark.ID</title>
    
    <!-- Tailwind CSS -->
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
    
    <style>
        [x-cloak] { display: none !important; }
        
        .step-indicator {
            transition: all 0.3s ease;
        }
        
        .step-indicator.active {
            background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
        }
        
        .form-fade-enter {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .spinner {
            border: 3px solid rgba(0, 119, 181, 0.1);
            border-top: 3px solid #0077B5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-linkedin-50 to-white min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-br from-linkedin-500 to-linkedin-600 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold text-linkedin-700">Bizmark</span>
                    <span class="text-xl font-bold text-gold-400">.ID</span>
                </div>
            </a>
            <a href="/" class="text-sm text-gray-600 hover:text-linkedin-500 transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tutup
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-8 pb-20" x-data="inquiryForm()">
        <!-- Hero Section -->
        <div class="text-center mb-8">
            <div class="inline-block bg-linkedin-100 text-linkedin-700 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                🤖 Powered by AI - Gratis & Instan
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                Analisis Perizinan <span class="text-linkedin-500">Gratis</span>
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Dapatkan rekomendasi izin usaha yang Anda butuhkan dalam 30 detik dengan AI
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center space-x-4">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-semibold text-white"
                         :class="step >= 1 ? 'active' : 'bg-gray-300'">
                        1
                    </div>
                    <div class="ml-2 hidden sm:block">
                        <div class="text-sm font-semibold" :class="step >= 1 ? 'text-linkedin-600' : 'text-gray-400'">
                            Kontak & Perusahaan
                        </div>
                    </div>
                </div>
                
                <!-- Connector -->
                <div class="w-12 md:w-24 h-1 rounded-full" :class="step >= 2 ? 'bg-linkedin-500' : 'bg-gray-300'"></div>
                
                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-semibold text-white"
                         :class="step >= 2 ? 'active' : 'bg-gray-300'">
                        2
                    </div>
                    <div class="ml-2 hidden sm:block">
                        <div class="text-sm font-semibold" :class="step >= 2 ? 'text-linkedin-600' : 'text-gray-400'">
                            Informasi Usaha
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
            <!-- Error Alert -->
            <div x-show="errorMessage" x-cloak class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-800" x-text="errorMessage"></p>
                    <p x-show="retryAfter" class="text-xs text-red-600 mt-1" x-text="'Coba lagi dalam: ' + retryAfter"></p>
                </div>
            </div>

            <form @submit.prevent="submitForm">
                <!-- Step 1: Contact & Company -->
                <div x-show="step === 1" class="form-fade-enter">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Kontak & Perusahaan</h2>
                    
                    <div class="space-y-5">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" x-model="formData.email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="email@perusahaan.com">
                            <p class="text-xs text-gray-500 mt-1">Hasil analisis akan dikirim ke email ini</p>
                        </div>

                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="formData.company_name" required
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
                                <option value="">Pilih jenis badan usaha</option>
                                <option value="PT">PT (Perseroan Terbatas)</option>
                                <option value="CV">CV (Commanditaire Vennootschap)</option>
                                <option value="Individual">Perorangan/Individual</option>
                                <option value="Koperasi">Koperasi</option>
                                <option value="Yayasan">Yayasan</option>
                                <option value="Belum Terdaftar">Belum Terdaftar</option>
                            </select>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor Telepon/WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" x-model="formData.phone" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="+62 atau 08xx-xxxx-xxxx">
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kontak Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="formData.contact_person" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Nama lengkap Anda">
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jabatan
                            </label>
                            <input type="text" x-model="formData.position"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Direktur, Owner, Manager, dll">
                        </div>
                    </div>

                    <!-- Next Button -->
                    <div class="mt-8 flex justify-end">
                        <button type="button" @click="nextStep()"
                                class="px-8 py-3 bg-gradient-to-r from-linkedin-500 to-linkedin-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition">
                            Lanjut ke Informasi Usaha
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Business Information -->
                <div x-show="step === 2" class="form-fade-enter">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Informasi Usaha</h2>
                        <button type="button" @click="step = 1" class="text-sm text-linkedin-600 hover:text-linkedin-700 font-medium">
                            ← Kembali
                        </button>
                    </div>

                    <div class="space-y-5">
                        <!-- Business Activity -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jenis Usaha/Aktivitas Bisnis <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="formData.business_activity" required rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                      placeholder="Deskripsikan usaha Anda, misal: Produksi makanan ringan, Cafe & Restoran, Jasa Konstruksi, dll"
                                      maxlength="1000"></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <span x-text="formData.business_activity.length"></span>/1000 karakter
                            </p>
                        </div>

                        <!-- Business Scale -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Skala Usaha <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'micro' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-200 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="micro" required class="sr-only">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Mikro</div>
                                        <div class="text-xs text-gray-600">< 10 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'small' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-200 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="small" required class="sr-only">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Kecil</div>
                                        <div class="text-xs text-gray-600">10-50 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'medium' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-200 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="medium" required class="sr-only">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Menengah</div>
                                        <div class="text-xs text-gray-600">50-100 karyawan</div>
                                    </div>
                                </label>
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition"
                                       :class="formData.business_scale === 'large' ? 'border-linkedin-500 bg-linkedin-50' : 'border-gray-200 hover:border-linkedin-300'">
                                    <input type="radio" x-model="formData.business_scale" value="large" required class="sr-only">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">Besar</div>
                                        <div class="text-xs text-gray-600">> 100 karyawan</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Location Province -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Provinsi <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.location_province" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih Provinsi</option>
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
                            <input type="text" x-model="formData.location_city" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                   placeholder="Nama kota atau kabupaten">
                        </div>

                        <!-- Location Category -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Kategori Lokasi <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.location_category" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih kategori</option>
                                <option value="industrial">Kawasan Industri</option>
                                <option value="commercial">Area Komersial</option>
                                <option value="residential">Area Residensial</option>
                                <option value="rural">Pedesaan</option>
                            </select>
                        </div>

                        <!-- Estimated Investment -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Estimasi Investasi <span class="text-red-500">*</span>
                            </label>
                            <select x-model="formData.estimated_investment" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih range investasi</option>
                                <option value="under_100m">< Rp 100 juta</option>
                                <option value="100m_500m">Rp 100 - 500 juta</option>
                                <option value="500m_2b">Rp 500 juta - 2 miliar</option>
                                <option value="over_2b">> Rp 2 miliar</option>
                            </select>
                        </div>

                        <!-- Timeline -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Timeline Target
                            </label>
                            <select x-model="formData.timeline"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition">
                                <option value="">Pilih timeline</option>
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
                                Catatan Tambahan
                            </label>
                            <textarea x-model="formData.additional_notes" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-linkedin-500 focus:border-transparent transition"
                                      placeholder="Informasi tambahan yang ingin Anda sampaikan (opsional)"
                                      maxlength="2000"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex gap-3">
                        <button type="button" @click="step = 1"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                            ← Kembali
                        </button>
                        <button type="submit" :disabled="loading"
                                class="flex-1 px-8 py-3 bg-gradient-to-r from-linkedin-500 to-linkedin-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                            <span x-show="!loading">
                                🤖 Analisis dengan AI
                            </span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <div class="spinner mr-2"></div>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Banner -->
        <div class="mt-8 bg-white rounded-xl p-6 shadow-md border border-linkedin-100">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-linkedin-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Yang Anda Dapatkan:</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="text-gold-400 mr-2">✓</span>
                            <span>Rekomendasi izin usaha yang dibutuhkan</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-gold-400 mr-2">✓</span>
                            <span>Estimasi biaya & timeline proses</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-gold-400 mr-2">✓</span>
                            <span>Analisis faktor risiko & langkah selanjutnya</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-gold-400 mr-2">✓</span>
                            <span>Hasil dikirim ke email Anda</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        function inquiryForm() {
            return {
                step: 1,
                loading: false,
                errorMessage: '',
                retryAfter: '',
                formData: {
                    email: '',
                    company_name: '',
                    company_type: '',
                    phone: '',
                    contact_person: '',
                    position: '',
                    business_activity: '',
                    kbli_code: '',
                    business_scale: '',
                    location_province: '',
                    location_city: '',
                    location_category: '',
                    estimated_investment: '',
                    timeline: '',
                    additional_notes: '',
                    utm_source: new URLSearchParams(window.location.search).get('utm_source') || '',
                    utm_medium: new URLSearchParams(window.location.search).get('utm_medium') || '',
                    utm_campaign: new URLSearchParams(window.location.search).get('utm_campaign') || '',
                },

                nextStep() {
                    // Validate step 1 fields
                    if (!this.formData.email || !this.formData.company_name || 
                        !this.formData.phone || !this.formData.contact_person) {
                        alert('Mohon lengkapi semua field yang wajib diisi (*)');
                        return;
                    }
                    this.step = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                async submitForm() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.retryAfter = '';

                    try {
                        const response = await fetch('{{ route('landing.service-inquiry.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (response.status === 429) {
                                // Rate limit exceeded
                                this.errorMessage = data.message || 'Terlalu banyak permintaan. Silakan coba lagi nanti.';
                                this.retryAfter = data.retry_after_formatted || '';
                            } else if (response.status === 422) {
                                // Validation error
                                const errors = Object.values(data.errors || {}).flat();
                                this.errorMessage = errors.join(', ') || 'Data tidak valid';
                            } else {
                                this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                            }
                            this.loading = false;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }

                        // Success - redirect to polling page
                        if (data.inquiry_number) {
                            window.location.href = `{{ route('landing.service-inquiry.result', '') }}/${data.inquiry_number}`;
                        }

                    } catch (error) {
                        console.error('Submit error:', error);
                        this.errorMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                        this.loading = false;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            }
        }
    </script>
</body>
</html>
