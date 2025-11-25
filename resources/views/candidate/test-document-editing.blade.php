<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $testSession->testTemplate->title }}</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS Browser Build -->
    <script src="{{ asset('js/tailwind-browser.js') }}" type="module"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        html {
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #000000;
            overflow-x: hidden;
        }
        
        /* Apple Design System Classes */
        .card-elevated {
            background: rgba(28,28,30,0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .rounded-apple-lg { border-radius: 12px; }
        .rounded-apple-xl { border-radius: 16px; }
        .rounded-apple-2xl { border-radius: 24px; }
        
        .btn-primary {
            background: linear-gradient(135deg, #0A84FF 0%, #0051D5 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(10,132,255,0.3);
        }
        
        .btn-secondary {
            background: rgba(142,142,147,0.2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #34C759 0%, #28A745 100%);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 16px;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(52,199,89,0.4);
        }
        
        .timer-warning {
            animation: pulse 1s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="card-elevated sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo & Title -->
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Bizmark" class="h-8">
                    <div>
                        <h1 class="text-white text-lg font-bold">{{ $testSession->testTemplate->title }}</h1>
                        <p class="text-gray-400 text-sm">{{ $testSession->jobApplication?->jobVacancy?->title ?? 'Assessment Test' }}</p>
                    </div>
                </div>
                
                <!-- Timer -->
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-gray-400 text-xs mb-1">Waktu Tersisa</p>
                        <p id="timer" class="text-xl font-bold text-white"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Switch Warning Banner -->
    <div id="warningBanner" class="hidden bg-yellow-500/20 border-b border-yellow-500/30 py-3">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                <p class="text-yellow-100 text-sm">
                    <strong>Peringatan:</strong> Anda telah berpindah tab/window 
                    <span id="tabSwitchCount" class="font-bold">0</span> kali. 
                    Ini akan mempengaruhi penilaian Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-6 py-8">
        <!-- Instructions Card -->
        <div class="card-elevated rounded-apple-2xl p-6 md:p-8 mb-6">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-apple-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-word text-xl text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl md:text-2xl font-bold text-white mb-2">Tes Editing Dokumen</h2>
                    <p class="text-gray-400 text-sm">Unduh template dokumen, edit sesuai instruksi, dan unggah kembali hasil Anda.</p>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-gray-800/50 rounded-apple-lg p-4 md:p-6 mb-6">
                <h3 class="text-base md:text-lg font-semibold text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-list-check text-blue-400"></i>
                    Instruksi
                </h3>
                <div class="space-y-3 text-gray-300 text-sm">
                    @if($testSession->testTemplate->instructions)
                        {!! nl2br(e($testSession->testTemplate->instructions)) !!}
                    @else
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Unduh template dokumen dengan mengklik tombol "Unduh Template" di bawah</li>
                            <li>Edit dokumen sesuai dengan kriteria yang diminta</li>
                            <li>Pastikan semua persyaratan terpenuhi sebelum mengunggah</li>
                            <li>Unggah hasil editing Anda dengan mengklik tombol "Unggah Hasil"</li>
                            <li>Submit tes setelah Anda yakin dengan hasil pekerjaan Anda</li>
                        </ol>
                    @endif
                </div>
            </div>

            <!-- Evaluation Criteria -->
            @if($testSession->testTemplate->evaluation_criteria && isset($testSession->testTemplate->evaluation_criteria['criteria']))
            <div class="bg-gray-800/50 rounded-apple-lg p-4 md:p-6 mb-6">
                <h3 class="text-base md:text-lg font-semibold text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-400"></i>
                    Kriteria Penilaian
                </h3>
                <div class="space-y-3">
                    @php
                        $groupedCriteria = collect($testSession->testTemplate->evaluation_criteria['criteria'])->groupBy('category');
                    @endphp
                    
                    @foreach($groupedCriteria as $category => $criteria)
                        <div class="bg-gray-900/30 rounded-lg p-3">
                            <h4 class="text-xs md:text-sm font-semibold text-blue-400 mb-2">{{ $category }}</h4>
                            <div class="space-y-1">
                                @foreach($criteria as $criterion)
                                <div class="flex items-start justify-between gap-3 py-1">
                                    <span class="text-gray-300 text-xs flex-1">{{ $criterion['description'] }}</span>
                                    <span class="text-blue-400 font-semibold text-xs flex-shrink-0">{{ $criterion['points'] }}pt</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-3 pt-3 border-t border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-white font-bold text-sm">Total Poin</span>
                            <span class="text-green-400 font-bold text-base">{{ $testSession->testTemplate->evaluation_criteria['total_points'] ?? 100 }} poin</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Template Download -->
            <div class="bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-apple-xl p-4 md:p-6 mb-6 border border-blue-500/30">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-apple-lg bg-blue-500/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-download text-lg md:text-xl text-blue-400"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-white font-semibold text-sm md:text-base">Template Dokumen</h4>
                            <p class="text-gray-400 text-xs md:text-sm truncate">{{ basename($testSession->testTemplate->template_file_path) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('candidate.test.download-template', $testSession->session_token) }}" 
                       class="btn-primary text-sm w-full md:w-auto text-center whitespace-nowrap"
                       download>
                        <i class="fas fa-download mr-2"></i>
                        Unduh Template
                    </a>
                </div>
            </div>

            <!-- File Upload -->
            <div class="bg-gray-800/50 rounded-apple-xl p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-upload text-green-400"></i>
                    Unggah Hasil Editing
                </h3>
                
                <form action="{{ route('candidate.test.submit-document', $testSession->session_token) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      onsubmit="return handleSubmit(event)">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-400 mb-2 text-xs md:text-sm">Pilih file hasil editing Anda (Word/PDF)</label>
                        <input type="file" 
                               id="documentFile" 
                               name="document" 
                               accept=".doc,.docx,.pdf"
                               class="w-full px-3 py-2 md:px-4 md:py-3 rounded-apple-lg bg-gray-900/50 border border-gray-700 text-white text-sm focus:border-blue-500 focus:outline-none"
                               required>
                        <p class="text-gray-500 text-xs mt-2">Format: .doc, .docx, .pdf (Max 10MB)</p>
                    </div>
                    
                    <button type="submit" 
                            id="uploadBtn"
                            class="btn-primary w-full text-sm md:text-base">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                        Unggah & Submit Dokumen
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card-elevated rounded-apple-2xl p-4 md:p-6 mt-6">
            <div class="flex items-start gap-3 text-gray-400 text-xs md:text-sm">
                <i class="fas fa-info-circle flex-shrink-0 mt-0.5"></i>
                <p>Setelah mengunggah dokumen, tes akan otomatis diselesaikan dan menunggu penilaian dari evaluator.</p>
            </div>
        </div>
    </div>

    <script>
        // Timer
        let remainingSeconds = Math.floor({{ $remainingMinutes * 60 }});
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            if (remainingSeconds <= 0) {
                // Time's up - show message and disable upload
                timerElement.textContent = '00:00:00';
                timerElement.classList.add('text-red-500', 'timer-warning');
                alert('Waktu tes telah habis. Halaman akan dimuat ulang.');
                window.location.reload();
                return;
            }
            
            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;
            
            timerElement.textContent = 
                `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            // Warning when less than 5 minutes
            if (remainingSeconds <= 300) {
                timerElement.classList.add('text-red-500', 'timer-warning');
            }
            
            remainingSeconds--;
        }
        
        // Update timer every second
        setInterval(updateTimer, 1000);
        updateTimer();

        // Tab switching detection
        let tabSwitchCount = 0;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                tabSwitchCount++;
                document.getElementById('tabSwitchCount').textContent = tabSwitchCount;
                document.getElementById('warningBanner').classList.remove('hidden');
                
                // Send to backend
                fetch('{{ route("candidate.test.track-tab", $testSession->session_token) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ count: tabSwitchCount })
                });
            }
        });

        // Form submission handler
        let isSubmitting = false; // Flag to prevent beforeunload warning
        
        function handleSubmit(event) {
            const fileInput = document.getElementById('documentFile');
            const submitBtn = document.getElementById('uploadBtn');
            
            if (!fileInput.files[0]) {
                alert('Pilih file terlebih dahulu');
                event.preventDefault();
                return false;
            }

            // Check file size (10MB)
            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 10MB');
                event.preventDefault();
                return false;
            }

            // Confirm submission
            if (!confirm('Apakah Anda yakin ingin mengunggah dan menyelesaikan tes ini? Anda tidak dapat mengubah setelah submit.')) {
                event.preventDefault();
                return false;
            }

            // Set flag to prevent beforeunload warning
            isSubmitting = true;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengunggah...';
            
            return true;
        }

        // Prevent accidental page close (but allow when submitting)
        window.addEventListener('beforeunload', function(e) {
            if (!isSubmitting) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Disable right-click
        document.addEventListener('contextmenu', e => e.preventDefault());
    </script>
</body>
</html>
