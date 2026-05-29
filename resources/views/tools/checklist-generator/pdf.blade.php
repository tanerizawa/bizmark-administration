<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Dokumen — {{ $record->permit_type }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1e293b; line-height: 1.5; }

        .header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            padding: 18pt 24pt;
            margin-bottom: 16pt;
        }

        .header-brand { font-size: 7pt; text-transform: uppercase; letter-spacing: 1pt; opacity: 0.75; margin-bottom: 4pt; }
        .header-title { font-size: 16pt; font-weight: bold; margin-bottom: 4pt; }
        .header-subtitle { font-size: 9pt; opacity: 0.85; }

        .meta-bar {
            display: flex;
            gap: 12pt;
            margin-bottom: 16pt;
            padding: 0 24pt;
        }

        .meta-item {
            border: 1pt solid #e2e8f0;
            border-radius: 6pt;
            padding: 6pt 10pt;
            flex: 1;
        }

        .meta-label { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.5pt; color: #64748b; margin-bottom: 2pt; }
        .meta-value { font-size: 9pt; font-weight: 600; color: #1e293b; }

        .content { padding: 0 24pt; }

        .category { margin-bottom: 14pt; }

        .cat-title {
            background: #f1f5f9;
            border-left: 3pt solid #6366f1;
            padding: 6pt 10pt;
            font-size: 9pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 6pt;
        }

        .doc-row {
            display: flex;
            align-items: flex-start;
            padding: 4pt 0;
            border-bottom: 0.5pt solid #f1f5f9;
        }

        .doc-check {
            width: 12pt;
            height: 12pt;
            border: 1.5pt solid #6366f1;
            border-radius: 2pt;
            margin-right: 8pt;
            flex-shrink: 0;
            margin-top: 1pt;
        }

        .doc-check.filled { background: #6366f1; }

        .doc-info { flex: 1; }
        .doc-name { font-size: 9pt; font-weight: 600; }
        .doc-notes { font-size: 7.5pt; color: #64748b; margin-top: 1pt; }

        .doc-copies {
            font-size: 7.5pt;
            color: #6366f1;
            font-weight: 700;
            white-space: nowrap;
            margin-left: 6pt;
        }

        .tips-section {
            background: #fffbeb;
            border: 1pt solid #fde68a;
            border-radius: 6pt;
            padding: 10pt;
            margin-top: 16pt;
            margin-bottom: 12pt;
        }

        .tips-title { font-size: 9pt; font-weight: bold; color: #92400e; margin-bottom: 6pt; }
        .tip-item { font-size: 8.5pt; color: #78350f; margin-bottom: 3pt; padding-left: 10pt; }

        .footer {
            margin-top: 20pt;
            padding: 10pt 24pt;
            border-top: 0.5pt solid #e2e8f0;
            font-size: 7.5pt;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <div class="header">
        <p class="header-brand">Bizmark.id · Checklist Dokumen AI</p>
        <h1 class="header-title">{{ $record->permit_type }}</h1>
        <p class="header-subtitle">Dibuat {{ $record->created_at->format('d F Y') }}</p>
    </div>

    <div class="meta-bar">
        <div class="meta-item">
            <p class="meta-label">Kode KBLI</p>
            <p class="meta-value">{{ $record->kbli_code }}</p>
        </div>
        <div class="meta-item">
            <p class="meta-label">Kota</p>
            <p class="meta-value">{{ $record->city }}</p>
        </div>
        <div class="meta-item">
            <p class="meta-label">Skala Usaha</p>
            <p class="meta-value">{{ ucfirst($record->business_scale) }}</p>
        </div>
        <div class="meta-item">
            <p class="meta-label">Estimasi Waktu</p>
            <p class="meta-value">{{ $record->checklist_data['estimated_days'] ?? 30 }} hari</p>
        </div>
    </div>

    <div class="content">

        @if(!empty($record->checklist_data['summary']))
            <p style="font-size:9pt;color:#475569;margin-bottom:14pt;">{{ $record->checklist_data['summary'] }}</p>
        @endif

        @foreach($record->checklist_data['categories'] ?? [] as $category)
        <div class="category">
            <div class="cat-title">{{ $category['name'] }}</div>
            @foreach($category['documents'] ?? [] as $doc)
            <div class="doc-row">
                <div class="doc-check {{ ($doc['required'] ?? true) ? 'filled' : '' }}"></div>
                <div class="doc-info">
                    <p class="doc-name">{{ $doc['name'] }}</p>
                    @if(!empty($doc['notes']))
                        <p class="doc-notes">{{ $doc['notes'] }}</p>
                    @endif
                </div>
                @if(!empty($doc['copies']) && $doc['copies'] > 1)
                    <span class="doc-copies">×{{ $doc['copies'] }}</span>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach

        @php $tips = $record->checklist_data['tips'] ?? []; @endphp
        @if(count($tips))
        <div class="tips-section">
            <p class="tips-title">Tips dari Konsultan</p>
            @foreach($tips as $tip)
                <p class="tip-item">• {{ $tip }}</p>
            @endforeach
        </div>
        @endif

    </div>

    <div class="footer">
        <span>Bizmark.id — Jasa Perizinan Usaha Berbasis AI</span>
        <span>Checklist #{{ $record->id }} · Dicetak {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>
</html>
