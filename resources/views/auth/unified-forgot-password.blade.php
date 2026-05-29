<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - Bizmark.ID</title>

    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --brand-primary: #b8860b;
            --brand-primary-dark: #8b6508;
            --brand-ink: #0f172a;
        }
        * { font-family: 'Inter', -apple-system, system-ui, sans-serif; }
        .auth-title { font-family: 'Fraunces', Georgia, serif; letter-spacing: -0.02em; }
        body {
            background:
                radial-gradient(circle at top left, rgba(184, 134, 11, 0.14), transparent 30%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.08), transparent 26%),
                linear-gradient(145deg, #faf8f3 0%, #f5efe2 48%, #ffffff 100%);
            min-height: 100vh;
            color: var(--brand-ink);
        }
        .form-input-gold:focus { border-color: var(--brand-primary); outline: none; box-shadow: 0 0 0 3px rgba(184, 134, 11, 0.18); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen py-12 px-4">

    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/">
                <img src="{{ asset('images/logo-white.png') }}" alt="Bizmark.ID" class="h-10 mx-auto"
                     onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block'">
                <p id="logo-fallback" class="auth-title text-2xl font-bold" style="display:none; color: var(--brand-ink);">Bizmark.ID</p>
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-8" style="border: 1px solid rgba(184, 134, 11, 0.18);">

            <div class="text-center mb-6">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background: rgba(184, 134, 11, 0.12);">
                    <i class="fas fa-key text-2xl" style="color: var(--brand-primary);"></i>
                </div>
                <h1 class="auth-title text-2xl font-bold text-gray-900">Lupa Password?</h1>
                <p class="text-sm text-gray-500 mt-2">
                    Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset password.
                </p>
            </div>

            @if(session('status'))
                <div class="mb-5 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 flex items-start gap-3">
                    <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                    <span class="text-sm">{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-600 rounded-xl p-4">
                    <ul class="text-sm space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="email@example.com"
                            class="form-input-gold w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm transition"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl text-sm font-semibold text-white transition-all"
                    style="background: var(--brand-primary);"
                    onmouseover="this.style.background='var(--brand-primary-dark)'"
                    onmouseout="this.style.background='var(--brand-primary)'"
                >
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Link Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium hover:underline" style="color: var(--brand-primary);">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke halaman login
                </a>
            </div>
        </div>

        <p class="text-center text-xs mt-6" style="color: rgba(15, 23, 42, 0.45);">
            &copy; {{ date('Y') }} Bizmark.ID · Semua hak dilindungi
        </p>
    </div>

</body>
</html>
