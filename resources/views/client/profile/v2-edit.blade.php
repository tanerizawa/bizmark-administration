{{-- Profile Edit — Portal v2 --}}
@php
    $clientTypeLabel = match($client->client_type ?? '') {
        'company'    => 'Perusahaan',
        'government' => 'Pemerintah',
        default      => 'Perorangan',
    };
    $clientTypeIcon = ($client->client_type ?? '') === 'company' ? 'fa-building' : 'fa-user';
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 65%, #001020) 100%); color:#fff;"
         aria-label="Profil Saya">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="w-14 h-14 rounded-full overflow-hidden bg-white/20 border-2 border-white/30 flex-shrink-0">
                    @if($client->profile_picture)
                    <img src="{{ asset('storage/' . $client->profile_picture) }}" alt="{{ $client->name }}" loading="lazy" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas {{ $clientTypeIcon }} text-white/80 text-2xl" aria-hidden="true"></i>
                    </div>
                    @endif
                </div>
                <div>
                    <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                        <i class="fas {{ $clientTypeIcon }} text-[9px]" aria-hidden="true"></i>
                        {{ $clientTypeLabel }}
                    </span>
                    <h1 class="mt-1.5 text-xl font-bold text-white">{{ $client->name }}</h1>
                    <p class="text-sm text-white/70">{{ $client->email }}</p>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                @if($client->email_verified_at)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-green-400/20 border border-green-300/30 rounded-full text-green-200">
                    <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i> Email Terverifikasi
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-amber-400/20 border border-amber-300/30 rounded-full text-amber-200">
                    <i class="fas fa-triangle-exclamation text-[10px]" aria-hidden="true"></i> Belum Terverifikasi
                </span>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ─── ALERTS ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 pt-5">
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300 mb-4">
        <i class="fas fa-check-circle text-green-500" aria-hidden="true"></i> {{ session('success') }}
    </div>
    @endif
    @if(!$client->email_verified_at)
    <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl px-4 py-3 mb-4">
        <i class="fas fa-triangle-exclamation text-amber-500 flex-shrink-0" aria-hidden="true"></i>
        <p class="text-sm text-amber-800 dark:text-amber-200 flex-1"><strong>Email belum diverifikasi.</strong> Verifikasi untuk mengamankan akun.</p>
        <form method="POST" action="{{ route('client.verification.send') }}">
            @csrf
            <button type="submit" class="text-xs font-semibold px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                Kirim Link
            </button>
        </form>
    </div>
    @endif
</div>

