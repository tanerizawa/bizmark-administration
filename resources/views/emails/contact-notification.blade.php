<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .field-value {
            background: white;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: #007AFF;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">📧 Pesan Baru dari Contact Form</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Bizmark.ID Website</p>
    </div>
    
    <div class="content">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Anda menerima pesan baru dari form contact di website Bizmark.ID:
        </p>
        
        <div class="field">
            <div class="field-label">👤 Nama:</div>
            <div class="field-value">{{ $name }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">📧 Email:</div>
            <div class="field-value">
                <a href="mailto:{{ $email }}" style="color: #007AFF; text-decoration: none;">{{ $email }}</a>
            </div>
        </div>
        
        <div class="field">
            <div class="field-label">📱 Telepon:</div>
            <div class="field-value">
                <a href="tel:{{ $phone }}" style="color: #007AFF; text-decoration: none;">{{ $phone }}</a>
            </div>
        </div>
        
        <div class="field">
            <div class="field-label">📋 Subjek:</div>
            <div class="field-value">{{ $subject }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">💬 Pesan:</div>
            <div class="field-value" style="white-space: pre-wrap;">{{ $messageContent }}</div>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
            <p style="margin: 0; color: #6b7280;">
                <strong>⏰ Dikirim pada:</strong> {{ date('d F Y, H:i') }} WIB
            </p>
        </div>
        
        <div style="text-align: center;">
            <a href="mailto:{{ $email }}?subject=Re: {{ $subject }}" class="btn">
                Balas Email
            </a>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 0 0 10px 0;">
            <strong>PT Cangah Pajaratan Mandiri (Bizmark)</strong><br>
            Jl. Sudirman No. 123, Jakarta Selatan 12190
        </p>
        <p style="margin: 0;">
            📞 +62 21 1234 5678 / +62 838 7960 2855 | 📧 cs@bizmark.id | 🌐 www.bizmark.id
        </p>
    </div>
</body>
</html>
