<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
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
            color: #000;
            line-height: 1.6;
            padding: 10mm 10mm;
        }
        
        .header {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2c3e50;
        }
        
        .header-content {
            width: 100%;
        }
        
        .company-section {
            width: 55%;
            float: left;
        }
        
        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1px;
        }
        
        .company-tagline {
            font-size: 9pt;
            color: #7f8c8d;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .company-details {
            font-size: 8.5pt;
            color: #555;
            line-height: 1.4;
        }
        
        .invoice-section {
            width: 40%;
            float: right;
            text-align: right;
        }
        
        .invoice-title {
            font-size: 26pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 11pt;
            color: #34495e;
            font-weight: 600;
        }
        
        .invoice-dates {
            font-size: 9pt;
            color: #7f8c8d;
            margin-top: 3px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .info-section {
            margin: 18px 0;
        }
        
        .info-row {
            width: 100%;
        }
        
        .bill-to, .project-info {
            width: 48%;
            float: left;
        }
        
        .project-info {
            float: right;
        }
        
        .info-box {
            background: #ecf0f1;
            padding: 15px;
            border-left: 4px solid #3498db;
            min-height: 105px;
        }
        
        .info-box h3 {
            font-size: 9pt;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        
        .info-box p {
            font-size: 9pt;
            color: #34495e;
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .info-box strong {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 15px 0;
        }
        
        .items-table thead {
            background: #34495e;
            color: white;
        }
        
        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #2c3e50;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #bdc3c7;
        }
        
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .items-table td {
            padding: 8px;
            font-size: 9pt;
            color: #2c3e50;
            border-left: 1px solid #ecf0f1;
            border-right: 1px solid #ecf0f1;
        }
        
        .items-table td.text-right {
            text-align: right;
            font-weight: 500;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .totals-section {
            width: 100%;
            margin: 12px 0 20px 0;
        }
        
        .totals-box {
            width: 320px;
            float: right;
            background: #ecf0f1;
            padding: 12px;
            border: 1px solid #bdc3c7;
        }
        
        .total-row {
            width: 100%;
            margin-bottom: 6px;
            font-size: 9.5pt;
            overflow: hidden;
        }
        
        .total-row .total-label {
            float: left;
            color: #555;
        }
        
        .total-row .total-value {
            float: right;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .total-row.grand-total {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #34495e;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .total-row.grand-total .total-label,
        .total-row.grand-total .total-value {
            color: #2c3e50;
        }
        
        .payment-info {
            clear: both;
            margin: 15px 0;
            background: #fff9e6;
            padding: 12px;
            border: 1px solid #f39c12;
            border-left: 4px solid #f39c12;
        }
        
        .payment-info h3 {
            font-size: 9pt;
            color: #d68910;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .payment-info p {
            font-size: 8.5pt;
            color: #555;
            margin: 3px 0;
            line-height: 1.4;
        }
        
        .payment-info strong {
            color: #2c3e50;
        }
        
        .payment-history {
            margin: 15px 0;
        }
        
        .payment-history h3 {
            font-size: 9pt;
            color: #27ae60;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 4px;
        }
        
        .payment-item {
            background: #e8f8f5;
            padding: 10px;
            margin-bottom: 6px;
            border-left: 4px solid #27ae60;
        }
        
        .payment-item p {
            font-size: 8pt;
            color: #2c3e50;
            margin: 2px 0;
        }
        
        .payment-item strong {
            color: #27ae60;
        }
        
        .notes-section {
            margin: 15px 0;
            background: #f8f9fa;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        
        .notes-section h3 {
            font-size: 9pt;
            color: #555;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .notes-section p {
            font-size: 8.5pt;
            color: #333;
            line-height: 1.5;
        }
        
        .footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #bdc3c7;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-left: 8px;
        }
        
        .status-draft {
            background: #95a5a6;
            color: white;
        }
        
        .status-sent {
            background: #3498db;
            color: white;
        }
        
        .status-partial {
            background: #f39c12;
            color: white;
        }
        
        .status-paid {
            background: #27ae60;
            color: white;
        }
        
        .status-overdue {
            background: #e74c3c;
            color: white;
        }
        
        .status-cancelled {
            background: #95a5a6;
            color: white;
        }
        
        .amount-outstanding {
            background: #fee;
            border: 1px solid #fcc;
            padding: 8px;
            margin: 12px 0;
            text-align: center;
        }
        
        .amount-outstanding strong {
            color: #e74c3c;
            font-size: 11pt;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content clearfix">
            <div class="company-section">
                <div class="company-name">BizMark.ID</div>
                <div class="company-tagline">Professional Project Management Solutions</div>
                <div class="company-details">
                    <p><strong>PT BizMark Indonesia</strong></p>
                    <p>Jl. Bisnis Raya No. 123, Jakarta 12345</p>
                    <p>Telp: +62 21 1234 5678 | Email: finance@bizmark.id</p>
                    <p>NPWP: 01.234.567.8-901.000</p>
                </div>
            </div>
            
            <div class="invoice-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div class="invoice-dates">
                    <p>Tanggal: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
                    <p>Jatuh Tempo: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</p>
                </div>
                @php
                    $statusClass = 'status-' . $invoice->status;
                    $statusLabel = [
                        'draft' => 'DRAFT',
                        'sent' => 'TERKIRIM',
                        'partial' => 'SEBAGIAN',
                        'paid' => 'LUNAS',
                        'overdue' => 'TERLAMBAT',
                        'cancelled' => 'DIBATALKAN'
                    ][$invoice->status] ?? strtoupper($invoice->status);
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
        </div>
    </div>

    <!-- Client and Project Info -->
    <div class="info-section">
        <div class="info-row clearfix">
            <div class="bill-to">
                <div class="info-box">
                    <h3>Ditagih Kepada:</h3>
                    <p><strong>{{ $invoice->client_name }}</strong></p>
                    @if($invoice->client_address)
                        <p>{{ $invoice->client_address }}</p>
                    @endif
                    @if($invoice->client_tax_id)
                        <p>NPWP: {{ $invoice->client_tax_id }}</p>
                    @endif
                </div>
            </div>
            
            <div class="project-info">
                <div class="info-box">
                    <h3>Informasi Proyek:</h3>
                    <p><strong>{{ $invoice->project->name }}</strong></p>
                    @if($invoice->project->client_name)
                        <p>Klien: {{ $invoice->project->client_name }}</p>
                    @endif
                    @if($invoice->project->contract_number ?? false)
                        <p>No. Kontrak: {{ $invoice->project->contract_number }}</p>
                    @endif
                    <p>PIC: {{ $invoice->project->pic_name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="40%">Deskripsi</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Section -->
    <div class="totals-section clearfix">
        <div class="totals-box">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span class="total-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Pajak ({{ number_format($invoice->tax_rate, 0) }}%)</span>
                <span class="total-value">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label">TOTAL</span>
                <span class="total-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
            </div>
            @if($invoice->paid_amount > 0)
            <div class="total-row" style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #bdc3c7;">
                <span class="total-label">Terbayar</span>
                <span class="total-value" style="color: #27ae60;">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Sisa</span>
                <span class="total-value" style="color: #e74c3c;">Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>

    @if($invoice->remaining_amount > 0 && $invoice->status !== 'paid')
    <!-- Outstanding Amount Alert -->
    <div class="amount-outstanding">
        <p>Jumlah yang Harus Dibayar: <strong>Rp {{ number_format($invoice->remaining_amount, 0, ',', '.') }}</strong></p>
    </div>
    @endif

    <!-- Payment Instructions -->
    <div class="payment-info">
        <h3>Informasi Pembayaran</h3>
        <p><strong>Metode Pembayaran:</strong></p>
        <p>Transfer Bank ke:</p>
        <p><strong>Bank Central Asia (BCA)</strong></p>
        <p>No. Rekening: 1234567890</p>
        <p>Atas Nama: PT BizMark Indonesia</p>
        <p style="margin-top: 8px; font-style: italic;">Mohon cantumkan nomor invoice pada berita transfer.</p>
    </div>

    @if($invoice->paymentSchedules && $invoice->paymentSchedules->where('status', 'paid')->count() > 0)
    <!-- Payment History -->
    <div class="payment-history">
        <h3>Riwayat Pembayaran</h3>
        @foreach($invoice->paymentSchedules->where('status', 'paid') as $payment)
        <div class="payment-item">
            <p><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong> - {{ \Carbon\Carbon::parse($payment->paid_date)->format('d/m/Y') }}</p>
            <p>Metode: {{ ucfirst($payment->payment_method) }}
                @if($payment->payment_reference)
                 | Ref: {{ $payment->payment_reference }}
                @endif
            </p>
        </div>
        @endforeach
    </div>
    @endif

    @if($invoice->notes)
    <!-- Notes -->
    <div class="notes-section">
        <h3>Catatan</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Terima kasih atas kepercayaan Anda!</strong></p>
        <p>Dokumen ini adalah bukti invoice resmi dari PT BizMark Indonesia</p>
        <p>Untuk pertanyaan, hubungi finance@bizmark.id atau +62 21 1234 5678</p>
        <p style="margin-top: 8px; font-size: 7pt;">Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} WIB</p>
    </div>
</body>
</html>
