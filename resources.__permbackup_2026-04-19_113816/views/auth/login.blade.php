<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login - Bizmark.ID</title>

    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand-ink: #0f172a;
            --brand-primary: #0b63c7;
            --brand-primary-dark: #0a4b93;
            --brand-secondary: #17a3b8;
            --brand-success: #10b981;
            --brand-danger: #ef4444;
            --surface-strong: rgba(255, 255, 255, 0.95);
            --border-soft: rgba(148, 163, 184, 0.28);
            --text-muted: #475569;
            --text-soft: #64748b;
        }

        * {
            font-family: 'Inter', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(23, 163, 184, 0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(11, 99, 199, 0.12), transparent 24%),
                linear-gradient(145deg, #eff6ff 0%, #dbeafe 46%, #f8fafc 100%);
            min-height: 100vh;
            color: var(--brand-ink);
        }

        .page-shell {
            width: min(430px, 100%);
            margin: 0 auto;
            animation: fadeInUp 0.45s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-panel {
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            overflow: hidden;
            background: var(--surface-strong);
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .form-input {
            transition: all 0.25s ease;
            border: 1.5px solid #dbe2ea;
            background-color: #ffffff;
        }

        .form-input:focus {
            border-color: var(--brand-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 102, 194, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(10, 102, 194, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(10, 102, 194, 0.35);
        }

        .back-link {
            color: var(--brand-primary);
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--brand-primary-dark);
        }

        .checkbox-custom {
            accent-color: var(--brand-primary);
            width: 1.05rem;
            height: 1.05rem;
        }

        .logo-badge {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(11, 99, 199, 0.12) 0%, rgba(23, 163, 184, 0.16) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin: 0 auto 0.85rem;
        }

        .decorative-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
        }

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            filter: blur(8px);
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            background: var(--brand-primary);
            top: -140px;
            right: -100px;
        }

        .circle-2 {
            width: 220px;
            height: 220px;
            background: var(--brand-secondary);
            bottom: -110px;
            left: -110px;
        }

        .alert-error {
            background-color: #fee2e2;
            border-left: 4px solid var(--brand-danger);
        }

        .alert-success {
            background-color: #d1fae5;
            border-left: 4px solid var(--brand-success);
        }

        .support-mini {
            border-top: 1px solid #e5e7eb;
            background: rgba(248, 250, 252, 0.8);
        }

        @media (max-height: 920px) and (min-width: 640px) {
            body {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .compact-space {
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 14px 10px;
                align-items: flex-start;
            }

            .auth-content,
            .auth-header,
            .auth-footer {
                padding-left: 16px;
                padding-right: 16px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-3 lg:p-4">
    <div class="decorative-bg">
        <div class="decorative-circle circle-1"></div>
        <div class="decorative-circle circle-2"></div>
    </div>

    <main class="page-shell" role="main">
        <section class="auth-panel">
            <div class="auth-header p-5 pb-3 text-center">
                <a href="{{ route('landing.id') }}" class="back-link inline-flex items-center gap-2 text-xs font-medium compact-space" aria-label="Kembali ke halaman beranda">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Kembali ke Beranda
                </a>
                <div class="logo-badge" aria-hidden="true">
                    <i class="fas fa-user-lock text-2xl" style="color: var(--brand-primary);"></i>
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] mb-1" style="color: var(--brand-primary);">Admin Access</p>
                <h1 class="text-2xl font-bold mb-1 text-gray-900">Masuk</h1>
                <p class="text-sm" style="color: var(--text-muted);">Login cepat ke panel internal.</p>
            </div>

            <div class="auth-content px-5 pb-5">
                @if ($errors->any())
                    <div class="mb-3 alert-error p-3 rounded-lg" role="alert">
                        <p class="text-sm font-semibold text-red-800 mb-1">Login Gagal</p>
                        <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-3 alert-success p-3 rounded-lg" role="alert">
                        <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-3" novalidate>
                    @csrf

                    <div>
                        <label for="login" class="block text-sm font-semibold text-gray-700 mb-1.5">Username atau Email</label>
                        <input
                            id="login"
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            required
                            autocomplete="username"
                            autofocus
                            aria-describedby="{{ $errors->has('login') ? 'login-error' : '' }}"
                            class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 @error('login') border-red-500 @enderror"
                            placeholder="username atau email"
                        >
                        @error('login')
                            <p id="login-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                                class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 pr-11 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan password"
                            >
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i id="toggleIcon" class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center cursor-pointer text-sm">
                        <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} class="checkbox-custom rounded cursor-pointer">
                        <span class="ml-2 text-gray-700 select-none">Ingat saya</span>
                    </label>

                    <button type="submit" class="btn-primary w-full flex justify-center items-center py-2.5 px-4 border-0 rounded-lg text-sm font-semibold text-white">
                        <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                        Masuk ke Panel
                    </button>
                </form>
            </div>

            <div class="support-mini px-5 py-3 text-center text-xs" style="color: var(--text-soft);">
                Bantuan internal:
                <a href="{{ config('landing_metrics.contact.whatsapp_link', 'https://wa.me/6283879602855') }}" class="back-link font-semibold" target="_blank" rel="noopener noreferrer">WhatsApp</a> atau
                <a href="mailto:{{ config('landing_metrics.contact.email', 'info@bizmark.id') }}" class="back-link font-semibold">Email</a>
            </div>

            <div class="auth-footer px-5 py-3 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">&copy; {{ date('Y') }} Bizmark.ID</p>
            </div>
        </section>
    </main>

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

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-success, .alert-error');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.4s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() { alert.remove(); }, 400);
                }, 5000);
            });
        });
    </script>
</body>
</html>
