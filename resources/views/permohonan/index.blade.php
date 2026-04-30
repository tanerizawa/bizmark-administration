@extends('landing.layout')

@section('meta_title', 'Permohonan Penghitungan Biaya Jasa - Bizmark.ID')
@section('meta_description', 'Ajukan permohonan penghitungan biaya jasa untuk layanan perizinan, legalitas, dan konsultasi bisnis. Layanan untuk perorangan dan badan usaha.')

@section('content')
<div class="permohonan-page" x-data="permohonanForm()" x-cloak>
    
    <!-- Hero Section - Magazine Style -->
    <section class="gradient-hero relative overflow-hidden" style="padding-top: 6rem;">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;40&quot; height=&quot;40&quot; viewBox=&quot;0 0 40 40&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-rule=&quot;evenodd&quot;%3E%3Ccircle cx=&quot;20&quot; cy=&quot;20&quot; r=&quot;2&quot;/%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="container relative z-10 py-12 md:py-16">
            <div class="max-w-3xl mx-auto text-center text-white">
                <!-- Badge -->
                <span class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-xs font-semibold tracking-wider uppercase rounded-full" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.2);">
                    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                    Permohonan Resmi
                </span>
                
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-4" style="line-height: 1.1; letter-spacing: -0.03em;">
                    Surat Permohonan<br>
                    <span style="color: var(--color-secondary);">Penghitungan Biaya Jasa</span>
                </h1>
                <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto" style="line-height: 1.7;">
                    Ajukan permohonan resmi untuk mendapatkan penawaran biaya layanan yang sesuai dengan kebutuhan Anda
                </p>
            </div>
        </div>
        
        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full" style="margin-bottom: -1px;">
                <path d="M0 60L48 55C96 50 192 40 288 35C384 30 480 30 576 33.3C672 36.7 768 43.3 864 45C960 46.7 1056 43.3 1152 40C1248 36.7 1344 33.3 1392 31.7L1440 30V60H1392C1344 60 1248 60 1152 60C1056 60 960 60 864 60C768 60 672 60 576 60C480 60 384 60 288 60C192 60 96 60 48 60H0Z" fill="var(--surface-warm)"/>
            </svg>
        </div>
    </section>
    
    <!-- Main Form Section -->
    <section class="section" style="background: var(--surface-warm);">
        <div class="container">
            <div class="max-w-4xl mx-auto">
                
                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between relative">
                        <!-- Progress Line Background -->
                        <div class="absolute top-5 left-0 right-0 h-0.5" style="background: var(--border-light);"></div>
                        <!-- Progress Line Active -->
                        <div class="absolute top-5 left-0 h-0.5 transition-all duration-500" 
                             :style="{ width: (currentStep / (steps.length - 1)) * 100 + '%', background: 'var(--color-primary)' }"></div>
                        
                        <template x-for="(step, index) in steps" :key="index">
                            <div class="flex flex-col items-center relative z-10" style="flex: 1;">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 border-2"
                                     :class="{
                                         'bg-[var(--color-primary)] border-[var(--color-primary)] text-white shadow-lg': currentStep >= index,
                                         'bg-[#e8eef7] border-[#cbd5e1] text-[#475569]': currentStep < index
                                     }">
                                    <i x-show="currentStep > index" class="fas fa-check text-xs"></i>
                                    <span x-show="currentStep <= index" x-text="index + 1"></span>
                                </div>
                                <span class="mt-2 text-xs font-semibold text-center hidden sm:block"
                                      :class="currentStep >= index ? 'text-[var(--text-primary)]' : 'text-[var(--text-tertiary)]'"
                                      x-text="step.title"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden" style="border: 1px solid var(--border-light);">
                    
                    <form @submit.prevent="submitForm" enctype="multipart/form-data">
                        
                        <!-- Step 1: Jenis Pemohon -->
                        <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="p-6 sm:p-10">
                                <div class="text-center mb-10">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);">
                                        <i class="fas fa-user-tag text-2xl text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Jenis Pemohon</h2>
                                    <p style="color: var(--text-secondary);">Pilih apakah Anda mengajukan sebagai perorangan atau badan usaha</p>
                                </div>
                                
                                <div class="grid sm:grid-cols-2 gap-5 max-w-2xl mx-auto">
                                    <!-- Perorangan Card -->
                                    <div class="type-selector-card group cursor-pointer rounded-xl p-6 transition-all duration-300 border-2"
                                         @mouseenter="perorangan_hover = true"
                                         @mouseleave="perorangan_hover = false"
                                         :style="{
                                             'border-color': formData.applicant_type === 'perorangan' ? 'var(--color-primary)' : perorangan_hover ? 'var(--color-primary)' : 'var(--border-light)',
                                             'background-color': formData.applicant_type === 'perorangan' ? 'var(--surface-cool)' : perorangan_hover ? 'var(--surface-cool)' : 'transparent',
                                             'box-shadow': formData.applicant_type === 'perorangan' ? '0 10px 15px -3px rgba(15,23,42,0.1)' : perorangan_hover ? '0 4px 6px -1px rgba(15,23,42,0.1)' : 'none'
                                         }"
                                         @click="formData.applicant_type = 'perorangan'">
                                        <div class="flex items-start gap-4">
                                            <div class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors shadow-sm"
                                                 :style="{
                                                     'background-color': formData.applicant_type === 'perorangan' ? 'var(--color-primary)' : 'var(--surface-cool)',
                                                     'color': formData.applicant_type === 'perorangan' ? 'white' : 'var(--text-primary)'
                                                 }">
                                                <i class="fas fa-user text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold" style="color: var(--text-primary);">Perorangan</h3>
                                                <p class="text-sm mt-1" style="color: var(--text-secondary);">Untuk individu atau usaha tidak berbadan hukum</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t" style="border-color: var(--border-light);">
                                            <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                                                <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Form lebih ringkas</li>
                                                <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Proses lebih cepat</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <!-- Badan Usaha Card -->
                                    <div class="type-selector-card group cursor-pointer rounded-xl p-6 transition-all duration-300 border-2"
                                         @mouseenter="badan_hover = true"
                                         @mouseleave="badan_hover = false"
                                         :style="{
                                             'border-color': formData.applicant_type === 'badan' ? 'var(--color-primary)' : badan_hover ? 'var(--color-primary)' : 'var(--border-light)',
                                             'background-color': formData.applicant_type === 'badan' ? 'var(--surface-cool)' : badan_hover ? 'var(--surface-warm)' : 'transparent',
                                             'box-shadow': formData.applicant_type === 'badan' ? '0 10px 15px -3px rgba(15,23,42,0.1)' : badan_hover ? '0 4px 6px -1px rgba(15,23,42,0.1)' : 'none'
                                         }"
                                         @click="formData.applicant_type = 'badan'">
                                        <div class="flex items-start gap-4">
                                            <div class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors shadow-sm"
                                                 :style="{
                                                     'background-color': formData.applicant_type === 'badan' ? 'var(--color-primary)' : 'var(--surface-warm)',
                                                     'color': formData.applicant_type === 'badan' ? 'white' : 'var(--text-primary)'
                                                 }">
                                                <i class="fas fa-building text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold" style="color: var(--text-primary);">Badan Usaha</h3>
                                                <p class="text-sm mt-1" style="color: var(--text-secondary);">PT, CV, UD, Yayasan, Koperasi, atau badan hukum lainnya</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t" style="border-color: var(--border-light);">
                                            <ul class="space-y-2 text-sm" style="color: var(--text-secondary);">
                                                <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Data perusahaan lengkap</li>
                                                <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Penawaran lebih detail</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <p x-show="errors.applicant_type" class="text-center mt-4 text-sm text-red-600 font-medium flex items-center justify-center gap-1"><i class="fas fa-exclamation-circle"></i><span x-text="errors.applicant_type"></span></p>
                            </div>
                        </div>
                        
                        <!-- Step 2: Data Pemohon -->
                        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="p-6 sm:p-10">
                                <div class="text-center mb-10">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-success) 0%, #059669 100%);">
                                        <i class="fas fa-id-card text-2xl text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Data Pemohon</h2>
                                    <p style="color: var(--text-secondary);" x-text="formData.applicant_type === 'badan' ? 'Isi data perusahaan dan kontak person' : 'Isi data diri Anda dengan lengkap'"></p>
                                </div>
                                
                                <div class="max-w-2xl mx-auto space-y-6">
                                    <!-- Badan Usaha Fields -->
                                    <template x-if="formData.applicant_type === 'badan'">
                                        <div class="space-y-6">
                                            <div class="p-5 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                                <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">
                                                    <i class="fas fa-building mr-2"></i>Data Perusahaan
                                                </h3>
                                                
                                                <div class="space-y-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                                            Nama Perusahaan <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" x-model="formData.company_name" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc]"
                                                               :class="errors.company_name ? 'border-red-300 focus:border-red-500 focus:bg-white focus:ring-1 focus:ring-red-200' : 'border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10'\"
                                                               placeholder="PT. Nama Perusahaan">
                                                        <p x-show="errors.company_name" class="mt-1 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle mt-0.5"></i><span x-text="errors.company_name"></span></p>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                                            Jenis Badan Usaha <span class="text-red-500">*</span>
                                                        </label>
                                                        <select x-model="formData.business_type" 
                                                                class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none appearance-none bg-white"
                                                                :class="errors.business_type ? 'border-red-300 focus:border-red-500 focus:bg-white focus:ring-1 focus:ring-red-200' : 'border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10'\"
                                                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 20px; padding-right: 40px;">
                                                            <option value="">Pilih Jenis Badan</option>
                                                            <option value="pt">PT (Perseroan Terbatas)</option>
                                                            <option value="cv">CV (Commanditaire Vennootschap)</option>
                                                            <option value="ud">UD (Usaha Dagang)</option>
                                                            <option value="yayasan">Yayasan</option>
                                                            <option value="koperasi">Koperasi</option>
                                                            <option value="lainnya">Lainnya</option>
                                                        </select>
                                                        <p x-show="errors.business_type" class="mt-1 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle mt-0.5"></i><span x-text="errors.business_type"></span></p>
                                                    </div>
                                                    
                                                    <div class="grid sm:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">NPWP</label>
                                                            <input type="text" x-model="formData.npwp" 
                                                                   class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                                   placeholder="00.000.000.0-000.000">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">NIB</label>
                                                            <input type="text" x-model="formData.nib" 
                                                                   class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                                   placeholder="Nomor Induk Berusaha">
                                                        </div>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Bidang Usaha</label>
                                                        <input type="text" x-model="formData.business_sector" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="Contoh: Konstruksi, Perdagangan, Manufaktur">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="p-5 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                                <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">
                                                    <i class="fas fa-user-tie mr-2"></i>Kontak Person (PIC)
                                                </h3>
                                                
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Nama PIC</label>
                                                        <input type="text" x-model="formData.pic_name" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="Nama lengkap PIC">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Jabatan</label>
                                                        <input type="text" x-model="formData.pic_position" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="Contoh: Direktur, Manager">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Common Fields -->
                                    <div class="p-5 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                        <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">
                                            <i class="fas fa-address-card mr-2"></i>Data Kontak
                                        </h3>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                                    <span x-text="formData.applicant_type === 'badan' ? 'Nama Kontak' : 'Nama Lengkap'"></span> <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" x-model="formData.name" 
                                                       class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc]"
                                                       :class="errors.name ? 'border-red-300 focus:border-red-500 focus:bg-white focus:ring-1 focus:ring-red-200' : 'border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10'\"
                                                       placeholder="Nama lengkap">
                                                <p x-show="errors.name" class="mt-1 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle mt-0.5"></i><span x-text="errors.name"></span></p>
                                            </div>
                                            
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                                        Email <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="email" x-model="formData.email" 
                                                           class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc]"
                                                           :class="errors.email ? 'border-red-300 focus:border-red-500 focus:bg-white focus:ring-1 focus:ring-red-200' : 'border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10'\"
                                                           placeholder="email@contoh.com">
                                                    <p x-show="errors.email" class="mt-1 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle mt-0.5"></i><span x-text="errors.email"></span></p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                                        No. Telepon <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="tel" x-model="formData.phone" 
                                                           class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc]"
                                                           :class="errors.phone ? 'border-red-300 focus:border-red-500 focus:bg-white focus:ring-1 focus:ring-red-200' : 'border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10'\"
                                                           placeholder="08xxxxxxxxxx">
                                                    <p x-show="errors.phone" class="mt-1 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle mt-0.5"></i><span x-text="errors.phone"></span></p>
                                                </div>
                                            </div>
                                            
                                            <!-- Perorangan specific -->
                                            <template x-if="formData.applicant_type === 'perorangan'">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">NIK</label>
                                                        <input type="text" x-model="formData.nik" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="16 digit NIK">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Pekerjaan</label>
                                                        <input type="text" x-model="formData.occupation" 
                                                               class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="Contoh: Wiraswasta, Karyawan">
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <div>
                                                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Alamat</label>
                                                <input type="text" x-model="formData.address" 
                                                       class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                       placeholder="Alamat lengkap">
                                            </div>
                                            
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Kota/Kabupaten</label>
                                                    <input type="text" x-model="formData.city" 
                                                           class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                           placeholder="Contoh: Jakarta Selatan">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Provinsi</label>
                                                    <input type="text" x-model="formData.province" 
                                                           class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                           placeholder="Contoh: DKI Jakarta">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Pilih Layanan -->
                        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="p-6 sm:p-10">
                                <div class="text-center mb-10">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-secondary) 0%, #ea580c 100%);">
                                        <i class="fas fa-clipboard-list text-2xl text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Layanan yang Dibutuhkan</h2>
                                    <p style="color: var(--text-secondary);">Pilih kategori dan jenis layanan yang Anda perlukan</p>
                                </div>
                                
                                <div class="max-w-2xl mx-auto">
                                    <!-- Service Categories -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-semibold mb-3" style="color: var(--text-primary);">
                                            Kategori Layanan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            @foreach($serviceCategories as $key => $label)
                                            <button type="button"
                                                    class="p-4 rounded-xl text-center transition-all duration-200 border-2"
                                                    :class="formData.service_category === '{{ $key }}' 
                                                        ? 'border-[var(--color-primary)] bg-[var(--surface-cool)] shadow-md' 
                                                        : 'border-[var(--border-light)] hover:border-[var(--color-primary)]/20 hover:bg-[#f8fbfd] hover:shadow-md'"
                                                    @click="selectCategory('{{ $key }}')">>
                                                <i class="fas {{ $key === 'perizinan' ? 'fa-id-card' : ($key === 'lingkungan' ? 'fa-leaf' : ($key === 'konstruksi' ? 'fa-hard-hat' : ($key === 'ketenagakerjaan' ? 'fa-users' : ($key === 'perpajakan' ? 'fa-file-invoice' : ($key === 'legalitas' ? 'fa-gavel' : ($key === 'sertifikasi' ? 'fa-certificate' : 'fa-ellipsis-h')))))) }} text-xl mb-2"
                                                     :class="formData.service_category === '{{ $key }}' ? 'text-[var(--color-primary)]' : 'text-[var(--text-tertiary)]'"></i>
                                                <p class="text-xs font-semibold" :class="formData.service_category === '{{ $key }}' ? 'text-[var(--text-primary)]' : 'text-[var(--text-secondary)]'">{{ $label }}</p>
                                            </button>
                                            @endforeach
                                        </div>
                                        <p x-show="errors.service_category" class="mt-2 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle"></i><span x-text="errors.service_category"></span></p>
                                    </div>
                                    
                                    <!-- Services Checklist -->
                                    <div x-show="formData.service_category" x-transition class="p-5 rounded-xl mb-6" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                        <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">
                                            Pilih Layanan <span class="text-red-500">*</span>
                                        </label>
                                        <p class="text-sm mb-4" style="color: var(--text-secondary);">Anda dapat memilih lebih dari satu layanan</p>
                                        
                                        <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                            @foreach($servicesByCategory as $category => $services)
                                            <template x-if="formData.service_category === '{{ $category }}'">
                                                <div class="space-y-2">
                                                    @foreach($services as $key => $label)
                                                    <label class="flex items-center gap-3 p-3 rounded-lg cursor-pointer transition-all border"
                                                           :class="formData.services_requested.includes('{{ $key }}') 
                                                               ? 'border-[var(--color-primary)] bg-white shadow-sm' 
                                                               : 'border-transparent hover:bg-white'">
                                                        <input type="checkbox" value="{{ $key }}" 
                                                               :checked="formData.services_requested.includes('{{ $key }}')"
                                                               @change="toggleService('{{ $key }}')"
                                                               class="w-5 h-5 rounded border-2 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                                        <span style="color: var(--text-primary);">{{ $label }}</span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </template>
                                            @endforeach
                                        </div>
                                        <p x-show="errors.services_requested" class="mt-2 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle"></i><span x-text="errors.services_requested"></span></p>
                                    </div>
                                    
                                    <!-- Project Details -->
                                    <div class="p-5 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                        <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">
                                            <i class="fas fa-info-circle mr-2"></i>Detail Kebutuhan
                                        </h3>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Deskripsi Kebutuhan</label>
                                                <textarea x-model="formData.project_description" rows="4"
                                                          class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none border-[var(--border-light)] focus:border-[var(--color-primary)] resize-none"
                                                          placeholder="Jelaskan kebutuhan Anda secara detail..."></textarea>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Lokasi Proyek/Usaha</label>
                                                <input type="text" x-model="formData.project_location" 
                                                       class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                       placeholder="Contoh: Kawasan Industri MM2100, Bekasi">
                                            </div>
                                            
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Estimasi Budget</label>
                                                    <div class="relative">
                                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color: var(--text-tertiary);">Rp</span>
                                                        <input type="text" x-model="formData.estimated_budget_display" 
                                                               @input="formatBudget($event)"
                                                               class="w-full pl-12 pr-4 py-3 rounded-lg border-2 transition-all focus:outline-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                               placeholder="0">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-primary);">Ekspektasi Waktu</label>
                                                    <select x-model="formData.timeline_expectation" 
                                                            class="w-full px-4 py-3 rounded-lg border-2 transition-all focus:outline-none appearance-none bg-white hover:bg-[#fafbfc] border-[var(--border-light)] focus:border-[var(--color-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--color-primary)]/10"
                                                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 20px; padding-right: 40px;">
                                                        <option value="">Pilih waktu</option>
                                                        <option value="segera">Segera (&lt; 1 minggu)</option>
                                                        <option value="1-bulan">1 Bulan</option>
                                                        <option value="1-3-bulan">1-3 Bulan</option>
                                                        <option value="3-6-bulan">3-6 Bulan</option>
                                                        <option value="fleksibel">Fleksibel</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 4: Preview Surat Permohonan -->
                        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="p-6 sm:p-10">
                                <div class="text-center mb-8">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-accent) 0%, #0369a1 100%);">
                                        <i class="fas fa-file-signature text-2xl text-white"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold mb-2" style="color: var(--text-primary);">Surat Permohonan Resmi</h2>
                                    <p style="color: var(--text-secondary);">Preview surat permohonan yang akan dikirimkan</p>
                                </div>
                                
                                <!-- Official Letter Preview -->
                                <div class="max-w-3xl mx-auto">
                                    <div class="mb-5 p-4 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold" style="color: var(--text-primary);">Optimasi Surat dengan AI</p>
                                                <p class="text-xs" style="color: var(--text-secondary);">Sistem sedang menyusun surat formal Anda berdasarkan data yang diisi...</p>
                                            </div>
                                            <div x-show="isGeneratingAi" class="flex items-center gap-2 px-4 py-2 rounded-lg" style="background: rgba(15, 23, 42, 0.05); color: var(--color-primary);">
                                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                <span class="text-sm font-medium">Menyusun surat...</span>
                                            </div>
                                            <div x-show="!isGeneratingAi && formData.ai_letter_body" class="flex items-center gap-2 px-3 py-1 rounded-lg text-green-600 text-sm font-medium" style="display: none;">
                                                <i class="fas fa-check-circle"></i> Selesai
                                            </div>
                                        </div>
                                        <p x-show="aiMessage" class="mt-3 text-xs" :class="aiMessageType === 'error' ? 'text-red-600' : 'text-green-600'" x-text="aiMessage"></p>
                                    </div>

                                    <div class="bg-white rounded-xl shadow-lg overflow-hidden" style="border: 2px solid var(--border-light);">
                                        <!-- Letter Header -->
                                        <div class="p-6 text-center" style="background: linear-gradient(135deg, var(--surface-cool) 0%, var(--surface) 100%); border-bottom: 3px double var(--border-medium);">
                                            <div class="flex items-center justify-center gap-3 mb-2">
                                                <i class="fas fa-certificate text-2xl" style="color: var(--color-primary);"></i>
                                                <span class="text-xl font-bold tracking-wide" style="color: var(--color-primary);">SURAT PERMOHONAN</span>
                                            </div>
                                            <p class="text-xs font-semibold tracking-widest uppercase" style="color: var(--text-tertiary);">Penghitungan Biaya Jasa Konsultansi</p>
                                        </div>
                                        
                                        <!-- Letter Content -->
                                        <div class="p-6 sm:p-8" style="font-family: 'Times New Roman', Georgia, serif; line-height: 1.8;">
                                            <!-- Date & Number -->
                                            <div class="flex justify-between items-start mb-6 text-sm">
                                                <div>
                                                    <p style="color: var(--text-tertiary);">No. Permohonan:</p>
                                                    <p class="font-semibold" style="color: var(--text-primary);" x-text="generateRequestNumber()"></p>
                                                </div>
                                                <div class="text-right">
                                                    <p style="color: var(--text-primary);" x-text="formData.city || 'Jakarta'"></p>
                                                    <p style="color: var(--text-primary);" x-text="currentDate"></p>
                                                </div>
                                            </div>
                                            
                                            <!-- Recipient -->
                                            <div class="mb-6">
                                                <p style="color: var(--text-primary);">Kepada Yth.</p>
                                                <p class="font-bold" style="color: var(--text-primary);">PT Cangah Pajaratan Mandiri</p>
                                                <p style="color: var(--text-primary);">(Bizmark.ID)</p>
                                                <p style="color: var(--text-secondary);">di Tempat</p>
                                            </div>
                                            
                                            <!-- Subject -->
                                            <div class="mb-6">
                                                <p style="color: var(--text-primary);">Perihal: <span class="font-semibold">Permohonan Penghitungan Biaya Jasa</span></p>
                                            </div>
                                            
                                            <!-- Salutation - Only show if NOT using AI (AI generates its own) -->
                                            <template x-if="!formData.ai_letter_body">
                                                <p class="mb-4" style="color: var(--text-primary);">Dengan hormat,</p>
                                            </template>
                                            
                                            <!-- Opening Paragraph -->
                                            <template x-if="!formData.ai_letter_body">
                                            <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                <template x-if="formData.applicant_type === 'perorangan'">
                                                    <span>
                                                        Yang bertanda tangan di bawah ini, saya <strong x-text="formData.name || '[Nama Lengkap]'"></strong>
                                                        <span x-show="formData.occupation">, yang berprofesi sebagai <strong x-text="formData.occupation"></strong></span>,
                                                        beralamat di <strong x-text="(formData.address || '[Alamat]') + (formData.city ? ', ' + formData.city : '') + (formData.province ? ', ' + formData.province : '')"></strong>,
                                                        dengan ini mengajukan permohonan penghitungan biaya jasa konsultansi kepada PT Cangah Pajaratan Mandiri (Bizmark.ID).
                                                    </span>
                                                </template>
                                                <template x-if="formData.applicant_type === 'badan'">
                                                    <span>
                                                        Yang bertanda tangan di bawah ini, atas nama <strong x-text="formData.company_name || '[Nama Perusahaan]'"></strong>
                                                        (<span x-text="getBusinessTypeLabel()"></span>),
                                                        <span x-show="formData.nib">dengan NIB: <strong x-text="formData.nib"></strong>,</span>
                                                        beralamat di <strong x-text="(formData.address || '[Alamat]') + (formData.city ? ', ' + formData.city : '') + (formData.province ? ', ' + formData.province : '')"></strong>,
                                                        yang dalam hal ini diwakili oleh <strong x-text="formData.name || formData.pic_name || '[Nama PIC]'"></strong>
                                                        <span x-show="formData.pic_position"> selaku <strong x-text="formData.pic_position"></strong></span>,
                                                        dengan ini mengajukan permohonan penghitungan biaya jasa konsultansi kepada PT Cangah Pajaratan Mandiri (Bizmark.ID).
                                                    </span>
                                                </template>
                                            </p>
                                            </template>

                                            <template x-if="formData.ai_letter_body">
                                                <div class="space-y-4 mb-4">
                                                    <template x-for="(paragraph, idx) in aiLetterParagraphs()" :key="'ai-par-' + idx">
                                                        <p class="text-justify" style="color: var(--text-primary);" x-text="paragraph"></p>
                                                    </template>
                                                </div>
                                            </template>
                                            
                                            <!-- Purpose Section -->
                                            <template x-if="!formData.ai_letter_body">
                                            <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                Adapun maksud dan tujuan permohonan ini adalah untuk memperoleh informasi mengenai biaya jasa layanan <strong x-text="getServiceCategoryLabel()"></strong>, dengan rincian layanan yang dibutuhkan sebagai berikut:
                                            </p>
                                            </template>
                                            
                                            <!-- Services List -->
                                            <template x-if="!formData.ai_letter_body">
                                            <div class="mb-4 pl-6">
                                                <ol class="list-decimal space-y-1" style="color: var(--text-primary);">
                                                    <template x-for="(service, index) in formData.services_requested" :key="index">
                                                        <li x-text="getServiceLabel(service)"></li>
                                                    </template>
                                                </ol>
                                            </div>
                                            </template>
                                            
                                            <!-- Project Description -->
                                            <template x-if="!formData.ai_letter_body && formData.project_description">
                                                <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                    Untuk memberikan gambaran lebih jelas, berikut adalah deskripsi kebutuhan kami: <em x-text="formData.project_description"></em>
                                                </p>
                                            </template>

                                            <!-- Additional Info -->
                                            <template x-if="!formData.ai_letter_body">
                                            <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                <span x-show="formData.project_location">Lokasi proyek/usaha berada di <strong x-text="formData.project_location"></strong>. </span>
                                                <span x-show="formData.estimated_budget_display">Estimasi anggaran yang kami siapkan adalah sekitar <strong>Rp <span x-text="formData.estimated_budget_display"></span></strong>. </span>
                                                <span x-show="formData.timeline_expectation">Ekspektasi waktu penyelesaian adalah <strong x-text="getTimelineLabel()"></strong>.</span>
                                            </p>
                                            </template>

                                            <!-- Contact Info -->
                                            <template x-if="!formData.ai_letter_body">
                                            <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                Untuk keperluan komunikasi lebih lanjut, kami dapat dihubungi melalui:
                                            </p>
                                            </template>
                                            <template x-if="!formData.ai_letter_body">
                                            <div class="mb-4 pl-6" style="color: var(--text-primary);">
                                                <p>Email: <strong x-text="formData.email || '[Email]'"></strong></p>
                                                <p>Telepon: <strong x-text="formData.phone || '[No. Telepon]'"></strong></p>
                                            </div>
                                            </template>

                                            <!-- Closing -->
                                            <template x-if="!formData.ai_letter_body">
                                            <p class="mb-4 text-justify" style="color: var(--text-primary);">
                                                Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
                                            </p>
                                            </template>
                                            
                                            <!-- Signature -->
                                            <div class="mt-10 text-right">
                                                <!-- Only show if NOT using AI -->
                                                <template x-if="!formData.ai_letter_body">
                                                    <p style="color: var(--text-primary);">Hormat kami,</p>
                                                </template>
                                                <div class="mt-16 mb-4">
                                                    <p class="font-bold" style="color: var(--text-primary);" x-text="formData.name || '[Nama Lengkap]'"></p>
                                                    <template x-if="formData.applicant_type === 'badan'">
                                                        <p style="color: var(--text-secondary);" x-text="formData.pic_position || 'Perwakilan'"></p>
                                                    </template>
                                                </div>
                                                
                                                <!-- Professional Digital Signature with QR Code - Compact -->
                                                <div class="mt-4 pt-3 border-t-2" style="border-color: var(--color-primary);">
                                                    <div class="p-3 rounded-lg" style="background: rgba(15, 23, 42, 0.02); border: 1px solid var(--border-light);">
                                                        <div class="flex gap-3">
                                                            <!-- QR Code -->
                                                            <div class="flex-shrink-0">
                                                                <div class="w-20 h-20 bg-white rounded p-1 border-2 flex items-center justify-center" style="border-color: var(--color-primary);">
                                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' + encodeURIComponent('Bizmark.ID|' + generateRequestNumber() + '|' + generateRequestHash() + '|' + currentDate)" alt="QR" class="w-full h-full object-contain">
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Certificate Info - Compact -->
                                                            <div class="flex-1 text-xs" style="color: var(--text-primary);">
                                                                <div class="mb-2">
                                                                    <h4 class="font-bold" style="color: var(--color-primary); font-size: 0.75rem; text-transform: uppercase;">Sertifikat Digital</h4>
                                                                </div>
                                                                <div style="font-size: 0.7rem;">
                                                                    <p class="mb-1"><strong>No.:</strong> <span style="font-family: monospace; color: var(--color-primary);" x-text="generateRequestNumber()"></span></p>
                                                                    <p class="mb-1"><strong>Hash:</strong> <span style="font-family: monospace; color: var(--color-primary); font-size: 0.65rem;" x-text="generateRequestHash()"></span></p>
                                                                    <p><strong>Tgl:</strong> <span x-text="currentDate"></span></p>
                                                                </div>
                                                                <div class="mt-2 pt-2 border-t flex items-center gap-1" style="border-color: var(--border-light); color: #10b981;">
                                                                    <i class="fas fa-shield-alt" style="font-size: 0.65rem;"></i>
                                                                    <span style="font-size: 0.65rem; font-weight: 600;">Terverifikasi Digital</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Consent Checkbox -->
                                    <div class="mt-6 p-4 rounded-xl" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                                        <label class="flex items-start gap-3 cursor-pointer">
                                            <input type="checkbox" x-model="formData.consent" 
                                                   class="w-5 h-5 mt-0.5 rounded border-2 text-[var(--color-primary)] focus:ring-[var(--color-primary)]">
                                            <span class="text-sm" style="color: var(--text-primary);">
                                                Saya menyatakan bahwa data yang saya isi adalah benar dan saya menyetujui <a href="{{ route('privacy.policy.id') }}" target="_blank" class="text-[var(--color-accent)] hover:underline">Kebijakan Privasi</a> serta <a href="{{ route('terms.conditions.id') }}" target="_blank" class="text-[var(--color-accent)] hover:underline">Syarat & Ketentuan</a> yang berlaku.
                                            </span>
                                        </label>
                                        <p x-show="errors.consent" class="mt-2 text-sm text-red-600 font-medium flex items-start gap-1"><i class="fas fa-exclamation-circle"></i><span x-text="errors.consent"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="px-6 sm:px-10 py-6 flex justify-between items-center" style="background: var(--surface-cool); border-top: 1px solid var(--border-light);">
                            <button type="button" 
                                    x-show="currentStep > 0"
                                    @click="prevStep"
                                    class="btn btn-outline-primary hover:shadow-md active:scale-95">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </button>
                            <div x-show="currentStep === 0"></div>
                            
                            <button type="button" 
                                    x-show="currentStep < steps.length - 1"
                                    @click="nextStep"
                                    class="btn btn-primary ml-auto hover:shadow-lg active:scale-95">
                                Lanjut
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            
                            <button type="submit" 
                                    x-show="currentStep === steps.length - 1"
                                    :disabled="isSubmitting || !formData.consent"
                                    class="btn btn-secondary ml-auto hover:shadow-lg active:scale-95"
                                    :class="{ 'opacity-60 cursor-not-allowed': isSubmitting || !formData.consent }">
                                <span x-show="!isSubmitting">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Kirim Permohonan
                                </span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Trust Badges -->
                <div class="mt-10 flex flex-wrap justify-center items-center gap-6" style="color: var(--text-tertiary);">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span class="text-sm">Data Terenkripsi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i>
                        <span class="text-sm">Respon 1x24 Jam</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-headset text-purple-500"></i>
                        <span class="text-sm">Konsultasi Gratis</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
