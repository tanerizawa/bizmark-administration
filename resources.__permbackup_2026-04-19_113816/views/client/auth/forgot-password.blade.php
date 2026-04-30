<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Bizmark.ID</title>

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

        .page-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
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

        .icon-badge {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(10, 102, 194, 0.25);
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

    <div class="w-full max-w-md page-container">
        <!-- Back to Login -->
        <div class="mb-6 text-center">
            <a href="{{ route('login') }}" class="back-link inline-flex items-center gap-2 text-sm font-medium" aria-label="Kembali ke halaman login">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Kembali ke Login
            </a>
        </div>

        <!-- Forgot Password Card -->
        <div class="card rounded-2xl overflow-hidden" role="main">
            <div class="p-8 pb-6 text-center">
                <div class="icon-badge mb-4" aria-hidden="true">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h1>
                <p class="text-gray-600">Tidak masalah! Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>
            </div>

            <div class="px-8 pb-8">
                @if (session('success'))
                    <div class="mb-6 alert-success p-4 rounded-lg" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-green-800 mb-1">Email Terkirim!</p>
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                                <p class="text-xs text-green-600 mt-1">Cek inbox atau folder spam Anda.</p>
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

                @if ($errors->any())
                    <div class="mb-6 alert-error p-4 rounded-lg" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3" aria-hidden="true"></i>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</p>
                                @foreach ($errors->all() as $error)
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('client.password.email') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1 text-gray-400" aria-hidden="true"></i>
                            Alamat Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            aria-describedby="email-help {{ $errors->has('email') ? 'email-error' : '' }}"
                            class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com"
                        >
                        <p id="email-help" class="text-xs text-gray-500 mt-1.5">Gunakan email yang terdaftar saat mendaftar</p>
                    </div>

                    <button
                        type="submit"
                        class="btn-primary w-full flex justify-center items-center py-3.5 px-4 border-0 rounded-lg text-base font-semibold text-white"
                    >
                        <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                        Kirim Link Reset Password
                    </button>
                </form>

                <!-- Info Banner -->
                <div class="info-banner rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <i class="fas fa-shield-halved text-blue-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Tips Keamanan</p>
                            <ul class="text-xs text-gray-600 space-y-0.5">
                                <li>Link reset berlaku selama 60 menit</li>
                                <li>Jangan bagikan link reset kepada siapapun</li>
                                <li>Gunakan password yang kuat (min. 8 karakter)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100">
                <p class="text-center text-sm text-gray-600">
                    &copy; {{ date('Y') }} <strong class="text-gray-900">Bizmark.ID</strong> - PT Cangah Pajaratan Mandiri
                </p>
            </div>
        </div>

        <!-- Support -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-3">Masih butuh bantuan?</p>
            <div class="flex justify-center gap-4">
                @php
                    $whatsappLink = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                @endphp
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-lg shadow-sm hover:shadow-md transition text-sm text-gray-700 font-medium">
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
        // Auto-hide alerts after 8 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-error');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 500);
                }, 8000);
            });
        });
    </script>
</body>
</html>
