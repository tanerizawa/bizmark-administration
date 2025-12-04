<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Beta Tester - Bizmark.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="{{ route('beta-tester.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
                    ← Kembali ke Halaman Utama
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Form Registrasi Beta Tester</h1>
                <p class="text-gray-600">Lengkapi data diri Anda dengan benar</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <form action="{{ route('beta-tester.store') }}" method="POST" x-data="registrationForm()">
                    @csrf

                    <!-- Data Pribadi -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b">
                            📋 Data Pribadi
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-600">*</span>
                                </label>
                                <input type="text" 
                                       name="full_name" 
                                       value="{{ old('full_name') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Contoh: Ahmad Fauzi"
                                       required>
                                @error('full_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tempat Lahir <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" 
                                           name="place_of_birth" 
                                           value="{{ old('place_of_birth') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Contoh: Jakarta"
                                           required>
                                    @error('place_of_birth')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Lahir <span class="text-red-600">*</span>
                                    </label>
                                    <input type="date" 
                                           name="date_of_birth" 
                                           value="{{ old('date_of_birth') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                    @error('date_of_birth')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat Lengkap <span class="text-red-600">*</span>
                                </label>
                                <textarea name="address" 
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Contoh: Jl. Sudirman No. 123, Kelurahan ABC, Kecamatan XYZ, Kota/Kabupaten"
                                          required>{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Jenis Identitas <span class="text-red-600">*</span>
                                    </label>
                                    <select name="identity_type" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required>
                                        <option value="">Pilih Jenis Identitas</option>
                                        <option value="ktp" {{ old('identity_type') == 'ktp' ? 'selected' : '' }}>KTP</option>
                                        <option value="ktm" {{ old('identity_type') == 'ktm' ? 'selected' : '' }}>Kartu Tanda Mahasiswa</option>
                                    </select>
                                    @error('identity_type')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Identitas <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" 
                                           name="identity_number" 
                                           value="{{ old('identity_number') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Nomor KTP atau KTM"
                                           required>
                                    @error('identity_number')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Akademik -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b">
                            🎓 Data Akademik
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Universitas <span class="text-red-600">*</span>
                                </label>
                                <input type="text" 
                                       name="university" 
                                       value="{{ old('university') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Contoh: Universitas Indonesia"
                                       required>
                                @error('university')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Fakultas <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" 
                                           name="faculty" 
                                           value="{{ old('faculty') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Contoh: Fakultas Ilmu Komputer"
                                           required>
                                    @error('faculty')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Program Studi <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" 
                                           name="major" 
                                           value="{{ old('major') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Contoh: Sistem Informasi"
                                           required>
                                    @error('major')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        NIM <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" 
                                           name="student_id" 
                                           value="{{ old('student_id') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Nomor Induk Mahasiswa"
                                           required>
                                    @error('student_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Semester <span class="text-red-600">*</span>
                                    </label>
                                    <select name="semester" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required>
                                        <option value="">Pilih Semester</option>
                                        @for($i = 1; $i <= 14; $i++)
                                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                                Semester {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('semester')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Kontak -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b">
                            📞 Data Kontak
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-600">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="email@example.com"
                                       required>
                                @error('email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor Telepon <span class="text-red-600">*</span>
                                    </label>
                                    <input type="tel" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="08xxxxxxxxxx"
                                           required>
                                    @error('phone')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        WhatsApp <span class="text-gray-500">(Opsional)</span>
                                    </label>
                                    <input type="tel" 
                                           name="whatsapp" 
                                           value="{{ old('whatsapp') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="08xxxxxxxxxx (jika berbeda)">
                                    @error('whatsapp')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motivasi -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b">
                            💡 Motivasi
                        </h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ceritakan motivasi Anda mengikuti program ini <span class="text-red-600">*</span>
                            </label>
                            <textarea name="motivation" 
                                      rows="5"
                                      x-model="motivation"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Minimal 100 karakter. Jelaskan mengapa Anda tertarik mengikuti program beta tester ini dan apa yang ingin Anda pelajari."
                                      required>{{ old('motivation') }}</textarea>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-sm" :class="motivation.length >= 100 ? 'text-green-600' : 'text-gray-500'">
                                    <span x-text="motivation.length"></span> / 100 karakter minimum
                                </span>
                                @error('motivation')
                                    <p class="text-red-600 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('beta-tester.index') }}" 
                           class="text-gray-600 hover:text-gray-900">
                            ← Kembali
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Daftar Sekarang →
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-bold text-blue-900 mb-2">ℹ️ Informasi Penting</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Pastikan semua data yang Anda isi adalah benar dan valid</li>
                    <li>• Setelah mendaftar, Anda akan diminta menandatangani Pakta Integritas dan NDA</li>
                    <li>• Tim kami akan melakukan verifikasi dalam 1-3 hari kerja</li>
                    <li>• Jika disetujui, Anda akan mendapat akses ke sistem dan GitLab</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function registrationForm() {
            return {
                motivation: '{{ old("motivation") }}'
            }
        }
    </script>

    @if(session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg">
        {{ session('error') }}
    </div>
    @endif
</body>
</html>
