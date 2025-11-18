<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamar {{ $vacancy->title }} - Bizmark.ID</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Animation */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-slide-in {
            animation: slideInRight 0.3s ease-out;
        }
        
        /* Custom Input Focus */
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Progress Step Line */
        .step-line {
            position: relative;
            height: 2px;
            background: #e5e7eb;
            margin: 0 8px;
        }
        
        .step-line.active {
            background: linear-gradient(to right, #3b82f6, #8b5cf6);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ 
    currentStep: 1, 
    totalSteps: 4,
    formData: {
        work_experiences: [],
        skills: []
    },
    addWorkExperience() {
        this.formData.work_experiences.push({
            company: '',
            position: '',
            duration: '',
            responsibilities: ''
        });
    },
    removeWorkExperience(index) {
        this.formData.work_experiences.splice(index, 1);
    },
    addSkill(event) {
        if (event.key === 'Enter' && event.target.value.trim()) {
            event.preventDefault();
            this.formData.skills.push(event.target.value.trim());
            event.target.value = '';
        }
    },
    removeSkill(index) {
        this.formData.skills.splice(index, 1);
    },
    nextStep() {
        if (this.currentStep < this.totalSteps) this.currentStep++;
    },
    prevStep() {
        if (this.currentStep > 1) this.currentStep--;
    }
}">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">B</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Bizmark.ID</span>
                </a>
                
                <a href="{{ route('career.show', $vacancy->slug) }}" class="text-gray-600 hover:text-blue-600 transition flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Application Form -->
    <section class="py-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Job Info Card - Enhanced -->
            <div class="bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white mb-8 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
                
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                Formulir Lamaran
                            </span>
                            <h1 class="text-3xl md:text-4xl font-bold mt-4 mb-2">{{ $vacancy->title }}</h1>
                            <p class="text-blue-100 flex items-center">
                                <i class="fas fa-building mr-2"></i>
                                PT. Cangah Pajaratan Mandiri (Bizmark.ID)
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <i class="fas fa-briefcase text-3xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mt-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <i class="fas fa-map-marker-alt text-sm mb-2"></i>
                            <div class="text-xs opacity-80">Lokasi</div>
                            <div class="font-semibold text-sm">{{ $vacancy->location }}</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <i class="fas fa-briefcase text-sm mb-2"></i>
                            <div class="text-xs opacity-80">Tipe</div>
                            <div class="font-semibold text-sm">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <i class="fas fa-clock text-sm mb-2"></i>
                            <div class="text-xs opacity-80">Deadline</div>
                            <div class="font-semibold text-sm">{{ $vacancy->deadline ? $vacancy->deadline->format('d M Y') : 'Open' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg mb-6 flex items-start shadow-md">
                    <i class="fas fa-exclamation-circle text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Terjadi Kesalahan</p>
                        <p class="text-sm mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-6 py-4 rounded-lg mb-6 flex items-start shadow-md">
                    <i class="fas fa-info-circle text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Informasi</p>
                        <p class="text-sm mt-1">{{ session('info') }}</p>
                    </div>
                </div>
            @endif

            <!-- Progress Steps - Enhanced -->
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <template x-for="(step, idx) in [
                        {num: 1, label: 'Data Pribadi', icon: 'fa-user'},
                        {num: 2, label: 'Pendidikan', icon: 'fa-graduation-cap'},
                        {num: 3, label: 'Pengalaman', icon: 'fa-briefcase'},
                        {num: 4, label: 'Dokumen', icon: 'fa-file-alt'}
                    ]" :key="step.num">
                        <div class="flex items-center flex-1">
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 shadow-md"
                                     :class="currentStep >= step.num ? 'bg-gradient-to-br from-blue-600 to-purple-600 text-white scale-110' : 'bg-gray-200 text-gray-500'">
                                    <i :class="'fas ' + step.icon" class="text-base md:text-lg"></i>
                                </div>
                                <span class="text-xs mt-2 font-semibold text-center hidden md:block"
                                      :class="currentStep >= step.num ? 'text-blue-600' : 'text-gray-400'"
                                      x-text="step.label"></span>
                            </div>
                            <div x-show="idx < 3" class="flex-1 h-1 mx-3 rounded-full transition-all duration-300"
                                 :class="currentStep > step.num ? 'bg-gradient-to-r from-blue-600 to-purple-600' : 'bg-gray-200'"></div>
                        </div>
                    </template>
                </div>
                
                <!-- Mobile Step Label -->
                <div class="md:hidden text-center">
                    <p class="text-sm font-semibold text-gray-600">
                        <span x-text="['Data Pribadi', 'Pendidikan', 'Pengalaman', 'Dokumen'][currentStep - 1]"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Langkah <span x-text="currentStep"></span> dari 4</p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('career.apply.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="job_vacancy_id" value="{{ $vacancy->id }}">
                <input type="hidden" name="work_experience" x-model="JSON.stringify(formData.work_experiences)">
                <input type="hidden" name="skills" x-model="JSON.stringify(formData.skills)">

                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10">
                    <!-- Step 1: Personal Information -->
                    <div x-show="currentStep === 1" x-transition class="animate-slide-in">
                        <div class="flex items-center mb-8">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center text-white mr-4">
                                <i class="fas fa-user text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Data Pribadi</h2>
                                <p class="text-gray-500 text-sm mt-1">Lengkapi informasi dasar Anda</p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user text-blue-600 mr-2"></i>
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       placeholder="Masukkan nama lengkap Anda">
                                @error('full_name')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       placeholder="nama@email.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-blue-600 mr-2"></i>
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="083879602855"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-birthday-cake text-blue-600 mr-2"></i>
                                    Tanggal Lahir
                                </label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-venus-mars text-blue-600 mr-2"></i>
                                    Jenis Kelamin
                                </label>
                                <select name="gender" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Pria" {{ old('gender') == 'Pria' ? 'selected' : '' }}>Pria</option>
                                    <option value="Wanita" {{ old('gender') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                Alamat Lengkap
                            </label>
                            <textarea name="address" rows="3" 
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                      placeholder="Masukkan alamat lengkap Anda">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Step 2: Education -->
                    <div x-show="currentStep === 2" x-transition class="animate-slide-in">
                        <div class="flex items-center mb-8">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center text-white mr-4">
                                <i class="fas fa-graduation-cap text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Pendidikan</h2>
                                <p class="text-gray-500 text-sm mt-1">Riwayat pendidikan terakhir Anda</p>
                            </div>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                                <select name="education_level" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                    <option value="">Pilih Jenjang</option>
                                    <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                                    <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                                    <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                                    <option value="S3" {{ old('education_level') == 'S3' ? 'selected' : '' }}>S3</option>
                                </select>
                                @error('education_level')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan <span class="text-red-500">*</span></label>
                                <input type="text" name="major" value="{{ old('major') }}" required placeholder="Contoh: Teknik Lingkungan"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                @error('major')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Institusi <span class="text-red-500">*</span></label>
                                <input type="text" name="institution" value="{{ old('institution') }}" required placeholder="Nama Universitas/Akademi"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                @error('institution')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Lulus</label>
                                <input type="number" name="graduation_year" value="{{ old('graduation_year') }}" min="1980" max="{{ date('Y') + 5 }}" placeholder="{{ date('Y') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">IPK (Skala 4.0)</label>
                                <input type="number" name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="4" placeholder="3.50"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Experience -->
                    <div x-show="currentStep === 3" x-transition>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Pengalaman & Keahlian</h2>
                        
                        <!-- Experience UKL-UPL -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="has_experience_ukl_upl" value="1" {{ old('has_experience_ukl_upl') ? 'checked' : '' }}
                                       class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-semibold text-gray-700">
                                    Saya memiliki pengalaman menyusun dokumen UKL-UPL/Kajian Teknis
                                </span>
                            </label>
                        </div>

                        <!-- Work Experience (Dynamic) -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-semibold text-gray-700">Riwayat Pekerjaan</label>
                                <button type="button" @click="addWorkExperience()" 
                                        class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center space-x-1">
                                    <i class="fas fa-plus"></i>
                                    <span>Tambah Pengalaman</span>
                                </button>
                            </div>

                            <template x-for="(exp, index) in formData.work_experiences" :key="index">
                                <div class="mb-4 p-4 border border-gray-300 rounded-lg relative">
                                    <button type="button" @click="removeWorkExperience(index)"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <div class="grid md:grid-cols-2 gap-4 mb-3">
                                        <input type="text" x-model="exp.company" placeholder="Nama Perusahaan"
                                               class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm">
                                        <input type="text" x-model="exp.position" placeholder="Posisi"
                                               class="px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm">
                                    </div>
                                    <input type="text" x-model="exp.duration" placeholder="Durasi (contoh: Jan 2020 - Des 2022)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm mb-3">
                                    <textarea x-model="exp.responsibilities" placeholder="Tanggung jawab..." rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                                </div>
                            </template>

                            <p x-show="formData.work_experiences.length === 0" class="text-gray-500 text-sm text-center py-4 bg-gray-50 rounded-lg">
                                Belum ada pengalaman kerja ditambahkan. Klik "Tambah Pengalaman" untuk menambahkan.
                            </p>
                        </div>

                        <!-- Skills (Dynamic Tags) -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Keahlian</label>
                            <input type="text" @keydown="addSkill($event)" placeholder="Ketik keahlian dan tekan Enter (contoh: Ms. Office, AutoCAD)" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm">
                            
                            <div class="flex flex-wrap gap-2 mt-3">
                                <template x-for="(skill, index) in formData.skills" :key="index">
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center space-x-2">
                                        <span x-text="skill"></span>
                                        <button type="button" @click="removeSkill(index)" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Availability -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ekspektasi Gaji (Rp)</label>
                                <input type="number" name="expected_salary" value="{{ old('expected_salary') }}" placeholder="5000000"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Bisa Mulai Kerja</label>
                                <input type="date" name="available_from" value="{{ old('available_from') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Documents -->
                    <div x-show="currentStep === 4" x-transition>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Dokumen & Surat Lamaran</h2>
                        
                        <div class="space-y-6">
                            <!-- CV Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Upload CV <span class="text-red-500">*</span>
                                    <span class="text-gray-500 font-normal">(PDF/DOC, Max 2MB)</span>
                                </label>
                                <input type="file" name="cv" accept=".pdf,.doc,.docx" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('cv')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Portfolio Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Upload Portfolio (Opsional)
                                    <span class="text-gray-500 font-normal">(PDF/DOC/ZIP, Max 5MB)</span>
                                </label>
                                <input type="file" name="portfolio" accept=".pdf,.doc,.docx,.zip"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            </div>

                            <!-- Cover Letter -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Surat Lamaran / Cover Letter</label>
                                <textarea name="cover_letter" rows="6" placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini dan mengapa Anda kandidat yang tepat..." 
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">{{ old('cover_letter') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 2000 karakter</p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex items-center justify-between mt-10 pt-8 border-t-2 border-gray-100">
                        <button type="button" @click="prevStep()" x-show="currentStep > 1"
                                class="group px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50 font-semibold flex items-center space-x-2 transition-all duration-200">
                            <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
                            <span>Sebelumnya</span>
                        </button>

                        <div x-show="currentStep < totalSteps" class="ml-auto">
                            <button type="button" @click="nextStep()"
                                    class="group px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold flex items-center space-x-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <span>Selanjutnya</span>
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                            </button>
                        </div>

                        <button type="submit" x-show="currentStep === totalSteps"
                                class="group px-8 py-4 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white rounded-xl font-bold flex items-center space-x-3 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 ml-auto">
                                <i class="fas fa-paper-plane text-lg group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform duration-200"></i>
                            <span class="text-lg">Kirim Lamaran</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Bizmark.ID - PT. Cangah Pajaratan Mandiri. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
