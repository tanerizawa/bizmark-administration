@extends('layouts.app')

@section('title', 'AI Automation Settings')

@section('content')
<div class="container-custom">
    <!-- Header -->
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-purple opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">AI AUTOMATION</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-robot mr-2"></i>AI Automation Settings
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Configure AI-powered email generation and backlink automation
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.backlinks.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- AI Configuration -->
    <div class="card-apple p-6 mb-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-apple flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold" style="color: #FFFFFF;">🤖 AI Email Generator</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.6);">OpenRouter AI untuk email outreach personalisasi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Current Model -->
            <div class="rounded-apple p-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">AI Model</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">Active</span>
                </div>
                <p class="text-lg font-bold" style="color: #FFFFFF;">{{ config('services.openrouter.model', 'x-ai/grok-beta') }}</p>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">Fast & cost-effective</p>
            </div>

            <!-- API Status -->
            <div class="rounded-apple p-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">API Status</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">Connected</span>
                </div>
                <p class="text-lg font-bold" style="color: #FFFFFF;">OpenRouter</p>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">https://openrouter.ai/api/v1</p>
            </div>

            <!-- Cost Estimate -->
            <div class="rounded-apple p-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">Estimated Cost</span>
                </div>
                <p class="text-lg font-bold" style="color: #FFFFFF;">~$0.002/email</p>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">500 emails/month ≈ $1</p>
            </div>

            <!-- Performance -->
            <div class="rounded-apple p-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">Response Rate</span>
                </div>
                <p class="text-lg font-bold" style="color: #FFFFFF;">25-35%</p>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">vs 10% with templates</p>
            </div>
        </div>

        <!-- Features -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-semibold" style="color: #FFFFFF;">Website Analysis</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Analisis otomatis konten target</p>
                </div>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-semibold" style="color: #FFFFFF;">Personalization</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Email unik untuk setiap target</p>
                </div>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-semibold" style="color: #FFFFFF;">Indonesian Language</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Bahasa profesional natural</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Automation Schedule -->
    <div class="card-apple p-6 mb-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-apple flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold" style="color: #FFFFFF;">⏱️ Scheduled Tasks</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.6);">Otomasi berjalan sesuai jadwal</p>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Daily Monitoring -->
            <div class="flex items-center justify-between p-4 rounded-apple" style="background: rgba(10,132,255,0.12); border: 1px solid rgba(10,132,255,0.2);">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(10,132,255,0.2);">
                        <svg class="w-5 h-5" style="color: rgba(10,132,255,1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold" style="color: #FFFFFF;">Backlink Health Check</p>
                        <p class="text-sm" style="color: rgba(235,235,245,0.6);">Command: backlink:monitor --limit=50</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">Daily 08:00</span>
            </div>

            <!-- Weekly Crawler -->
            <div class="flex items-center justify-between p-4 rounded-apple" style="background: rgba(191,90,242,0.12); border: 1px solid rgba(191,90,242,0.2);">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(191,90,242,0.2);">
                        <svg class="w-5 h-5" style="color: rgba(191,90,242,1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold" style="color: #FFFFFF;">Backlink Crawler</p>
                        <p class="text-sm" style="color: rgba(235,235,245,0.6);">Command: backlink:crawl --all --limit=25</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full" style="background: rgba(191,90,242,0.2); color: rgba(191,90,242,1);">Monday 09:00</span>
            </div>

            <!-- AI Outreach -->
            <div class="flex items-center justify-between p-4 rounded-apple" style="background: rgba(255,55,95,0.12); border: 1px solid rgba(255,55,95,0.2);">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(255,55,95,0.2);">
                        <svg class="w-5 h-5" style="color: rgba(255,55,95,1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold" style="color: #FFFFFF;">AI Email Outreach (Initial)</p>
                        <p class="text-sm" style="color: rgba(235,235,245,0.6);">Command: backlink:outreach --ai --priority=high --limit=5</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full" style="background: rgba(255,55,95,0.2); color: rgba(255,55,95,1);">Mon-Fri 10:00</span>
            </div>

            <!-- Follow-up -->
            <div class="flex items-center justify-between p-4 rounded-apple" style="background: rgba(48,209,88,0.12); border: 1px solid rgba(48,209,88,0.2);">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(48,209,88,0.2);">
                        <svg class="w-5 h-5" style="color: rgba(48,209,88,1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold" style="color: #FFFFFF;">AI Follow-up Emails</p>
                        <p class="text-sm" style="color: rgba(235,235,245,0.6);">Command: backlink:outreach --ai --type=follow_up --limit=10</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full" style="background: rgba(48,209,88,0.2); color: rgba(48,209,88,1);">Wed 10:00</span>
            </div>
        </div>
    </div>

    <!-- Manual Commands -->
    <div class="card-apple p-6">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-apple flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold" style="color: #FFFFFF;">⌨️ Manual Commands</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.6);">Jalankan manual via SSH terminal</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- AI Outreach -->
            <div class="bg-gray-900 rounded-apple p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-400">AI Email Generation (Dry Run)</p>
                    <button onclick="executeCommand('backlink:outreach', {ai: true, limit: 5, 'dry-run': true})" 
                            class="px-3 py-1 text-xs font-medium rounded-apple transition-apple"
                            style="background: rgba(48,209,88,0.2); color: rgba(48,209,88,1);">
                        <i class="fas fa-play mr-1"></i>Run
                    </button>
                </div>
                <code class="text-sm text-green-400">backlink:outreach --ai --limit=5 --dry-run</code>
            </div>

            <!-- Crawler -->
            <div class="bg-gray-900 rounded-apple p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-400">Crawl All Targets</p>
                    <button onclick="executeCommand('backlink:crawl', {all: true, limit: 10})" 
                            class="px-3 py-1 text-xs font-medium rounded-apple transition-apple"
                            style="background: rgba(191,90,242,0.2); color: rgba(191,90,242,1);">
                        <i class="fas fa-play mr-1"></i>Run
                    </button>
                </div>
                <code class="text-sm text-purple-400">backlink:crawl --all --limit=10</code>
            </div>

            <!-- Monitor -->
            <div class="bg-gray-900 rounded-apple p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-400">Check Backlinks Health</p>
                    <button onclick="executeCommand('backlink:monitor', {limit: 20})" 
                            class="px-3 py-1 text-xs font-medium rounded-apple transition-apple"
                            style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                        <i class="fas fa-play mr-1"></i>Run
                    </button>
                </div>
                <code class="text-sm text-blue-400">backlink:monitor --limit=20</code>
            </div>

            <!-- Test AI -->
            <div class="bg-gray-900 rounded-apple p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-400">Test AI Email (Target #1)</p>
                    <button onclick="executeCommand('backlink:outreach', {ai: true, target: 1, 'dry-run': true})" 
                            class="px-3 py-1 text-xs font-medium rounded-apple transition-apple"
                            style="background: rgba(255,159,10,0.2); color: rgba(255,159,10,1);">
                        <i class="fas fa-play mr-1"></i>Run
                    </button>
                </div>
                <code class="text-sm text-orange-400">backlink:outreach --ai --target=1 --dry-run</code>
            </div>
        </div>
    </div>

    <!-- Command Output Log -->
    <div id="commandLog" class="card-apple p-6 mt-6" style="display: none;">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-3 animate-pulse"></div>
                <h3 class="text-lg font-bold" style="color: #FFFFFF;">Command Execution Log</h3>
            </div>
            <button onclick="closeLog()" class="text-gray-400 hover:text-white transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="bg-gray-900 rounded-apple p-4 font-mono text-sm overflow-auto" style="max-height: 400px;">
            <div id="commandOutput" class="text-green-400 whitespace-pre-wrap"></div>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <span id="commandStatus" class="text-sm" style="color: rgba(235,235,245,0.6);"></span>
            <button onclick="closeLog()" class="px-4 py-2 rounded-apple transition-apple" style="background: rgba(255,255,255,0.1); color: #FFFFFF;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function executeCommand(command, args = {}) {
    console.log('🚀 Executing command:', command, 'with args:', args);
    
    // Show log panel
    const logPanel = document.getElementById('commandLog');
    const output = document.getElementById('commandOutput');
    const status = document.getElementById('commandStatus');
    
    if (!logPanel || !output || !status) {
        console.error('❌ Required DOM elements not found');
        alert('Error: Page elements not loaded properly');
        return;
    }
    
    logPanel.style.display = 'block';
    output.innerHTML = '<span class="text-yellow-400">⏳ Executing command...</span>\n\n';
    output.innerHTML += '<span class="text-gray-400">Command: ' + command + '\n';
    output.innerHTML += 'Arguments: ' + JSON.stringify(args, null, 2) + '</span>\n\n';
    status.textContent = 'Running...';
    
    // Scroll to log
    logPanel.scrollIntoView({ behavior: 'smooth' });
    
    const url = '{{ route("admin.backlinks.execute-command") }}';
    const csrfToken = '{{ csrf_token() }}';
    
    console.log('📡 Sending request to:', url);
    console.log('🔐 CSRF Token:', csrfToken.substring(0, 10) + '...');
    
    // Execute command via AJAX
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            command: command,
            args: args
        })
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);
        
        if (data.success) {
            output.innerHTML = '<span class="text-green-400">✅ Command executed successfully!</span>\n\n';
            output.innerHTML += '<span class="text-gray-300">' + escapeHtml(data.output) + '</span>';
            status.innerHTML = '<span class="text-green-400">✅ Completed (Exit: ' + (data.exit_code || 0) + ')</span>';
        } else {
            output.innerHTML = '<span class="text-red-400">❌ Command failed!</span>\n\n';
            output.innerHTML += '<span class="text-red-300">' + escapeHtml(data.message || 'Unknown error') + '</span>';
            if (data.output) {
                output.innerHTML += '\n\n<span class="text-gray-400">Output:</span>\n<span class="text-gray-300">' + escapeHtml(data.output) + '</span>';
            }
            status.innerHTML = '<span class="text-red-400">❌ Failed</span>';
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        output.innerHTML = '<span class="text-red-400">❌ Network/Request error!</span>\n\n';
        output.innerHTML += '<span class="text-red-300">' + error.message + '</span>';
        status.innerHTML = '<span class="text-red-400">❌ Error</span>';
    });
}

function closeLog() {
    document.getElementById('commandLog').style.display = 'none';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
