<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a66c2">
    <title>Login - Bizmark.id</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', 'Fira Sans', Ubuntu, Oxygen, 'Oxygen Sans', Cantarell, 'Droid Sans', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        .login-card {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.06);
        }
        
        .btn-primary {
            background: #0a66c2;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: #004182;
            box-shadow: 0 2px 8px rgba(10, 102, 194, 0.3);
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
        
        input:focus {
            border-color: #0a66c2 !important;
            ring-color: #0a66c2 !important;
        }
        
        .divider-text {
            position: relative;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" x-data="{ email: '' }">
    <div class="w-full max-w-md">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex flex-col items-center group">
                <img src="{{ asset('images/logo-bizmark.svg') }}" 
                     alt="BizMark Indonesia" 
                     class="h-16 w-16 mb-3 transition-transform group-hover:scale-105">
                <div class="flex flex-col leading-tight">
                    <span class="text-2xl font-bold text-gray-800">BizMark<span class="text-[#0a66c2]">.ID</span></span>
                    <span class="text-sm text-gray-600 mt-1">Portal Login</span>
                </div>
            </a>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-lg p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-2">Selamat Datang</h1>
                <p class="text-sm text-gray-600">Masuk untuk mengakses portal Anda</p>
            </div>

            <!-- User Type Indicator (Optional UX Enhancement) -->
            <div class="mb-4" x-show="email.length > 0">
                <div class="p-3 rounded-lg" 
                     :class="email.includes('@bizmark.id') ? 'bg-[#0077B5]/10 border border-[#0077B5]/30' : 'bg-[#0077B5]/10 border border-[#0077B5]/30'">
                    <div class="flex items-center gap-2 text-sm">
                        <i class="fas" :class="email.includes('@bizmark.id') ? 'fa-shield-alt text-[#0077B5]' : 'fa-user text-[#0077B5]'"></i>
                        <span class="font-medium" :class="email.includes('@bizmark.id') ? 'text-[#005582]' : 'text-[#005582]'">
                            <span x-show="email.includes('@bizmark.id')">🔐 Admin Access</span>
                            <span x-show="!email.includes('@bizmark.id')">👤 Client Portal</span>
                        </span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-start gap-3">
                    <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                    <div class="flex-1 text-sm">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-lg flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5"></i>
                    <div class="flex-1 text-sm">{{ session('warning') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <div class="flex items-start gap-3 mb-2">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                        <span class="font-semibold text-sm">Terjadi Kesalahan</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-7 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            x-model="email"
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('password') border-red-500 @enderror"
                            placeholder="Masukkan password Anda"
                        >
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-[#0a66c2] border-gray-300 rounded focus:ring-[#0a66c2] cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition">Ingat saya</span>
                    </label>

                    <a href="{{ route('client.password.request') }}" class="text-sm text-[#0a66c2] hover:text-[#004182] font-medium transition">
                        Lupa password?
                    </a>
                </div>

                <!-- Info Box -->
                <div class="bg-[#0077B5]/10 border border-[#0077B5]/30 rounded-lg p-3 mt-4">
                    <div class="flex items-start gap-2 text-xs text-[#005582]">
                        <i class="fas fa-info-circle text-[#0077B5] mt-0.5"></i>
                        <div>
                            <p class="font-medium mb-1">Login Otomatis</p>
                            <p>Sistem akan otomatis mendeteksi apakah Anda admin atau klien berdasarkan email Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full btn-primary text-white font-semibold py-3 px-6 rounded-lg mt-6"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="divider-text bg-white">Belum punya akun?</span>
                </div>
            </div>

            <!-- Register Link -->
            <a href="{{ route('client.register') }}" 
               class="block w-full text-center py-3 px-6 border-2 border-[#0a66c2] text-[#0a66c2] font-semibold rounded-lg hover:bg-[#0a66c2] hover:text-white transition">
                <i class="fas fa-user-plus mr-2"></i>
                Daftar sebagai Klien
            </a>
        </div>

        <!-- Bottom Links -->
        <div class="mt-6 text-center space-y-3">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
            
            <div class="flex items-center justify-center gap-4 text-sm text-gray-500">
                <a href="https://wa.me/6283879602855" target="_blank" class="hover:text-[#0a66c2] transition">
                    <i class="fab fa-whatsapp mr-1"></i>WhatsApp
                </a>
                <span>•</span>
                <a href="tel:+6283879602855" class="hover:text-[#0a66c2] transition">
                    <i class="fas fa-phone mr-1"></i>Telepon
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-xs mt-8">
            © {{ date('Y') }} BizMark.ID - Solusi Manajemen Perizinan Terpercaya
        </p>
    </div>
    
    <script>
        // Debug CSRF Token
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('input[name="_token"]');
            if (csrfToken) {
                console.log('CSRF Token found:', csrfToken.value.substring(0, 20) + '...');
            } else {
                console.error('CSRF Token NOT FOUND!');
            }
        });
    </script>
</body>
</html>
