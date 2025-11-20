<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Bizmark.id</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-block">
                <h1 class="text-4xl font-bold text-white">Bizmark<span class="text-yellow-300">.id</span></h1>
            </a>
            <p class="text-white/80 mt-2">Daftar Portal Klien</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-user-plus text-[#0077B5] text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h2>
                <p class="text-gray-600 mt-2">Mulai monitoring proyek perizinan Anda</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="font-semibold">Terjadi Kesalahan</span>
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('client.register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="John Doe"
                    >
                </div>

                <!-- Company Name -->
                <div class="mb-4">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-2"></i>Nama Perusahaan <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input 
                        type="text" 
                        id="company_name" 
                        name="company_name" 
                        value="{{ old('company_name') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent"
                        placeholder="PT. Contoh Indonesia"
                    >
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika pendaftaran pribadi</p>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="email@company.com"
                    >
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2"></i>No. Telepon <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent"
                        placeholder="6283879602855"
                    >
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="Minimal 8 karakter"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter, kombinasi huruf & angka</p>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077B5] focus:border-transparent"
                            placeholder="Ketik ulang password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" required class="mt-1 w-4 h-4 text-[#0077B5] border-gray-300 rounded focus:ring-[#0077B5]">
                        <span class="ml-2 text-sm text-gray-600">
                            Saya setuju dengan <a href="#" class="text-[#0077B5] hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-[#0077B5] hover:underline">Kebijakan Privasi</a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-[#0077B5] to-[#005582] text-white font-semibold py-3 px-6 rounded-lg hover:from-[#005582] hover:to-[#004466] transition duration-300 shadow-lg"
                >
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Sudah punya akun?</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-[#0077B5] hover:text-[#004466] font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Akun Anda
                </a>
            </div>
        </div>

        <!-- Benefits -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mt-6">
            <h3 class="text-white font-semibold mb-3 flex items-center">
                <i class="fas fa-star mr-2"></i>Keuntungan Portal Klien
            </h3>
            <ul class="text-white/80 text-sm space-y-2">
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-2 flex-shrink-0"></i>
                    <span>Monitor progress proyek real-time 24/7</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-2 flex-shrink-0"></i>
                    <span>Akses dokumen perizinan kapan saja</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-2 flex-shrink-0"></i>
                    <span>Notifikasi otomatis untuk setiap update</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-2 flex-shrink-0"></i>
                    <span>Komunikasi langsung dengan tim kami</span>
                </li>
            </ul>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/60 text-sm mt-8">
            © {{ date('Y') }} Bizmark.id - Solusi Perizinan Terpercaya
        </p>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