{{-- ─── FORMS GRID ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 pb-8 grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- LEFT COL --}}
    <div class="space-y-6">

        {{-- Profile Info --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                <i class="fas fa-id-card text-[var(--client-primary)]" aria-hidden="true"></i>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Informasi Profil</h2>
                    <p class="text-xs text-[var(--text-tertiary)]">Data dasar pemilik akun.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="px-5 py-5 space-y-4">
                @csrf @method('PUT')

                {{-- Profile picture --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-[var(--surface-cool)] border-2 border-[var(--border-subtle)] flex-shrink-0">
                            @if($client->profile_picture)
                            <img id="profilePreview" src="{{ asset('storage/' . $client->profile_picture) }}" alt="{{ $client->name }}" loading="lazy" class="w-full h-full object-cover">
                            @else
                            <div id="profilePreview" class="w-full h-full flex items-center justify-center">
                                <i class="fas {{ $clientTypeIcon }} text-[var(--text-tertiary)] text-2xl" aria-hidden="true"></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="profile_picture" id="profileInput" accept="image/*"
                                   class="block w-full text-xs text-[var(--text-secondary)] file:mr-3 file:text-xs file:font-semibold file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--surface-cool)] file:text-[var(--text-primary)] hover:file:bg-[var(--border-subtle)] cursor-pointer"
                                   onchange="previewProfilePic(this)">
                            <p class="text-[10px] text-[var(--text-tertiary)] mt-1">JPG, PNG, GIF — Maks 2MB</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nama Lengkap <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Email <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}" required
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">No. Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Tipe Akun</label>
                        <div class="px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-tertiary)] select-none">
                            {{ $clientTypeLabel }}
                        </div>
                    </div>
                </div>

                @if($client->client_type === 'company')
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Jabatan PIC</label>
                        <input type="text" name="pic_position" value="{{ old('pic_position', $client->pic_position) }}"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $client->npwp) }}"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Alamat</label>
                    <textarea name="address" rows="3"
                              class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] resize-none">{{ old('address', $client->address) }}</textarea>
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-floppy-disk text-xs" aria-hidden="true"></i> Simpan Perubahan
                </button>
            </form>
        </section>

        {{-- Notification preferences --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                <i class="fas fa-bell text-[var(--client-primary)]" aria-hidden="true"></i>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Preferensi Notifikasi</h2>
                    <p class="text-xs text-[var(--text-tertiary)]">Pilih cara Anda menerima pembaruan.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.notifications') }}" class="px-5 py-5 space-y-3">
                @csrf @method('PUT')
                @foreach([
                    ['name'=>'notif_email',  'label'=>'Notifikasi Email',         'desc'=>'Pembaruan status permohonan via email'],
                    ['name'=>'notif_whatsapp','label'=>'Notifikasi WhatsApp',      'desc'=>'Pengingat pembayaran & jatuh tempo'],
                    ['name'=>'notif_push',   'label'=>'Push Notification Browser', 'desc'=>'Notifikasi real-time di browser'],
                ] as $pref)
                <label class="flex items-start gap-3 cursor-pointer group">
                    <div class="mt-0.5">
                        <input type="checkbox" name="{{ $pref['name'] }}" value="1"
                               {{ $client->{$pref['name']} ?? false ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-[var(--border-subtle)] text-[var(--client-primary)] focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[var(--text-primary)] group-hover:text-[var(--client-primary)] transition-colors">{{ $pref['label'] }}</p>
                        <p class="text-xs text-[var(--text-tertiary)]">{{ $pref['desc'] }}</p>
                    </div>
                </label>
                @endforeach
                <button type="submit"
                        class="mt-2 inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] text-[var(--text-primary)] text-sm font-semibold rounded-lg hover:bg-[var(--surface-elevated)] transition-colors">
                    <i class="fas fa-floppy-disk text-xs" aria-hidden="true"></i> Simpan Preferensi
                </button>
            </form>
        </section>
    </div>

    {{-- RIGHT COL --}}
    <div class="space-y-6">

        {{-- Change password --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                <i class="fas fa-lock text-[var(--client-primary)]" aria-hidden="true"></i>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Ubah Password</h2>
                    <p class="text-xs text-[var(--text-tertiary)]">Gunakan password yang kuat dan unik.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.password') }}" class="px-5 py-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Password Saat Ini <span class="text-[var(--apple-red)]">*</span></label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Password Baru <span class="text-[var(--apple-red)]">*</span></label>
                    <input type="password" name="password" required autocomplete="new-password" minlength="8"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Konfirmasi Password <span class="text-[var(--apple-red)]">*</span></label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" minlength="8"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-lock text-xs" aria-hidden="true"></i> Ubah Password
                </button>
            </form>
        </section>

        {{-- Two-Factor Authentication --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                <i class="fas fa-shield-check text-[var(--client-primary)]" aria-hidden="true"></i>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Autentikasi Dua Faktor (2FA)</h2>
                    <p class="text-xs text-[var(--text-tertiary)]">Tambahkan lapisan keamanan ekstra.</p>
                </div>
            </div>
            <div class="px-5 py-5">
                @php $twoFactorEnabled = $client->two_factor_secret ?? false; @endphp
                @if($twoFactorEnabled)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-check text-green-500 text-xs" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400">2FA Aktif</p>
                        <p class="text-xs text-[var(--text-tertiary)]">Akun Anda lebih aman.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('client.profile.two-factor.disable') }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700/50 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <i class="fas fa-xmark text-xs" aria-hidden="true"></i> Nonaktifkan 2FA
                    </button>
                </form>
                @else
                <p class="text-sm text-[var(--text-secondary)] mb-4">Aktifkan 2FA menggunakan aplikasi authenticator (Google Authenticator, Authy, dll.) untuk keamanan login yang lebih baik.</p>
                <a href="{{ route('client.profile.two-factor.enable') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-shield-halved text-xs" aria-hidden="true"></i> Aktifkan 2FA
                </a>
                @endif
            </div>
        </section>

        {{-- Danger zone --}}
        <section class="bg-[var(--surface-elevated)] border border-red-200 dark:border-red-800/50 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-red-200 dark:border-red-800/50 flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-red-500" aria-hidden="true"></i>
                <div>
                    <h2 class="text-sm font-bold text-[var(--text-primary)]">Zona Berbahaya</h2>
                    <p class="text-xs text-[var(--text-tertiary)]">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="px-5 py-5">
                <p class="text-xs text-[var(--text-secondary)] mb-4">Menghapus akun akan menghapus semua data, permohonan, dan dokumen Anda secara permanen.</p>
                <button type="button"
                        @click="if(confirm('Yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')) document.getElementById('delete-account-form').submit()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700/50 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <i class="fas fa-trash text-xs" aria-hidden="true"></i> Hapus Akun
                </button>
                <form id="delete-account-form" method="POST" action="{{ route('client.profile.destroy') }}" class="hidden">
                    @csrf @method('DELETE')
                </form>
            </div>
        </section>
    </div>
</div>

<script>
function previewProfilePic(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
