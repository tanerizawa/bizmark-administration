<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Instructions - {{ $testSession->testTemplate->title }}</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS Browser Build -->
    <script src="{{ asset('js/tailwind-browser.js') }}" type="module"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>

<div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block bg-purple-100 dark:bg-purple-900 p-4 rounded-full mb-4">
                <svg class="w-16 h-16 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $testSession->testTemplate->title }}</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $testSession->jobApplication?->jobVacancy?->title ?? 'Position' }}</p>
        </div>

        <!-- Test Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Test Information</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $testSession->testTemplate->total_questions }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Questions</div>
                </div>
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $testSession->testTemplate->duration_minutes }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Minutes</div>
                </div>
                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $testSession->testTemplate->passing_score }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Passing Score</div>
                </div>
                <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">1</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Attempt</div>
                </div>
            </div>

            @if($testSession->testTemplate->description)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <p class="text-gray-700 dark:text-gray-300">{{ $testSession->testTemplate->description }}</p>
            </div>
            @endif

            <!-- Expiry Warning -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800 dark:text-yellow-300">Test Expires In</p>
                        <p class="text-yellow-700 dark:text-yellow-400 mt-1">
                            {{ $testSession->expires_at->diffForHumans() }} ({{ $testSession->expires_at->format('d M Y, H:i') }})
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Rules -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6 flex items-center">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Important Rules
            </h2>

            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center mr-3">
                        <span class="text-red-600 dark:text-red-400 font-bold text-sm">1</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Single Attempt Only</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">You can only take this test once. Make sure you're ready before starting.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center mr-3">
                        <span class="text-red-600 dark:text-red-400 font-bold text-sm">2</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Time Limit Enforced</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">You have {{ $testSession->testTemplate->duration_minutes }} minutes to complete. The test will auto-submit when time expires.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center mr-3">
                        <span class="text-red-600 dark:text-red-400 font-bold text-sm">3</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">No Tab Switching</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Do not switch tabs or minimize the browser. Tab switches are tracked and may result in disqualification.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center mr-3">
                        <span class="text-red-600 dark:text-red-400 font-bold text-sm">4</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Full Screen Required</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">The test will run in full-screen mode. Press F11 or click the full-screen button when prompted.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                        <span class="text-green-600 dark:text-green-400 font-bold text-sm">5</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Auto-Save Enabled</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Your answers are automatically saved. But we recommend completing the test in one sitting.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                        <span class="text-green-600 dark:text-green-400 font-bold text-sm">6</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Submit to Complete</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Remember to click "Submit Test" when finished. Incomplete tests won't be evaluated.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Requirements -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Technical Requirements</h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Desktop or Laptop</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Mobile devices are not supported</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Modern Browser</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Chrome, Firefox, Safari, or Edge (latest)</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">Stable Internet</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Minimum 2 Mbps connection</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">JavaScript Enabled</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Required for test functionality</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips for Success -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Tips for Success</h2>
            
            <ul class="space-y-3">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Find a quiet environment free from distractions</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Ensure your device is fully charged or plugged in</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Close all unnecessary applications and browser tabs</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Read each question carefully before answering</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Manage your time wisely - don't spend too long on any question</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-700 dark:text-gray-300">Stay calm and focused throughout the assessment</span>
                </li>
            </ul>
        </div>

        <!-- Start Test Button -->
        <form action="{{ route('candidate.test.start', $testSession->session_token) }}" method="POST" id="startTestForm">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
                <div class="mb-6">
                    <label class="flex items-center justify-center cursor-pointer">
                        <input type="checkbox" id="agreeTerms" required class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-gray-700 dark:text-gray-300">
                            I have read and agree to all the rules and requirements
                        </span>
                    </label>
                </div>

                <button type="submit" id="startButton" disabled
                        class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed text-white px-12 py-4 rounded-lg font-bold text-xl transition duration-150 shadow-lg transform hover:scale-105 disabled:transform-none">
                    Start Test Now
                </button>

                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    Once you start, the timer begins immediately
                </p>
            </div>
        </form>

        <!-- Support -->
        <div class="text-center mt-6">
            <p class="text-gray-600 dark:text-gray-400">
                Need help? Contact us at 
                <a href="mailto:hr@bizmark.id" class="text-blue-600 dark:text-blue-400 hover:underline">hr@bizmark.id</a> or 
                <a href="tel:+6281234567890" class="text-blue-600 dark:text-blue-400 hover:underline">+62 812-3456-7890</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const agreeCheckbox = document.getElementById('agreeTerms');
    const startButton = document.getElementById('startButton');
    const startTestForm = document.getElementById('startTestForm');
    let isStartingTest = false;

    agreeCheckbox.addEventListener('change', function() {
        startButton.disabled = !this.checked;
    });

    // Handle form submission
    startTestForm.addEventListener('submit', function() {
        isStartingTest = true;
    });

    // Warn before leaving page (but not when starting test)
    window.addEventListener('beforeunload', function(e) {
        if (!isStartingTest && !startButton.disabled) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>

</body>
</html>