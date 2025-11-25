<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview - {{ $interview->jobApplication?->jobVacancy?->title ?? 'Position' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .interview-card {
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .countdown-timer {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            margin: 2rem 0;
        }
        .tip-item {
            padding: 0.75rem;
            border-left: 3px solid #667eea;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="interview-card card">
        <!-- Header -->
        <div class="header-section text-center">
            <h1 class="mb-2">
                <i class="bi bi-camera-video me-2"></i>
                Interview Invitation
            </h1>
            <p class="mb-0 opacity-75">{{ config('app.name') }}</p>
        </div>

        <!-- Main Content -->
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Candidate & Position Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-muted mb-2">Candidate</h5>
                    <h3>{{ $interview->jobApplication->full_name }}</h3>
                    <p class="mb-0">
                        <i class="bi bi-envelope me-1"></i>
                        {{ $interview->jobApplication->email }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted mb-2">Position</h5>
                    <h3>{{ $interview->jobApplication?->jobVacancy?->title ?? 'Position' }}</h3>
                    <p class="mb-0">
                        <i class="bi bi-building me-1"></i>
                        {{ config('app.name') }}
                    </p>
                </div>
            </div>

            <hr>

            <!-- Interview Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-calendar3 fs-2 text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Date</small>
                            <strong class="fs-5">{{ $interview->scheduled_at->format('l, F j, Y') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock fs-2 text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Time</small>
                            <strong class="fs-5">{{ $interview->scheduled_at->format('H:i') }} WIB</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-hourglass-split fs-2 text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Duration</small>
                            <strong class="fs-5">{{ $interview->duration_minutes }} minutes</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-camera-video fs-2 text-primary me-3"></i>
                        <div>
                            <small class="text-muted d-block">Type</small>
                            <strong class="fs-5">{{ $interview->getMeetingTypeLabel() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Countdown or Join Button -->
            @if($canJoin)
                <div class="text-center my-5">
                    <h4 class="mb-4 text-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Your interview is ready!
                    </h4>
                    <a href="{{ route('candidate.interview.join', $interview->access_token) }}" 
                       class="btn btn-success btn-lg join-button">
                        <i class="bi bi-camera-video me-2"></i>
                        Join Interview Now
                    </a>
                    <p class="mt-3 text-muted">
                        <small>Meeting link: {{ parse_url($interview->meeting_link, PHP_URL_HOST) }}</small>
                    </p>
                </div>
            @else
                <div class="text-center my-5">
                    <h4 class="mb-3">Time until your interview:</h4>
                    <div class="countdown-timer text-primary" id="countdown">
                        {{ $timeUntil }}
                    </div>
                    <p class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        You can join 15 minutes before scheduled time
                    </p>
                </div>
            @endif

            <hr>

            <!-- Preparation Tips -->
            <div class="mb-4">
                <h5 class="mb-3">
                    <i class="bi bi-lightbulb text-warning me-2"></i>
                    Preparation Tips
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        @foreach(array_slice($tips, 0, ceil(count($tips)/2)) as $tip)
                            <div class="tip-item">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                {{ $tip }}
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        @foreach(array_slice($tips, ceil(count($tips)/2)) as $tip)
                            <div class="tip-item">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                {{ $tip }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Reschedule Request -->
            @if($interview->scheduled_at->subHours(24)->isFuture() && $interview->status === 'scheduled')
                <div class="card bg-light mt-4">
                    <div class="card-body">
                        <h6 class="card-title">Need to Reschedule?</h6>
                        <p class="small mb-3">
                            If you can't attend, please request reschedule at least 24 hours before the interview.
                        </p>
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#rescheduleModal">
                            <i class="bi bi-calendar-x me-1"></i>
                            Request Reschedule
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="card-footer text-center text-muted">
            <small>
                <i class="bi bi-question-circle me-1"></i>
                Need help? Contact HR at {{ config('mail.from.address') }}
            </small>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('candidate.interview.reschedule', $interview->access_token) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Request Reschedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason *</label>
                            <textarea name="reason" 
                                      id="reason" 
                                      class="form-control" 
                                      rows="3" 
                                      required
                                      placeholder="Please explain why you need to reschedule"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferred Alternative Dates *</label>
                            <input type="datetime-local" 
                                   name="preferred_dates[]" 
                                   class="form-control mb-2" 
                                   min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}"
                                   required>
                            <input type="datetime-local" 
                                   name="preferred_dates[]" 
                                   class="form-control mb-2"
                                   min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}">
                            <input type="datetime-local" 
                                   name="preferred_dates[]" 
                                   class="form-control"
                                   min="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}">
                            <small class="text-muted">Provide at least one alternative date</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh countdown every minute
        setInterval(() => {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
