<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - Bizmark.ID | Portal Klien</title>
    <meta name="description" content="Daftar akun Portal Klien Bizmark.ID untuk monitoring proyek perizinan usaha Anda secara real-time.">

    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand-primary: #0a66c2;
            --brand-primary-dark: #084e96;
            --brand-secondary: #00a0dc;
            --brand-success: #10b981;
            --brand-danger: #ef4444;
            --text-muted: #475569;
            --text-soft: #64748b;
            --surface-strong: rgba(255, 255, 255, 0.95);
            --border-soft: rgba(148, 163, 184, 0.3);
        }

        * {
            font-family: 'Inter', -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(0, 160, 220, 0.15), transparent 28%),
                radial-gradient(circle at bottom right, rgba(10, 102, 194, 0.12), transparent 24%),
                linear-gradient(145deg, #eff6ff 0%, #dbeafe 46%, #f8fafc 100%);
            min-height: 100vh;
        }

        .page-shell {
            width: min(860px, 100%);
            margin: 0 auto;
            animation: fadeInUp 0.45s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: var(--surface-strong);
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .field-full {
            grid-column: 1 / -1;
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

        .btn-outline {
            border: 1.5px solid rgba(10, 102, 194, 0.28);
            color: var(--brand-primary);
            background: rgba(10, 102, 194, 0.04);
            transition: all 0.25s ease;
        }

        .btn-outline:hover {
            background: rgba(10, 102, 194, 0.09);
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
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(10, 102, 194, 0.12), rgba(0, 160, 220, 0.16));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .alert-error {
            background-color: #fee2e2;
            border-left: 4px solid var(--brand-danger);
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
            width: 320px;
            height: 320px;
            background: var(--brand-primary);
            top: -150px;
            right: -110px;
        }

        .circle-2 {
            width: 240px;
            height: 240px;
            background: var(--brand-secondary);
            bottom: -120px;
            left: -120px;
        }

        .strength-check {
            transition: color 0.2s ease;
        }

        .strength-check.valid {
            color: var(--brand-success);
        }

        @media (max-height: 900px) and (min-width: 1024px) {
            body {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .tight-space {
                margin-bottom: 0.35rem;
            }
        }

        @media (max-width: 760px) {
            body {
                padding: 14px 10px;
                align-items: flex-start;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .px-mobile {
                padding-left: 16px;
                padding-right: 16px;
            }

            .header-stack {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
        <div class="mb-3 text-center tight-space">
            <a href="{{ route('login') }}" class="back-link inline-flex items-center gap-2 text-xs font-medium" aria-label="Kembali ke halaman login">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Kembali ke Login
            </a>
        </div>

        <section class="card">
            <header class="px-6 py-4 border-b border-slate-100 px-mobile">
                <div class="flex items-center justify-between gap-3 header-stack">
                    <div class="flex items-center gap-3">
                        <div class="logo-badge" aria-hidden="true">
                            <i class="fas fa-user-plus text-xl" style="color: var(--brand-primary);"></i>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.18em] font-semibold" style="color: var(--brand-primary);">Portal Klien</p>
                            <h1 class="text-xl font-bold text-slate-900">Buat Akun</h1>
                        </div>
                    </div>
                    <p class="text-xs" style="color: var(--text-soft);">Cepat, aman, dan langsung aktif.</p>
                </div>
            </header>

            <div class="px-6 py-4 px-mobile">
                @if ($errors->any())
                    <div class="mb-3 alert-error p-3 rounded-lg" role="alert">
                        <p class="text-sm font-semibold text-red-800 mb-1">Terjadi Kesalahan</p>
                        @foreach ($errors->all() as $error)
                            <p class="text-xs text-red-700">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('client.register') }}" class="space-y-3" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap *</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
                                class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 @error('name') border-red-500 @enderror"
                                placeholder="Nama lengkap"
                            >
                            @error('name')
                                <p id="name-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1.5">Perusahaan</label>
                            <input
                                type="text"
                                id="company_name"
                                name="company_name"
                                value="{{ old('company_name') }}"
                                autocomplete="organization"
                                class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400"
                                placeholder="Opsional"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                                class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 @error('email') border-red-500 @enderror"
                                placeholder="nama@email.com"
                            >
                            @error('email')
                                <p id="email-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">No. Telepon</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400"
                                placeholder="08xx-xxxx-xxxx"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password *</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                    aria-describedby="password-requirements {{ $errors->has('password') ? 'password-error' : '' }}"
                                    class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 pr-11 @error('password') border-red-500 @enderror"
                                    placeholder="Minimal 8 karakter"
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition"
                                    aria-label="Tampilkan atau sembunyikan password"
                                >
                                    <i class="fas fa-eye" id="password-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                            <div id="password-requirements" class="mt-1.5">
                                <ul class="text-[11px] text-gray-500 space-y-0.5">
                                    <li id="length-check" class="strength-check flex items-center gap-1"> <i class="fas fa-circle text-[6px]" aria-hidden="true"></i> Minimal 8 karakter</li>
                                    <li id="uppercase-check" class="strength-check flex items-center gap-1"> <i class="fas fa-circle text-[6px]" aria-hidden="true"></i> Minimal 1 huruf besar</li>
                                    <li id="number-check" class="strength-check flex items-center gap-1"> <i class="fas fa-circle text-[6px]" aria-hidden="true"></i> Minimal 1 angka</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password *</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                    class="form-input block w-full px-3.5 py-2.5 rounded-lg text-gray-900 placeholder-gray-400 pr-11"
                                    placeholder="Ulangi password"
                                >
                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition"
                                    aria-label="Tampilkan atau sembunyikan konfirmasi password"
                                >
                                    <i class="fas fa-eye" id="password_confirmation-icon" aria-hidden="true"></i>
                                </button>
                            </div>
                            <p id="match-status" class="mt-1 text-xs hidden"></p>
                        </div>

                        <div class="field-full pt-1">
                            <label class="flex items-start cursor-pointer text-sm">
                                <input type="checkbox" name="terms" required class="checkbox-custom mt-0.5 rounded cursor-pointer">
                                <span class="ml-2 text-gray-600 select-none">
                                    Saya setuju dengan
                                    <a href="{{ route('terms.conditions.id') }}" target="_blank" class="back-link font-medium">Syarat & Ketentuan</a>
                                    dan
                                    <a href="{{ route('privacy.policy.id') }}" target="_blank" class="back-link font-medium">Kebijakan Privasi</a>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1">
                        <button type="submit" class="btn-primary w-full flex justify-center items-center py-2.5 px-4 border-0 rounded-lg text-sm font-semibold text-white">
                            <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                            Daftar
                        </button>

                        <a href="{{ route('login') }}" class="btn-outline w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold">
                            <i class="fas fa-sign-in-alt mr-2" aria-hidden="true"></i>
                            Sudah punya akun
                        </a>
                    </div>
                </form>
            </div>

            <div class="px-6 py-3 border-t border-slate-100 text-center text-xs text-slate-500 px-mobile">
                Butuh bantuan? <a href="{{ config('landing_metrics.contact.whatsapp_link', 'https://wa.me/6283879602855') }}" class="back-link font-semibold" target="_blank" rel="noopener noreferrer">WhatsApp</a> atau
                <a href="mailto:{{ config('landing_metrics.contact.email', 'info@bizmark.id') }}" class="back-link font-semibold">Email</a>
            </div>
        </section>
    </main>

    <script>
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

        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const matchStatus = document.getElementById('match-status');

        passwordInput.addEventListener('input', function() {
            const password = this.value;

            updateCheck('length-check', password.length >= 8, 'Minimal 8 karakter');
            updateCheck('uppercase-check', /[A-Z]/.test(password), 'Minimal 1 huruf besar');
            updateCheck('number-check', /[0-9]/.test(password), 'Minimal 1 angka');

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
                matchStatus.className = 'mt-1 text-xs text-green-600';
                matchStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Password cocok';
            } else {
                matchStatus.className = 'mt-1 text-xs text-red-500';
                matchStatus.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Password tidak cocok';
            }
        }
    </script>
</body>
</html>
