<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Bizmark.id</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">

    <style>
        :root {
            --brand-primary: #0a66c2;
            --brand-primary-dark: #084e96;
            --brand-secondary: #00a0dc;
            --brand-success: #10b981;
            --brand-danger: #ef4444;
            --text-muted: #475569;
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
            width: min(430px, 100%);
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

        .logo-badge {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(10, 102, 194, 0.12), rgba(0, 160, 220, 0.16));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
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
            border: 1.5px solid #d1d5db;
            color: #374151;
            background: #ffffff;
            transition: all 0.25s ease;
        }

        .btn-outline:hover {
            background: #f8fafc;
        }

        .alert-success {
            background-color: #d1fae5;
            border-left: 4px solid var(--brand-success);
        }

        .info-box {
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.95) 0%, rgba(219, 234, 254, 0.85) 100%);
            border: 1px solid #bfdbfe;
        }

        .back-link {
            color: var(--brand-primary);
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--brand-primary-dark);
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

        @media (max-width: 640px) {
            body {
                padding: 14px 10px;
                align-items: flex-start;
            }

            .px-mobile {
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
        <section class="card">
            <header class="px-6 py-4 border-b border-slate-100 text-center px-mobile">
                <div class="logo-badge" aria-hidden="true">
                    <i class="fas fa-envelope-open-text text-2xl" style="color: var(--brand-primary);"></i>
                </div>
                <p class="text-xs uppercase tracking-[0.18em] font-semibold mb-1" style="color: var(--brand-primary);">Portal Klien</p>
                <h1 class="text-2xl font-bold text-slate-900">Verifikasi Email</h1>
                <p class="text-sm mt-1" style="color: var(--text-muted);">Aktifkan akun Anda untuk mulai menggunakan portal.</p>
            </header>

            <div class="px-6 py-4 space-y-3 px-mobile">
                @if (session('success'))
                    <div class="alert-success p-3 rounded-lg" role="alert">
                        <p class="text-sm font-semibold text-green-800">Berhasil!</p>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                <div class="info-box rounded-lg p-3">
                    <p class="text-sm text-slate-700 leading-relaxed">
                        Kami telah mengirim link verifikasi ke <strong>{{ Auth::guard('client')->user()->email }}</strong>.
                        Jika belum masuk, cek folder spam/junk lalu kirim ulang.
                    </p>
                </div>

                <form method="POST" action="{{ route('client.verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full text-white font-semibold py-2.5 px-4 rounded-lg text-sm">
                        <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline w-full font-semibold py-2.5 px-4 rounded-lg text-sm">
                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i>
                        Logout
                    </button>
                </form>
            </div>

            <footer class="px-6 py-3 border-t border-slate-100 text-center text-xs px-mobile" style="color: var(--text-muted);">
                Butuh bantuan?
                <a href="{{ config('landing_metrics.contact.whatsapp_link', 'https://wa.me/6283879602855') }}" target="_blank" rel="noopener noreferrer" class="back-link font-semibold">WhatsApp</a>
                atau
                <a href="mailto:{{ config('landing_metrics.contact.email', 'info@bizmark.id') }}" class="back-link font-semibold">Email</a>
            </footer>
        </section>

        <p class="text-center text-xs mt-3" style="color: var(--text-muted);">&copy; {{ date('Y') }} Bizmark.id</p>
    </main>
</body>
</html>
