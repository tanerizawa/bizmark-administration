<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Rescheduled</title>
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
            border-bottom: 3px solid #f59e0b;
        }
        h1 {
            color: #d97706;
            font-size: 24px;
            margin: 10px 0;
        }
        .notice-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
        }
        .notice-box .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .date-comparison {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 30px 0;
            gap: 20px;
        }
        .date-box {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .old-date {
            background-color: #fee2e2;
            border: 2px solid #fca5a5;
            position: relative;
        }
        .old-date::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 3px;
            background-color: #dc2626;
            transform: translateY(-50%);
        }
        .new-date {
            background-color: #d1fae5;
            border: 2px solid #6ee7b7;
        }
        .date-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .old-date .date-label {
            color: #991b1b;
        }
        .new-date .date-label {
            color: #065f46;
        }
        .date-value {
            font-size: 16px;
            font-weight: 700;
            margin: 5px 0;
        }
        .old-date .date-value {
            color: #7f1d1d;
            text-decoration: line-through;
        }
        .new-date .date-value {
            color: #064e3b;
        }
        .time-value {
            font-size: 20px;
            font-weight: 800;
            margin: 5px 0;
        }
        .old-date .time-value {
            color: #991b1b;
            text-decoration: line-through;
        }
        .new-date .time-value {
            color: #059669;
        }
        .arrow {
            font-size: 36px;
            color: #f59e0b;
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
        .reason-box {
            background-color: #f3f4f6;
            border-left: 4px solid #6b7280;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reason-box strong {
            color: #374151;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: 600;
            text-align: center;
        }
        .button-primary {
            background-color: #22c55e;
            color: #ffffff !important;
        }
        .button-primary:hover {
            background-color: #16a34a;
        }
        .button-secondary {
            background-color: #6b7280;
            color: #ffffff !important;
        }
        .button-secondary:hover {
            background-color: #4b5563;
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
        .apology-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
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
            .date-comparison {
                flex-direction: column;
            }
            .arrow {
                transform: rotate(90deg);
            }
            .date-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div style="font-size: 64px;">📅</div>
            <h1>Interview Rescheduled</h1>
            <p style="color: #78350f; margin: 0;">Your interview date has been updated</p>
        </div>

        <div class="apology-box">
            <p style="margin: 0; color: #991b1b; font-size: 15px;">
                <strong>We sincerely apologize for any inconvenience caused by this change.</strong><br>
                We appreciate your understanding and flexibility.
            </p>
        </div>

        <p>Dear {{ $candidate->full_name }},</p>

        <p>We are writing to inform you that your interview for the position of <strong>{{ $vacancy->title }}</strong> has been rescheduled.</p>

        <div class="date-comparison">
            <div class="date-box old-date">
                <div class="date-label">❌ Previous Date</div>
                @if($oldDate)
                <div class="date-value">{{ $oldDate->format('d F Y') }}</div>
                <div class="time-value">{{ $oldDate->format('H:i') }} WIB</div>
                @else
                <div class="date-value">-</div>
                @endif
            </div>

            <div class="arrow">→</div>

            <div class="date-box new-date">
                <div class="date-label">✅ New Date</div>
                <div class="date-value">{{ $interview->scheduled_at->format('d F Y') }}</div>
                <div class="time-value">{{ $interview->scheduled_at->format('H:i') }} WIB</div>
            </div>
        </div>

        @if($reason)
        <div class="reason-box">
            <strong>Reason for Rescheduling:</strong>
            <p style="margin: 8px 0 0 0;">{{ $reason }}</p>
        </div>
        @endif

        <div class="notice-box">
            <div class="icon">📌</div>
            <p style="margin: 0; font-weight: 600; color: #92400e; font-size: 16px;">
                Please update your calendar with the new date and time
            </p>
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #1e40af;">Updated Interview Details:</h3>
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

        <div class="calendar-note">
            <div style="font-size: 32px; margin-bottom: 10px;">📅</div>
            <strong>Updated Calendar Invitation Attached</strong>
            <p style="margin: 10px 0 0 0; font-size: 13px;">
                We've attached an updated calendar file (.ics) with the new date and time. Add it to your calendar to replace the old invitation.
            </p>
        </div>

        <div style="background-color: #fef3c7; padding: 20px; border-radius: 8px; margin: 25px 0;">
            <h3 style="margin-top: 0; color: #92400e; font-size: 16px;">📋 Please Confirm Your Availability</h3>
            <p style="margin: 10px 0; color: #78350f;">
                If the new date and time work for you, no action is needed. However, if you have a conflict, please let us know as soon as possible.
            </p>
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ $interviewLink }}" class="button button-primary">
                    ✅ Confirm Availability
                </a>
                <a href="mailto:hr@bizmark.id?subject=Interview Reschedule Request - {{ $vacancy->title }}" class="button button-secondary">
                    📧 Request Different Time
                </a>
            </div>
        </div>

        @if($interview->interview_type === 'video')
        <div style="background-color: #eff6ff; padding: 20px; border-radius: 8px; margin: 25px 0; text-align: center;">
            <p style="margin: 0; font-size: 15px; color: #1e40af;">
                <strong>Video Interview Link:</strong><br>
                <a href="{{ $interviewLink }}" style="color: #3b82f6; font-size: 16px; font-weight: 600;">{{ $interviewLink }}</a>
            </p>
            <p style="margin: 10px 0 0 0; font-size: 13px; color: #6b7280;">
                The join button will be active 10 minutes before the interview
            </p>
        </div>
        @endif

        <div style="background-color: #f9fafb; padding: 15px; border-radius: 4px; margin: 25px 0;">
            <p style="margin: 0; color: #374151; font-size: 14px;">
                <strong>💡 Reminder:</strong> Please prepare the same way you would for the original interview date. Review the company information, prepare your answers, and ensure your technology is working (for video interviews).
            </p>
        </div>

        <p>Once again, we apologize for the inconvenience and appreciate your flexibility. We look forward to meeting you at the rescheduled time.</p>

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
                This is an automated notification. For urgent matters, please contact us directly.
            </p>
        </div>
    </div>
</body>
</html>
