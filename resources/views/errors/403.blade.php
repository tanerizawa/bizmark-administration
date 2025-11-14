<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-red: #FF3B30;
            --apple-orange: #FF9500;
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-separator: rgba(84, 84, 88, 0.35);
            --dark-text-primary: #FFFFFF;
            --dark-text-secondary: rgba(235, 235, 245, 0.6);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--dark-bg);
            color: var(--dark-text-primary);
        }
        .card-elevated {
            background-color: var(--dark-bg-secondary);
            border: 1px solid var(--dark-separator);
            border-radius: 12px;
        }
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="max-w-4xl w-full">
        <!-- Main Error Card -->
        <div class="card-elevated p-8 mb-6">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Left Side - Icon & Error Code -->
                <div class="flex-shrink-0 text-center md:text-left">
                    <div class="inline-block p-6 rounded-full bg-red-500/10 mb-4">
                        <i class="fas fa-shield-halved text-6xl text-red-500 pulse-slow"></i>
                    </div>
                    <h1 class="text-7xl font-bold text-red-500 mb-2">403</h1>
                    <p class="text-xl font-semibold" style="color: var(--dark-text-primary);">Akses Ditolak</p>
                </div>

                <!-- Right Side - Details -->
                <div class="flex-1">
                    <!-- Error Message -->
                    <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                            <div>
                                <h3 class="font-semibold text-red-400 mb-1">Pesan Error:</h3>
                                <p class="text-red-300 text-sm leading-relaxed">
                                    @if(isset($exception) && $exception->getMessage())
                                        {{ $exception->getMessage() }}
                                    @else
                                        Anda tidak memiliki izin untuk mengakses halaman ini.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Current User Info -->
                    @auth
                    <div class="card-elevated p-4 mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-user-circle text-2xl" style="color: var(--apple-blue);"></i>
                            <div>
                                <h3 class="font-semibold" style="color: var(--dark-text-primary);">Informasi Akun Anda</h3>
                                <p class="text-xs" style="color: var(--dark-text-secondary);">Akun yang sedang login</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--dark-separator);">
                                <span style="color: var(--dark-text-secondary);">Nama</span>
                                <span class="font-medium" style="color: var(--dark-text-primary);">{{ auth()->user()->name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b" style="border-color: var(--dark-separator);">
                                <span style="color: var(--dark-text-secondary);">Email</span>
                                <span class="font-medium" style="color: var(--dark-text-primary);">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span style="color: var(--dark-text-secondary);">Role</span>
                                <div class="flex gap-2">
                                    @if(auth()->user()->roles && auth()->user()->roles->count() > 0)
                                        @foreach(auth()->user()->roles as $role)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                              style="background-color: {{ $role->name === 'admin' ? 'var(--apple-blue)' : ($role->name === 'manager' ? 'var(--apple-orange)' : 'var(--dark-bg-tertiary)') }}; color: white;">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                        @endforeach
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-500 text-white">
                                            No Role
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth

                    <!-- What to do next -->
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-lightbulb text-blue-400 text-xl mt-0.5"></i>
                            <div>
                                <h3 class="font-semibold text-blue-400 mb-3">Apa yang harus dilakukan?</h3>
                                <ol class="space-y-2 text-sm text-blue-300">
                                    <li class="flex items-start gap-2">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">1</span>
                                        <span>Pastikan Anda login dengan akun yang memiliki role yang sesuai</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">2</span>
                                        <span>Hubungi administrator sistem untuk meminta akses (email: <a href="mailto:admin@bizmark.id" class="underline hover:text-blue-200">admin@bizmark.id</a>)</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-500/20 flex items-center justify-center text-xs font-bold">3</span>
                                        <span>Jika Anda adalah administrator, periksa pengaturan role dan permission di menu Settings</span>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
            <button 
                onclick="window.history.back()" 
                class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium transition-all"
                style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                onmouseover="this.style.backgroundColor='var(--dark-bg-secondary)'"
                onmouseout="this.style.backgroundColor='var(--dark-bg-tertiary)'"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Halaman Sebelumnya
            </button>
            
            <a 
                href="{{ route('dashboard') }}" 
                class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium text-white transition-all"
                style="background-color: var(--apple-blue);"
                onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'"
            >
                <i class="fas fa-home mr-2"></i>
                Ke Dashboard
            </a>
        </div>

        <!-- Role Hierarchy Info (collapsed by default) -->
        <details class="card-elevated p-6 mb-6">
            <summary class="cursor-pointer font-semibold flex items-center gap-2" style="color: var(--dark-text-primary);">
                <i class="fas fa-info-circle" style="color: var(--apple-blue);"></i>
                Informasi Role & Permission (Klik untuk melihat)
            </summary>
            <div class="mt-4 space-y-4">
                <!-- Admin -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-crown text-blue-400"></i>
                        <h4 class="font-semibold text-blue-400">Admin</h4>
                    </div>
                    <p class="text-sm" style="color: var(--dark-text-secondary);">Akses penuh ke semua fitur sistem tanpa batasan. Dapat mengelola user, role, dan semua modul.</p>
                </div>

                <!-- Manager -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-user-tie text-orange-400"></i>
                        <h4 class="font-semibold text-orange-400">Manager</h4>
                    </div>
                    <p class="text-sm" style="color: var(--dark-text-secondary);">Dapat mengelola proyek, tugas, dokumen, klien, instansi, dan melihat laporan keuangan.</p>
                </div>

                <!-- Accountant -->
                <div class="border-l-4 border-green-500 pl-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-calculator text-green-400"></i>
                        <h4 class="font-semibold text-green-400">Accountant</h4>
                    </div>
                    <p class="text-sm" style="color: var(--dark-text-secondary);">Fokus pada keuangan - dapat mengelola invoice, pembayaran, dan laporan finansial.</p>
                </div>

                <!-- Staff -->
                <div class="border-l-4 border-purple-500 pl-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-user text-purple-400"></i>
                        <h4 class="font-semibold text-purple-400">Staff</h4>
                    </div>
                    <p class="text-sm" style="color: var(--dark-text-secondary);">Dapat melihat proyek dan tugas, terbatas pada akses view only untuk operasional.</p>
                </div>

                <!-- Viewer -->
                <div class="border-l-4 border-gray-500 pl-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-eye text-gray-400"></i>
                        <h4 class="font-semibold text-gray-400">Viewer</h4>
                    </div>
                    <p class="text-sm" style="color: var(--dark-text-secondary);">Akses terbatas hanya untuk melihat proyek dan dokumen tanpa kemampuan edit.</p>
                </div>
            </div>
        </details>
    </div>
</body>
</html>
