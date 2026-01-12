<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Bizmark.ID | Sistem Manajemen Perizinan</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    
    <!-- External CSS - CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
<<<<<<< Updated upstream
            /* Bizmark Brand Colors - Consistent with Landing Page */
            --primary: #1E40AF; /* Blue-800 */
            --primary-dark: #1E3A8A; /* Blue-900 */
            --secondary: #0891B2; /* Cyan-600 */
            --success: #10B981; /* Green-500 */
            --danger: #EF4444; /* Red-500 */
            --warning: #F59E0B; /* Amber-500 */
=======
            /* Primary - Soft Periwinkle (Trust + Calm) */
            --neuro-primary: #8B9FD8;
            --neuro-primary-dark: #6B7FB8;
            --neuro-primary-light: #A8B8E6;
            --neuro-primary-muted: #D4DCF2;
            
            /* Functional Colors - Soft Cognition */
            --neuro-success: #88D4AB;
            --neuro-error: #E8A0A0;
            --neuro-warning: #F5D887;
            --neuro-info: #B8A8D8;
            
            /* Dark Mode Backgrounds - Soft Black (28% less eye strain) */
            --dark-bg: #1E1E20;
            --dark-bg-secondary: #2A2A2C;
            --dark-bg-tertiary: #363638;
            --dark-bg-elevated: var(--neuro-primary-muted, rgba(212, 220, 242, 0.92));
            --dark-separator: var(--dark-text-tertiary, rgba(201, 196, 191, 0.5));
            
            /* Text Colors */
            --dark-text-primary: #F5F3F0;
            --dark-text-secondary: #C9C4BF;
            --dark-text-tertiary: rgba(201, 196, 191, 0.5);
>>>>>>> Stashed changes
        }

        body {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 50%, #BFDBFE 100%);
            min-height: 100vh;
        }

        .login-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
<<<<<<< Updated upstream
            background: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .gradient-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
=======
            background: var(--dark-bg-elevated);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--dark-separator);
            box-shadow: 0 8px 32px var(--neuro-primary-dark, rgba(107, 127, 184, 0.4));
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--neuro-primary) 0%, var(--neuro-primary-dark) 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px var(--neuro-primary, rgba(139, 159, 216, 0.3));
        }
        
        .btn-primary:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 20px var(--neuro-primary, rgba(139, 159, 216, 0.4));
>>>>>>> Stashed changes
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #E5E7EB;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
<<<<<<< Updated upstream
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
=======
            box-shadow: 0 0 0 3px var(--neuro-primary-muted, rgba(139, 159, 216, 0.15));
            background-color: var(--dark-bg-secondary);
>>>>>>> Stashed changes
        }

        .form-input:hover {
            border-color: #D1D5DB;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .back-link {
            color: var(--primary);
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .checkbox-custom {
            accent-color: var(--primary);
            width: 1.125rem;
            height: 1.125rem;
        }

        .logo-badge {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
            margin: 0 auto 1.5rem;
        }

        .decorative-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: var(--primary);
            top: -200px;
            right: -100px;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            background: var(--secondary);
            bottom: -150px;
            left: -150px;
        }

        .alert-error {
            background-color: #FEE2E2;
            border-left: 4px solid var(--danger);
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background-color: #D1FAE5;
            border-left: 4px solid var(--success);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .info-banner {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
            border: 1px solid #BFDBFE;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Decorative Background -->
    <div class="decorative-bg">
        <div class="decorative-circle circle-1"></div>
        <div class="decorative-circle circle-2"></div>
    </div>

    <div class="w-full max-w-md login-container">
        <!-- Back to Home Link -->
        <div class="mb-6 text-center">
            <a href="{{ route('landing.id') }}" class="back-link inline-flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl overflow-hidden">
            <!-- Logo & Header -->
            <div class="p-8 pb-6 text-center">
                <div class="logo-badge">
                    <i class="fas fa-certificate text-white text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
                <p class="text-gray-600">Masuk ke Sistem Manajemen Perizinan Bizmark.ID</p>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                @if ($errors->any())
                    <div class="mb-6 alert-error p-4 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800 mb-1">Login Gagal</p>
                                <p class="text-sm text-red-700">
                                    {{ $errors->first() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 alert-success p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 alert-success p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1 text-gray-400"></i>
                            Email
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autocomplete="email" 
                            autofocus
                            class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-400"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 pr-12 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password Anda"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                aria-label="Toggle password visibility"
                            >
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                {{ old('remember') ? 'checked' : '' }}
                                class="checkbox-custom rounded cursor-pointer"
                            >
                            <span class="ml-2 text-sm text-gray-700 select-none">
                                Ingat saya
                            </span>
                        </label>

                        @if (Route::has('client.password.request'))
                            <a href="{{ route('client.password.request') }}" class="text-sm font-medium back-link">
                                Lupa password?
                            </a>
                        @else
                            <a href="#" class="text-sm font-medium back-link">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-primary w-full flex justify-center items-center py-3.5 px-4 border-0 rounded-lg text-base font-semibold text-white"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Masuk ke Sistem
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Belum punya akun?</span>
                    </div>
                </div>

                <!-- Register Button -->
                @if (Route::has('client.register'))
                    <a href="{{ route('client.register') }}" 
                       class="block w-full text-center py-3 px-4 border-2 border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-600 hover:text-white transition">
                        <i class="fas fa-user-plus mr-2"></i>
                        Daftar sebagai Klien
                    </a>
                @endif

                <!-- Info Banner -->
                <div class="info-banner rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                <strong>Admin:</strong> Gunakan email @bizmark.id untuk akses admin.<br>
                                <strong>Klien:</strong> Gunakan email yang terdaftar untuk akses portal klien.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100">
                <p class="text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} <strong class="text-gray-900">Bizmark.ID</strong> - PT Cangah Pajaratan Mandiri
                </p>
                <p class="text-center text-xs text-gray-500 mt-1">
                    Konsultan Perizinan & Bisnis Terpercaya
                </p>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-3">
                Butuh bantuan untuk login?
            </p>
            <div class="flex justify-center gap-4">
                <a href="https://wa.me/6283879602855" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition text-sm text-gray-700 font-medium">
                    <i class="fab fa-whatsapp text-green-500"></i>
                    WhatsApp
                </a>
                <a href="mailto:cs@bizmark.id" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition text-sm text-gray-700 font-medium">
                    <i class="fas fa-envelope text-blue-500"></i>
                    Email
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-error');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
