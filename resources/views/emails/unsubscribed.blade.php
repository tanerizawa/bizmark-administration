<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhenti Berlangganan - Bizmark.ID</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px rgba(15,23,42,.08);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef3c7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap i { font-size: 2rem; color: #d97706; }
        h1 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: .75rem; }
        p { color: #64748b; font-size: .95rem; line-height: 1.65; margin-bottom: 1rem; }
        .email-badge {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            padding: .35rem 1rem;
            font-size: .85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 1.5rem;
            word-break: break-all;
        }
        .divider { border: 0; border-top: 1px solid #f1f5f9; margin: 1.5rem 0; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .75rem 1.75rem;
            border-radius: .875rem;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .18s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #0f172a;
            color: #fff;
        }
        .btn-primary:hover { background: #1e293b; }
        .btn-ghost {
            background: #f1f5f9;
            color: #475569;
        }
        .btn-ghost:hover { background: #e2e8f0; }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; justify-content: center; }
        .footer-note {
            margin-top: 2rem;
            font-size: .8rem;
            color: #94a3b8;
        }
        .footer-note a { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <i class="fas fa-bell-slash"></i>
        </div>

        <h1>Berhasil Berhenti Berlangganan</h1>

        <p>Anda telah berhasil berhenti berlangganan dari newsletter Bizmark.ID.</p>

        <div class="email-badge">
            <i class="fas fa-envelope mr-1"></i>
            {{ $subscriber->email }}
        </div>

        <p style="font-size:.85rem;">
            Anda tidak akan menerima email pemasaran dari kami lagi.
            Jika ini adalah kesalahan, Anda dapat berlangganan kembali kapan saja.
        </p>

        <hr class="divider">

        <div class="actions">
            <a href="{{ route('landing.id') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <p class="footer-note">
        &copy; {{ date('Y') }} Bizmark.ID &mdash;
        <a href="{{ route('landing.id') }}">bizmark.id</a>
    </p>
</body>
</html>
