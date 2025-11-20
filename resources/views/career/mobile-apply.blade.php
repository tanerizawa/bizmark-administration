@extends('mobile-landing.layouts.content')

@push('styles')
<style>
    /* Hide footer on application form */
    footer {
        display: none !important;
    }
    
    /* Remove default magazine-section spacing */
    .magazine-section {
        padding: 0 !important;
    }
    
    /* Animation */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
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
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(0, 119, 181, 0.1);
    }
    
    /* Progress Circle */
    .progress-circle {
        transition: all 0.3s ease;
    }
    
    /* File Input */
    input[type="file"]::file-selector-button {
        background: linear-gradient(135deg, var(--color-primary), #8b5cf6);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        margin-right: 1rem;
    }
    
    /* Remove extra spacing */
    main {
        padding-bottom: 0 !important;
    }
</style>
@endpush

<!-- Alpine.js for form interactivity -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@section('content')

<div x-data="{ 
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
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            window.scrollTo(0, 0);
        }
    },
    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            window.scrollTo(0, 0);
        }
    }
}" class="min-h-screen bg-gray-50">
    <!-- Job Info Banner -->
    <section class="bg-gradient-to-br from-[#0077B5] to-[#005582] text-white pt-2 pb-3">
        <div class="px-4">
            <a href="{{ route('career.show', $vacancy->slug) }}" class="inline-flex items-center text-white/90 text-xs mb-2">
                <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Kembali
            </a>
            
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase mb-1.5">
                Formulir Lamaran
            </span>
            
            <h1 class="text-lg font-bold mb-1 leading-tight">{{ $vacancy->title }}</h1>
            <p class="text-white/90 text-xs flex items-center mb-2">
                <i class="fas fa-building mr-1.5 text-xs"></i>
                Bizmark.ID
            </p>
            
            <div class="grid grid-cols-3 gap-1.5">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-1.5">
                    <i class="fas fa-map-marker-alt text-[10px] mb-0.5 block"></i>
                    <div class="text-[9px] opacity-80">Lokasi</div>
                    <div class="font-semibold text-[11px] mt-0.5">{{ $vacancy->location ?? 'Indonesia' }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-1.5">
                    <i class="fas fa-briefcase text-[10px] mb-0.5 block"></i>
                    <div class="text-[9px] opacity-80">Tipe</div>
                    <div class="font-semibold text-[11px] mt-0.5">{{ ucfirst(str_replace('-', ' ', $vacancy->employment_type ?? 'Full Time')) }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-1.5">
                    <i class="fas fa-clock text-[10px] mb-0.5 block"></i>
                    <div class="text-[9px] opacity-80">Deadline</div>
                    <div class="font-semibold text-[11px] mt-0.5">{{ isset($vacancy->deadline) && $vacancy->deadline ? $vacancy->deadline->format('d M') : 'Open' }}</div>
                </div>
            </div>
        </div>
    </section>

    @if(session('error'))
    <section class="py-2 bg-red-50">
        <div class="px-4">
            <div class="border-l-4 border-red-500 text-red-800 p-2.5 rounded-r-lg flex items-start">
                <i class="fas fa-exclamation-circle text-sm mr-2 mt-0.5 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold text-xs">Terjadi Kesalahan</p>
                    <p class="text-[10px] mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    @if(session('info'))
    <section class="py-2 bg-blue-50">
        <div class="px-4">
            <div class="border-l-4 border-blue-500 text-blue-800 p-2.5 rounded-r-lg flex items-start">
                <i class="fas fa-info-circle text-sm mr-2 mt-0.5 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold text-xs">Informasi</p>
                    <p class="text-[10px] mt-0.5">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Progress Indicator -->
    <section class="py-2.5 bg-white sticky top-16 z-40 shadow-sm">
        <div class="px-4">
            <div class="flex items-center justify-between gap-1">
                <template x-for="step in [
                    {num: 1, icon: 'fa-user'},
                    {num: 2, icon: 'fa-graduation-cap'},
                    {num: 3, icon: 'fa-briefcase'},
                    {num: 4, icon: 'fa-file-alt'}
                ]" :key="step.num">
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 progress-circle"
                             :class="currentStep >= step.num ? 'bg-gradient-to-br from-[#0077B5] to-[#005582] text-white scale-105 shadow-md' : 'bg-gray-200 text-gray-400'">
                            <i :class="'fas ' + step.icon" class="text-[10px]"></i>
                        </div>
                        <div class="w-full h-0.5 rounded-full transition-all duration-300 mt-1"
                             :class="currentStep > step.num ? 'bg-gradient-to-r from-[#0077B5] to-[#005582]' : 'bg-gray-200'"
                             x-show="step.num < 4"></div>
                    </div>
                </template>
            </div>
            
            <div class="text-center mt-1.5">
                <p class="text-xs font-semibold text-gray-700">
                    <span x-text="['Data Pribadi', 'Pendidikan', 'Pengalaman', 'Dokumen'][currentStep - 1]"></span>
                </p>
                <p class="text-[10px] text-gray-500 mt-0.5">Langkah <span x-text="currentStep"></span> dari 4</p>
            </div>
        </div>
    </section>

    <!-- Form -->
    <form action="{{ route('career.apply.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="job_vacancy_id" value="{{ $vacancy->id }}">
        <input type="hidden" name="work_experience" x-model="JSON.stringify(formData.work_experiences)">
        <input type="hidden" name="skills" x-model="JSON.stringify(formData.skills)">

        <!-- Step 1: Personal Information -->
        <section x-show="currentStep === 1" x-transition class="py-3 bg-white animate-slide-in">
            <div class="px-4">
                <div class="flex items-center mb-3 bg-gradient-to-r from-[#0077B5] to-[#005582] text-white p-2.5 rounded-xl -mx-4 px-4">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold">Data Pribadi</h2>
                        <p class="text-[9px] text-white/80">Lengkapi informasi dasar Anda</p>
                    </div>
                </div>
                
                <div class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-user text-[#0077B5] mr-1 text-xs"></i>
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm"
                               placeholder="Masukkan nama lengkap Anda">
                        @error('full_name')
                            <p class="text-red-500 text-[10px] mt-0.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-envelope text-[#0077B5] mr-1 text-xs"></i>
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm"
                               placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-500 text-[10px] mt-0.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-phone text-[#0077B5] mr-1 text-xs"></i>
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="083879602855"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                        @error('phone')
                            <p class="text-red-500 text-[10px] mt-0.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-birthday-cake text-[#0077B5] mr-1 text-xs"></i>
                            Tanggal Lahir
                        </label>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-venus-mars text-[#0077B5] mr-1 text-xs"></i>
                            Jenis Kelamin
                        </label>
                        <select name="gender" class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Pria" {{ old('gender') == 'Pria' ? 'selected' : '' }}>Pria</option>
                            <option value="Wanita" {{ old('gender') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt text-[#0077B5] mr-1 text-xs"></i>
                            Alamat Lengkap
                        </label>
                        <textarea name="address" rows="2" 
                                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm"
                                  placeholder="Masukkan alamat lengkap Anda">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 2: Education -->
        <section x-show="currentStep === 2" x-transition class="py-3 bg-white animate-slide-in">
            <div class="px-4">
                <div class="flex items-center mb-3 bg-gradient-to-r from-[#0077B5] to-[#005582] text-white p-2.5 rounded-xl -mx-4 px-4">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold">Pendidikan</h2>
                        <p class="text-[9px] text-white/80">Riwayat pendidikan terakhir</p>
                    </div>
                </div>
                
                <div class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                        <select name="education_level" required class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                            <option value="">Pilih Jenjang</option>
                            <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('education_level') == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('education_level')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jurusan <span class="text-red-500">*</span></label>
                        <input type="text" name="major" value="{{ old('major') }}" required placeholder="Contoh: Teknik Lingkungan"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                        @error('major')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Institusi <span class="text-red-500">*</span></label>
                        <input type="text" name="institution" value="{{ old('institution') }}" required placeholder="Nama Universitas/Akademi"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                        @error('institution')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun Lulus</label>
                        <input type="number" name="graduation_year" value="{{ old('graduation_year') }}" min="1980" max="{{ date('Y') + 5 }}" placeholder="{{ date('Y') }}"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">IPK (Skala 4.0)</label>
                        <input type="number" name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="4" placeholder="3.50"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 3: Experience -->
        <section x-show="currentStep === 3" x-transition class="py-3 bg-white animate-slide-in">
            <div class="px-4">
                <div class="flex items-center mb-3 bg-gradient-to-r from-[#0077B5] to-[#005582] text-white p-2.5 rounded-xl -mx-4 px-4">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold">Pengalaman & Keahlian</h2>
                        <p class="text-[9px] text-white/80">Riwayat kerja dan skill</p>
                    </div>
                </div>
                
                <!-- Experience UKL-UPL -->
                <div class="mb-2.5 p-2.5 bg-blue-50 border border-blue-200 rounded-lg">
                    <label class="flex items-start space-x-2 cursor-pointer">
                        <input type="checkbox" name="has_experience_ukl_upl" value="1" {{ old('has_experience_ukl_upl') ? 'checked' : '' }}
                               class="w-4 h-4 text-[#0077B5] rounded mt-0.5 flex-shrink-0">
                        <span class="text-xs font-semibold text-gray-700 leading-tight">
                            Saya memiliki pengalaman menyusun dokumen UKL-UPL/Kajian Teknis
                        </span>
                    </label>
                </div>

                <!-- Work Experience -->
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700">Riwayat Pekerjaan</label>
                        <button type="button" @click="addWorkExperience()" 
                                class="text-[#0077B5] hover:text-[#005582] text-[10px] font-semibold flex items-center space-x-1">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah</span>
                        </button>
                    </div>

                    <template x-for="(exp, index) in formData.work_experiences" :key="index">
                        <div class="mb-2 p-2 border-2 border-gray-200 rounded-xl relative bg-white">
                            <button type="button" @click="removeWorkExperience(index)"
                                    class="absolute top-1.5 right-1.5 text-red-500 hover:text-red-700 text-xs">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="space-y-1.5 pr-6">
                                <input type="text" x-model="exp.company" placeholder="Nama Perusahaan"
                                       class="w-full px-2.5 py-1.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0077B5] text-xs">
                                <input type="text" x-model="exp.position" placeholder="Posisi"
                                       class="w-full px-2.5 py-1.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0077B5] text-xs">
                                <input type="text" x-model="exp.duration" placeholder="Durasi (Jan 2020 - Des 2022)"
                                       class="w-full px-2.5 py-1.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0077B5] text-xs">
                                <textarea x-model="exp.responsibilities" placeholder="Tanggung jawab..." rows="2"
                                          class="w-full px-2.5 py-1.5 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-[#0077B5] text-xs"></textarea>
                            </div>
                        </div>
                    </template>

                    <p x-show="formData.work_experiences.length === 0" class="text-gray-500 text-[10px] text-center py-2.5 bg-gray-100 rounded-lg">
                        Belum ada pengalaman kerja. Klik "Tambah" untuk menambahkan.
                    </p>
                </div>

                <!-- Skills -->
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keahlian</label>
                    <input type="text" @keydown="addSkill($event)" placeholder="Ketik keahlian dan tekan Enter" 
                           class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    
                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                        <template x-for="(skill, index) in formData.skills" :key="index">
                            <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full text-[10px] flex items-center space-x-1.5">
                                <span x-text="skill"></span>
                                <button type="button" @click="removeSkill(index)" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times text-[10px]"></i>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- Availability -->
                <div class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Ekspektasi Gaji (Rp)</label>
                        <input type="number" name="expected_salary" value="{{ old('expected_salary') }}" placeholder="5000000"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bisa Mulai Kerja</label>
                        <input type="date" name="available_from" value="{{ old('available_from') }}"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 4: Documents -->
        <section x-show="currentStep === 4" x-transition class="py-3 bg-white animate-slide-in pb-20">
            <div class="px-4">
                <div class="flex items-center mb-3 bg-gradient-to-r from-[#0077B5] to-[#005582] text-white p-2.5 rounded-xl -mx-4 px-4">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center mr-2 flex-shrink-0">
                        <i class="fas fa-file-alt text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold">Dokumen & Surat Lamaran</h2>
                        <p class="text-[9px] text-white/80">Upload CV dan dokumen</p>
                    </div>
                </div>
                
                <div class="space-y-2.5">
                    <!-- CV Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Upload CV <span class="text-red-500">*</span>
                            <span class="text-gray-500 font-normal text-[10px]">(PDF/DOC, Max 2MB)</span>
                        </label>
                        <input type="file" name="cv" accept=".pdf,.doc,.docx" required
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-xs bg-white">
                        @error('cv')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Portfolio Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Upload Portfolio (Opsional)
                            <span class="text-gray-500 font-normal text-[10px]">(PDF/DOC/ZIP, Max 5MB)</span>
                        </label>
                        <input type="file" name="portfolio" accept=".pdf,.doc,.docx,.zip"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-xs bg-white">
                    </div>

                    <!-- Cover Letter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Surat Lamaran / Cover Letter</label>
                        <textarea name="cover_letter" rows="4" placeholder="Ceritakan mengapa Anda tertarik dengan posisi ini dan mengapa Anda kandidat yang tepat..." 
                                  class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#0077B5] text-sm">{{ old('cover_letter') }}</textarea>
                        <p class="text-[10px] text-gray-500 mt-0.5">Maksimal 2000 karakter</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Navigation Buttons -->
        <section class="py-2.5 bg-white fixed bottom-0 left-0 right-0 shadow-lg border-t border-gray-200 z-30">
            <div class="px-4">
                <div class="flex items-center justify-between gap-2">
                    <button type="button" @click="prevStep()" x-show="currentStep > 1"
                            class="flex-1 px-3 py-2 border-2 border-gray-300 rounded-xl text-gray-700 hover:border-[#0077B5] hover:text-[#0077B5] font-semibold flex items-center justify-center space-x-1 transition-all text-xs">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        <span>Kembali</span>
                    </button>

                    <button type="button" @click="nextStep()" x-show="currentStep < totalSteps"
                            class="flex-1 px-3 py-2 bg-gradient-to-r from-[#0077B5] to-[#005582] hover:from-[#005582] hover:to-[#003d5c] text-white rounded-xl font-semibold flex items-center justify-center space-x-1 transition-all shadow-lg text-xs">
                        <span>Lanjut</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </button>

                    <button type="submit" x-show="currentStep === totalSteps"
                            class="flex-1 px-3 py-2.5 bg-gradient-to-r from-green-600 to-[#0077B5] hover:from-green-700 hover:to-[#005582] text-white rounded-xl font-bold flex items-center justify-center space-x-1.5 transition-all shadow-xl text-xs">
                        <i class="fas fa-paper-plane text-[10px]"></i>
                        <span>Kirim</span>
                    </button>
                </div>
            </div>
        </section>
    </form>
</div>
@endsection
