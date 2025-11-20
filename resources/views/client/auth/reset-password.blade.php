<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bizmark.id</title>
    
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
            <p class="text-white/80 mt-2">Portal Klien - Buat Password Baru</p>
        </div>

        <!-- Reset Password Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                    <i class="fas fa-lock text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
                <p class="text-gray-600 mt-2">Buat password baru untuk akun Anda</p>
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

            <form method="POST" action="{{ route('client.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email (readonly) -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $email) }}" 
                        required 
                        readonly
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 @error('email') border-red-500 @enderror"
                    >
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password Baru
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
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
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 mb-1">Password harus mengandung:</p>
                        <ul class="text-xs text-gray-500 space-y-1">
                            <li id="length-check">• Minimal 8 karakter</li>
                            <li id="uppercase-check">• Minimal 1 huruf besar</li>
                            <li id="lowercase-check">• Minimal 1 huruf kecil</li>
                            <li id="number-check">• Minimal 1 angka</li>
                        </ul>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
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

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-purple-700 hover:to-purple-800 transition duration-300 shadow-lg"
                >
                    <i class="fas fa-check-circle mr-2"></i>Reset Password
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Link tidak valid?</span>
                </div>
            </div>

            <!-- Back to Login -->
            <div class="text-center space-y-2">
                <a href="{{ route('client.password.request') }}" class="block text-sm text-gray-600 hover:text-purple-600 font-medium">
                    <i class="fas fa-redo mr-2"></i>Kirim Ulang Link Reset
                </a>
                <a href="{{ route('login') }}" class="block text-sm text-gray-600 hover:text-purple-600">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
                </a>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mt-6">
            <h3 class="text-white font-semibold mb-2 flex items-center">
                <i class="fas fa-shield-alt mr-2"></i>Tips Password Aman
            </h3>
            <ul class="text-white/80 text-sm space-y-1">
                <li>• Gunakan kombinasi huruf besar, kecil, angka</li>
                <li>• Jangan gunakan informasi pribadi (nama, tanggal lahir)</li>
                <li>• Jangan gunakan password yang sama di berbagai situs</li>
                <li>• Simpan password di tempat yang aman</li>
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

        // Password strength validation
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            // Check length
            const lengthCheck = document.getElementById('length-check');
            if (password.length >= 8) {
                lengthCheck.classList.add('text-green-600');
                lengthCheck.innerHTML = '• <i class="fas fa-check"></i> Minimal 8 karakter';
            } else {
                lengthCheck.classList.remove('text-green-600');
                lengthCheck.innerHTML = '• Minimal 8 karakter';
            }
            
            // Check uppercase
            const uppercaseCheck = document.getElementById('uppercase-check');
            if (/[A-Z]/.test(password)) {
                uppercaseCheck.classList.add('text-green-600');
                uppercaseCheck.innerHTML = '• <i class="fas fa-check"></i> Minimal 1 huruf besar';
            } else {
                uppercaseCheck.classList.remove('text-green-600');
                uppercaseCheck.innerHTML = '• Minimal 1 huruf besar';
            }
            
            // Check lowercase
            const lowercaseCheck = document.getElementById('lowercase-check');
            if (/[a-z]/.test(password)) {
                lowercaseCheck.classList.add('text-green-600');
                lowercaseCheck.innerHTML = '• <i class="fas fa-check"></i> Minimal 1 huruf kecil';
            } else {
                lowercaseCheck.classList.remove('text-green-600');
                lowercaseCheck.innerHTML = '• Minimal 1 huruf kecil';
            }
            
            // Check number
            const numberCheck = document.getElementById('number-check');
            if (/[0-9]/.test(password)) {
                numberCheck.classList.add('text-green-600');
                numberCheck.innerHTML = '• <i class="fas fa-check"></i> Minimal 1 angka';
            } else {
                numberCheck.classList.remove('text-green-600');
                numberCheck.innerHTML = '• Minimal 1 angka';
            }
        });
    </script>
</body>
</html>
