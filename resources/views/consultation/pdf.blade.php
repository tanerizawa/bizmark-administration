<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Estimasi Biaya #{{ $consultation->id }} - Bizmark.ID</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a1a;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-flex {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .document-title {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .request-info {
            text-align: right;
        }
        .request-id {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
        }
        .request-date {
            font-size: 10px;
            color: #666;
        }
        
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 10px;
            border: 1px solid #e5e7eb;
        }
        .info-table .label {
            background: #f3f4f6;
            font-weight: bold;
            width: 35%;
            color: #374151;
        }
        .info-table .value {
            color: #1a1a1a;
        }
        
        .cost-summary {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .cost-summary-grid {
            display: table;
            width: 100%;
        }
        .cost-item {
            display: table-cell;
            text-align: center;
            padding: 10px;
        }
        .cost-label {
            font-size: 10px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .cost-value {
            font-size: 14px;
            font-weight: bold;
        }
        .cost-value.main {
            font-size: 18px;
        }
        
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .breakdown-table th,
        .breakdown-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        .breakdown-table th {
            background: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        .breakdown-table td.number {
            text-align: right;
            font-family: monospace;
        }
        .breakdown-table tr.subtotal {
            background: #e0f2fe;
            font-weight: bold;
        }
        
        .timeline-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
        }
        .timeline-grid {
            display: table;
            width: 100%;
        }
        .timeline-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
        }
        .timeline-label {
            font-size: 9px;
            color: #666;
        }
        .timeline-value {
            font-size: 14px;
            font-weight: bold;
            color: #059669;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .footer-contact {
            margin-top: 8px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .disclaimer {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 10px;
            font-size: 9px;
            margin-top: 20px;
        }
        .disclaimer-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        .disclaimer-text {
            color: #78350f;
        }
        
        .ai-badge {
            display: inline-block;
            background: #8b5cf6;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-flex">
            <div class="header-left">
                <div class="logo">Bizmark.ID</div>
                <div class="document-title">Laporan Estimasi Biaya Perizinan</div>
            </div>
            <div class="header-right">
                <div class="request-info">
                    <div class="request-id">Request #{{ $consultation->id }}</div>
                    <div class="request-date">{{ $consultation->created_at->format('d M Y, H:i') }} WIB</div>
                    <div style="margin-top: 5px;"><span class="ai-badge">AI-Powered Analysis</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Information -->
    <div class="section">
        <div class="section-title">Informasi Usaha</div>
        <table class="info-table">
            <tr>
                <td class="label">Kode KBLI</td>
                <td class="value">{{ $consultation->kbli->code ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Usaha</td>
                <td class="value">{{ $consultation->kbli->description ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Kategori</td>
                <td class="value">{{ $consultation->kbli->category ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Skala Bisnis</td>
                <td class="value">{{ ucfirst($consultation->business_size ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Badan Usaha</td>
                <td class="value">{{ strtoupper($consultation->entity_type ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td class="value">{{ $consultation->location ?? '-' }} ({{ ucfirst($consultation->geographic_region ?? '-') }})</td>
            </tr>
            <tr>
                <td class="label">Zona/Kawasan</td>
                <td class="value">{{ ucfirst(str_replace('_', ' ', $consultation->location_type ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="label">Level Investasi</td>
                <td class="value">{{ ucfirst($consultation->investment_level ?? '-') }}</td>
            </tr>
            @if($consultation->employee_count)
            <tr>
                <td class="label">Jumlah Karyawan</td>
                <td class="value">{{ $consultation->employee_count }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Cost Summary -->
    @php
        $estimate = $consultation->estimate_data ?? [];
        $breakdown = $estimate['cost_breakdown'] ?? [];
    @endphp
    
    <div class="section">
        <div class="section-title">Ringkasan Biaya</div>
        <div class="cost-summary">
            <div class="cost-summary-grid">
                <div class="cost-item">
                    <div class="cost-label">Subtotal</div>
                    <div class="cost-value">{{ $estimate['cost_summary']['formatted']['subtotal'] ?? '-' }}</div>
                </div>
                <div class="cost-item">
                    <div class="cost-label">Total Estimasi</div>
                    <div class="cost-value main">{{ $estimate['cost_summary']['formatted']['grand_total'] ?? '-' }}</div>
                </div>
                <div class="cost-item">
                    <div class="cost-label">Kisaran</div>
                    <div class="cost-value">{{ $estimate['cost_summary']['formatted']['range'] ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown -->
    <div class="section">
        <div class="section-title">Rincian Biaya</div>
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Komponen</th>
                    <th>Rincian</th>
                    <th style="text-align: right;">Biaya (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Biaya Pemerintah -->
                <tr class="subtotal">
                    <td colspan="2"><strong>Biaya Pemerintah</strong></td>
                    <td class="number"><strong>{{ number_format($breakdown['biaya_pemerintah']['total'] ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
                @foreach(($breakdown['biaya_pemerintah']['breakdown'] ?? []) as $key => $value)
                <tr>
                    <td></td>
                    <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                    <td class="number">{{ number_format($value, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <!-- Biaya Konsultan -->
                <tr class="subtotal">
                    <td colspan="2"><strong>Biaya Konsultan</strong></td>
                    <td class="number"><strong>{{ number_format($breakdown['biaya_konsultan']['total'] ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
                @foreach(($breakdown['biaya_konsultan']['breakdown'] ?? []) as $key => $detail)
                <tr>
                    <td></td>
                    <td>
                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                        @if(isset($detail['hours']))
                            ({{ $detail['hours'] }} jam)
                        @endif
                    </td>
                    <td class="number">{{ number_format($detail['cost'] ?? $detail, 0, ',', '.') }}</td>
                </tr>
                @endforeach

                <!-- Overhead -->
                <tr class="subtotal">
                    <td colspan="2"><strong>Overhead ({{ $breakdown['overhead']['percentage'] ?? 20 }}%)</strong></td>
                    <td class="number"><strong>{{ number_format($breakdown['overhead']['amount'] ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Timeline -->
    @if(isset($estimate['ai_analysis']['timeline']))
    @php $timeline = $estimate['ai_analysis']['timeline']; @endphp
    <div class="section">
        <div class="section-title">Estimasi Timeline</div>
        <div class="timeline-box">
            <div class="timeline-grid">
                <div class="timeline-item">
                    <div class="timeline-label">Minimum</div>
                    <div class="timeline-value">{{ $timeline['minimum_days'] ?? 0 }} hari</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-label">Realistis</div>
                    <div class="timeline-value" style="font-size: 18px;">{{ $timeline['realistic_days'] ?? 0 }} hari</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-label">Maximum</div>
                    <div class="timeline-value">{{ $timeline['maximum_days'] ?? 0 }} hari</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Disclaimer -->
    <div class="disclaimer">
        <div class="disclaimer-title">⚠️ Disclaimer</div>
        <div class="disclaimer-text">
            Estimasi ini dibuat berdasarkan analisis AI dengan data regulasi terkini. Biaya aktual dapat berbeda tergantung kondisi lapangan, perubahan regulasi, dan faktor lainnya. Untuk penawaran resmi, silakan hubungi tim kami.
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Dokumen ini dibuat secara otomatis oleh sistem Bizmark.ID</div>
        <div>Confidence Score: {{ number_format(($estimate['confidence_score'] ?? 0) * 100, 0) }}%</div>
        <div class="footer-contact">
            bizmark.id | wa.me/6283879602855 | info@bizmark.id
        </div>
    </div>
</body>
</html>
