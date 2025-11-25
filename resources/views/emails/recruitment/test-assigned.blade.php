<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Invitation - Bizmark.id</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            border-bottom: 3px solid #0A66C2;
        }
        .company-logo {
            max-width: 180px;
            height: auto;
            margin-bottom: 20px;
        }
        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0A66C2 0%, #004182 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .header-icon i {
            color: #ffffff;
            font-size: 28px;
        }
        h1 {
            color: #0A66C2;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #0A66C2;
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
            color: #0A66C2;
            min-width: 140px;
        }
        .info-label i {
            margin-right: 8px;
            width: 16px;
            text-align: center;
        }
        .info-value {
            color: #374151;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #0A66C2 0%, #004182 100%);
            color: #ffffff !important;
            padding: 16px 40px;
            text-decoration: none;
            border-radius: 8px;
            margin: 25px 0;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 4px 12px rgba(10, 102, 194, 0.3);
        }
        .button i {
            margin-right: 8px;
        }
        .tips {
            background-color: #fff8e1;
            border-left: 4px solid #ff9800;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .tips h3 {
            color: #e65100;
            margin-top: 0;
            font-size: 16px;
        }
        .tips h3 i {
            margin-right: 8px;
        }
        .tips ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .tips li {
            color: #5d4037;
            margin-bottom: 8px;
        }
        .alert-box {
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .alert-box i {
            margin-right: 8px;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #90caf9;
            color: #0d47a1;
        }
        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }
        .alert-danger {
            background-color: #ffebee;
            border: 1px solid #ef9a9a;
            color: #b71c1c;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
        }
        .footer i {
            margin-right: 5px;
        }
        @media only screen and (max-width: 600px) {
            body { padding: 10px; }
            .email-container { padding: 20px; }
            .info-row { flex-direction: column; }
            .info-label { margin-bottom: 5px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Bizmark.id Logo" class="company-logo">
            <h1>Assessment Invitation</h1>
            <p class="subtitle">You've been selected for the next stage</p>
        </div>

        <p>Dear <strong>{{ $candidate->full_name }}</strong>,</p>

        <p>Congratulations! We are pleased to inform you that your application for the position of <strong>{{ $vacancy?->title ?? 'the position you applied for' }}</strong> has progressed to the assessment stage. We invite you to complete the following evaluation as the next step in our recruitment process.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-file-lines"></i>Test Type:</span>
                <span class="info-value">{{ $testType }} Assessment</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-list-check"></i>Questions:</span>
                <span class="info-value">{{ $totalQuestions }} questions</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-clock"></i>Duration:</span>
                <span class="info-value">{{ $duration }} minutes</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-bullseye"></i>Passing Score:</span>
                <span class="info-value">{{ $passingScore }}%</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-calendar-xmark"></i>Deadline:</span>
                <span class="info-value">{{ $expiresAt->format('l, d F Y - H:i') }} WIB</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-rotate"></i>Attempts:</span>
                <span class="info-value">Single attempt only</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $testLink }}" class="button">
                <i class="fa-solid fa-rocket"></i>Begin Assessment Now
            </a>
            <p style="font-size: 13px; color: #6b7280; margin-top: 5px;">Click the button above to start your assessment</p>
        </div>

        <div class="tips">
            <h3><i class="fa-solid fa-circle-exclamation"></i>Important Guidelines:</h3>
            <ul>
                <li><strong>Single Attempt:</strong> You can only take this assessment once</li>
                <li><strong>Time Limit:</strong> Must be completed within {{ $duration }} minutes once started</li>
                <li><strong>No Tab Switching:</strong> Do not switch tabs or browsers during the assessment</li>
                <li><strong>Desktop Required:</strong> Use a desktop or laptop (mobile devices not supported)</li>
                <li><strong>Stable Internet:</strong> Ensure reliable internet connection</li>
                <li><strong>Submit to Complete:</strong> Click "Submit" when finished</li>
            </ul>
        </div>

        <div class="alert-box alert-info">
            <h3 style="color: #0A66C2; margin-top: 0; font-size: 16px;">
                <i class="fa-solid fa-lightbulb"></i>Tips for Success:
            </h3>
            <ul style="margin: 10px 0; padding-left: 20px; color: #1565c0;">
                <li>Find a quiet environment free from distractions</li>
                <li>Ensure your device is fully charged or plugged in</li>
                <li>Read each question carefully before answering</li>
                <li>Manage your time effectively throughout</li>
                <li>Review your answers before submission if time permits</li>
            </ul>
        </div>

        <div class="alert-box alert-warning">
            <p style="margin: 0;">
                <strong><i class="fa-solid fa-triangle-exclamation"></i>Time Sensitive:</strong> 
                This link expires in {{ $hoursUntilExpiry }} hours. Complete before <strong>{{ $expiresAt->format('d F Y, H:i') }} WIB</strong>
            </p>
        </div>

        <div class="alert-box alert-success">
            <p style="margin: 0; font-size: 14px;">
                <strong><i class="fa-solid fa-link"></i>Your Assessment Link:</strong><br>
                <a href="{{ $testLink }}" style="color: #0A66C2; word-break: break-all;">{{ $testLink }}</a>
            </p>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #6b7280;">
                <i class="fa-solid fa-lock"></i> This link is unique to you and should not be shared
            </p>
        </div>

        <div class="alert-box alert-danger">
            <p style="margin: 0; font-size: 14px;">
                <strong><i class="fa-solid fa-circle-question"></i>Need Help?</strong> 
                Contact us at <a href="mailto:cs@bizmark.id" style="color: #c62828;">cs@bizmark.id</a> 
                or call <a href="tel:+6283879602855" style="color: #c62828;">+62 838 7960 2855</a>
            </p>
        </div>

        <p style="margin-top: 30px;">Best of luck with your assessment!</p>
        <p>We appreciate your time and effort. Your results will be carefully reviewed.</p>

        <p style="margin-top: 30px;">
            Warm regards,<br>
            <strong style="color: #0A66C2;">Human Resources Team</strong><br>
            Bizmark.id
        </p>

        <div class="footer">
            <img src="{{ asset('images/logo.png') }}" alt="Bizmark.id" style="max-width: 120px; margin-bottom: 15px;">
            <p>
                <i class="fa-solid fa-location-dot"></i> Jln Lingkar Luar Karawang. Ruko Permata Sari Indah No.2<br>
                Karawang, Jawa Barat 41314, Indonesia
            </p>
            <p>
                <i class="fa-solid fa-envelope"></i> <a href="mailto:cs@bizmark.id">cs@bizmark.id</a> | 
                <i class="fa-solid fa-phone"></i> <a href="tel:+6283879602855">+62 838 7960 2855</a>
            </p>
            <p style="font-size: 11px; color: #9ca3af; margin-top: 15px;">
                This is an automated email. For assessment support, use the contact methods above.
            </p>
        </div>
    </div>
</body>
</html>
