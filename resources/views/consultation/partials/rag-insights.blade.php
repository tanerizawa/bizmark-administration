{{-- RAG Regulation Insights Component --}}
@if(isset($consultation->rag_insights) && $consultation->rag_insights)
    @php
        $ragData = is_array($consultation->rag_insights) 
            ? $consultation->rag_insights 
            : json_decode($consultation->rag_insights, true);
    @endphp
    
    <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border border-indigo-200 dark:border-gray-700 p-8 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
            <i class="fas fa-book-open text-indigo-600 dark:text-indigo-400"></i>
            <span>Konteks Regulasi</span>
            <span class="ml-auto text-xs font-normal px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-full">
                <i class="fas fa-robot mr-1"></i> AI-Powered
            </span>
        </h2>

        <!-- RAG Answer -->
        @if(isset($ragData['answer']))
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 mb-6 border border-indigo-100 dark:border-gray-700 shadow-sm">
            <div class="prose prose-indigo dark:prose-invert max-w-none" 
                 x-data="{ expanded: false }">
                
                @php
                    // Format the answer text for better display
                    $answer = $ragData['answer'];
                    
                    // Convert markdown-style headings to HTML
                    $answer = preg_replace('/^### (.+)$/m', '<h3 class="text-lg font-bold text-gray-900 dark:text-white mt-4 mb-2 flex items-center gap-2"><i class="fas fa-folder text-indigo-600 dark:text-indigo-400 text-base"></i>$1</h3>', $answer);
                    $answer = preg_replace('/^## (.+)$/m', '<h2 class="text-xl font-bold text-gray-900 dark:text-white mt-5 mb-3">$1</h2>', $answer);
                    
                    // Convert numbered lists (1. 2. etc)
                    $answer = preg_replace_callback('/^(\d+)\.\s+\*\*(.+?)\*\*:?\s*(.*)$/m', function($matches) {
                        $number = $matches[1];
                        $bold = $matches[2];
                        $text = $matches[3];
                        return '<div class="flex gap-3 mb-3"><span class="flex-shrink-0 w-6 h-6 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-xs font-bold">' . $number . '</span><div class="flex-1"><strong class="text-gray-900 dark:text-white">' . $bold . '</strong>' . ($text ? ': <span class="text-gray-700 dark:text-gray-300">' . $text . '</span>' : '') . '</div></div>';
                    }, $answer);
                    
                    // Convert remaining numbered lists
                    $answer = preg_replace('/^(\d+)\.\s+(.+)$/m', '<div class="flex gap-3 mb-2"><span class="flex-shrink-0 w-6 h-6 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-xs font-bold">$1</span><span class="flex-1 text-gray-700 dark:text-gray-300">$2</span></div>', $answer);
                    
                    // Convert bullet points (-)
                    $answer = preg_replace('/^-\s+\*\*(.+?)\*\*:?\s*(.*)$/m', '<div class="flex gap-2 mb-2 ml-8"><span class="text-indigo-600 dark:text-indigo-400">•</span><div class="flex-1"><strong class="text-gray-900 dark:text-white">$1</strong>' . '$2' . '</div></div>', $answer);
                    $answer = preg_replace('/^-\s+(.+)$/m', '<div class="flex gap-2 mb-2 ml-8"><span class="text-indigo-600 dark:text-indigo-400">•</span><span class="flex-1 text-gray-700 dark:text-gray-300">$1</span></div>', $answer);
                    
                    // Convert **bold** text
                    $answer = preg_replace('/\*\*(.+?)\*\*/s', '<strong class="font-semibold text-gray-900 dark:text-white">$1</strong>', $answer);
                    
                    // Convert paragraphs
                    $answer = preg_replace('/^(?!<[dh]|-)(.+)$/m', '<p class="text-gray-700 dark:text-gray-300 mb-3 leading-relaxed">$1</p>', $answer);
                    
                    // Clean up extra spacing
                    $answer = preg_replace('/<\/h[23]>\s*<p>/', '</h3>', $answer);
                    $answer = preg_replace('/<\/div>\s*<p>/', '</div>', $answer);
                @endphp
                
                <!-- Collapsed view (first ~400 chars) -->
                <div x-show="!expanded">
                    @php
                        $plainText = strip_tags($answer);
                        $isLong = strlen($plainText) > 400;
                        $preview = $isLong ? Str::limit($plainText, 400) : $plainText;
                    @endphp
                    <p class="text-gray-700 dark:text-gray-300 mb-0">{{ $preview }}</p>
                    @if($isLong)
                        <button @click="expanded = true" 
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-lg font-medium text-sm transition-colors">
                            <span>Baca Selengkapnya</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Expanded view (full formatted content) -->
                <div x-show="expanded" x-cloak class="rag-content">
                    {!! \App\Support\HtmlSanitizer::clean($answer) !!}
                    @if(strlen(strip_tags($answer)) > 400)
                        <button @click="expanded = false" 
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium text-sm transition-colors">
                            <span>Tampilkan Lebih Sedikit</span>
                            <i class="fas fa-chevron-up text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(isset($ragData['activity_requirements']) && count($ragData['activity_requirements']) > 0)
        <div class="mb-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-diagram-project text-fuchsia-600"></i>
                <span>Persyaratan Per Aktivitas KBLI</span>
            </h3>
            <div class="space-y-4">
                @foreach($ragData['activity_requirements'] as $activity)
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-fuchsia-100 dark:border-gray-700 shadow-sm" x-data="{ expanded: false }">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/40 dark:text-fuchsia-300">
                                        {{ $activity['label'] ?? 'Aktivitas' }}
                                    </span>
                                    @if(isset($activity['kbli_code']))
                                        <span class="font-mono text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            KBLI {{ $activity['kbli_code'] }}
                                        </span>
                                    @endif
                                    @if(isset($activity['confidence']))
                                        <span class="text-xs font-semibold {{ $activity['confidence'] >= 0.7 ? 'text-green-600 dark:text-green-400' : ($activity['confidence'] >= 0.5 ? 'text-yellow-600 dark:text-yellow-400' : 'text-orange-600 dark:text-orange-400') }}">
                                            {{ number_format(($activity['confidence'] ?? 0) * 100, 0) }}% confidence
                                        </span>
                                    @endif
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $activity['description'] ?? 'Aktivitas usaha' }}</p>
                            </div>
                            @if(!empty($activity['answer']))
                                <button @click="expanded = !expanded" class="inline-flex items-center gap-2 self-start px-3 py-2 rounded-lg bg-fuchsia-50 text-fuchsia-700 hover:bg-fuchsia-100 dark:bg-fuchsia-900/30 dark:text-fuchsia-300 dark:hover:bg-fuchsia-900/50 text-sm font-medium transition-colors">
                                    <span x-text="expanded ? 'Ringkas' : 'Detail'"></span>
                                    <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                            @endif
                        </div>

                        @if(!empty($activity['answer']))
                            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-show="!expanded">
                                {{ \Illuminate\Support\Str::limit(strip_tags($activity['answer']), 220) }}
                            </div>
                            <div class="mt-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line" x-show="expanded" x-cloak>
                                {{ $activity['answer'] }}
                            </div>
                        @endif

                        @if(isset($activity['sources']) && count($activity['sources']) > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Sumber Aktivitas</p>
                                <div class="space-y-2">
                                    @foreach($activity['sources'] as $source)
                                        <div class="flex items-start justify-between gap-3 rounded-lg bg-gray-50 dark:bg-gray-700/60 px-3 py-2">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $source['source_name'] ?? $source['title'] ?? 'Sumber regulasi' }}</p>
                                                @if(isset($source['pasal']) || isset($source['section']))
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $source['section'] ?? ('Pasal ' . ($source['pasal'] ?? '-')) }}</p>
                                                @endif
                                            </div>
                                            @if(isset($source['score']))
                                                <span class="text-xs font-semibold text-fuchsia-600 dark:text-fuchsia-300 shrink-0">{{ number_format($source['score'] * 100, 0) }}%</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Source Documents -->
            @if(isset($ragData['sources']) && count($ragData['sources']) > 0)
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-file-alt text-indigo-600"></i>
                    <span>Sumber Regulasi</span>
                </h3>
                <div class="space-y-2">
                    @foreach(array_slice($ragData['sources'], 0, 5) as $index => $source)
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:border-indigo-300 dark:hover:border-indigo-600 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="w-6 h-6 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-xs font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ $source['source_name'] ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    
                                    @if(isset($source['pasal']) && $source['pasal'])
                                        <p class="text-xs text-gray-600 dark:text-gray-400 ml-8">
                                            Pasal {{ $source['pasal'] }}
                                            @if(isset($source['ayat']) && $source['ayat'])
                                                Ayat {{ $source['ayat'] }}
                                            @endif
                                        </p>
                                    @endif

                                    @if(isset($source['text']) && $source['text'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-8 line-clamp-2">
                                            {{ Str::limit($source['text'], 100) }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <div class="flex items-center gap-1">
                                        <div class="w-16 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all" 
                                                 style="width: {{ (($source['score'] ?? 0) * 100) }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format(($source['score'] ?? 0) * 100, 0) }}%
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">relevansi</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($ragData['sources']) > 5)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Menampilkan 5 dari {{ count($ragData['sources']) }} sumber regulasi
                    </p>
                @endif
            </div>
            @endif

            <!-- Confidence & Metadata -->
            <div>
                <!-- Confidence Score -->
                @if(isset($consultation->rag_confidence))
                <div class="mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line text-green-600"></i>
                        <span>Tingkat Keyakinan</span>
                    </h3>
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Confidence Score</span>
                            <span class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                                {{ number_format($consultation->rag_confidence * 100, 1) }}%
                            </span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: {{ $consultation->rag_confidence * 100 }}%"></div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                @if($consultation->rag_confidence >= 0.7)
                                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                    <span class="font-medium text-green-600 dark:text-green-400">Tinggi</span> - Informasi regulasi sangat relevan dan akurat
                                @elseif($consultation->rag_confidence >= 0.5)
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">Baik</span> - Informasi regulasi cukup relevan sebagai referensi
                                @else
                                    <i class="fas fa-exclamation-circle text-orange-500 mr-1"></i>
                                    <span class="font-medium text-orange-600 dark:text-orange-400">Umum</span> - Informasi bersifat umum, konsultasi lebih lanjut disarankan
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Query Metadata -->
                <div class="bg-white dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        <span>Informasi Query</span>
                    </h3>
                    <div class="space-y-3 text-sm">
                        @if(isset($ragData['query_type']))
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Tipe Query</span>
                                <span class="font-medium text-gray-900 dark:text-white capitalize">
                                    {{ str_replace('_', ' ', $ragData['query_type']) }}
                                </span>
                            </div>
                        @endif

                        @if(isset($ragData['query_params']['entity_type']))
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Jenis Badan Usaha</span>
                                <span class="font-medium text-gray-900 dark:text-white uppercase">
                                    {{ $ragData['query_params']['entity_type'] }}
                                </span>
                            </div>
                        @endif

                        @if(isset($ragData['query_params']['location']))
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Lokasi</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $ragData['query_params']['location'] }}
                                </span>
                            </div>
                        @endif

                        @if(isset($ragData['query_params']['primary_kbli']))
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">KBLI Utama</span>
                                <span class="font-medium font-mono text-gray-900 dark:text-white">
                                    {{ $ragData['query_params']['primary_kbli'] }}
                                </span>
                            </div>
                        @endif

                        @if(isset($ragData['query_params']['activities_count']))
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Aktivitas Dianalisis</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $ragData['query_params']['activities_count'] }}
                                </span>
                            </div>
                        @endif

                        @if(isset($consultation->rag_processed_at))
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-gray-600 dark:text-gray-400">Diproses</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $consultation->rag_processed_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Disclaimer -->
        <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">
                        Informasi Penting
                    </p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 leading-relaxed">
                        Konteks regulasi di atas dihasilkan oleh AI berdasarkan database regulasi Indonesia. 
                        Untuk kepastian hukum dan detail persyaratan terkini, silakan konsultasi langsung dengan tim kami.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Alpine.js x-cloak style + RAG content formatting --}}
@push('styles')
<style>
    [x-cloak] { display: none !important; }
    
    /* RAG Content Formatting */
    .rag-content h2 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .rag-content h3 {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .rag-content h3:first-child,
    .rag-content h2:first-child {
        margin-top: 0;
    }
    
    .rag-content p:empty {
        display: none;
    }
    
    .rag-content > p:last-child,
    .rag-content > div:last-child {
        margin-bottom: 0;
    }
    
    /* Spacing for list groups */
    .rag-content h3 + div[class*="flex gap-"] {
        margin-top: 0.75rem;
    }
    
    .rag-content div[class*="flex gap-"] + h3 {
        margin-top: 1.25rem;
    }
</style>
@endpush
