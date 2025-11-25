@extends('layouts.app')

@section('title', 'Test Management')

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                    <a href="{{ route('admin.recruitment.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Rekrutmen
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Test</span>
                </div>
                <h1 class="text-2xl font-semibold text-white leading-tight">Template & Sesi Tes</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Kelola template penilaian dan pantau sesi tes kandidat.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.tests.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Buat Template Tes
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Template</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total_templates'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Seluruh template tersedia</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Template Aktif</p>
            <p class="text-2xl font-bold text-white">{{ $stats['active_templates'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Dapat ditugaskan</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Sesi Aktif</p>
            <p class="text-2xl font-bold text-white">{{ $stats['active_sessions'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Kandidat sedang tes</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Selesai Hari Ini</p>
            <p class="text-2xl font-bold text-white">{{ $stats['completed_today'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tes selesai</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-xl p-4 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Filter</p>
                <h3 class="text-base font-semibold text-white">Susun Template</h3>
            </div>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $templates->total() }} template ditemukan</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="space-y-1">
                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Pencarian</label>
                <input type="text" id="searchTest" placeholder="Judul, tipe, deskripsi"
                       class="w-full px-3 py-2 rounded-apple text-sm text-white placeholder-gray-500"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
            </div>
            <div class="space-y-1">
                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tipe Tes</label>
                <select id="filterType" class="w-full px-3 py-2 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Tipe</option>
                    <option value="psychology">Psychology</option>
                    <option value="psychometric">Psychometric</option>
                    <option value="technical">Technical</option>
                    <option value="aptitude">Aptitude</option>
                    <option value="personality">Personality</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Status</label>
                <select id="filterStatus" class="w-full px-3 py-2 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </section>

    {{-- Templates Table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($templates->count())
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Template</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pertanyaan</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Durasi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Sesi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            @php
                                $typeColors = [
                                    'psychology' => 'rgba(10,132,255,0.2)',
                                    'psychometric' => 'rgba(255,214,10,0.2)',
                                    'technical' => 'rgba(255,69,58,0.2)',
                                    'aptitude' => 'rgba(52,199,89,0.2)',
                                    'personality' => 'rgba(191,90,242,0.2)',
                                ];
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-apple-purple to-apple-blue flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($template->title, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-white">{{ $template->title }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Dibuat {{ $template->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $typeColors[$template->test_type] ?? 'rgba(255,255,255,0.1)' }};">
                                        {{ ucfirst($template->test_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $template->total_questions }} pertanyaan
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $template->duration_minutes }} menit
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $template->test_sessions_count }} sesi
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $template->is_active ? 'rgba(52,199,89,0.2)' : 'rgba(142,142,147,0.2)' }};">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.recruitment.tests.show', $template) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('admin.recruitment.tests.edit', $template) }}" class="btn-primary-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.recruitment.tests.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,0.95); border-color: rgba(255,59,48,0.4);">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($templates->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $templates->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 space-y-3" style="color: rgba(235,235,245,0.7);">
                <i class="fas fa-clipboard-list text-4xl"></i>
                <p class="text-sm">Belum ada template tes.</p>
                <a href="{{ route('admin.recruitment.tests.create') }}" class="btn-primary-sm">
                    <i class="fas fa-plus mr-2"></i>Buat Template Pertama
                </a>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTest');
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const rows = document.querySelectorAll('tbody tr');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilter = filterType.value;
        const statusFilter = filterStatus.value;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = text.includes(searchTerm);
            const typeCell = row.querySelector('td:nth-child(2) span');
            const statusCell = row.querySelector('td:nth-child(6) span');
            const matchesType = !typeFilter || (typeCell && typeCell.textContent.toLowerCase().includes(typeFilter));
            const matchesStatus = !statusFilter || (statusCell && statusCell.textContent.toLowerCase().includes(statusFilter));
            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);
    filterType.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
});
</script>
@endpush
