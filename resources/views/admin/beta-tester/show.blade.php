@extends('layouts.app')

@section('title', 'Detail Beta Tester - ' . $betaTester->full_name)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Hero Header -->
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full transform translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-apple-green opacity-20 blur-3xl rounded-full transform -translate-x-1/3 translate-y-1/3"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-start gap-4 mb-4">
                <a href="{{ route('admin.beta-tester.index') }}" 
                   class="w-10 h-10 rounded-apple flex items-center justify-center transition-all hover:scale-105"
                   style="background: rgba(255,255,255,0.1); color: white;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] mb-2" style="color: rgba(235,235,245,0.6);">
                                Beta Tester Detail
                            </p>
                            <h1 class="text-3xl font-bold mb-2" style="color: white;">
                                {{ $betaTester->full_name }}
                            </h1>
                            <p class="text-sm font-mono" style="color: rgba(235,235,245,0.8);">
                                <i class="fas fa-fingerprint mr-2"></i>
                                {{ $betaTester->registration_number }}
                            </p>
                        </div>
                        @php
                            $statusConfig = [
                                'registered' => ['color' => 'rgba(235,235,245,1)', 'bg' => 'rgba(235,235,245,0.15)', 'label' => 'Terdaftar', 'icon' => 'user-plus'],
                                'documents_pending' => ['color' => 'rgba(255,214,10,1)', 'bg' => 'rgba(255,214,10,0.15)', 'label' => 'Pending', 'icon' => 'clock'],
                                'documents_signed' => ['color' => 'rgba(10,132,255,1)', 'bg' => 'rgba(10,132,255,0.15)', 'label' => 'Signed', 'icon' => 'check'],
                                'active' => ['color' => 'rgba(48,209,88,1)', 'bg' => 'rgba(48,209,88,0.15)', 'label' => 'Aktif', 'icon' => 'check-circle'],
                                'completed' => ['color' => 'rgba(175,82,222,1)', 'bg' => 'rgba(175,82,222,0.15)', 'label' => 'Selesai', 'icon' => 'flag-checkered'],
                                'rejected' => ['color' => 'rgba(255,69,58,1)', 'bg' => 'rgba(255,69,58,0.15)', 'label' => 'Ditolak', 'icon' => 'times-circle'],
                            ];
                            $config = $statusConfig[$betaTester->status] ?? $statusConfig['registered'];
                        @endphp
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-apple text-sm font-semibold"
                              style="background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                            <i class="fas fa-{{ $config['icon'] }}"></i>
                            {{ $config['label'] }}
                        </span>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="flex items-center gap-6 mt-4 text-xs flex-wrap" style="color: rgba(235,235,245,0.6);">
                        <span>
                            <i class="fas fa-envelope mr-1"></i>
                            {{ $betaTester->email }}
                        </span>
                        <span>
                            <i class="fas fa-phone mr-1"></i>
                            {{ $betaTester->phone }}
                        </span>
                        <span>
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Registered {{ $betaTester->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-xl font-bold mb-6" style="color: white;">
                    <i class="fas fa-user mr-2" style="color: rgba(10,132,255,1);"></i>
                    Informasi Personal
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Nama Lengkap
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            {{ $betaTester->full_name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Email
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            <i class="fas fa-envelope mr-2" style="color: rgba(10,132,255,1);"></i>
                            {{ $betaTester->email }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            No. Telepon
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            <i class="fas fa-phone mr-2" style="color: rgba(48,209,88,1);"></i>
                            {{ $betaTester->phone }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Tanggal Lahir
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            <i class="fas fa-birthday-cake mr-2" style="color: rgba(255,214,10,1);"></i>
                            {{ \Carbon\Carbon::parse($betaTester->date_of_birth)->format('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            No. Identitas ({{ strtoupper($betaTester->identity_type ?? 'KTP') }})
                        </label>
                        <p class="text-sm font-medium font-mono" style="color: white;">
                            {{ $betaTester->identity_number }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Alamat
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            {{ $betaTester->address }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Tempat Lahir
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            {{ $betaTester->place_of_birth }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            WhatsApp
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            <i class="fab fa-whatsapp mr-2" style="color: rgba(48,209,88,1);"></i>
                            {{ $betaTester->whatsapp }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Education Information -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-xl font-bold mb-6" style="color: white;">
                    <i class="fas fa-graduation-cap mr-2" style="color: rgba(48,209,88,1);"></i>
                    Informasi Pendidikan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Universitas
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            <i class="fas fa-university mr-2" style="color: rgba(10,132,255,1);"></i>
                            {{ $betaTester->university }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Fakultas
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            {{ $betaTester->faculty ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Program Studi
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            {{ $betaTester->major }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            NIM
                        </label>
                        <p class="text-sm font-medium font-mono" style="color: white;">
                            {{ $betaTester->student_id ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Semester
                        </label>
                        <p class="text-sm font-medium" style="color: white;">
                            Semester {{ $betaTester->semester }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Motivasi
                        </label>
                        <p class="text-sm" style="color: rgba(235,235,245,0.8); line-height: 1.6;">
                            {{ $betaTester->motivation }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Documents -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-xl font-bold mb-6" style="color: white;">
                    <i class="fas fa-file-signature mr-2" style="color: rgba(10,132,255,1);"></i>
                    Dokumen
                </h2>

                <div class="space-y-4">
                    @foreach($betaTester->documents as $document)
                    <div class="p-4 rounded-apple" 
                         style="background: rgba(255,255,255,0.05); border: 2px solid {{ $document->is_signed ? 'rgba(48,209,88,0.5)' : 'rgba(255,255,255,0.1)' }};">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="flex-1">
                                <h3 class="font-semibold text-sm mb-2" style="color: white;">
                                    <i class="fas fa-file-contract mr-2"></i>
                                    {{ $document->document_title }}
                                </h3>
                                
                                <div class="flex items-center gap-4 flex-wrap text-xs" style="color: rgba(235,235,245,0.6);">
                                    <span>
                                        <i class="fas fa-tag mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                    </span>
                                    <span>
                                        <i class="far fa-calendar mr-1"></i>
                                        Dibuat: {{ $document->created_at->format('d M Y H:i') }}
                                    </span>
                                    @if($document->is_signed && $document->signed_at)
                                    <span style="color: rgba(48,209,88,1);">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Signed: {{ \Carbon\Carbon::parse($document->signed_at)->format('d M Y H:i') }}
                                    </span>
                                    @endif
                                </div>

                                @if($document->is_signed)
                                <div class="mt-3 p-3 rounded-apple" style="background: rgba(48,209,88,0.15);">
                                    <p class="text-xs font-semibold mb-1" style="color: rgba(48,209,88,1);">
                                        <i class="fas fa-fingerprint mr-1"></i>
                                        Digital Signature Hash
                                    </p>
                                    <p class="text-xs font-mono" style="color: rgba(235,235,245,0.6);">
                                        {{ $document->signature_hash }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Status Badge -->
                                @if($document->is_signed)
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-apple text-xs font-semibold"
                                      style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                                    <i class="fas fa-check-circle"></i>
                                    Signed
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-apple text-xs font-semibold"
                                      style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">
                                    <i class="fas fa-clock"></i>
                                    Pending
                                </span>
                                @endif

                                <!-- Actions -->
                                <a href="{{ route('beta-tester.document.view', ['documentId' => $document->id]) }}?token={{ $betaTester->access_token }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-apple text-xs font-semibold transition-all hover:scale-105"
                                   style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                    <i class="fas fa-eye"></i>
                                    Lihat
                                </a>

                                @if($document->is_signed)
                                <form action="{{ route('admin.beta-tester.document.verify', $document) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-apple text-xs font-semibold transition-all hover:scale-105"
                                            style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                                        <i class="fas fa-check-double"></i>
                                        Verify
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Activity Log -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-xl font-bold mb-6" style="color: white;">
                    <i class="fas fa-history mr-2" style="color: rgba(175,82,222,1);"></i>
                    Activity Log
                </h2>

                <div class="space-y-4">
                    @forelse($betaTester->activities()->latest()->take(20)->get() as $activity)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-apple flex items-center justify-center"
                                 style="background: {{ in_array($activity->activity_type, ['status_changed', 'document_signed']) ? 'rgba(48,209,88,0.15)' : 'rgba(10,132,255,0.15)' }};">
                                <i class="fas fa-{{ $activity->activity_type == 'registration' ? 'user-plus' : ($activity->activity_type == 'document_signed' ? 'file-signature' : ($activity->activity_type == 'status_changed' ? 'exchange-alt' : 'sticky-note')) }}"
                                   style="color: {{ in_array($activity->activity_type, ['status_changed', 'document_signed']) ? 'rgba(48,209,88,1)' : 'rgba(10,132,255,1)' }};"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <p class="text-sm font-semibold" style="color: white;">
                                    {{ $activity->description }}
                                </p>
                                <span class="text-xs whitespace-nowrap" style="color: rgba(235,235,245,0.6);">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </div>
                            @if($activity->metadata)
                            <div class="mt-2 p-2 rounded-apple text-xs font-mono" style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.6);">
                                @foreach(json_decode($activity->metadata, true) as $key => $value)
                                <div>{{ ucfirst($key) }}: {{ $value }}</div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </section>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-lg font-bold mb-4" style="color: white;">
                    <i class="fas fa-chart-pie mr-2" style="color: rgba(10,132,255,1);"></i>
                    Quick Stats
                </h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,255,255,0.05);">
                        <div>
                            <p class="text-xs mb-1 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Dokumen Signed</p>
                            <p class="text-xl font-bold" style="color: rgba(48,209,88,1);">
                                {{ $betaTester->documents->where('is_signed', true)->count() }}/{{ $betaTester->documents->count() }}
                            </p>
                        </div>
                        <i class="fas fa-file-signature text-2xl" style="color: rgba(48,209,88,0.3);"></i>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,255,255,0.05);">
                        <div>
                            <p class="text-xs mb-1 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Total Aktivitas</p>
                            <p class="text-xl font-bold" style="color: rgba(10,132,255,1);">
                                {{ $betaTester->activities->count() }}
                            </p>
                        </div>
                        <i class="fas fa-history text-2xl" style="color: rgba(10,132,255,0.3);"></i>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,255,255,0.05);">
                        <div>
                            <p class="text-xs mb-1 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Terdaftar Sejak</p>
                            <p class="text-sm font-bold" style="color: white;">
                                {{ $betaTester->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <i class="fas fa-calendar-plus text-2xl" style="color: rgba(255,214,10,0.3);"></i>
                    </div>
                </div>
            </section>

            <!-- Change Status -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-lg font-bold mb-4" style="color: white;">
                    <i class="fas fa-exchange-alt mr-2" style="color: rgba(10,132,255,1);"></i>
                    Ubah Status
                </h2>

                <form action="{{ route('admin.beta-tester.change-status', $betaTester) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Status Baru
                        </label>
                        <select name="status" 
                                class="w-full px-4 py-2.5 rounded-apple border transition-colors text-sm"
                                style="border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white;"
                                required>
                            <option value="registered" {{ $betaTester->status == 'registered' ? 'selected' : '' }}>
                                Terdaftar
                            </option>
                            <option value="documents_pending" {{ $betaTester->status == 'documents_pending' ? 'selected' : '' }}>
                                Pending Dokumen
                            </option>
                            <option value="documents_signed" {{ $betaTester->status == 'documents_signed' ? 'selected' : '' }}>
                                Dokumen Signed
                            </option>
                            <option value="active" {{ $betaTester->status == 'active' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="completed" {{ $betaTester->status == 'completed' ? 'selected' : '' }}>
                                Selesai
                            </option>
                            <option value="rejected" {{ $betaTester->status == 'rejected' ? 'selected' : '' }}>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Alasan (Opsional)
                        </label>
                        <textarea name="reason" 
                                  rows="3"
                                  class="w-full px-4 py-2.5 rounded-apple border transition-colors text-sm"
                                  style="border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white;"
                                  placeholder="Tambahkan catatan..."></textarea>
                    </div>

                    <button type="submit" class="w-full btn-primary-sm">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Status
                    </button>
                </form>
            </section>

            <!-- Admin Actions -->
            <section class="card-elevated rounded-apple-lg p-5">
                <h2 class="text-lg font-bold mb-4" style="color: white;">
                    <i class="fas fa-tools mr-2" style="color: rgba(48,209,88,1);"></i>
                    Admin Actions
                </h2>

                <div class="space-y-3">
                    <!-- Resend Documents -->
                    <form action="{{ route('admin.beta-tester.resend-documents', $betaTester) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-apple text-sm font-semibold transition-all hover:scale-105"
                                style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);"
                                onclick="return confirm('Kirim ulang link dokumen ke {{ $betaTester->email }}?')">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Ulang Dokumen
                        </button>
                    </form>

                    <!-- Add Note -->
                    <button type="button" 
                            onclick="document.getElementById('noteModal').classList.remove('hidden')"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-apple text-sm font-semibold transition-all hover:scale-105"
                            style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                        <i class="fas fa-sticky-note"></i>
                        Tambah Catatan
                    </button>

                    <!-- Delete -->
                    <form action="{{ route('admin.beta-tester.destroy', $betaTester) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-apple text-sm font-semibold transition-all hover:scale-105"
                                style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1);"
                                onclick="return confirm('PERINGATAN: Menghapus beta tester akan menghapus semua data terkait (dokumen, aktivitas). Lanjutkan?')">
                            <i class="fas fa-trash"></i>
                            Hapus Beta Tester
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div id="noteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0, 0, 0, 0.75);">
    <div class="card-elevated rounded-apple-lg max-w-lg w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold" style="color: white;">
                <i class="fas fa-sticky-note mr-2" style="color: rgba(48,209,88,1);"></i>
                Tambah Catatan
            </h3>
            <button type="button" 
                    onclick="document.getElementById('noteModal').classList.add('hidden')"
                    class="w-8 h-8 rounded-apple flex items-center justify-center transition-all hover:scale-105"
                    style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.6);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.beta-tester.add-note', $betaTester) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                    Catatan
                </label>
                <textarea name="note" 
                          rows="5"
                          class="w-full px-4 py-3 rounded-apple border transition-colors text-sm"
                          style="border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white;"
                          placeholder="Tulis catatan admin..."
                          required></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" 
                        onclick="document.getElementById('noteModal').classList.add('hidden')"
                        class="flex-1 btn-secondary-sm">
                    Batal
                </button>
                <button type="submit" class="flex-1 btn-primary-sm">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
