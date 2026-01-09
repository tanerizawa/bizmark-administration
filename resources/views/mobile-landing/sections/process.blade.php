@php
    $currentLocale = app()->getLocale();
    $isEnglish = $currentLocale === 'en';
    $steps = config('landing.process_steps', []);
@endphp

<!-- Process Section - Dark Theme -->
<section id="process" class="py-20 px-4 bg-black">
    <div class="container mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                {{ $isEnglish ? 'Our' : 'Alur' }} 
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-green-400">
                    {{ $isEnglish ? 'Process' : 'Kerja Kami' }}
                </span>
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto">
                {{ $isEnglish ? 'Five core phases ensure every permit is handled with measurable SLA' : 'Lima fase inti memastikan setiap izin ditangani dengan SLA terukur' }}
            </p>
        </div>

        <!-- Timeline Steps -->
        <div class="max-w-4xl mx-auto space-y-8">
            @foreach($steps as $index => $step)
            <!-- Step Card -->
            <div class="section p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                         style="background: linear-gradient(135deg, {{ $step['color'] }}, {{ $step['color'] }}dd);">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">
                            {{ $isEnglish ? $step['title']['en'] : $step['title']['id'] }}
                        </h3>
                        <p class="text-gray-400">
                            {{ $isEnglish ? $step['body']['en'] : $step['body']['id'] }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.section {
    background: #1C1C1E;
    border-radius: 1rem;
    border: 1px solid rgba(84,84,88,0.35);
    transition: all 0.3s ease;
}

.section:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px 0 rgba(0, 122, 255, 0.2);
}
</style>
