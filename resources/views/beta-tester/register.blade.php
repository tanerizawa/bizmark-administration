@extends('beta-tester.layouts.app')

@section('title', 'Registrasi Beta Tester')

@section('content')
<div class="min-h-screen py-12" style="background: var(--light-bg-secondary);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="{{ route('beta-tester.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 mb-4 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Halaman Utama</span>
            </a>
            <h1 class="text-3xl font-bold mb-2" style="color: var(--light-text-primary);">Form Registrasi Beta Tester</h1>
            <p style="color: var(--light-text-secondary);">Lengkapi data diri Anda dengan benar</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 p-4 rounded-lg" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="mb-6 p-4 rounded-lg" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
        <div class="mb-6 p-4 rounded-lg" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <div>
                    <strong>Terdapat {{ $errors->count() }} kesalahan:</strong>
                    <ul class="mt-2 ml-4 list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Form -->
        <div class="card p-8">
            <form action="{{ route('beta-tester.store') }}" method="POST" x-data="registrationForm()" @submit="handleSubmit">
                @csrf

                <!-- Data Pribadi -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-3" style="border-bottom: 1px solid var(--light-separator);">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold" style="color: var(--light-text-primary);">Data Pribadi</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="full_name"
                                   value="{{ old('full_name') }}"
                                   class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                   placeholder="Contoh: Ahmad Fauzi"
                                   required>
                            @error('full_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Tempat Lahir <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="place_of_birth"
                                       value="{{ old('place_of_birth') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="Contoh: Jakarta"
                                       required>
                                @error('place_of_birth')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="date_of_birth"
                                       value="{{ old('date_of_birth') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       required>
                                @error('date_of_birth')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address"
                                      rows="3"
                                      class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                      style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                      placeholder="Contoh: Jl. Sudirman No. 123, Kelurahan ABC, Kecamatan XYZ"
                                      required>{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Jenis Identitas <span class="text-red-500">*</span>
                                </label>
                                <select name="identity_type"
                                        class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                        required>
                                    <option value="">Pilih Jenis Identitas</option>
                                    <option value="ktp" {{ old('identity_type') == 'ktp' ? 'selected' : '' }}>KTP</option>
                                    <option value="ktm" {{ old('identity_type') == 'ktm' ? 'selected' : '' }}>Kartu Tanda Mahasiswa</option>
                                </select>
                                @error('identity_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Nomor Identitas <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="identity_number"
                                       value="{{ old('identity_number') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="Nomor KTP atau KTM"
                                       required>
                                @error('identity_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-3" style="border-bottom: 1px solid var(--light-separator);">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold" style="color: var(--light-text-primary);">Data Akademik</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                Universitas <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="university"
                                   value="{{ old('university') }}"
                                   class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                   placeholder="Contoh: Universitas Indonesia"
                                   required>
                            @error('university')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Fakultas <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="faculty"
                                       value="{{ old('faculty') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="Contoh: Fakultas Ilmu Komputer"
                                       required>
                                @error('faculty')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Program Studi <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="major"
                                       value="{{ old('major') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="Contoh: Sistem Informasi"
                                       required>
                                @error('major')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    NIM <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="student_id"
                                       value="{{ old('student_id') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="Nomor Induk Mahasiswa"
                                       required>
                                @error('student_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Semester <span class="text-red-500">*</span>
                                </label>
                                <select name="semester"
                                        class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                        style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                        required>
                                    <option value="">Pilih Semester</option>
                                    @for($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                            Semester {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('semester')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Kontak -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-3" style="border-bottom: 1px solid var(--light-separator);">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-phone text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold" style="color: var(--light-text-primary);">Data Kontak</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                   style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                   placeholder="email@example.com"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="08xxxxxxxxxx"
                                       required>
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                                    WhatsApp <span style="color: var(--light-text-secondary);">(Opsional)</span>
                                </label>
                                <input type="tel"
                                       name="whatsapp"
                                       value="{{ old('whatsapp') }}"
                                       class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                       style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                       placeholder="08xxxxxxxxxx (jika berbeda)">
                                @error('whatsapp')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Motivasi -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6 pb-3" style="border-bottom: 1px solid var(--light-separator);">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lightbulb text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold" style="color: var(--light-text-primary);">Motivasi</h2>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--light-text-primary);">
                            Ceritakan motivasi Anda mengikuti program ini <span class="text-red-500">*</span>
                        </label>
                        <textarea name="motivation"
                                  rows="5"
                                  x-model="motivation"
                                  class="w-full px-4 py-3 rounded-lg transition focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                  style="background: var(--light-bg); border: 1px solid var(--light-separator); color: var(--light-text-primary);"
                                  placeholder="Minimal 100 karakter. Jelaskan mengapa Anda tertarik mengikuti program beta tester ini dan apa yang ingin Anda pelajari."
                                  required>{{ old('motivation') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-sm" :class="motivation.length >= 100 ? 'text-green-500' : 'text-gray-500'">
                                <span x-text="motivation.length"></span> / 100 karakter minimum
                            </span>
                            @error('motivation')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-between pt-6" style="border-top: 1px solid var(--light-separator);">
                    <a href="{{ route('beta-tester.index') }}" 
                       class="inline-flex items-center gap-2 hover:text-blue-600 transition"
                       style="color: var(--light-text-secondary);">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="submit" class="btn-primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Daftar Sekarang</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                        <i class="fas fa-arrow-right" x-show="!submitting"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info -->
        <div class="mt-8 card p-6" style="background: var(--light-bg); border-color: var(--apple-blue);">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1"></i>
                <div>
                    <h3 class="font-bold mb-2" style="color: var(--light-text-primary);">Informasi Penting</h3>
                    <ul class="text-sm space-y-1" style="color: var(--light-text-secondary);">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Pastikan semua data yang Anda isi adalah benar dan valid</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Setelah mendaftar, Anda akan diminta menandatangani Pakta Integritas dan NDA</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tim kami akan melakukan verifikasi dalam 1-3 hari kerja</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Jika disetujui, Anda akan mendapat akses ke sistem dan GitLab</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function registrationForm() {
        return {
            motivation: '{{ old("motivation") }}',
            submitting: false,
            
            handleSubmit(e) {
                console.log('=== Form Submit Started ===');
                console.log('Motivation length:', this.motivation.length);
                console.log('Form action:', e.target.action);
                console.log('Form method:', e.target.method);
                
                // Check motivation length
                if (this.motivation.length < 100) {
                    console.log('❌ Motivation too short');
                    alert('Motivasi minimal 100 karakter. Saat ini: ' + this.motivation.length + ' karakter');
                    e.preventDefault();
                    return false;
                }
                
                console.log('✅ Form validation passed, setting submitting state...');
                this.submitting = true;
                
                // Let form submit naturally
                console.log('✅ Submitting form...');
            }
        }
    }
    
    // Log when page loads
    console.log('=== Beta Tester Registration Page Loaded ===');
    console.log('CSRF Token:', document.querySelector('input[name="_token"]')?.value);
    console.log('Form action:', document.querySelector('form')?.action);
</script>
@endsection
