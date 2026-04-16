<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lokasi - {{ $project->name }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.6;
            padding: 12mm;
            background: #fff;
        }
        
        .header {
            padding-bottom: 12px;
            margin-bottom: 16px;
            border-bottom: 3px solid #059669;
        }
        
        .header-content {
            width: 100%;
        }
        
        .company-section {
            width: 50%;
            float: left;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #059669;
            margin-bottom: 2px;
        }
        
        .company-tagline {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
        }
        
        .report-section {
            width: 45%;
            float: right;
            text-align: right;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .report-date {
            font-size: 9pt;
            color: #6b7280;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .section {
            margin-bottom: 16px;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #059669;
            border-bottom: 2px solid #d1fae5;
            padding-bottom: 4px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-grid {
            width: 100%;
        }
        
        .info-row {
            width: 100%;
            margin-bottom: 6px;
        }
        
        .info-label {
            width: 35%;
            float: left;
            font-size: 9pt;
            color: #6b7280;
        }
        
        .info-value {
            width: 65%;
            float: left;
            font-size: 9pt;
            color: #1f2937;
            font-weight: 500;
        }
        
        .stats-grid {
            width: 100%;
            margin: 12px 0;
        }
        
        .stat-box {
            width: 30%;
            float: left;
            margin-right: 5%;
            text-align: center;
            background: #f3f4f6;
            padding: 12px 8px;
            border-radius: 6px;
        }
        
        .stat-box:last-child {
            margin-right: 0;
        }
        
        .stat-value {
            font-size: 16pt;
            font-weight: bold;
            color: #059669;
        }
        
        .stat-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .rtrw-section {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 6px;
            padding: 12px;
            margin-top: 16px;
        }
        
        .rtrw-title {
            font-size: 10pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 8px;
        }
        
        .rtrw-zona {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .rtrw-perda {
            font-size: 9pt;
            color: #78350f;
        }
        
        .rtrw-disclaimer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #fbbf24;
            font-size: 7pt;
            color: #92400e;
            font-style: italic;
        }
        
        .zones-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        
        .zones-table th {
            background: #fef3c7;
            color: #78350f;
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #fbbf24;
            font-weight: 600;
        }
        
        .zones-table td {
            padding: 8px;
            border-bottom: 1px solid #fde68a;
        }
        
        .zones-table tr:nth-child(even) {
            background: #fffbeb;
        }
        
        .coverage-bar {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 4px;
        }
        
        .coverage-fill {
            height: 100%;
            background: #059669;
            border-radius: 3px;
        }
        
        .coordinates-section {
            margin-top: 16px;
        }
        
        .coord-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        
        .coord-table th {
            background: #f3f4f6;
            padding: 6px;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
        }
        
        .coord-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        
        .footer {
            position: fixed;
            bottom: 12mm;
            left: 12mm;
            right: 12mm;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 7pt;
            color: #9ca3af;
        }
        
        .footer-content {
            width: 100%;
        }
        
        .footer-left {
            float: left;
        }
        
        .footer-right {
            float: right;
        }
        
        .map-placeholder {
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 9pt;
            margin: 12px 0;
        }
        
        .map-placeholder i {
            font-size: 24pt;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header clearfix">
        <div class="header-content">
            <div class="company-section">
                <div class="company-name">BIZMARK.ID</div>
                <div class="company-tagline">Polygon SHP Maker - Laporan Lokasi</div>
            </div>
            <div class="report-section">
                <div class="report-title">{{ $project->name }}</div>
                <div class="report-date">{{ $project->created_at->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>
    </div>

    <!-- Project Information -->
    <div class="section">
        <div class="section-title">Informasi Proyek</div>
        <div class="info-grid clearfix">
            <div class="info-row clearfix">
                <div class="info-label">Nama Lahan</div>
                <div class="info-value">{{ $project->name }}</div>
            </div>
            @if($project->company_name)
            <div class="info-row clearfix">
                <div class="info-label">Perusahaan</div>
                <div class="info-value">{{ $project->company_name }}</div>
            </div>
            @endif
            @if($project->contact_person)
            <div class="info-row clearfix">
                <div class="info-label">PIC</div>
                <div class="info-value">{{ $project->contact_person }}</div>
            </div>
            @endif
            <div class="info-row clearfix">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $project->email }}</div>
            </div>
            <div class="info-row clearfix">
                <div class="info-label">Telepon</div>
                <div class="info-value">{{ $project->phone }}</div>
            </div>
        </div>
    </div>

    <!-- Location Information -->
    @if($project->metadata)
    <div class="section">
        <div class="section-title">Lokasi</div>
        <div class="info-grid clearfix">
            @if(data_get($project->metadata, 'provinsi'))
            <div class="info-row clearfix">
                <div class="info-label">Provinsi</div>
                <div class="info-value">{{ data_get($project->metadata, 'provinsi') }}</div>
            </div>
            @endif
            @if(data_get($project->metadata, 'kabkota'))
            <div class="info-row clearfix">
                <div class="info-label">Kabupaten/Kota</div>
                <div class="info-value">{{ data_get($project->metadata, 'kabkota') }}</div>
            </div>
            @endif
            @if(data_get($project->metadata, 'kecamatan'))
            <div class="info-row clearfix">
                <div class="info-label">Kecamatan</div>
                <div class="info-value">{{ data_get($project->metadata, 'kecamatan') }}</div>
            </div>
            @endif
            @if(data_get($project->metadata, 'kelurahan'))
            <div class="info-row clearfix">
                <div class="info-label">Kelurahan/Desa</div>
                <div class="info-value">{{ data_get($project->metadata, 'kelurahan') }}</div>
            </div>
            @endif
            @if(data_get($project->metadata, 'keterangan'))
            <div class="info-row clearfix">
                <div class="info-label">Keterangan</div>
                <div class="info-value">{{ data_get($project->metadata, 'keterangan') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Area Statistics -->
    <div class="section">
        <div class="section-title">Statistik Lahan</div>
        <div class="stats-grid clearfix">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($project->area_m2, 2, ',', '.') }}</div>
                <div class="stat-label">Luas (m²)</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($project->area_ha, 4, ',', '.') }}</div>
                <div class="stat-label">Luas (Ha)</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($project->perimeter_m, 2, ',', '.') }}</div>
                <div class="stat-label">Keliling (m)</div>
            </div>
        </div>
    </div>

    <!-- RTRW Information -->
    @if($project->rtrw_zona || ($project->rtrw_raw && count($project->rtrw_raw) > 0))
    <div class="rtrw-section">
        <div class="rtrw-title">
            <span style="margin-right: 6px;">🏛️</span> Informasi Tata Ruang (RTRW)
        </div>
        
        @if($project->rtrw_zona)
        <div class="rtrw-zona">{{ $project->rtrw_zona }}</div>
        @endif
        
        @if($project->rtrw_perda)
        <div class="rtrw-perda">Dasar Hukum: {{ $project->rtrw_perda }}</div>
        @endif

        @if($project->rtrw_raw && count($project->rtrw_raw) > 0)
        <table class="zones-table">
            <thead>
                <tr>
                    <th style="width: 40%;">Zona</th>
                    <th style="width: 25%;">Kabupaten/Kota</th>
                    <th style="width: 20%;">Coverage</th>
                    <th style="width: 15%;">Hits</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->rtrw_raw as $zone)
                <tr>
                    <td>{{ $zone['zona'] ?? '-' }}</td>
                    <td>{{ $zone['kabupaten_kota'] ?? '-' }}</td>
                    <td>
                        {{ $zone['coverage_percent'] ?? 0 }}%
                        <div class="coverage-bar">
                            <div class="coverage-fill" style="width: {{ min($zone['coverage_percent'] ?? 0, 100) }}%;"></div>
                        </div>
                    </td>
                    <td>{{ $zone['hits'] ?? 1 }} titik</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="rtrw-disclaimer">
            Data zona tata ruang bersumber dari GISTARU (gistaru.atrbpn.go.id) Direktorat Jenderal Tata Ruang, Kementerian ATR/BPN. 
            Data bersifat informatif dan 'sebagaimana adanya' (as is). Untuk kepastian hukum, silakan merujuk pada produk hukum (Perda) yang berlaku.
        </div>
    </div>
    @endif

    <!-- Coordinates -->
    @if($coordinates && count($coordinates) > 0)
    <div class="coordinates-section">
        <div class="section-title">Koordinat Poligon ({{ count($coordinates) }} titik)</div>
        <table class="coord-table">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 45%;">Longitude</th>
                    <th style="width: 45%;">Latitude</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($coordinates, 0, 20) as $index => $coord)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($coord[0], 8, '.', '') }}</td>
                    <td>{{ number_format($coord[1], 8, '.', '') }}</td>
                </tr>
                @endforeach
                @if(count($coordinates) > 20)
                <tr>
                    <td colspan="3" style="text-align: center; color: #6b7280; font-style: italic;">
                        ... dan {{ count($coordinates) - 20 }} titik lainnya (lihat file SHP untuk data lengkap)
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer clearfix">
        <div class="footer-content">
            <div class="footer-left">
                Dibuat oleh Polygon SHP Maker - bizmark.id/polygon-shp-maker
            </div>
            <div class="footer-right">
                ID: {{ $project->id }} | Proyeksi: WGS84 (EPSG:4326)
            </div>
        </div>
    </div>
</body>
</html>
