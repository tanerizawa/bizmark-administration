    {{-- Compact Hero section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden" role="region" aria-labelledby="dashboard-hero">
        {{-- Decorative Background --}}
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Pusat Operasional</p>
                <h1 id="dashboard-hero" class="admin-hero-title text-white">Ringkasan Eksekutif</h1>
                <p class="admin-hero-desc">Pantau KPI, arus kas, dan perkembangan proyek</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-clock mr-1.5"></i>{{ now()->format('d M Y, H:i') }}</span>
                    <span><i class="fas fa-user-shield mr-1.5"></i>Direksi & Kepala Ops</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.index') }}"
                   class="admin-btn admin-btn-sm rounded bg-apple-blue/25 text-white">
                    <i class="fas fa-project-diagram mr-1"></i>Proyek
                </a>
                <a href="{{ route('dashboard') }}"
                   class="admin-btn admin-btn-sm rounded" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.8);">
                    <i class="fas fa-arrows-rotate"></i>
                </a>
            </div>
        </div>
    </section>

