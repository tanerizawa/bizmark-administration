<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Scheduled</title>
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
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        h1 {
            color: #1e40af;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
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
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            margin: 25px 0;
            font-weight: 600;
            text-align: center;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .tips {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .tips h3 {
            color: #92400e;
            margin-top: 0;
            font-size: 16px;
        }
        .tips ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .tips li {
            color: #78350f;
            margin-bottom: 8px;
        }
        .calendar-note {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
            color: #166534;
        }
        .calendar-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Bizmark" class="logo">
            <h1>🎉 Interview Scheduled</h1>
            <p class="subtitle">Your interview has been successfully scheduled</p>
        </div>

        <p>Dear {{ $candidate->full_name }},</p>

        <p>Congratulations! We are pleased to inform you that your application for the position of <strong>{{ $vacancy->title }}</strong> has been shortlisted, and we would like to invite you for an interview.</p>

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
                <span class="info-label">🎥 Platform:</span>
                <span class="info-value">Video Conference</span>
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
            <a href="{{ $interviewLink }}" class="button">
                🎥 Join Interview Room
            </a>
            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">
                The join button will be active 10 minutes before the interview
            </p>
        </div>
        @endif

        <div class="calendar-note">
            <div class="calendar-icon">📅</div>
            <strong>Calendar Invitation Attached</strong>
            <p style="margin: 5px 0 0 0; font-size: 13px;">
                We've attached a calendar file (.ics) to this email. Click it to add this interview to your calendar app (Outlook, Google Calendar, Apple Calendar, etc.)
            </p>
        </div>

        <div class="tips">
            <h3>📋 Interview Preparation Tips:</h3>
            <ul>
                <li><strong>Research the company:</strong> Review our website and understand our business model</li>
                <li><strong>Prepare your answers:</strong> Think about your experience and how it relates to this role</li>
                <li><strong>Prepare questions:</strong> Have 2-3 thoughtful questions ready for the interviewer</li>
                @if($interview->interview_type === 'video')
                <li><strong>Test your technology:</strong> Check your internet, camera, and microphone 30 minutes before</li>
                <li><strong>Choose a quiet location:</strong> Find a well-lit, quiet space with a professional background</li>
                @else
                <li><strong>Arrive early:</strong> Plan to arrive 10-15 minutes before the scheduled time</li>
                <li><strong>Dress professionally:</strong> First impressions matter</li>
                @endif
                <li><strong>Bring necessary documents:</strong> CV, portfolio, certificates, and ID card</li>
            </ul>
        </div>

        @if($interview->notes)
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <strong>Additional Notes:</strong>
            <p style="margin: 8px 0 0 0;">{{ $interview->notes }}</p>
        </div>
        @endif

        <p>If you need to reschedule this interview, please contact us at least 24 hours in advance.</p>

        <p>We look forward to meeting you!</p>

        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>HR Team - Bizmark.id</strong>
        </p>

        <div class="footer">
            <p>
                <strong>Bizmark.id</strong><br>
                Jl. Raya Cilebut No.10, Sukaraja, Bogor<br>
                Email: <a href="mailto:hr@bizmark.id">hr@bizmark.id</a> | 
                Phone: <a href="tel:+6281234567890">+62 812-3456-7890</a><br>
                <a href="{{ route('career.index') }}">View All Career Opportunities</a>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin-top: 15px;">
                This is an automated email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
