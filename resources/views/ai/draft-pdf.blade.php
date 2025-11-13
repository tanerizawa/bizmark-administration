<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $draft->title }}</title>
    <style>
        @page {
            margin: 2cm 2.5cm;
            @top-center {
                content: "{{ $draft->title }}";
                font-size: 9pt;
                color: #666;
            }
            @bottom-center {
                content: "Halaman " counter(page) " dari " counter(pages);
                font-size: 9pt;
                color: #666;
            }
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.8;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .header .project-info {
            font-size: 11pt;
            color: #333;
            margin: 5px 0;
        }

        .metadata {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 30px;
            border-left: 4px solid #007AFF;
        }

        .metadata table {
            width: 100%;
            border-collapse: collapse;
        }

        .metadata td {
            padding: 5px 10px;
            font-size: 10pt;
        }

        .metadata td:first-child {
            width: 150px;
            font-weight: bold;
            color: #333;
        }

        .content {
            text-align: justify;
            margin-bottom: 30px;
        }

        .content h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 25px 0 15px 0;
            color: #000;
            page-break-after: avoid;
        }

        .content h3 {
            font-size: 13pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #333;
            page-break-after: avoid;
        }

        .content p {
            margin: 0 0 15px 0;
            text-indent: 1cm;
        }

        .content ul, .content ol {
            margin: 10px 0 15px 30px;
        }

        .content li {
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }

        .footer .signature {
            margin-top: 50px;
            text-align: right;
        }

        .footer .signature .name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }

        .page-break {
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table th {
            background: #f0f0f0;
            padding: 8px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: left;
        }

        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .ai-watermark {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 8pt;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $draft->title }}</h1>
        <div class="project-info">
            <strong>Proyek:</strong> {{ $draft->project->name }}<br>
            @if($draft->project->client)
            <strong>Klien:</strong> {{ $draft->project->client->name }}<br>
            @endif
            <strong>Tanggal:</strong> {{ now()->format('d F Y') }}
        </div>
    </div>

    <!-- Metadata -->
    <div class="metadata">
        <table>
            <tr>
                <td>Jenis Dokumen</td>
                <td>: {{ strtoupper(str_replace('_', ' ', $draft->template->permit_type)) }}</td>
            </tr>
            <tr>
                <td>Nomor Dokumen</td>
                <td>: {{ $draft->project->code }}/{{ $draft->id }}/{{ now()->format('Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ strtoupper($draft->status) }}</td>
            </tr>
            @if($draft->status === 'approved' && $draft->approver)
            <tr>
                <td>Disetujui Oleh</td>
                <td>: {{ $draft->approver->name }} ({{ $draft->approved_at->format('d M Y') }})</td>
            </tr>
            @endif
            <tr>
                <td>Dibuat Oleh</td>
                <td>: {{ $draft->creator->name }}</td>
            </tr>
        </table>
    </div>

    <!-- Content -->
    <div class="content">
        {!! nl2br(e($draft->content)) !!}
    </div>

    <!-- Footer / Signature -->
    <div class="footer">
        <p style="text-align: center; margin-bottom: 30px;">
            <em>Dokumen ini dihasilkan secara otomatis dan telah diverifikasi oleh pihak yang berwenang.</em>
        </p>

        <div class="signature">
            <div>
                {{ $draft->project->client_address ?? 'Jakarta' }}, {{ now()->format('d F Y') }}<br>
                <strong>{{ config('app.name') }}</strong>
            </div>
            
            <div class="name">
                @if($draft->status === 'approved' && $draft->approver)
                    {{ $draft->approver->name }}
                @else
                    {{ $draft->creator->name }}
                @endif
            </div>
        </div>
    </div>

    <!-- AI Watermark -->
    <div class="ai-watermark">
        Generated by AI • Template: {{ $draft->template->name }} • Draft ID: {{ $draft->id }}
    </div>
</body>
</html>
