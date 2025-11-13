{{-- Trusted Clients Section - Corporate --}}
<section class="py-24 lg:py-32 bg-gradient-to-b from-white to-gray-50">
    <div class="container-wide space-y-14">
        @php($clientList = config('landing.clients', []))

        <div class="text-center max-w-3xl mx-auto space-y-4" data-aos="fade-up">
            <div class="pill pill-brand mx-auto justify-center">
                Dipercaya Oleh
            </div>
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                Partner Strategis dari Industri Manufaktur, Infrastruktur, hingga Energi
            </h2>
            <p class="text-lg text-gray-600">
                Lebih dari 100 organisasi mempercayakan audit izin, penyusunan AMDAL, dan pengelolaan legalitasnya kepada tim Bizmark.ID demi memastikan tata kelola yang konsisten.
            </p>
        </div>

        {{-- Client Logos Grid --}}
        <div class="relative" data-aos="fade-up" data-aos-delay="80">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 lg:gap-8 xl:gap-10" role="list" aria-label="Daftar klien Bizmark.ID">
                @foreach($clientList as $index => $clientName)
                <article class="client-logo-card group" role="listitem" data-aos="zoom-in" data-aos-delay="{{ 80 + ($index * 40) }}">
                    <div class="relative aspect-[3/2] flex items-center justify-center p-6 bg-white rounded-2xl border border-slate-100 transition-all duration-400 hover:-translate-y-1 hover:border-primary/30 hover:bg-primary/5">
                        <div class="text-center grayscale group-hover:grayscale-0 transition-all duration-500">
                            <div class="monogram mb-1">
                                {{ strtoupper(substr($clientName, 0, 2)) }}
                            </div>
                            <div class="text-sm text-slate-500 group-hover:text-slate-700 font-medium leading-tight">
                                {{ $clientName }}
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="absolute -top-8 -left-8 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10"></div>
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-secondary/10 rounded-full blur-3xl -z-10"></div>
        </div>

        {{-- Bottom Note --}}
        <div class="text-center space-y-3" data-aos="fade-up" data-aos-delay="160">
            <p class="text-sm uppercase tracking-[0.4em] text-gray-400">Reference Projects</p>
            <p class="text-base text-gray-600">Kumpulan studi kasus tersedia berdasarkan NDA – hubungi tim kami untuk penjadwalan review.</p>
        </div>
    </div>
</section>
