<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6; margin: 0; padding: 24px; background: #f1f5f9;">
    <div style="max-width: 720px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
        <div style="padding: 20px; background: #0f172a; color: #ffffff;">
            <h2 style="margin: 0; font-size: 20px; color: #ffffff;">Lead Permohonan Baru Masuk</h2>
            <p style="margin: 8px 0 0 0; opacity: 0.95; color: #e2e8f0;">{{ $serviceRequest->request_number }}</p>
        </div>

        <div style="padding: 20px;">
            <div style="margin-bottom: 16px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                <p style="margin: 0 0 6px 0;"><strong>Jenis Pemohon:</strong> {{ $serviceRequest->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan' }}</p>
                <p style="margin: 0 0 6px 0;"><strong>Nama:</strong> {{ $serviceRequest->display_name }}</p>
                <p style="margin: 0 0 6px 0;"><strong>Email:</strong> {{ $serviceRequest->email }}</p>
                <p style="margin: 0 0 6px 0;"><strong>Telepon:</strong> {{ $serviceRequest->phone }}</p>
                <p style="margin: 0;"><strong>Kategori:</strong> {{ \App\Models\ServiceCostRequest::getServiceCategories()[$serviceRequest->service_category] ?? $serviceRequest->service_category }}</p>
            </div>

            <p style="margin: 0 0 8px 0;"><strong>Lihat detail:</strong></p>
            <p style="margin: 0 0 8px 0;"><a href="{{ $resultUrl }}" style="color: #0b63c7; font-weight: 600; text-decoration: underline;">Halaman hasil pemohon</a></p>
            <p style="margin: 0;"><a href="{{ $adminLeadsUrl }}" style="color: #0b63c7; font-weight: 600; text-decoration: underline;">Lead Management Admin</a></p>
        </div>
    </div>
</body>
</html>
