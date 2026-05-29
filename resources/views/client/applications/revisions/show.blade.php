@extends('client.layouts.app')

@section('title', 'Review Revisi Paket — ' . $application->application_number)

@section('content')
{{-- ══ PORTAL-V2 HERO ══ --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #92400e 0%, color-mix(in oklab, #92400e 55%, #001020) 100%); color:#fff;"
         aria-label="Revisi Paket">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.13) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-xs mb-3 transition-colors">
            <i class="fas fa-arrow-left text-[9px]" aria-hidden="true"></i> Kembali ke Permohonan
        </a>

        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.25);">
                    <i class="fas fa-file-signature text-[9px]" aria-hidden="true"></i>
                    Revisi Paket
                </span>
                <h1 class="mt-1.5 text-xl font-bold text-white leading-tight">
                    Revisi #{{ $revision->revision_number }}
                    <span class="text-white/70 font-normal text-base ml-2">{{ $application->application_number }}</span>
                </h1>
                <p class="text-sm text-white/70 mt-0.5">Diajukan {{ $revision->created_at->diffForHumans() }} oleh {{ $revision->revisedBy->name ?? 'Admin' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($revision->status === 'pending_client_approval')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background: rgba(251,191,36,0.2); border: 1px solid rgba(251,191,36,0.35); color: #fef08a;">
                    <i class="fas fa-hourglass-half text-[9px]" aria-hidden="true"></i>
                    Menunggu Persetujuan
                </span>
                @elseif($revision->status === 'approved')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background: rgba(16,185,129,0.2); border: 1px solid rgba(16,185,129,0.35); color: #6ee7b7;">
                    <i class="fas fa-check-circle text-[9px]" aria-hidden="true"></i>
                    Disetujui
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background: rgba(239,68,68,0.2); border: 1px solid rgba(239,68,68,0.35); color: #fca5a5;">
                    <i class="fas fa-times-circle text-[9px]" aria-hidden="true"></i>
                    Ditolak
                </span>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ══ CONTENT ══ --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6"
     x-data="{ showRejectModal: false }">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 mb-5 text-sm border"
         style="background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.25); color: var(--apple-green);">
        <i class="fas fa-check-circle flex-shrink-0" aria-hidden="true"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 rounded-xl px-4 py-3 mb-5 text-sm border"
         style="background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); color: var(--apple-red);">
        <i class="fas fa-exclamation-circle flex-shrink-0" aria-hidden="true"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── LEFT: Main Content ─── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Alasan Revisi --}}
            <div class="rounded-xl overflow-hidden border"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="border-color: var(--border-subtle); background: var(--surface-cool);">
                    <i class="fas fa-info-circle text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Alasan Revisi</h2>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-[var(--text-secondary)] font-medium">Tipe:</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: var(--client-primary-light); color: var(--client-primary); border: 1px solid var(--client-primary-border);">
                            @switch($revision->revision_type)
                                @case('technical_adjustment') Penyesuaian Teknis @break
                                @case('client_request') Permintaan Client @break
                                @case('cost_update') Update Biaya @break
                                @case('document_incomplete') Dokumen Tidak Lengkap @break
                                @default {{ $revision->revision_type }}
                            @endswitch
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-[var(--text-primary)] mb-1">Penjelasan:</p>
                        <p class="text-[var(--text-secondary)] leading-relaxed">{{ $revision->revision_reason }}</p>
                    </div>
                    <p class="text-[var(--text-tertiary)] text-xs">
                        <i class="fas fa-user mr-1" aria-hidden="true"></i>
                        Direvisi oleh {{ $revision->revisedBy->name }} pada {{ $revision->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Perbandingan Paket --}}
            <div class="rounded-xl overflow-hidden border"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="border-color: var(--border-subtle); background: var(--surface-cool);">
                    <i class="fas fa-exchange-alt text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Perbandingan Paket</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[var(--text-tertiary)] text-xs uppercase tracking-wide border-b"
                                style="background: var(--surface-sunken); border-color: var(--border-subtle);">
                                <th class="px-4 py-3 text-left w-2/5">Paket Original</th>
                                <th class="px-4 py-3 text-left w-2/5">Paket Revisi (Baru)</th>
                                <th class="px-4 py-3 text-center w-1/5">Perubahan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--border-subtle);">
                            {{-- Section header: Daftar Izin --}}
                            <tr style="background: var(--surface-sunken);">
                                <td colspan="3" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[var(--text-tertiary)]">Daftar Izin</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <ul class="space-y-2">
                                        @forelse($originalPackage['permits'] ?? [] as $permit)
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-file-alt text-[var(--text-tertiary)] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                            <div>
                                                <p class="text-[var(--text-primary)] text-sm">{{ $permit['permit_name'] ?? 'Izin #'.$loop->iteration }}</p>
                                                <p class="text-[var(--text-tertiary)] text-xs">Rp {{ number_format($permit['unit_price'] ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="text-[var(--text-tertiary)] text-xs">Tidak ada data</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <ul class="space-y-2">
                                        @foreach($revision->permits_data as $permit)
                                        @php $permitType = \App\Models\PermitType::find($permit['permit_type_id']); @endphp
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-file-alt text-[var(--apple-green)] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                            <div>
                                                <p class="text-[var(--text-primary)] text-sm">{{ $permitType->name ?? 'Unknown Permit' }}</p>
                                                <p class="text-[var(--text-tertiary)] text-xs">Rp {{ number_format($permit['unit_price'], 0, ',', '.') }} · {{ $permit['estimated_days'] }} hari</p>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-4 py-4 text-center align-top">
                                    @php
                                        $origCount = count($originalPackage['permits'] ?? []);
                                        $revCount  = count($revision->permits_data);
                                    @endphp
                                    @if($revCount > $origCount)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: rgba(16,185,129,0.1); color: var(--apple-green);">+{{ $revCount - $origCount }} Izin</span>
                                    @elseif($revCount < $origCount)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: rgba(245,158,11,0.1); color: var(--apple-orange);">-{{ $origCount - $revCount }} Izin</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: var(--surface-cool); color: var(--text-tertiary);">Sama</span>
                                    @endif
                                </td>
                            </tr>
                            {{-- Section header: Total Biaya --}}
                            <tr style="background: var(--surface-sunken);">
                                <td colspan="3" class="px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[var(--text-tertiary)]">Total Biaya</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-5 text-center">
                                    <p class="text-xl font-bold text-[var(--text-primary)]">Rp {{ number_format($originalPackage['total_cost'], 0, ',', '.') }}</p>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <p class="text-xl font-bold" style="color: var(--apple-green);">Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    @php $diff = $revision->total_cost - $originalPackage['total_cost']; @endphp
                                    @if($diff > 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: rgba(239,68,68,0.1); color: var(--apple-red);">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                    @elseif($diff < 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: rgba(16,185,129,0.1); color: var(--apple-green);">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: var(--surface-cool); color: var(--text-tertiary);">Sama</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rincian Paket Revisi --}}
            <div class="rounded-xl overflow-hidden border"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="border-color: var(--border-subtle); background: var(--surface-cool);">
                    <i class="fas fa-list text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                    <h2 class="text-sm font-semibold text-[var(--text-primary)]">Rincian Paket Revisi</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[var(--text-tertiary)] text-xs uppercase tracking-wide border-b"
                                style="background: var(--surface-sunken); border-color: var(--border-subtle);">
                                <th class="px-4 py-3 text-left w-8">No</th>
                                <th class="px-4 py-3 text-left">Jenis Izin</th>
                                <th class="px-4 py-3 text-left">Layanan</th>
                                <th class="px-4 py-3 text-right">Biaya</th>
                                <th class="px-4 py-3 text-center">Estimasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--border-subtle);">
                            @foreach($revision->quotationItems as $item)
                            <tr class="transition-colors hover:bg-[var(--surface-cool)]">
                                <td class="px-4 py-3 text-[var(--text-tertiary)]">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-[var(--text-primary)]">{{ $item->item_name }}</p>
                                    @if($item->description)
                                    <p class="text-[var(--text-tertiary)] text-xs mt-0.5">{{ Str::limit($item->description, 60) }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium"
                                          style="background: rgba(6,182,212,0.1); color: #06b6d4;">{{ $item->service_type_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-[var(--text-primary)]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center text-[var(--text-secondary)]">{{ $item->estimated_days }} hari</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t font-bold" style="background: var(--surface-sunken); border-color: var(--border-subtle);">
                                <td colspan="3" class="px-4 py-3 text-right text-[var(--text-primary)] text-sm">TOTAL</td>
                                <td class="px-4 py-3 text-right text-[var(--client-primary)] text-base">Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT: Actions Sidebar ─── --}}
        <div class="space-y-4">

            {{-- Actions Card --}}
            <div class="rounded-xl overflow-hidden border sticky top-4"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="border-color: rgba(245,158,11,0.3); background: rgba(245,158,11,0.06);">
                    <i class="fas fa-clipboard-check text-[var(--apple-orange)] text-sm" aria-hidden="true"></i>
                    <h3 class="text-sm font-semibold text-[var(--text-primary)]">Tindakan</h3>
                </div>
                <div class="p-5">
                    @if($revision->status === 'pending_client_approval')
                    <p class="text-sm text-[var(--text-secondary)] mb-4 leading-relaxed">
                        Revisi ini menunggu persetujuan Anda. Silakan review perubahan dan pilih tindakan yang sesuai.
                    </p>

                    <form action="{{ route('client.applications.revisions.approve', [$application->id, $revision->id]) }}"
                          method="POST" class="mb-2"
                          x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menyetujui revisi ini?')) $el.submit()">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all active:scale-95 hover:opacity-90"
                                style="background: var(--apple-green);">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Setuju dengan Revisi
                        </button>
                    </form>

                    <button type="button" @click="showRejectModal = true"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all mb-2 active:scale-95 border"
                            style="border-color: rgba(239,68,68,0.4); color: var(--apple-red); background: rgba(239,68,68,0.04);">
                        <i class="fas fa-times-circle" aria-hidden="true"></i>
                        Tolak Revisi
                    </button>

                    <a href="{{ route('client.applications.show', $application->id) }}#communication"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all border hover:opacity-80"
                       style="border-color: var(--client-primary-border); color: var(--client-primary); background: var(--client-primary-light);">
                        <i class="fas fa-comments" aria-hidden="true"></i>
                        Diskusi dengan Admin
                    </a>

                    @else
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm border
                        @if($revision->status === 'approved') " style="background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.25); color: var(--apple-green);"
                        @else " style="background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); color: var(--apple-red);"
                        @endif>
                        <i class="fas fa-{{ $revision->status === 'approved' ? 'check' : 'times' }}-circle mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <span>
                            @if($revision->status === 'approved')
                                Disetujui pada {{ $revision->client_approved_at->format('d M Y H:i') }}
                            @else
                                Revisi ini telah ditolak
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
                <div class="px-5 py-3 border-t" style="border-color: var(--border-subtle);">
                    @php $supportEmail = data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id'); @endphp
                    <p class="text-[var(--text-tertiary)] text-xs">
                        <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                        Perlu bantuan?
                        <a href="mailto:{{ $supportEmail }}"
                           class="hover:underline" style="color: var(--client-primary);">Hubungi kami</a>
                    </p>
                </div>
            </div>

            {{-- Info Aplikasi --}}
            <div class="rounded-xl overflow-hidden border"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-2"
                     style="border-color: var(--border-subtle); background: var(--surface-cool);">
                    <i class="fas fa-info text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                    <h3 class="text-sm font-semibold text-[var(--text-primary)]">Informasi Permohonan</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    @foreach([
                        ['Nomor Permohonan', $application->application_number],
                        ['Nomor Revisi', '#'.$revision->revision_number],
                        ['Diajukan', $revision->created_at->format('d M Y')],
                    ] as [$label, $value])
                    <div class="flex justify-between items-center">
                        <span class="text-[var(--text-tertiary)]">{{ $label }}</span>
                        <span class="font-medium text-[var(--text-primary)] text-right">{{ $value }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between items-center">
                        <span class="text-[var(--text-tertiary)]">Status</span>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                            @if($revision->status === 'approved') " style="background: rgba(16,185,129,0.1); color: var(--apple-green);"
                            @elseif($revision->status === 'rejected') " style="background: rgba(239,68,68,0.1); color: var(--apple-red);"
                            @else " style="background: rgba(245,158,11,0.1); color: var(--apple-orange);"
                            @endif>
                            @if($revision->status === 'pending_client_approval') Menunggu
                            @elseif($revision->status === 'approved') Disetujui
                            @else Ditolak
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Reject Modal (Alpine.js) ── --}}
    <div x-show="showRejectModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showRejectModal = false"
         role="dialog" aria-modal="true" aria-labelledby="reject-modal-title">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showRejectModal = false"></div>
        <div class="relative rounded-2xl shadow-2xl w-full max-w-lg border"
             style="background: var(--surface-elevated); border-color: var(--border-subtle);">
            <form action="{{ route('client.applications.revisions.reject', [$application->id, $revision->id]) }}"
                  method="POST">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b rounded-t-2xl"
                     style="border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.06);">
                    <h4 id="reject-modal-title" class="font-semibold text-[var(--text-primary)]">Tolak Revisi</h4>
                    <button type="button" @click="showRejectModal = false"
                            aria-label="Tutup modal"
                            class="text-[var(--text-tertiary)] hover:text-[var(--text-primary)] transition-colors">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-[var(--text-secondary)] leading-relaxed">
                        Anda yakin ingin menolak revisi ini? Silakan berikan alasan penolakan:
                    </p>
                    <div>
                        <label for="rejection_reason" class="block text-sm font-medium text-[var(--text-primary)] mb-1.5">
                            Alasan Penolakan <span style="color: var(--apple-red);">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                                  placeholder="Contoh: Biaya terlalu tinggi, saya ingin konsultasi lebih lanjut..."
                                  class="w-full rounded-xl px-3 py-2.5 text-sm resize-none transition-colors"
                                  style="background: var(--surface-cool); border: 1px solid var(--border-subtle); color: var(--text-primary);"
                                  onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                  onblur="this.style.borderColor='var(--border-subtle)'; this.style.boxShadow='none';"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t" style="border-color: var(--border-subtle);">
                    <button type="button" @click="showRejectModal = false"
                            class="px-4 py-2 text-sm font-semibold rounded-xl transition-colors border"
                            style="border-color: var(--border-subtle); color: var(--text-secondary); background: var(--surface-cool);">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white rounded-xl transition-all active:scale-95 hover:opacity-90"
                            style="background: var(--apple-red);">
                        Tolak Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
