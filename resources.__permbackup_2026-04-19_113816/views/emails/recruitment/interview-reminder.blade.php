<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Reminder</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .urgent-banner {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .urgent-banner h1 {
            margin: 0;
            font-size: 28px;
            color: white;
        }
        .urgent-banner .time {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .countdown {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }
        .countdown-number {
            font-size: 48px;
            font-weight: bold;
            color: #92400e;
            margin: 10px 0;
        }
        .countdown-label {
            color: #78350f;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .info-label {
            font-weight: 600;
            color: #1e40af;
            min-width: 120px;
        }
        .info-value {
            color: #374151;
        }
        .button-primary {
            display: inline-block;
            background-color: #f59e0b;
            color: #ffffff !important;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 8px;
            margin: 25px 0;
            font-weight: 700;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
        }
        .button-primary:hover {
            background-color: #d97706;
        }
        .checklist {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .checklist h3 {
            color: #166534;
            margin-top: 0;
            font-size: 18px;
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
        }
        .checklist-icon {
            min-width: 24px;
            font-size: 18px;
            margin-right: 12px;
        }
        .checklist-text {
            color: #166534;
            font-size: 14px;
        }
        .alert {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            color: #991b1b;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .urgent-banner .time {
                font-size: 28px;
            }
            .countdown-number {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="urgent-banner">
            <div style="font-size: 48px; margin-bottom: 10px;">⏰</div>
            <h1>Interview Reminder</h1>
            <div class="time">{{ $interview->scheduled_at->format('H:i') }} WIB</div>
            <p style="margin: 5px 0 0 0; font-size: 16px;">{{ $interview->scheduled_at->format('l, d F Y') }}</p>
        </div>

        <div class="countdown">
            <div class="countdown-number">{{ $hoursUntil }}</div>
            <div class="countdown-label">Hours Until Interview</div>
        </div>

        <p>Dear {{ $candidate->full_name }},</p>

        <p>This is a friendly reminder that your interview for the position of <strong>{{ $vacancy->title }}</strong> is scheduled for tomorrow.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">📅 Date:</span>
                <span class="info-value">{{ $interview->scheduled_at->format('l, d F Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">🕐 Time:</span>
                <span class="info-value">{{ $interview->scheduled_at->format('H:i') }} WIB ({{ $interview->duration }} minutes)</span>
            </div>
            <div class="info-row">
                <span class="info-label">📍 Type:</span>
                <span class="info-value">{{ ucfirst($interview->interview_type) }}</span>
            </div>
            @if($interview->interview_type === 'video')
            <div class="info-row">
                <span class="info-label">🎥 Meeting Link:</span>
                <span class="info-value"><a href="{{ $interviewLink }}" style="color: #3b82f6;">Click here to join</a></span>
            </div>
            @elseif($interview->interview_type === 'in-person')
            <div class="info-row">
                <span class="info-label">📌 Location:</span>
                <span class="info-value">{{ $interview->location }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">👥 Interviewer:</span>
                <span class="info-value">{{ $interview->interviewer }}</span>
            </div>
        </div>

        @if($interview->interview_type === 'video')
        <div style="text-align: center;">
            <a href="{{ $interviewLink }}" class="button-primary">
                🎥 Join Interview Room
            </a>
            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">
                Available 10 minutes before scheduled time
            </p>
        </div>
        @endif

        <div class="checklist">
            <h3>✅ Last-Minute Checklist:</h3>
            
            <div class="checklist-item">
                <span class="checklist-icon">📄</span>
                <span class="checklist-text">
                    <strong>Review your application</strong><br>
                    Re-read your CV and the job description to refresh your memory
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">🎯</span>
                <span class="checklist-text">
                    <strong>Prepare your STAR stories</strong><br>
                    Have examples ready: Situation, Task, Action, Result
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">❓</span>
                <span class="checklist-text">
                    <strong>Prepare questions to ask</strong><br>
                    Show interest by having 2-3 thoughtful questions ready
                </span>
            </div>

            @if($interview->interview_type === 'video')
            <div class="checklist-item">
                <span class="checklist-icon">💻</span>
                <span class="checklist-text">
                    <strong>Test your technology NOW</strong><br>
                    Check internet speed, camera, microphone, and lighting
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">🏠</span>
                <span class="checklist-text">
                    <strong>Prepare your environment</strong><br>
                    Choose a quiet, well-lit space with a professional background
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">📱</span>
                <span class="checklist-text">
                    <strong>Eliminate distractions</strong><br>
                    Silence your phone, close other tabs, inform household members
                </span>
            </div>
            @else
            <div class="checklist-item">
                <span class="checklist-icon">🚗</span>
                <span class="checklist-text">
                    <strong>Plan your route</strong><br>
                    Check traffic and plan to arrive 15 minutes early
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">👔</span>
                <span class="checklist-text">
                    <strong>Prepare your outfit</strong><br>
                    Dress professionally, iron your clothes tonight
                </span>
            </div>
            @endif

            <div class="checklist-item">
                <span class="checklist-icon">📑</span>
                <span class="checklist-text">
                    <strong>Bring documents</strong><br>
                    CV (3 copies), portfolio, certificates, ID card, pen, and notepad
                </span>
            </div>

            <div class="checklist-item">
                <span class="checklist-icon">😴</span>
                <span class="checklist-text">
                    <strong>Get a good night's sleep</strong><br>
                    Rest well tonight to be sharp and energized tomorrow
                </span>
            </div>
        </div>

        @if($interview->interview_type === 'video')
        <div class="alert">
            <strong>⚠️ Technical Tips for Video Interview:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Use a desktop or laptop (not mobile) for best experience</li>
                <li>Use headphones to avoid echo</li>
                <li>Position camera at eye level</li>
                <li>Look at the camera when speaking, not the screen</li>
                <li>Have the interviewer's contact info ready in case of technical issues</li>
            </ul>
        </div>
        @endif

        <div style="background-color: #eff6ff; padding: 20px; border-radius: 8px; margin: 25px 0; text-align: center;">
            <p style="margin: 0; font-size: 16px; color: #1e40af;">
                <strong>💡 Pro Tip:</strong> Arrive/login 10 minutes early to show punctuality and professionalism
            </p>
        </div>

        <p>If you encounter any issues or need to reschedule, please contact us immediately at <a href="mailto:hr@bizmark.id">hr@bizmark.id</a> or call <a href="tel:+6281234567890">+62 812-3456-7890</a>.</p>

        <p style="margin-top: 30px; font-size: 18px; color: #166534;">
            <strong>Good luck! We're excited to meet you! 🍀</strong>
        </p>

        <p>
            Best regards,<br>
            <strong>HR Team - Bizmark.id</strong>
        </p>

        <div class="footer">
            <p>
                <strong>Bizmark.id</strong><br>
                Jl. Raya Cilebut No.10, Sukaraja, Bogor<br>
                Email: <a href="mailto:hr@bizmark.id">hr@bizmark.id</a> | 
                Phone: <a href="tel:+6281234567890">+62 812-3456-7890</a>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin-top: 15px;">
                This is an automated reminder. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
