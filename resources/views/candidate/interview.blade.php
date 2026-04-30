<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview - {{ $interview->jobApplication?->jobVacancy?->title ?? 'Position' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
        }
        .interview-card {
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        .countdown-timer {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            margin: 2rem 0;
            color: #667eea;
        }
        .tip-item {
            padding: 0.75rem;
            border-left: 3px solid #667eea;
            margin-bottom: 0.5rem;
            background: #f8fafc;
            border-radius: 0 0.375rem 0.375rem 0;
        }
        .join-button {
            font-size: 1.5rem;
            padding: 1rem 3rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            background: rgba(0,0,0,0.5);
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 0.5rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="interview-card">
        <!-- Header -->
        <div class="header-section text-center">
            <h1 class="text-3xl font-bold mb-2 flex items-center justify-center gap-2">
                <i class="fas fa-video"></i>
                Interview Invitation
            </h1>
            <p class="opacity-75">{{ config('app.name') }}</p>
        </div>

        <!-- Main Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ session('error') }}
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Candidate & Position Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h5 class="text-gray-500 text-sm mb-2 uppercase tracking-wide">Candidate</h5>
                    <h3 class="text-xl font-bold text-gray-900">{{ $interview->jobApplication->full_name }}</h3>
                    <p class="text-gray-600 flex items-center gap-2 mt-1">
                        <i class="fas fa-envelope text-gray-400"></i>
                        {{ $interview->jobApplication->email }}
                    </p>
                </div>
                <div>
                    <h5 class="text-gray-500 text-sm mb-2 uppercase tracking-wide">Position</h5>
                    <h3 class="text-xl font-bold text-gray-900">{{ $interview->jobApplication?->jobVacancy?->title ?? 'Position' }}</h3>
                    <p class="text-gray-600 flex items-center gap-2 mt-1">
                        <i class="fas fa-building text-gray-400"></i>
                        {{ config('app.name') }}
                    </p>
                </div>
            </div>

            <hr>

            <!-- Interview Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <i class="fas fa-calendar-alt text-2xl text-indigo-500 w-8"></i>
                    <div>
                        <small class="text-gray-500 block text-sm">Date</small>
                        <strong class="text-lg text-gray-900">{{ $interview->scheduled_at->format('l, F j, Y') }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-clock text-2xl text-indigo-500 w-8"></i>
                    <div>
                        <small class="text-gray-500 block text-sm">Time</small>
                        <strong class="text-lg text-gray-900">{{ $interview->scheduled_at->format('H:i') }} WIB</strong>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-hourglass-half text-2xl text-indigo-500 w-8"></i>
                    <div>
                        <small class="text-gray-500 block text-sm">Duration</small>
                        <strong class="text-lg text-gray-900">{{ $interview->duration_minutes }} minutes</strong>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-video text-2xl text-indigo-500 w-8"></i>
                    <div>
                        <small class="text-gray-500 block text-sm">Type</small>
                        <strong class="text-lg text-gray-900">{{ $interview->getMeetingTypeLabel() }}</strong>
                    </div>
                </div>
            </div>

            <!-- Countdown or Join Button -->
            @if($canJoin)
                <div class="text-center my-8">
                    <h4 class="text-xl font-bold text-green-600 mb-4 flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        Your interview is ready!
                    </h4>
                    <a href="{{ route('candidate.interview.join', $interview->access_token) }}" 
                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-full join-button transition-colors">
                        <i class="fas fa-video"></i>
                        Join Interview Now
                    </a>
                    <p class="mt-4 text-gray-500 text-sm">
                        Meeting link: {{ parse_url($interview->meeting_link, PHP_URL_HOST) }}
                    </p>
                </div>
            @else
                <div class="text-center my-8">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Time until your interview:</h4>
                    <div class="countdown-timer" id="countdown">
                        {{ $timeUntil }}
                    </div>
                    <p class="text-gray-500 flex items-center justify-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        You can join 15 minutes before scheduled time
                    </p>
                </div>
            @endif

            <hr>

            <!-- Preparation Tips -->
            <div class="mb-6">
                <h5 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                    Preparation Tips
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div>
                        @foreach(array_slice($tips, 0, ceil(count($tips)/2)) as $tip)
                            <div class="tip-item flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span class="text-gray-700 text-sm">{{ $tip }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        @foreach(array_slice($tips, ceil(count($tips)/2)) as $tip)
                            <div class="tip-item flex items-start gap-2">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <span class="text-gray-700 text-sm">{{ $tip }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reschedule Request -->
            @if($interview->scheduled_at->subHours(24)->isFuture() && $interview->status === 'scheduled')
                <div class="bg-gray-50 border border-gray-200 rounded-lg mt-6 p-4">
                    <h6 class="font-semibold text-gray-800 mb-2">Need to Reschedule?</h6>
                    <p class="text-sm text-gray-600 mb-3">
                        If you can't attend, please request reschedule at least 24 hours before the interview.
                    </p>
                    <button type="button" 
                            onclick="document.getElementById('rescheduleModal').classList.add('active')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-calendar-times"></i>
                        Request Reschedule
                    </button>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 text-center">
            <p class="text-sm text-gray-500 flex items-center justify-center gap-2">
                <i class="fas fa-question-circle"></i>
                Need help? Contact HR at {{ config('mail.from.address') }}
            </p>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="modal" onclick="if(event.target === this) this.classList.remove('active')">
        <div class="modal-content">
            <form action="{{ route('candidate.interview.reschedule', $interview->access_token) }}" method="POST">
                @csrf
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-800">Request Reschedule</h5>
                    <button type="button" onclick="document.getElementById('rescheduleModal').classList.remove('active')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                        <textarea name="reason" 
                                  id="reason" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                  rows="3" 
                                  required
                                  placeholder="Please explain why you need to reschedule"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Alternative Dates *</label>
                        <input type="datetime-local" 
                               name="preferred_dates[]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-2" 
                               min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}"
                               required>
                        <input type="datetime-local" 
                               name="preferred_dates[]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-2"
                               min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}">
                        <input type="datetime-local" 
                               name="preferred_dates[]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}">
                        <small class="text-gray-500 text-xs">Provide at least one alternative date</small>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 p-4 border-t border-gray-200">
                    <button type="button" onclick="document.getElementById('rescheduleModal').classList.remove('active')" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-refresh countdown every minute
        setInterval(() => {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
