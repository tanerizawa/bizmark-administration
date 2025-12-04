<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_title }}</title>
    <style>
        @page {
            margin: 3cm 2.5cm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'Georgia', serif;
            font-size: 11pt;
            line-height: 1.15;
            color: #1f2937;
            background: #fff;
            padding: 0.5cm;
        }
        
        /* Header Styling */
        .header {
            text-align: center;
            margin-bottom: 1.5cm;
            padding-bottom: 0.8cm;
            border-bottom: 3px double #0A66C2;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 0.5cm;
            letter-spacing: 1px;
            color: #0A66C2;
        }

        .header h2 {
            font-size: 12pt;
            font-weight: normal;
            color: #4b5563;
        }
        
        /* Content Styling */
        .content {
            text-align: justify;
            margin-bottom: 1.5cm;
        }
        
        .content h1 {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 1.2cm 0 0.8cm 0;
            page-break-after: avoid;
        }
        
        .content h2 {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            margin: 1cm 0 0.7cm 0;
            page-break-after: avoid;
        }
        
        .content h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0.6cm 0 0.45cm 0;
            page-break-after: avoid;
        }
        
        .content p {
            margin-bottom: 0.35cm;
            text-indent: 0;
        }
        
        .content strong {
            font-weight: bold;
        }
        
        .content ul,
        .content ol {
            margin-left: 1.2cm;
            margin-bottom: 0.35cm;
        }

        .content li {
            margin-bottom: 0.2cm;
            line-height: 1.15;
        }
        
        .content ul {
            list-style-type: disc;
        }
        
        .content ol {
            list-style-type: decimal;
        }
        
        .content blockquote {
            margin: 0.45cm 1cm;
            padding: 0.35cm 0.5cm;
            border-left: 3px solid #0A66C2;
            background: #eef3fb;
            font-style: italic;
            line-height: 1.15;
        }
        
        /* Info Box */
        .info-box {
            background: #eef4fb;
            border: 1px solid #c7d7f2;
            padding: 0.6cm;
            margin: 0.8cm 0;
            page-break-inside: avoid;
            line-height: 1.15;
        }

        .info-box p {
            margin-bottom: 0.2cm;
            text-indent: 0;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 2cm;
            page-break-inside: avoid;
        }
        
        .signature-parties {
            margin: 1.2cm 0;
            display: table;
            width: 100%;
        }
        
        .signature-party {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 0.8cm;
        }
        
        .signature-party p {
            margin-bottom: 2cm;
            font-weight: bold;
        }
        
        .signature-party .name {
            border-top: 2px solid #0A66C2;
            padding-top: 0.2cm;
            font-weight: bold;
            color: #0A66C2;
        }
        
        /* Digital Signature Box */
        .signature-box {
            background: #f5f8fc;
            border: 2px solid #0A66C2;
            padding: 0.8cm;
            margin-top: 1.2cm;
            page-break-inside: avoid;
        }

        .signature-box h4 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 0.6cm;
            text-align: center;
            padding-bottom: 0.4cm;
            border-bottom: 2px solid #0A66C2;
            color: #0A66C2;
        }
        
        .signature-box table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.4cm;
        }
        
        .signature-box td {
            padding: 0.25cm 0.2cm;
            font-size: 10pt;
            vertical-align: top;
            line-height: 1.15;
        }
        
        .signature-box td:first-child {
            width: 5cm;
            font-weight: bold;
            color: #1f2937;
        }
        
        .signature-box tr {
            border-bottom: 1px solid #cbd5e0;
        }

        .signature-box tr:last-child {
            border-bottom: none;
        }
        
        .hash {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 8pt;
            word-break: break-all;
            background: #fff;
            padding: 0.2cm;
            border: 1px solid #cbd5e0;
        }
        
        /* Verification Badge */
        .verification-badge {
            text-align: center;
            margin: 0.8cm 0;
            padding: 0.6cm;
            background: #e3eefc;
            border: 2px solid #0A66C2;
            border-radius: 5px;
            line-height: 1.15;
        }

        .verification-badge p {
            font-size: 10pt;
            font-weight: bold;
            color: #0A66C2;
            margin-bottom: 0.2cm;
            line-height: 1.15;
        }

        .verification-badge small {
            font-size: 9pt;
            color: #0A66C2;
            font-weight: normal;
            line-height: 1.15;
        }
        
        /* Footer */
        .footer {
            margin-top: 2cm;
            text-align: center;
            font-size: 9pt;
            color: #4b5563;
            border-top: 2px solid #d1d5db;
            padding-top: 0.8cm;
            page-break-inside: avoid;
        }
        
        .footer p {
            margin-bottom: 0.2cm;
            line-height: 1.15;
        }
        
        .footer strong {
            color: #0A66C2;
        }
        
        /* Page break helpers */
        .page-break {
            page-break-after: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($document->document_title) }}</h1>
        <h2>Program Beta Tester Bizmark.ID</h2>
    </div>

    <div class="content">
        {!! \Illuminate\Support\Str::markdown($document->filled_content) !!}
    </div>

    @if($document->is_signed)
    <div class="signature-section no-break">
        <div class="verification-badge">
            <p>✓ DOKUMEN TELAH DITANDATANGANI SECARA DIGITAL</p>
            <small>Tanda tangan digital memiliki kekuatan hukum yang sama dengan tanda tangan basah</small>
        </div>
        
        <div class="signature-box">
            <h4>INFORMASI TANDA TANGAN DIGITAL</h4>
            <table>
                <tr>
                    <td>Ditandatangani oleh:</td>
                    <td><strong>{{ $betaTester->full_name }}</strong></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $betaTester->email }}</td>
                </tr>
                <tr>
                    <td>Nomor Registrasi:</td>
                    <td><strong>{{ $betaTester->registration_number }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal & Waktu:</td>
                    <td><strong>{{ $document->signed_at->isoFormat('dddd, DD MMMM YYYY') }}</strong> pukul {{ $document->signed_at->isoFormat('HH:mm') }} WIB</td>
                </tr>
                <tr>
                    <td>IP Address:</td>
                    <td class="hash">{{ $document->signature_ip }}</td>
                </tr>
                <tr>
                    <td>Perangkat:</td>
                    <td style="font-size: 9pt;">{{ $document->signature_data['browser'] ?? 'Unknown' }}</td>
                </tr>
                <tr>
                    <td>User Agent:</td>
                    <td style="font-size: 8pt; color: #666;">{{ $document->signature_user_agent }}</td>
                </tr>
                <tr>
                    <td>Hash Digital (SHA-256):</td>
                    <td><div class="hash">{{ $document->signature_hash }}</div></td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 25px; padding: 15px; background: #eef4fb; border: 1px solid #0A66C2; border-radius: 5px;">
            <p style="font-size: 9pt; text-align: center; margin-bottom: 8px; color: #0A66C2;">
                <strong>TENTANG TANDA TANGAN DIGITAL</strong>
            </p>
            <p style="font-size: 8pt; text-align: justify; line-height: 1.15; color: #1f2937;">
                Tanda tangan digital ini telah dienkripsi menggunakan algoritma SHA-256 dan memenuhi standar keamanan digital. 
                Hash yang tercantum di atas menjamin bahwa dokumen ini tidak dapat diubah setelah ditandatangani. 
                Sistem telah merekam informasi waktu, lokasi (IP Address), dan perangkat yang digunakan sebagai bukti autentikasi.
                Sesuai dengan UU ITE No. 11 Tahun 2008, tanda tangan elektronik memiliki kekuatan hukum yang sama dengan tanda tangan konvensional.
            </p>
        </div>
    </div>
    @endif

    <div class="footer no-break">
        <p><strong>PT Cangah Pajaratan Mandiri</strong></p>
        <p><strong>Bizmark.ID</strong> - Solusi Manajemen Perizinan Bisnis</p>
        <p style="margin-top: 10px;">
            Dokumen ini dihasilkan secara otomatis oleh sistem<br>
            pada {{ now()->isoFormat('dddd, DD MMMM YYYY') }} pukul {{ now()->isoFormat('HH:mm') }} WIB
        </p>
        @if($document->pdf_filename)
        <p style="margin-top: 5px; font-size: 8pt; color: #999;">
            File: {{ $document->pdf_filename }}
        </p>
        @endif
    </div>
</body>
</html>