/* Permohonan Page Enhancements */
.permohonan-page input,
.permohonan-page textarea,
.permohonan-page select {
    transition: all 0.2s ease;
}

.permohonan-page input:disabled,
.permohonan-page textarea:disabled,
.permohonan-page select:disabled {
    background-color: var(--surface-cool);
    color: var(--text-tertiary);
    cursor: not-allowed;
}

/* Focus ring improvements for better visibility */
.permohonan-page input:focus,
.permohonan-page textarea:focus,
.permohonan-page select:focus {
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
}

.btn:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}

.btn:active:not(:disabled) {
    transform: scale(0.98);
}

/* Focus ring improvements for better visibility */
.permohonan-page input:focus,
.permohonan-page textarea:focus,
.permohonan-page select:focus {
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
}

/* Letter content improvements */
.letter-content {
    word-spacing: 0.05em;
    letter-spacing: 0.01em;
}

/* Error message styling */
.error-message {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: rgba(239, 68, 68, 0.05);
    border-left: 3px solid #ef4444;
    border-radius: 0.25rem;
}

/* Service category button improvements */
.service-category-btn {
    position: relative;
    overflow: hidden;
}

.service-category-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.4s ease;
}

.service-category-btn:hover::before {
    left: 100%;
}

/* Form section improvements */
.form-section {
    border-radius: 0.75rem;
    padding: 1.25rem;
    background: var(--surface-cool);
    border: 1px solid var(--border-light);
    transition: all 0.2s ease;
}

