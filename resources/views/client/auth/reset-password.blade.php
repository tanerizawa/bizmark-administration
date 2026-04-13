<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Bizmark.ID</title>

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

        .form-input[readonly] {
            background-color: #F9FAFB;
            cursor: not-allowed;
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

        .strength-check {
            transition: color 0.2s ease;
        }

        .strength-check.valid {
            color: var(--brand-success);
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

        <!-- Reset Password Card -->
        <div class="card rounded-2xl overflow-hidden" role="main">
            <div class="p-8 pb-6 text-center">
                <div class="icon-badge mb-4" aria-hidden="true">
                    <i class="fas fa-lock-open text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                <p class="text-gray-600">Buat password baru untuk akun Anda</p>
            </div>

            <div class="px-8 pb-8">
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

                <form method="POST" action="{{ route('client.password.update') }}" class="space-y-5" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email (readonly) -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1 text-gray-400" aria-hidden="true"></i>
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            required
                            readonly
                            class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 @error('email') border-red-500 @enderror"
                        >
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-400" aria-hidden="true"></i>
                            Password Baru
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                aria-describedby="password-requirements"
                                class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 pr-12 @error('password') border-red-500 @enderror"
                                placeholder="Minimal 8 karakter"
                            >
                            <button
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i class="fas fa-eye" id="password-icon" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-requirements" class="mt-2">
                            <p class="text-xs text-gray-600 mb-1">Password harus mengandung:</p>
                            <ul class="text-xs text-gray-500 space-y-0.5">
                                <li id="length-check" class="strength-check flex items-center gap-1">
                                    <i class="fas fa-circle text-[6px]" aria-hidden="true"></i>
                                    Minimal 8 karakter
                                </li>
                                <li id="uppercase-check" class="strength-check flex items-center gap-1">
                                    <i class="fas fa-circle text-[6px]" aria-hidden="true"></i>
                                    Minimal 1 huruf besar
                                </li>
                                <li id="lowercase-check" class="strength-check flex items-center gap-1">
                                    <i class="fas fa-circle text-[6px]" aria-hidden="true"></i>
                                    Minimal 1 huruf kecil
                                </li>
                                <li id="number-check" class="strength-check flex items-center gap-1">
                                    <i class="fas fa-circle text-[6px]" aria-hidden="true"></i>
                                    Minimal 1 angka
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-400" aria-hidden="true"></i>
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                class="form-input block w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 pr-12"
                                placeholder="Ketik ulang password"
                            >
                            <button
                                type="button"
                                onclick="togglePassword('password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password"
                            >
                                <i class="fas fa-eye" id="password_confirmation-icon" aria-hidden="true"></i>
                            </button>
                        </div>
                        <p id="match-status" class="mt-1.5 text-xs hidden"></p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="btn-primary w-full flex justify-center items-center py-3.5 px-4 border-0 rounded-lg text-base font-semibold text-white"
                    >
                        <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                        Reset Password
                    </button>
                </form>

                <!-- Links -->
                <div class="mt-6 text-center space-y-2">
                    <a href="{{ route('client.password.request') }}" class="block text-sm back-link font-medium">
                        <i class="fas fa-redo mr-1" aria-hidden="true"></i>
                        Kirim Ulang Link Reset
                    </a>
                </div>

                <!-- Info Banner -->
                <div class="info-banner rounded-lg p-4 mt-6">
                    <div class="flex items-start">
                        <i class="fas fa-shield-halved text-blue-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Tips Password Aman</p>
                            <ul class="text-xs text-gray-600 space-y-0.5">
                                <li>Gunakan kombinasi huruf besar, kecil, dan angka</li>
                                <li>Jangan gunakan informasi pribadi</li>
                                <li>Jangan gunakan password yang sama di situs lain</li>
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
            <p class="text-sm text-gray-600 mb-3">Link tidak berfungsi?</p>
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
        const confirmInput = document.getElementById('password_confirmation');
        const matchStatus = document.getElementById('match-status');

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            updateCheck('length-check', password.length >= 8, 'Minimal 8 karakter');
            updateCheck('uppercase-check', /[A-Z]/.test(password), 'Minimal 1 huruf besar');
            updateCheck('lowercase-check', /[a-z]/.test(password), 'Minimal 1 huruf kecil');
            updateCheck('number-check', /[0-9]/.test(password), 'Minimal 1 angka');

            // Check match if confirm has value
            if (confirmInput.value) {
                checkMatch();
            }
        });

        confirmInput.addEventListener('input', checkMatch);

        function updateCheck(id, isValid, text) {
            const el = document.getElementById(id);
            const iconClass = isValid ? 'fa-check-circle' : 'fa-circle';
            el.className = 'strength-check flex items-center gap-1' + (isValid ? ' valid' : '');
            el.innerHTML = '<i class="fas ' + iconClass + ' text-[6px]" aria-hidden="true"></i> ' + text;
        }

        function checkMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (!confirm) {
                matchStatus.classList.add('hidden');
                return;
            }

            matchStatus.classList.remove('hidden');
            if (password === confirm) {
                matchStatus.className = 'mt-1.5 text-xs text-green-600';
                matchStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Password cocok';
            } else {
                matchStatus.className = 'mt-1.5 text-xs text-red-500';
                matchStatus.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Password tidak cocok';
            }
        }
    </script>
</body>
</html>
