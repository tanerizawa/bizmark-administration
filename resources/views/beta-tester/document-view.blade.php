@extends('beta-tester.layouts.app')

@section('title', $document->document_title)

@section('styles')
    <style>
        /* A4 Document Container */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e3e9f0 100%);
        }
        
        .document-shell {
            background: linear-gradient(135deg, #f5f7fa 0%, #e3e9f0 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        /* A4 Paper Simulation */
        .a4-container {
            width: 21cm; /* A4 width */
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .a4-page {
            min-height: 29.7cm; /* A4 height */
            padding: 2.5cm 2cm; /* Formal margins: top/bottom 2.5cm, left/right 2cm */
            background: white;
            position: relative;
        }

        /* Document Header */
        .document-header {
            text-align: center;
            margin-bottom: 2cm;
            padding-bottom: 1cm;
            border-bottom: 3px double #1a202c;
        }

        .document-header h1 {
            font-size: 18pt;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .document-header .subtitle {
            font-size: 12pt;
            color: #4a5568;
            font-weight: 500;
        }

        /* Meta Info Bar */
        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5cm;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
        }

        .meta-bar .meta-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 10pt;
        }

        .meta-bar .meta-info span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-chip {
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 9pt;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Document Content */
        .document-content {
            font-family: 'Times New Roman', 'Georgia', serif;
            font-size: 11pt;
            line-height: 2;
            color: #1a202c;
            text-align: justify;
        }

        .document-content h1 {
            font-size: 16pt;
            font-weight: 700;
            text-align: center;
            margin: 1.5cm 0 1cm 0;
            color: #1a202c;
        }

        .document-content h2 {
            font-size: 13pt;
            font-weight: 700;
            text-align: center;
            margin: 1.2cm 0 0.8cm 0;
            color: #2d3748;
        }

        .document-content h3 {
            font-size: 11pt;
            font-weight: 700;
            margin: 1cm 0 0.5cm 0;
            color: #2d3748;
        }

        .document-content p {
            margin-bottom: 0.5cm;
            text-indent: 0;
        }

        .document-content strong {
            font-weight: 700;
            color: #1a202c;
        }

        .document-content ul,
        .document-content ol {
            margin-left: 1.5cm;
            margin-bottom: 0.5cm;
        }

        .document-content li {
            margin-bottom: 0.3cm;
            line-height: 1.8;
        }

        .document-content ul {
            list-style-type: disc;
        }

        .document-content ol {
            list-style-type: decimal;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 2cm;
            padding-top: 1cm;
            border-top: 2px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .signature-header {
            text-align: center;
            margin-bottom: 1cm;
        }

        .signature-header h3 {
            font-size: 14pt;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .signature-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5cm 1cm;
            border-radius: 8px;
            margin-bottom: 0.8cm;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .signature-table td {
            padding: 0.4cm 0;
            vertical-align: top;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .signature-table tr:last-child td {
            border-bottom: none;
        }

        .signature-table td:first-child {
            width: 180px;
            font-weight: 700;
            opacity: 0.9;
        }

        .signature-hash {
            font-family: 'Courier New', 'Consolas', monospace;
            font-size: 8pt;
            background: rgba(0, 0, 0, 0.2);
            padding: 0.4cm;
            border-radius: 4px;
            word-break: break-all;
            margin-top: 0.2cm;
        }

        /* Agreement Box */
        .agreement-box {
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 1cm;
            margin: 1cm 0;
            transition: all 0.3s ease;
        }

        .agreement-box:hover {
            border-color: #667eea;
            background: #eff6ff;
        }

        .agreement-box label {
            display: flex;
            align-items: start;
            gap: 1rem;
            cursor: pointer;
        }

        .agreement-box input[type="checkbox"] {
            width: 24px;
            height: 24px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .agreement-box .agreement-text {
            font-size: 10pt;
            line-height: 1.8;
            color: #2d3748;
        }

        /* Action Buttons */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1cm;
            padding-top: 1cm;
            border-top: 1px solid #e2e8f0;
        }

        .btn-action {
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 10pt;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-action:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary-action {
            background: white;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary-action:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1cm;
            border-radius: 8px;
            margin-top: 1cm;
        }

        .info-box h4 {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 0.5cm;
        }

        .info-box ul {
            list-style: none;
            padding: 0;
        }

        .info-box li {
            padding: 0.3cm 0;
            display: flex;
            align-items: start;
            gap: 0.5rem;
            font-size: 9pt;
            line-height: 1.6;
        }

        .info-box li i {
            margin-top: 2px;
            color: #a7f3d0;
        }

        /* Success Box */
        .success-box {
            background: linear-gradient(135deg, #d4fce4 0%, #a7f3d0 100%);
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 1cm;
            margin: 1cm 0;
        }

        .success-box h3 {
            font-size: 12pt;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 0.5cm;
        }

        .success-box p {
            color: #047857;
            font-size: 10pt;
            margin-bottom: 0.5cm;
        }

        .success-box .action-buttons {
            display: flex;
            gap: 0.5cm;
            flex-wrap: wrap;
        }

        /* Verification Badge */
        .verification-badge {
            text-align: center;
            padding: 0.5cm;
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 8px;
            margin-bottom: 0.8cm;
        }

        .verification-badge p {
            font-size: 11pt;
            font-weight: 700;
            color: #155724;
            margin: 0;
        }

        /* Back Link */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            text-decoration: none;
            font-weight: 600;
            font-size: 10pt;
            margin-bottom: 1cm;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #1a202c;
        }

        /* Responsive - Tablet and below */
        @media (max-width: 768px) {
            .a4-container {
                width: 100%;
                margin: 0;
                box-shadow: none;
            }

            .a4-page {
                min-height: auto;
                padding: 1.5cm 1cm;
            }

            .document-shell {
                padding: 0;
            }

            .meta-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .meta-bar .meta-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .document-content {
                font-size: 10pt;
            }

            .action-bar {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .document-shell {
                background: white;
                padding: 0;
            }

            .a4-container {
                width: 21cm;
                margin: 0 auto;
                box-shadow: none;
            }

            .a4-page {
                min-height: 29.7cm;
                padding: 2.5cm 2cm;
            }

            .back-link,
            .action-bar,
            .agreement-box,
            .info-box {
                display: none !important;
            }

            .meta-bar {
                background: #f7fafc !important;
                color: #1a202c !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('content')
    <div class="document-shell">
        <div class="a4-container">
            <div class="a4-page">
                <!-- Back Link -->
                <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}"
                   class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Dashboard</span>
                </a>

                <!-- Document Header -->
                <div class="document-header">
                    <h1>{{ strtoupper($document->document_title) }}</h1>
                    <div class="subtitle">Program Beta Tester Bizmark.ID</div>
                </div>

                <!-- Meta Bar -->
                <div class="meta-bar">
                    <div class="meta-info">
                        <span>
                            <i class="fas fa-user"></i>
                            {{ $betaTester->full_name }}
                        </span>
                        <span>
                            <i class="fas fa-id-badge"></i>
                            {{ $betaTester->registration_number }}
                        </span>
                    </div>
                    <span class="status-chip"
                          style="background: @if($document->signed_status['color'] == 'yellow') #F59E0B @elseif($document->signed_status['color'] == 'blue') #3B82F6 @else #10B981 @endif;">
                        <i class="fas fa-{{ $document->signed_status['icon'] }}"></i>
                        {{ $document->signed_status['label'] }}
                    </span>
                </div>

                <!-- Document Content -->
                <div class="document-content">
                    {!! \Illuminate\Support\Str::markdown($document->filled_content) !!}
                </div>

                @if($document->is_signed)
                <!-- Signature Section -->
                <div class="signature-section">
                    <div class="verification-badge">
                        <p>✓ DOKUMEN TELAH DITANDATANGANI SECARA DIGITAL</p>
                    </div>

                    <div class="signature-header">
                        <h3>INFORMASI TANDA TANGAN DIGITAL</h3>
                    </div>

                    <div class="signature-box">
                        <table class="signature-table">
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
                                <td style="font-family: 'Courier New', monospace; font-size: 9pt;">{{ $document->signature_ip }}</td>
                            </tr>
                            <tr>
                                <td>Perangkat:</td>
                                <td>{{ $document->signature_data['browser'] ?? 'Unknown Browser' }}</td>
                            </tr>
                            <tr>
                                <td>Hash Digital:</td>
                                <td>
                                    <div class="signature-hash">{{ $document->signature_hash }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="margin-top: 0.8cm; padding: 0.6cm; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px;">
                        <p style="font-size: 9pt; text-align: center; font-weight: 700; margin-bottom: 0.3cm; color: #856404;">
                            TENTANG TANDA TANGAN DIGITAL
                        </p>
                        <p style="font-size: 8.5pt; text-align: justify; line-height: 1.6; color: #856404; margin: 0;">
                            Tanda tangan digital ini telah dienkripsi menggunakan algoritma SHA-256 dan memenuhi standar keamanan digital. 
                            Hash yang tercantum di atas menjamin bahwa dokumen ini tidak dapat diubah setelah ditandatangani. 
                            Sesuai dengan UU ITE No. 11 Tahun 2008, tanda tangan elektronik memiliki kekuatan hukum yang sama dengan tanda tangan konvensional.
                        </p>
                    </div>
                </div>
                @endif

                @if(!$document->is_signed)
                <!-- Agreement Form -->
                <form action="{{ route('beta-tester.document.sign', ['documentId' => $document->id]) }}" 
                      method="POST"
                      x-data="{ agreed: false }">
                    @csrf
                    <input type="hidden" name="token" value="{{ $betaTester->registration_number }}">
                    
                    <div class="agreement-box">
                        <label>
                            <input type="checkbox" 
                                   name="agreement" 
                                   value="1"
                                   x-model="agreed"
                                   required>
                            <div class="agreement-text">
                                Saya telah membaca, memahami, dan menyetujui seluruh isi dokumen 
                                <strong>{{ $document->document_title }}</strong> di atas. 
                                Saya bersedia terikat dengan semua ketentuan yang tercantum dan 
                                bertanggung jawab penuh atas persetujuan ini.
                            </div>
                        </label>

                        @error('agreement')
                            <p style="color: #dc2626; font-size: 9pt; margin-top: 0.5cm;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="action-bar">
                        <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}" 
                           class="btn-action btn-secondary-action">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="submit" 
                                :disabled="!agreed"
                                class="btn-action btn-primary-action"
                                :class="agreed ? '' : 'opacity-50 cursor-not-allowed'">
                            <i class="fas fa-pen-fancy"></i>
                            <span>Tanda Tangani Dokumen</span>
                        </button>
                    </div>
                </form>
                @else
                <!-- Already Signed -->
                <div class="success-box">
                    <h3>✓ Dokumen Telah Ditandatangani</h3>
                    <p>
                        Anda telah menandatangani dokumen ini pada {{ $document->signed_at->isoFormat('DD MMMM YYYY, HH:mm') }} WIB.
                    </p>
                    <div class="action-buttons">
                        <a href="{{ route('beta-tester.dashboard', ['token' => $betaTester->registration_number]) }}" 
                           class="btn-action btn-secondary-action">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                        <a href="{{ route('beta-tester.document.download', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}" 
                           class="btn-action btn-primary-action">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
                @endif

                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-shield-alt"></i> Tentang Tanda Tangan Digital</h4>
                    <ul>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Tanda tangan digital memiliki kekuatan hukum yang sama dengan tanda tangan basah</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Sistem merekam waktu, IP address, dan browser yang digunakan</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Hash SHA-256 memastikan integritas dokumen tidak dapat diubah</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Dokumen yang telah ditandatangani tidak dapat dimodifikasi</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Anda dapat mengunduh PDF dokumen setelah penandatanganan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
