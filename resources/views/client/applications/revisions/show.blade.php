@extends('layouts.client')

@section('title', 'Review Revisi Paket')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto"
     x-data="{ showRejectModal: false }">

    {{-- Info Banner --}}
    <div class="flex items-start gap-3 bg-blue-500/10 border border-blue-500/30 text-blue-300 rounded-xl px-4 py-3 mb-5 text-sm">
        <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
        <span><strong>Revisi Paket Baru!</strong> Admin telah mengusulkan perubahan pada paket aplikasi Anda. Silakan review perubahan di bawah ini.</span>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-4">
        <i class="fas fa-check-circle flex-shrink-0"></i><span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-4">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Revisi Paket #{{ $revision->revision_number }}</h1>
            <p class="text-gray-400 mt-1">{{ $application->application_number }}</p>
        </div>
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Content --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Alasan Revisi --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-blue-800 bg-blue-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-400"></i>Alasan Revisi
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-white font-medium">Tipe:</span>
                        <span class="inline-flex px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 text-xs font-medium">
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
                        <p class="text-white font-medium mb-1">Penjelasan:</p>
                        <p class="text-gray-300">{{ $revision->revision_reason }}</p>
                    </div>
                    <p class="text-gray-500 text-xs">
                        <i class="fas fa-user mr-1"></i>Direvisi oleh: {{ $revision->revisedBy->name }} pada {{ $revision->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            {{-- Perbandingan Paket --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-blue-800 bg-blue-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-blue-400"></i>Perbandingan Paket
                    </h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-700/50 text-gray-400 text-xs uppercase">
                                <th class="px-4 py-3 text-left w-2/5">Paket Original</th>
                                <th class="px-4 py-3 text-left w-2/5">Paket Revisi (Baru)</th>
                                <th class="px-4 py-3 text-center w-1/5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <tr>
                                <td colspan="3" class="px-4 py-2 bg-gray-700/30 text-white font-medium text-xs uppercase tracking-wide">Daftar Izin</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <ul class="space-y-2">
                                        @forelse($originalPackage['permits'] ?? [] as $permit)
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-file-alt text-gray-500 mt-0.5 flex-shrink-0"></i>
                                            <div>
                                                <p class="text-white">{{ $permit['permit_name'] ?? 'Izin #'.$loop->iteration }}</p>
                                                <p class="text-gray-400 text-xs">Biaya: Rp {{ number_format($permit['unit_price'] ?? 0, 0, ',', '.') }}</p>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="text-gray-500">Tidak ada data</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <ul class="space-y-2">
                                        @foreach($revision->permits_data as $permit)
                                        @php $permitType = \App\Models\PermitType::find($permit['permit_type_id']); @endphp
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-file-alt text-green-400 mt-0.5 flex-shrink-0"></i>
                                            <div>
                                                <p class="text-white">{{ $permitType->name ?? 'Unknown Permit' }}</p>
                                                <p class="text-gray-400 text-xs">Biaya: Rp {{ number_format($permit['unit_price'], 0, ',', '.') }} | {{ $permit['estimated_days'] }} hari</p>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-4 py-4 text-center align-top">
                                    @php
                                        $origCount = count($originalPackage['permits'] ?? []);
                                        $revCount = count($revision->permits_data);
                                    @endphp
                                    @if($revCount > $origCount)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-green-500/20 text-green-400 text-xs">+{{ $revCount - $origCount }} Izin</span>
                                    @elseif($revCount < $origCount)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-yellow-500/20 text-yellow-400 text-xs">-{{ $origCount - $revCount }} Izin</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-500/20 text-gray-400 text-xs">Sama</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 bg-gray-700/30 text-white font-medium text-xs uppercase tracking-wide">Total Biaya</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 text-center">
                                    <p class="text-2xl font-bold text-white">Rp {{ number_format($originalPackage['total_cost'], 0, ',', '.') }}</p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <p class="text-2xl font-bold text-green-400">Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @php $diff = $revision->total_cost - $originalPackage['total_cost']; @endphp
                                    @if($diff > 0)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-red-500/20 text-red-400 text-xs">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                    @elseif($diff < 0)
                                        <span class="inline-flex px-2 py-0.5 rounded bg-green-500/20 text-green-400 text-xs">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded bg-gray-500/20 text-gray-400 text-xs">Sama</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rincian Paket Revisi --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-blue-800 bg-blue-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-400"></i>Rincian Paket Revisi
                    </h5>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-700/50 text-gray-400 text-xs uppercase">
                                <th class="px-4 py-3 text-left w-8">No</th>
                                <th class="px-4 py-3 text-left">Jenis Izin</th>
                                <th class="px-4 py-3 text-left">Layanan</th>
                                <th class="px-4 py-3 text-right">Biaya</th>
                                <th class="px-4 py-3 text-center">Estimasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($revision->quotationItems as $item)
                            <tr class="hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-white font-medium">{{ $item->item_name }}</p>
                                    @if($item->description)
                                    <p class="text-gray-400 text-xs mt-0.5">{{ Str::limit($item->description, 50) }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-400 text-xs">{{ $item->service_type_label }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center text-gray-300">{{ $item->estimated_days }} hari</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-700/30 border-t border-gray-600">
                                <td colspan="3" class="px-4 py-3 text-right text-white font-bold">TOTAL</td>
                                <td class="px-4 py-3 text-right text-white font-bold">Rp {{ number_format($revision->total_cost, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="space-y-4">

            {{-- Actions Card --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow sticky top-4">
                <div class="px-5 py-4 border-b border-yellow-700 bg-yellow-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-clipboard-check text-yellow-400"></i>Tindakan
                    </h5>
                </div>
                <div class="p-5">
                    @if($revision->status == 'pending_client_approval')
                    <p class="text-gray-400 text-sm mb-4">Revisi ini menunggu persetujuan Anda. Silakan review perubahan dan pilih tindakan yang sesuai.</p>

                    <form action="{{ route('client.applications.revisions.approve', [$application->id, $revision->id]) }}" method="POST"
                          class="mb-2" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui revisi ini?')">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                            <i class="fas fa-check-circle mr-2"></i>Setuju dengan Revisi
                        </button>
                    </form>

                    <button type="button" @click="showRejectModal = true"
                            class="w-full border border-red-700 text-red-400 hover:bg-red-900/30 font-medium py-2.5 rounded-xl transition text-sm mb-2">
                        <i class="fas fa-times-circle mr-2"></i>Tolak Revisi
                    </button>

                    <a href="{{ route('client.applications.show', $application->id) }}#communication"
                       class="block w-full text-center border border-blue-600 text-blue-400 hover:bg-blue-600/10 font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-comments mr-2"></i>Diskusi dengan Admin
                    </a>

                    @else
                    <div class="flex items-start gap-3 {{ $revision->status == 'approved' ? 'bg-green-500/10 border border-green-500/30 text-green-400' : 'bg-red-500/10 border border-red-500/30 text-red-400' }} rounded-xl px-4 py-3 text-sm">
                        <i class="fas fa-{{ $revision->status == 'approved' ? 'check' : 'times' }}-circle mt-0.5 flex-shrink-0"></i>
                        <span>
                            @if($revision->status == 'approved')
                                Revisi ini sudah Anda setujui pada {{ $revision->client_approved_at->format('d M Y H:i') }}
                            @else
                                Revisi ini telah ditolak
                            @endif
                        </span>
                    </div>
                    @endif
                </div>
                <div class="px-5 py-3 border-t border-gray-700">
                    @php $supportEmail = data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id'); @endphp
                    <p class="text-gray-500 text-xs">
                        <i class="fas fa-info-circle mr-1"></i>
                        Perlu bantuan? <a href="mailto:{{ $supportEmail }}" class="text-blue-400 hover:underline">Hubungi kami</a>
                    </p>
                </div>
            </div>

            {{-- Info Aplikasi --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-blue-800 bg-blue-600/20">
                    <h6 class="text-white font-semibold flex items-center gap-2 text-sm">
                        <i class="fas fa-info text-blue-400"></i>Informasi Aplikasi
                    </h6>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    @foreach([
                        ['Nomor Aplikasi', $application->application_number],
                        ['Revisi', '#'.$revision->revision_number],
                        ['Diajukan', $revision->created_at->format('d M Y')],
                    ] as [$label, $value])
                    <div class="flex justify-between">
                        <span class="text-gray-400">{{ $label }}:</span>
                        <span class="text-white">{{ $value }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status:</span>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            @if($revision->status == 'approved') bg-green-500/20 text-green-400
                            @elseif($revision->status == 'rejected') bg-red-500/20 text-red-400
                            @else bg-yellow-500/20 text-yellow-400
                            @endif">
                            {{ ucfirst($revision->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal (Alpine.js) --}}
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showRejectModal = false">
        <div class="absolute inset-0 bg-black/60" @click="showRejectModal = false"></div>
        <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-lg">
            <form action="{{ route('client.applications.revisions.reject', [$application->id, $revision->id]) }}" method="POST">
                @csrf
                <div class="flex items-center justify-between px-6 py-4 border-b border-red-800 bg-red-600/20 rounded-t-2xl">
                    <h5 class="text-white font-semibold">Tolak Revisi</h5>
                    <button type="button" @click="showRejectModal = false" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 text-sm mb-4">Anda yakin ingin menolak revisi ini? Silakan berikan alasan penolakan:</p>
                    <div>
                        <label class="block text-sm font-medium text-white mb-1">Alasan Penolakan <span class="text-red-400">*</span></label>
                        <textarea name="rejection_reason" rows="4" required
                                  placeholder="Contoh: Biaya terlalu tinggi, saya ingin konsultasi lebih lanjut..."
                                  class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-700">
                    <button type="button" @click="showRejectModal = false"
                            class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">Batal</button>
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">Tolak Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
