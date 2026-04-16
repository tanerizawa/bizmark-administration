<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;color:#1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f172a,#1e40af);padding:24px 26px;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:1.2px;text-transform:uppercase;opacity:0.85;">Bizmark.ID</div>
                            <h1 style="margin:8px 0 4px 0;font-size:22px;line-height:1.25;">Penawaran Layanan Konsultasi</h1>
                            <p style="margin:0;font-size:13px;opacity:0.9;">Nomor Permohonan: {{ $serviceRequest->request_number }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 26px 10px 26px;">
                            <div style="font-size:15px;line-height:1.72;color:#111827;">
                                {!! $htmlBody !!}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 26px 24px 26px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size:13px;color:#475569;">Nomor Permohonan</td>
                                                <td align="right" style="font-size:13px;color:#0f172a;font-weight:600;">{{ $serviceRequest->request_number }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size:13px;color:#475569;">Penerima</td>
                                                <td align="right" style="font-size:13px;color:#0f172a;font-weight:600;">{{ $serviceRequest->display_name }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="font-size:13px;color:#475569;">Tanggal Pengiriman</td>
                                                <td align="right" style="font-size:13px;color:#0f172a;font-weight:600;">{{ now()->format('d M Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 26px 10px 26px;background:#f8fafc;border-top:1px solid #e2e8f0;">
                            <div style="border:1px solid #dbe3f0;border-radius:12px;background:#ffffff;padding:14px 16px;">
                                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                                    <div>
                                        <p style="margin:0 0 2px 0;font-size:13px;color:#0f172a;font-weight:700;">Ditandatangani secara digital</p>
                                        <p style="margin:0;font-size:12px;color:#475569;">{{ $signature['signer_name'] ?? 'Tim Konsultan' }} · {{ $signature['signer_title'] ?? 'Business Licensing Consultant' }}</p>
                                    </div>
                                    <div style="text-align:right;">
                                        <p style="margin:0;font-family:'Courier New',monospace;font-size:11px;color:#1e3a8a;">{{ $signature['signature_id'] ?? '-' }}</p>
                                        <p style="margin:2px 0 0 0;font-family:'Courier New',monospace;font-size:11px;color:#334155;">{{ $signature['signature_hash'] ?? '-' }}</p>
                                    </div>
                                </div>
                                <p style="margin:10px 0 0 0;font-size:12px;color:#64748b;">Waktu tanda tangan: {{ $signature['issued_at'] ?? now()->format('d M Y H:i') }}</p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
