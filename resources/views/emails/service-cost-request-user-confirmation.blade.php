<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6; margin: 0; padding: 24px; background: #f8fafc;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
        <div style="padding: 20px; background: #0f172a; color: #ffffff;">
            <h2 style="margin: 0; font-size: 20px;">Permohonan Anda Sudah Kami Terima</h2>
            <p style="margin: 8px 0 0 0; opacity: 0.9;">Bizmark.ID</p>
        </div>

        <div style="padding: 20px;">
            <p>Halo {{ $serviceRequest->name }},</p>
            <p>Terima kasih, permohonan penghitungan biaya jasa Anda telah kami terima.</p>

            <div style="margin: 16px 0; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                <p style="margin: 0 0 6px 0;"><strong>Nomor Permohonan:</strong> {{ $serviceRequest->request_number }}</p>
                <p style="margin: 0 0 6px 0;"><strong>Status:</strong> {{ $serviceRequest->status_label }}</p>
                <p style="margin: 0;"><strong>Tanggal:</strong> {{ $serviceRequest->created_at->format('d M Y H:i') }}</p>
            </div>

            <p>Anda dapat melihat ringkasan permohonan melalui tautan berikut:</p>
            <p><a href="{{ $resultUrl }}" style="color: #0ea5e9;">{{ $resultUrl }}</a></p>

            <p>Tim kami akan meninjau permohonan Anda secepatnya.</p>
            <p style="margin-bottom: 0;">Salam,<br>Tim Bizmark.ID</p>
        </div>
    </div>
</body>
</html>
