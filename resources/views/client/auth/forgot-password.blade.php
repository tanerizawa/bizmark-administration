<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Bizmark.id</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            <p class="text-white/80 mt-2">Portal Klien - Reset Password</p>
        </div>

        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                    <i class="fas fa-key text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Lupa Password?</h2>
                <p class="text-gray-600 mt-2">Tidak masalah! Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-3"></i>
                    <div>
                        <p class="font-semibold">Email Terkirim!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                        <p class="text-sm mt-2">Cek inbox atau folder spam Anda.</p>
                    </div>
                </div>
            @endif

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

            <form method="POST" action="{{ route('client.password.email') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Alamat Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="client@company.com"
                    >
                    <p class="text-xs text-gray-500 mt-1">Gunakan email yang terdaftar saat mendaftar</p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-purple-800 transition duration-300 shadow-lg"
                >
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Atau</span>
                </div>
            </div>

            <!-- Back to Login -->
            <div class="text-center space-y-2">
                <a href="{{ route('login') }}" class="block text-sm text-gray-600 hover:text-purple-600 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Halaman Login
                </a>
                <a href="https://wa.me/62838796028550" target="_blank" class="block text-sm text-gray-600 hover:text-purple-600">
                    <i class="fab fa-whatsapp mr-2"></i>Hubungi Support
                </a>
            </div>
        </div>

        <!-- Help Text -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mt-6">
            <h3 class="text-white font-semibold mb-2 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>Tips Keamanan
            </h3>
            <ul class="text-white/80 text-sm space-y-1">
                <li>• Link reset hanya berlaku selama 60 menit</li>
                <li>• Jangan bagikan link reset kepada siapapun</li>
                <li>• Gunakan password yang kuat (min. 8 karakter)</li>
            </ul>
        </div>

        <!-- Footer -->
        <p class="text-center text-white/60 text-sm mt-8">
            © {{ date('Y') }} Bizmark.id - Solusi Perizinan Terpercaya
        </p>
    </div>
</body>
</html>
