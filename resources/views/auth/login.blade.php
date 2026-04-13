<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - Bizmark.ID</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand-primary: #0A66C2;
            --brand-primary-dark: #084E96;
            --brand-secondary: #00A0DC;
            --brand-accent: #F97316;
            --brand-success: #10B981;
            --brand-danger: #EF4444;
        }

        * {
            font-family: 'Inter', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 50%, #BFDBFE 100%);
            min-height: 100vh;
        }

        .login-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 8px 32px rgba(10, 102, 194, 0.12);
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #E5E7EB;
            background-color: #FFFFFF;
        }

        .form-input:focus {
            border-color: var(--brand-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 102, 194, 0.15);
            background-color: #FFFFFF;
        }

        .form-input:hover {
            border-color: #D1D5DB;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(10, 102, 194, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(10, 102, 194, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .back-link {
            color: var(--brand-primary);
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: var(--brand-primary-dark);
            text-decoration: underline;
        }

        .checkbox-custom {
            accent-color: var(--brand-primary);
            width: 1.125rem;
            height: 1.125rem;
        }

        .logo-badge {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(10, 102, 194, 0.3);
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
            opacity: 0.08;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            background: var(--brand-primary);
            top: -200px;
            right: -100px;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            background: var(--brand-secondary);
            bottom: -150px;
            left: -150px;
        }

        .alert-error {
            background-color: #FEE2E2;
            border-left: 4px solid var(--brand-danger);
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background-color: #D1FAE5;
            border-left: 4px solid var(--brand-success);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
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
            <a href="{{ route('landing.id') }}" class="back-link inline-flex items-center gap-2 text-sm font-medium" aria-label="Kembali ke halaman beranda">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Kembali ke Beranda
            </a>
        </div>

        <!-- Login Card -->
        <div class="login-card rounded-2xl overflow-hidden" role="main">
            <!-- Logo & Header -->
            <div class="p-8 pb-6 text-center">
                <div class="logo-badge" aria-hidden="true">
                    <i class="fas fa-shield-halved text-white text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Panel</h1>
                <p class="text-gray-600">Akses internal Sistem Manajemen Perizinan</p>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                @if ($errors->any())
                    <div class="mb-6 alert-error p-4 rounded-lg" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800 mb-1">Login Gagal</p>
                                <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 alert-success p-4 rounded-lg" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3" aria-hidden="true"></i>
                            <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ url('/__REDACTED_LEGACY_ADMIN_SEGMENT__') }}" class="space-y-5" novalidate>
                    @csrf

                    <!-- Email/Username Field -->
                    <div>
                        <label for="login" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1 text-gray-400" aria-hidden="true"></i>
                            Username atau Email
                        </label>
                        <input
                            id="login"
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            required
                            autocomplete="username"
                            autofocus
                            aria-describedby="{{ $errors->has('login') ? 'login-error' : '' }}"
                            class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 @error('login') border-red-500 @enderror"
                            placeholder="Masukkan username atau email"
                        >
                        @error('login')
                            <p id="login-error" class="mt-2 text-sm text-red-600 flex items-center" role="alert">
                                <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-400" aria-hidden="true"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                                class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 pr-12 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password Anda"
                            >
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i id="toggleIcon" class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="mt-2 text-sm text-red-600 flex items-center" role="alert">
                                <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                                class="checkbox-custom rounded cursor-pointer"
                            >
                            <span class="ml-2 text-sm text-gray-700 select-none">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn-primary w-full flex justify-center items-center py-3.5 px-4 border-0 rounded-lg text-base font-semibold text-white"
                    >
                        <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                        Masuk ke Admin Panel
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Informasi</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="info-banner rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                Halaman ini khusus untuk akses admin. Gunakan kredensial yang telah diberikan untuk masuk ke sistem manajemen perizinan.
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
            <p class="text-sm text-gray-600 mb-3">Butuh bantuan untuk login?</p>
            <div class="flex justify-center gap-4">
                <a href="{{ config('landing_metrics.contact.whatsapp_link') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition text-sm text-gray-700 font-medium">
                    <i class="fab fa-whatsapp text-green-500" aria-hidden="true"></i>
                    WhatsApp
                </a>
                <a href="mailto:{{ config('landing_metrics.contact.email') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition text-sm text-gray-700 font-medium">
                    <i class="fas fa-envelope text-blue-500" aria-hidden="true"></i>
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
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
