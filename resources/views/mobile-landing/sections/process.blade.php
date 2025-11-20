@php
    $steps = config('landing.process_steps', []);
    $metrics = config('landing_metrics');
@endphp

<!-- PROCESS TIMELINE: How We Work -->
<section id="process" class="magazine-section bg-gradient-to-b from-gray-50 to-white fade-in-up">
    <!-- Section Header -->
    <div class="mb-8">
        <h2 class="headline text-4xl text-gray-900 mb-3">
            Alur Kerja <span class="text-gradient">Terukur</span>
        </h2>
        <p class="text-gray-600 text-sm max-w-xl leading-relaxed">
            Lima fase inti memastikan setiap izin ditangani dengan SLA terukur 
            dan komunikasi transparan.
        </p>
        
        <!-- Quick Metrics -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-green-50 px-4 py-2 rounded-full">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-sm font-semibold text-green-900">{{ $metrics['display']['sla_rate'] }} SLA</span>
            </div>
            <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-full">
                <i class="fas fa-clock text-blue-600"></i>
                <span class="text-sm font-semibold text-blue-900">{{ $metrics['display']['process_time'] }}</span>
            </div>
            <div class="flex items-center gap-2 bg-purple-50 px-4 py-2 rounded-full">
                <i class="fas fa-shield-alt text-purple-600"></i>
                <span class="text-sm font-semibold text-purple-900">100% Legal</span>
            </div>
        </div>
    </div>
    
    <!-- Timeline Steps -->
    <div class="relative space-y-6">
        <!-- Vertical Timeline Line -->
        <div class="absolute left-[19px] top-8 bottom-8 w-0.5 bg-gradient-to-b from-blue-200 via-purple-200 to-green-200"></div>
        
        @foreach($steps as $index => $step)
        <!-- Process Step Card -->
        <article class="relative pl-14">
            <!-- Step Number Badge -->
            <div class="absolute left-0 top-0 z-10 w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-lg"
                 style="background: linear-gradient(135deg, {{ $step['color'] }} 0%, {{ $step['color'] }}cc 100%);">
                {{ $index + 1 }}
            </div>
            
            <!-- Step Content Card -->
            <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background: {{ $step['color'] }}15; color: {{ $step['color'] }};">
                        <i class="{{ $step['icon'] }} text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                            {{ $step['title']['id'] }}
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $step['body']['id'] }}
                        </p>
                    </div>
                </div>
                
                @if($index === 0)
                <!-- First step highlight -->
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-gift text-blue-500"></i>
                        <span class="font-semibold">Konsultasi Gratis - Tidak ada biaya di tahap ini</span>
                    </div>
                </div>
                @endif
                
                @if($index === 3)
                <!-- SLA Monitoring highlight -->
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-bell text-orange-500"></i>
                        <span class="font-semibold">Update Progress Mingguan via WhatsApp/Portal</span>
                    </div>
                </div>
                @endif
                
                @if($index === 4)
                <!-- Final step highlight -->
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-archive text-green-500"></i>
                        <span class="font-semibold">Arsip Digital + Panduan Compliance Gratis</span>
                    </div>
                </div>
                @endif
            </div>
        </article>
        @endforeach
    </div>
    
    <!-- Trust Banner -->
    <div class="mt-10 relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
        <div class="relative z-10">
            <div class="flex items-center justify-center gap-3 mb-3">
                <i class="fas fa-shield-check text-3xl"></i>
                <h3 class="text-xl font-bold">Proses 100% Terdokumentasi</h3>
            </div>
            <p class="text-sm opacity-90 mb-5">
                Setiap tahap terekam dalam workspace digital Anda dengan timeline real-time
            </p>
            <a href="https://wa.me/{{ $metrics['contact']['whatsapp'] }}?text=Halo%20BizMark%2C%20saya%20ingin%20tahu%20lebih%20detail%20tentang%20proses%20perizinan" 
               class="inline-block bg-white text-blue-600 font-semibold px-6 py-3 rounded-xl hover:shadow-lg transition-all">
                <i class="fab fa-whatsapp mr-2"></i>Tanya Proses Detail
            </a>
        </div>
        
        <!-- Decorative -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    </div>
</section>