.form-section:focus-within {
    border-color: var(--border-medium);
    box-shadow: 0 0 0 3px var(--color-primary)/5;
}

/* Mobile improvements */
@media (max-width: 640px) {
    .permohonan-page {
        --container-padding: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .btn {
        padding: 0.65rem 1.5rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function permohonanForm() {
    return {
        currentStep: 0,
        isSubmitting: false,
        isGeneratingAi: false,
        aiMessage: '',
        aiMessageType: 'success',
        perorangan_hover: false,
        badan_hover: false,
        
        steps: [
            { title: 'Jenis Pemohon', icon: 'user-tag' },
            { title: 'Data Pemohon', icon: 'id-card' },
            { title: 'Layanan', icon: 'clipboard-list' },
            { title: 'Surat Permohonan', icon: 'file-signature' }
        ],
        
        formData: {
            applicant_type: '',
            name: '',
            email: '',
            phone: '',
            address: '',
            city: '',
            province: '',
            nik: '',
            occupation: '',
            company_name: '',
            npwp: '',
            nib: '',
            business_type: '',
            business_sector: '',
            pic_name: '',
            pic_position: '',
            service_category: '',
            services_requested: [],
            project_description: '',
            project_location: '',
            estimated_budget: '',
            estimated_budget_display: '',
            timeline_expectation: '',
            ai_letter_body: '',
            consent: false
        },
        
        errors: {},
        requestTimestamp: new Date().getTime(),
        sessionSequence: Math.floor(Math.random() * 10000) + 1,
        
        serviceCategories: @json($serviceCategories),
        servicesByCategory: @json($servicesByCategory),
        
        get currentDate() {
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return new Date().toLocaleDateString('id-ID', options);
        },
        
        get currentDateShort() {
            const date = new Date();
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        },
        
        generateRequestNumber() {
            // Format: NNN/DD/MM/YYYY (sequential/date/month/year)
            const seqNumber = String(this.sessionSequence).padStart(3, '0');
            return `${seqNumber}/${this.currentDateShort}`;
        },
        
        generateRequestHash() {
            // Generate simple hash based on timestamp + sequence for digital signature
            const hash = (this.requestTimestamp + this.sessionSequence).toString(36).toUpperCase();
            return hash.substring(0, 8);
        },
        
        selectCategory(category) {
            this.formData.service_category = category;
            this.formData.services_requested = [];
        },
        
        toggleService(service) {
            const index = this.formData.services_requested.indexOf(service);
            if (index === -1) {
                this.formData.services_requested.push(service);
            } else {
                this.formData.services_requested.splice(index, 1);
            }
        },
        
        formatBudget(event) {
            let value = event.target.value.replace(/\D/g, '');
            this.formData.estimated_budget = value;
            this.formData.estimated_budget_display = value ? parseInt(value).toLocaleString('id-ID') : '';
        },
        
        getBusinessTypeLabel() {
            const labels = {
                'pt': 'Perseroan Terbatas',
                'cv': 'CV',
                'ud': 'Usaha Dagang',
                'yayasan': 'Yayasan',
                'koperasi': 'Koperasi',
                'lainnya': 'Badan Usaha'
            };
            return labels[this.formData.business_type] || 'Badan Usaha';
        },
        
        getServiceCategoryLabel() {
            return this.serviceCategories[this.formData.service_category] || this.formData.service_category;
        },
        
        getServiceLabel(serviceKey) {
            const category = this.formData.service_category;
            if (this.servicesByCategory[category] && this.servicesByCategory[category][serviceKey]) {
                return this.servicesByCategory[category][serviceKey];
            }
            return serviceKey;
        },
        
        getTimelineLabel() {
            const labels = {
                'segera': 'segera (kurang dari 1 minggu)',
                '1-bulan': '1 bulan',
                '1-3-bulan': '1-3 bulan',
                '3-6-bulan': '3-6 bulan',
                'fleksibel': 'fleksibel'
            };
            return labels[this.formData.timeline_expectation] || this.formData.timeline_expectation;
        },

        aiLetterParagraphs() {
            if (!this.formData.ai_letter_body) {
                return [];
            }

            return this.formData.ai_letter_body
                .split(/\n\s*\n/)
                .map((paragraph) => paragraph.trim())
                .filter((paragraph) => paragraph.length > 0);
        },

        async generateAiLetter() {
            if (!this.formData.applicant_type || !this.formData.name || !this.formData.email || !this.formData.phone || !this.formData.service_category || this.formData.services_requested.length === 0) {
                this.aiMessageType = 'error';
                this.aiMessage = 'Lengkapi data utama terlebih dahulu (jenis pemohon, kontak, kategori, dan layanan).';
                return;
            }

            this.isGeneratingAi = true;
            this.aiMessage = '';

            try {
                const payload = { ...this.formData };
                payload.consent = undefined;
                payload.estimated_budget_display = undefined;

                const response = await fetch('{{ route("permohonan.generate-letter-draft") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    this.aiMessageType = 'error';
                    this.aiMessage = data.message || 'Gagal menghasilkan draf surat AI.';
                    return;
                }

                this.formData.ai_letter_body = data.data.ai_letter_body || '';
                this.aiMessageType = 'success';
                this.aiMessage = 'Draf surat berhasil dioptimasi dengan AI. Silakan review hasilnya.';
            } catch (error) {
                console.error('AI letter generation error:', error);
                this.aiMessageType = 'error';
                this.aiMessage = 'Terjadi gangguan saat memanggil AI. Silakan coba lagi.';
            } finally {
                this.isGeneratingAi = false;
            }
        },
        
        validateStep() {
            this.errors = {};
            
            if (this.currentStep === 0) {
                if (!this.formData.applicant_type) {
                    this.errors.applicant_type = 'Pilih jenis pemohon';
                }
            }
            
            if (this.currentStep === 1) {
                if (!this.formData.name) this.errors.name = 'Nama wajib diisi';
                if (!this.formData.email) this.errors.email = 'Email wajib diisi';
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) {
                    this.errors.email = 'Format email tidak valid';
                }
                if (!this.formData.phone) this.errors.phone = 'No. telepon wajib diisi';
                
                if (this.formData.applicant_type === 'badan') {
                    if (!this.formData.company_name) this.errors.company_name = 'Nama perusahaan wajib diisi';
                    if (!this.formData.business_type) this.errors.business_type = 'Pilih jenis badan usaha';
                }
            }
            
            if (this.currentStep === 2) {
                if (!this.formData.service_category) {
                    this.errors.service_category = 'Pilih kategori layanan';
                }
                if (this.formData.services_requested.length === 0) {
                    this.errors.services_requested = 'Pilih minimal satu layanan';
                }
            }
            
            if (this.currentStep === 3) {
                if (!this.formData.consent) {
                    this.errors.consent = 'Anda harus menyetujui kebijakan privasi dan syarat ketentuan';
                }
            }
            
            return Object.keys(this.errors).length === 0;
        },
        
        nextStep() {
            if (this.validateStep()) {
                this.currentStep++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Auto-generate AI letter when entering step 4
                if (this.currentStep === 3) {
                    this.$nextTick(() => {
                        this.generateAiLetter();
                    });
                }
            }
        },
        
        prevStep() {
            this.currentStep--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        async submitForm() {
            if (!this.validateStep()) return;
            
            this.isSubmitting = true;
            
            const formData = new FormData();
            
            Object.keys(this.formData).forEach(key => {
                if (key === 'services_requested') {
                    formData.append(key, JSON.stringify(this.formData[key]));
                } else if (key !== 'estimated_budget_display' && key !== 'consent' && key !== 'ai_letter_body' && this.formData[key]) {
                    formData.append(key, this.formData[key]);
                }
            });
            
            try {
                const response = await fetch('{{ route("permohonan.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    if (data.errors) {
                        this.errors = {};
                        Object.keys(data.errors).forEach(key => {
                            this.errors[key] = Array.isArray(data.errors[key]) ? data.errors[key][0] : data.errors[key];
                        });
                        
                        if (data.errors.applicant_type) this.currentStep = 0;
                        else if (data.errors.name || data.errors.email || data.errors.phone || data.errors.company_name) this.currentStep = 1;
                        else if (data.errors.service_category || data.errors.services_requested) this.currentStep = 2;
                    }
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }

.permohonan-page {
    min-height: 100vh;
}

input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    border: 2px solid var(--border-medium);
    background: white;
    cursor: pointer;
}

input[type="checkbox"]:checked {
    background: var(--color-primary);
    border-color: var(--color-primary);
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3E%3C/svg%3E");
}
</style>
@endsection
