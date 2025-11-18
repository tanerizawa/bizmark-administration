<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Perizinan - {{ $kbli->code }}</title>
    
    <!-- SVG Logo Definitions -->
    <svg style="display: none;">
        <defs>
            <!-- BizMark Logo - Full Version with Leaf -->
            <symbol id="bizmark-logo-full" viewBox="0 0 200 80">
                <!-- Leaf Icon with Growth Concept -->
                <g id="leaf-icon">
                    <!-- Main Leaf Shape -->
                    <path d="M 40 20 Q 35 30, 30 45 Q 28 55, 32 65 Q 36 70, 42 68 Q 46 66, 48 60 L 50 50 Q 52 45, 55 42 Q 60 38, 62 32 Q 63 25, 60 20 Q 56 16, 50 18 Q 45 19, 40 20 Z" 
                          fill="#0a66c2" 
                          opacity="0.9"/>
                    <!-- Leaf Vein Details -->
                    <path d="M 42 25 Q 44 35, 45 45 Q 46 55, 44 62" 
                          stroke="#004182" 
                          stroke-width="1.5" 
                          fill="none" 
                          opacity="0.7"/>
                    <path d="M 42 30 Q 38 35, 35 42" 
                          stroke="#004182" 
                          stroke-width="1" 
                          fill="none" 
                          opacity="0.5"/>
                    <path d="M 45 35 Q 50 38, 54 40" 
                          stroke="#004182" 
                          stroke-width="1" 
                          fill="none" 
                          opacity="0.5"/>
                    <!-- Growth Dots -->
                    <circle cx="48" cy="28" r="2" fill="#28a745" opacity="0.8"/>
                    <circle cx="52" cy="35" r="1.5" fill="#28a745" opacity="0.6"/>
                    <circle cx="38" cy="48" r="1.5" fill="#28a745" opacity="0.6"/>
                </g>
                
                <!-- BizMark Text -->
                <text x="75" y="45" font-family="'DejaVu Sans', Arial, sans-serif" font-size="28" font-weight="bold" fill="#0a66c2">
                    BizMark
                </text>
                <text x="75" y="60" font-family="'DejaVu Sans', Arial, sans-serif" font-size="9" fill="#666" letter-spacing="1">
                    INDONESIA
                </text>
            </symbol>
            
            <!-- BizMark Icon Only - For Favicon/Small Spaces -->
            <symbol id="bizmark-icon" viewBox="0 0 80 80">
                <!-- Circular Background -->
                <circle cx="40" cy="40" r="38" fill="#0a66c2" opacity="0.1"/>
                <circle cx="40" cy="40" r="36" fill="#0a66c2"/>
                
                <!-- Leaf in Circle -->
                <g transform="translate(15, 15)">
                    <path d="M 25 10 Q 20 15, 15 25 Q 13 32, 16 40 Q 20 45, 26 43 Q 30 41, 32 35 L 34 27 Q 36 23, 39 21 Q 43 18, 45 13 Q 46 8, 43 5 Q 39 2, 33 4 Q 28 5, 25 10 Z" 
                          fill="#ffffff" 
                          opacity="0.95"/>
                    <path d="M 26 13 Q 28 20, 29 27 Q 30 34, 28 39" 
                          stroke="#e3f2fd" 
                          stroke-width="1.5" 
                          fill="none" 
                          opacity="0.8"/>
                    <!-- Letter B Integration -->
                    <text x="18" y="35" font-family="'DejaVu Sans', Arial, sans-serif" font-size="24" font-weight="bold" fill="#ffffff" opacity="0.3">
                        B
                    </text>
                </g>
            </symbol>
            
            <!-- BizMark Watermark -->
            <symbol id="bizmark-watermark" viewBox="0 0 300 100">
                <text x="10" y="60" font-family="'DejaVu Sans', Arial, sans-serif" font-size="48" font-weight="bold" fill="#0a66c2" opacity="0.05">
                    BIZMARK
                </text>
            </symbol>
        </defs>
    </svg>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.7;
            color: #2d2f38;
            background: #f5f7fa;
            padding: 22px 26px 70px;
        }
        
        .page-break {
            page-break-after: always;
        }

        .content {
            background: #ffffff;
            border: 1px solid #e6ebf1;
            border-radius: 10px;
            padding: 22px 24px 32px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 1;
        }
        
        /* Header / Kop Surat */
        .letterhead {
            border-bottom: 3px solid #0a66c2;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        
        .letterhead-header {
            display: table;
            width: 100%;
        }
        
        .letterhead-logo {
            display: table-cell;
            width: 85px;
            vertical-align: middle;
        }
        
        .letterhead-logo svg {
            width: 75px;
            height: 75px;
        }
        
        .letterhead-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 15px;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0a66c2;
            margin-bottom: 4px;
        }
        
        .company-tagline {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin-bottom: 5px;
        }
        
        .company-contact {
            font-size: 8pt;
            color: #555;
            line-height: 1.4;
        }
        
        /* Document Title */
        .doc-title {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }
        
        .doc-title h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .doc-title p {
            font-size: 9pt;
            opacity: 0.95;
        }
        
        /* Metadata */
        .metadata {
            background: #f8fafe;
            padding: 14px 16px;
            border-left: 4px solid #0a66c2;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        
        .metadata table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .metadata td {
            padding: 4px 0;
        }
        
        .metadata td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        /* Section */
        .section {
            margin-bottom: 22px;
            padding: 16px 18px;
            border: 1px solid #eef1f4;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0a66c2;
            border-bottom: 2px solid #0a66c2;
            padding-bottom: 5px;
            margin-bottom: 14px;
        }
        
        .section-content {
            padding-left: 6px;
            padding-right: 6px;
        }
        
        /* Info Box */
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            font-size: 9pt;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            font-size: 9pt;
        }
        
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            font-size: 9pt;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border-right: 1px solid #e6ebf1;
            background: #f9fbfd;
        }
        
        .stat-value {
            font-size: 20pt;
            font-weight: bold;
            color: #0a66c2;
        }
        
        .stat-label {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }

        .stat-item:last-child {
            border-right: none;
        }
        
        /* Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9pt;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            overflow: hidden;
        }
        
        table.data-table th {
            background: #0a66c2;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }
        
        table.data-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-mandatory {
            background: #dc3545;
            color: white;
        }
        
        .badge-recommended {
            background: #ffc107;
            color: #333;
        }
        
        .badge-conditional {
            background: #17a2b8;
            color: white;
        }
        
        /* List */
        ul.custom-list {
            list-style: none;
            padding-left: 0;
        }
        
        ul.custom-list li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
            color: #2f313a;
        }
        
        ul.custom-list li:before {
            content: "▪";
            color: #0a66c2;
            font-weight: bold;
            position: absolute;
            left: 5px;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 2px solid #0a66c2;
            padding-top: 10px;
            background: #ffffff;
            font-size: 8pt;
            color: #666;
        }
        
        .footer-content {
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            width: 60%;
        }
        
        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.05;
            z-index: -1;
        }
        
        .watermark svg {
            width: 400px;
            height: 150px;
        }
        
        /* Cost Table */
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .cost-table th,
        .cost-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .cost-table th {
            background: #0a66c2;
            color: white;
            font-weight: bold;
        }
        
        .cost-table .total-row {
            background: #004182;
            color: white;
            font-weight: bold;
            font-size: 11pt;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-10 {
            margin-bottom: 10px;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
        
        /* Logo Styles */
        .logo-icon-small {
            width: 20px;
            height: 20px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">
        <svg viewBox="0 0 500 150">
            <text x="10" y="100" font-family="'DejaVu Sans', Arial, sans-serif" font-size="72" font-weight="bold" fill="#0a66c2" opacity="1">
                BIZMARK
            </text>
        </svg>
    </div>
    
    <div class="content">
        <!-- Letterhead / Kop Surat -->
        <div class="letterhead">
            <div class="letterhead-header">
                <div class="letterhead-logo">
                    <!-- BizMark Logo SVG - Official Logo -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="75" height="75">
                        <g>
                            <path d="M 605.50 949.21 C589.72,950.54 520.05,951.06 511.00,949.91 C507.42,949.46 498.42,948.60 491.00,948.00 C483.58,947.39 473.45,946.27 468.50,945.51 C463.55,944.74 457.70,943.85 455.50,943.52 C437.48,940.87 415.88,936.60 407.50,934.04 C405.85,933.53 401.58,932.39 398.00,931.50 C390.29,929.58 389.53,929.35 371.50,923.48 C359.81,919.68 348.69,915.58 337.50,910.95 C336.40,910.49 328.08,906.53 319.00,902.13 C294.07,890.06 275.94,878.62 254.13,861.20 C245.54,854.33 222.88,831.56 216.62,823.50 C204.01,807.25 198.10,797.90 188.54,779.00 C182.55,767.18 181.00,763.88 181.00,762.93 C181.00,762.45 179.90,759.68 178.56,756.78 C174.87,748.79 169.02,730.95 166.99,721.50 C166.52,719.30 165.39,714.35 164.49,710.50 C159.95,691.17 158.65,676.80 158.62,645.50 C158.59,616.54 160.17,596.96 163.81,581.00 C164.37,578.53 165.36,573.80 165.99,570.50 C167.57,562.29 168.43,558.88 171.43,548.94 C172.84,544.23 174.00,539.65 174.00,538.75 C174.00,537.85 174.45,536.84 175.00,536.50 C175.55,536.16 176.00,534.63 176.00,533.11 C176.00,531.58 176.31,530.02 176.69,529.65 C177.37,528.96 178.74,525.43 181.58,517.00 C183.33,511.81 183.55,511.28 188.39,500.29 C190.37,495.77 192.01,491.72 192.02,491.29 C192.04,490.21 204.93,464.77 208.89,458.00 C210.66,454.98 212.36,451.71 212.67,450.75 C212.98,449.79 213.63,449.00 214.12,449.00 C214.60,449.00 215.00,448.55 215.00,448.01 C215.00,446.81 232.22,419.75 235.68,415.50 C237.03,413.85 240.35,409.58 243.07,406.00 C245.79,402.42 250.37,396.58 253.25,393.00 C260.07,384.54 299.61,345.01 308.00,338.27 C320.40,328.31 322.77,326.48 328.47,322.50 C331.62,320.30 334.71,318.02 335.35,317.44 C336.95,315.96 346.87,309.79 350.50,308.02 C352.15,307.21 354.67,305.61 356.10,304.47 C357.53,303.32 361.36,301.11 364.60,299.57 C367.85,298.02 372.08,295.70 374.00,294.42 C375.92,293.13 384.25,289.12 392.50,285.49 C400.75,281.87 408.40,278.50 409.50,278.01 C412.61,276.62 430.98,270.42 436.00,269.08 C438.48,268.41 441.85,267.46 443.50,266.97 C448.48,265.50 465.17,261.77 470.50,260.94 C480.30,259.41 488.69,258.09 502.50,255.90 C514.89,253.94 520.69,253.66 553.00,253.51 C587.40,253.35 592.60,253.06 597.50,251.01 C598.60,250.54 600.40,249.94 601.50,249.67 C603.52,249.17 608.80,247.37 612.50,245.92 C613.60,245.49 615.85,244.93 617.50,244.68 C619.15,244.43 623.08,243.51 626.24,242.63 C647.40,236.74 680.11,234.13 710.88,235.87 C729.06,236.90 739.56,237.98 744.50,239.33 C745.60,239.63 750.10,240.64 754.50,241.58 C761.57,243.09 764.68,243.85 775.00,246.57 C779.63,247.79 797.46,254.13 802.00,256.17 C804.47,257.28 808.08,258.87 810.00,259.70 C815.54,262.09 831.13,270.10 835.23,272.66 C837.28,273.95 839.19,275.00 839.47,275.00 C840.42,275.00 862.46,289.91 867.15,293.72 C869.71,295.80 874.21,299.44 877.15,301.82 C884.59,307.81 906.42,329.46 911.14,335.50 C913.28,338.25 917.06,342.98 919.54,346.00 C938.04,368.59 958.98,409.35 967.06,438.50 C970.85,452.18 973.34,462.36 974.04,467.00 C974.49,470.02 975.59,476.95 976.49,482.39 C977.51,488.56 977.76,492.64 977.18,493.22 C976.59,493.81 974.78,493.42 972.37,492.19 C969.05,490.49 962.85,488.30 949.50,484.09 C942.43,481.85 913.15,474.79 895.50,471.06 C880.72,467.93 867.28,465.05 860.50,463.54 C857.20,462.81 852.25,461.73 849.50,461.14 C839.89,459.09 821.47,454.92 818.00,454.01 C816.08,453.50 807.97,451.47 800.00,449.48 C786.72,446.18 779.32,444.16 767.00,440.45 C727.28,428.49 687.50,411.89 666.79,398.64 C663.66,396.64 660.81,395.00 660.45,395.00 C659.57,395.00 644.80,384.59 638.00,379.19 C621.79,366.30 596.00,337.34 596.00,332.02 C596.00,331.46 595.66,331.00 595.25,330.99 C594.84,330.99 594.01,329.98 593.42,328.74 C592.83,327.51 590.79,323.58 588.89,320.00 C585.43,313.48 581.18,302.97 579.00,295.50 C578.35,293.30 577.26,289.70 576.57,287.50 L 575.32 283.50 L 560.91 283.17 C552.98,282.99 542.00,283.34 536.50,283.95 C525.87,285.12 497.85,289.58 492.00,291.03 C490.08,291.51 483.77,293.07 478.00,294.51 C466.36,297.39 465.17,297.74 453.00,301.89 C427.24,310.65 400.46,323.77 380.01,337.65 C362.07,349.83 357.08,353.92 342.45,368.50 C331.30,379.61 326.29,385.52 313.20,403.00 C307.47,410.66 293.21,437.36 289.17,448.00 C285.00,459.00 280.25,474.74 274.99,495.00 C273.49,500.81 269.29,522.03 268.18,529.50 C267.60,533.35 266.64,539.88 266.03,544.00 C260.62,580.63 262.68,625.62 271.18,656.50 C278.91,684.55 290.99,708.65 308.04,730.00 C315.73,739.64 331.39,755.16 340.87,762.54 C344.38,765.27 348.72,768.74 350.51,770.25 C352.30,771.76 354.07,773.00 354.44,773.00 C354.81,773.00 357.90,774.91 361.30,777.24 C368.61,782.25 375.84,786.26 389.50,792.89 C403.55,799.70 410.89,803.00 412.04,803.00 C412.58,803.00 413.81,803.43 414.76,803.95 C416.89,805.10 428.05,809.32 433.50,811.03 C435.70,811.71 438.40,812.63 439.50,813.06 C443.81,814.74 458.20,819.00 459.58,819.00 C460.37,819.00 461.81,819.40 462.76,819.88 C465.51,821.28 481.49,825.04 497.00,827.95 C499.48,828.41 504.88,829.54 509.00,830.45 C516.14,832.04 521.33,832.80 539.50,834.91 C587.75,840.52 600.62,841.37 637.00,841.38 C662.82,841.38 673.82,840.98 685.00,839.62 C727.46,834.47 755.88,824.96 775.12,809.46 C791.40,796.35 799.00,777.42 796.13,757.14 C794.50,745.64 792.04,737.86 786.02,725.25 C783.32,719.61 781.38,715.00 781.70,715.00 C782.48,715.00 793.93,724.96 802.36,732.98 C810.04,740.28 820.38,752.44 824.06,758.50 C830.41,768.93 834.86,777.83 836.09,782.50 C836.52,784.15 837.79,788.88 838.92,793.00 C843.39,809.37 840.81,829.00 832.00,845.77 C819.05,870.41 794.43,891.87 758.09,910.21 C736.81,920.95 720.30,927.12 688.00,936.43 C679.10,938.99 667.07,941.31 649.04,943.92 C644.94,944.51 640.89,945.20 640.04,945.45 C637.93,946.07 621.22,947.89 605.50,949.21 Z" fill="rgba(0,80,58,1)"/>
                        </g>
                    </svg>
                </div>
                <div class="letterhead-info">
                    <div class="company-name">BizMark Indonesia</div>
                    <div class="company-tagline">Konsultan Perizinan Usaha Terpercaya</div>
                    <div class="company-contact">
                        📍 Jakarta, Indonesia | 
                        ☎ +62 812-3456-7890 | 
                        ✉ cs@bizmark.id | 
                        🌐 www.bizmark.id
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Document Title -->
        <div class="doc-title">
            <h1>Laporan Analisis Kebutuhan Perizinan Usaha</h1>
            <p>Rekomendasi Izin Berbasis KBLI dan Konteks Bisnis</p>
        </div>
        
        <!-- Metadata -->
        <div class="metadata">
            <table>
                <tr>
                    <td>Nomor Dokumen</td>
                    <td>: {{ $metadata['document_number'] }}</td>
                </tr>
                <tr>
                    <td>Tanggal Pembuatan</td>
                    <td>: {{ $metadata['generated_date'] }} pukul {{ $metadata['generated_time'] }} WIB</td>
                </tr>
                <tr>
                    <td>Masa Berlaku</td>
                    <td>: Hingga {{ $metadata['validity_period'] }}</td>
                </tr>
                <tr>
                    <td>Dibuat untuk</td>
                    <td>: <strong>{{ $client->name }}</strong></td>
                </tr>
                <tr>
                    <td>Jenis Usaha</td>
                    <td>: {{ $client->client_type === 'company' ? 'Perusahaan' : 'Perorangan' }}</td>
                </tr>
                @if($client->company_name)
                <tr>
                    <td>Nama Perusahaan</td>
                    <td>: {{ $client->company_name }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <!-- Section I: Informasi KBLI -->
        <div class="section">
            <div class="section-title">I. INFORMASI KBLI (Klasifikasi Baku Lapangan Usaha Indonesia)</div>
            <div class="section-content">
                <table class="data-table">
                    <tr>
                        <th style="width: 150px;">Kode KBLI</th>
                        <td><strong style="font-size: 12pt; color: #0a66c2;">{{ $kbli->code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $kbli->description }}</td>
                    </tr>
                    <tr>
                        <th>Sektor</th>
                        <td>{{ $kbli->sector }}</td>
                    </tr>
                    @if($kbli->notes)
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $kbli->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Section II: Konteks Bisnis -->
        <div class="section">
            <div class="section-title">II. KONTEKS BISNIS</div>
            <div class="section-content">
                @php
                    $scaleLabels = [
                        'mikro' => 'Usaha Mikro (Aset ≤ Rp 50 juta)',
                        'kecil' => 'Usaha Kecil (Aset Rp 50-500 juta)',
                        'menengah' => 'Usaha Menengah (Aset Rp 500 juta - 10 miliar)',
                        'besar' => 'Usaha Besar (Aset > Rp 10 miliar)',
                    ];
                    $locationLabels = [
                        'perkotaan' => 'Area Perkotaan',
                        'pedesaan' => 'Area Pedesaan',
                        'kawasan_industri' => 'Kawasan Industri',
                    ];
                @endphp
                
                <table class="data-table">
                    <tr>
                        <th style="width: 150px;">Skala Usaha</th>
                        <td>{{ $scaleLabels[$businessScale] ?? 'Tidak ditentukan (rekomendasi umum)' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Operasional</th>
                        <td>{{ $locationLabels[$locationType] ?? 'Tidak ditentukan' }}</td>
                    </tr>
                    @if($contextArray)
                        @if(isset($contextArray['land_area']))
                        <tr>
                            <th>Luas Lahan</th>
                            <td>{{ number_format($contextArray['land_area'], 0, ',', '.') }} m²</td>
                        </tr>
                        @endif
                        @if(isset($contextArray['building_area']))
                        <tr>
                            <th>Luas Bangunan</th>
                            <td>{{ number_format($contextArray['building_area'], 0, ',', '.') }} m²</td>
                        </tr>
                        @endif
                        @if(isset($contextArray['investment_value']))
                        <tr>
                            <th>Nilai Investasi</th>
                            <td>Rp {{ number_format($contextArray['investment_value'], 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if(isset($contextArray['environmental_impact']))
                        <tr>
                            <th>Dampak Lingkungan</th>
                            <td>{{ ucfirst($contextArray['environmental_impact']) }}</td>
                        </tr>
                        @endif
                    @endif
                </table>
                
                @if(!$contextArray)
                <div class="info-box">
                    <strong>ℹ️ Informasi:</strong> Analisis ini menggunakan rekomendasi umum. Untuk estimasi biaya yang lebih akurat, silakan lengkapi data konteks bisnis melalui portal klien.
                </div>
                @endif
            </div>
        </div>
        
        <!-- Section III: Ringkasan Rekomendasi -->
        <div class="section">
            <div class="section-title">III. RINGKASAN REKOMENDASI</div>
            <div class="section-content">
                @php
                    $totalPermits = count($recommendation->recommended_permits ?? []);
                    $mandatoryCount = $recommendation->mandatory_permits_count ?? 0;
                    $confidencePercent = max(5, min(100, round(($recommendation->confidence_score ?? 0) * 100)));
                    
                    if ($mandatoryCount <= 2) {
                        $complexityLevel = 'Rendah';
                    } elseif ($mandatoryCount <= 5) {
                        $complexityLevel = 'Menengah';
                    } else {
                        $complexityLevel = 'Tinggi';
                    }
                @endphp
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $totalPermits }}</div>
                        <div class="stat-label">Total Izin<br>Direkomendasi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $mandatoryCount }}</div>
                        <div class="stat-label">Izin Wajib<br>Prioritas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $confidencePercent }}%</div>
                        <div class="stat-label">Akurasi<br>Rekomendasi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $complexityLevel }}</div>
                        <div class="stat-label">Tingkat<br>Kompleksitas</div>
                    </div>
                </div>
                
                <div class="success-box mt-20">
                    <strong>✓ Estimasi Durasi Proses:</strong> {{ $recommendation->estimated_timeline['minimum_days'] ?? 'N/A' }} - {{ $recommendation->estimated_timeline['maximum_days'] ?? 'N/A' }} hari kerja (tergantung kelengkapan dokumen dan jadwal inspeksi)
                </div>
            </div>
        </div>
        
        <!-- Section IV: Rincian Biaya (jika ada) -->
        @if($formattedCosts)
        <div class="section">
            <div class="section-title">IV. ESTIMASI BIAYA INVESTASI</div>
            <div class="section-content">
                <table class="cost-table">
                    <thead>
                        <tr>
                            <th>Komponen Biaya</th>
                            <th class="text-right">Estimasi Minimum</th>
                            <th class="text-right">Estimasi Maksimum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formattedCosts['sections'] as $section)
                        <tr>
                            <td><strong>{{ $section['title'] }}</strong><br><small style="color: #666;">{{ $section['subtitle'] }}</small></td>
                            <td class="text-right">Rp {{ number_format($section['amount']['min'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($section['amount']['max'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td>TOTAL ESTIMASI INVESTASI</td>
                            <td class="text-right">Rp {{ number_format($formattedCosts['total']['min'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($formattedCosts['total']['max'], 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
                
                @if(isset($costBreakdown['factors']))
                <div class="info-box">
                    <strong>Faktor Perhitungan:</strong> 
                    Kompleksitas {{ number_format($costBreakdown['factors']['complexity'], 1) }}x • 
                    Lokasi {{ number_format($costBreakdown['factors']['location'], 1) }}x • 
                    Lingkungan {{ number_format($costBreakdown['factors']['environmental'], 1) }}x • 
                    Urgensi {{ number_format($costBreakdown['factors']['urgency'], 1) }}x
                </div>
                @endif
                
                <div class="warning-box">
                    <strong>⚠️ Catatan Penting:</strong><br>
                    @foreach($formattedCosts['notes'] as $note)
                    • {{ $note }}<br>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        
        <div class="page-break"></div>
        
        <!-- Section V: Daftar Izin Detail -->
        <div class="section">
            <div class="section-title">V. DAFTAR IZIN BERDASARKAN KATEGORI</div>
            <div class="section-content">
                @if(!empty($recommendation->recommended_permits))
                    @php
                        $permitsByCategory = collect($recommendation->recommended_permits)
                            ->groupBy(function($permit) {
                                return $permit['category'] ?? 'other';
                            });
                        
                        $categoryInfo = [
                            'foundational' => 'Izin Dasar & Legalitas',
                            'environmental' => 'Izin Lingkungan',
                            'technical' => 'Izin Teknis',
                            'operational' => 'Izin Operasional',
                            'sectoral' => 'Izin Khusus Sektoral',
                            'other' => 'Izin Lainnya'
                        ];
                    @endphp
                    
                    @foreach($permitsByCategory as $category => $permits)
                        <h3 style="color: #0a66c2; margin-top: 15px; margin-bottom: 8px; font-size: 11pt;">
                            {{ $categoryInfo[$category] ?? 'Kategori Lainnya' }} ({{ count($permits) }} izin)
                        </h3>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 30px;">No</th>
                                    <th>Nama Izin</th>
                                    <th style="width: 80px;">Prioritas</th>
                                    <th style="width: 120px;">Penerbit</th>
                                    <th style="width: 90px;">Biaya Pemerintah</th>
                                    <th style="width: 70px;">Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permits as $index => $permit)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $permit['name'] }}</strong><br>
                                        <small style="color: #666;">{{ $permit['description'] ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $type = $permit['type'] ?? 'optional';
                                            $badge = match($type) {
                                                'mandatory' => 'WAJIB',
                                                'recommended' => 'DISARANKAN',
                                                'conditional' => 'BERSYARAT',
                                                default => 'OPSIONAL'
                                            };
                                            $badgeClass = match($type) {
                                                'mandatory' => 'badge-mandatory',
                                                'recommended' => 'badge-recommended',
                                                'conditional' => 'badge-conditional',
                                                default => ''
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $badge }}</span>
                                    </td>
                                    <td><small>{{ $permit['issuing_authority'] ?? 'N/A' }}</small></td>
                                    <td class="text-right">
                                        @if(isset($permit['estimated_cost_range']))
                                            @if(($permit['estimated_cost_range']['min'] ?? 0) == 0 && ($permit['estimated_cost_range']['max'] ?? 0) == 0)
                                                <strong style="color: #28a745;">Gratis</strong>
                                            @else
                                                <small>Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}</small>
                                            @endif
                                        @else
                                            <small>N/A</small>
                                        @endif
                                    </td>
                                    <td class="text-center"><small>{{ $permit['estimated_processing_time'] ?? 'N/A' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                @else
                    <div class="warning-box">
                        <strong>⚠️ Perhatian:</strong> Belum ada rekomendasi izin yang tersedia untuk KBLI ini.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Section VI: Dokumen yang Dibutuhkan -->
        @if(!empty($recommendation->required_documents))
        <div class="section">
            <div class="section-title">VI. DOKUMEN YANG DIBUTUHKAN</div>
            <div class="section-content">
                <ul class="custom-list">
                    @foreach($recommendation->required_documents as $doc)
                        <li>
                            <strong>{{ is_array($doc) ? ($doc['name'] ?? 'Dokumen') : $doc }}</strong>
                            @if(is_array($doc) && isset($doc['notes']))
                                <br><small style="color: #666;">{{ $doc['notes'] }}</small>
                            @endif
                            @if(is_array($doc) && (isset($doc['type']) || isset($doc['format'])))
                                <br>
                                @if(isset($doc['type']))
                                    <span class="badge" style="background: #e3f2fd; color: #1976d2;">{{ ucfirst($doc['type']) }}</span>
                                @endif
                                @if(isset($doc['format']))
                                    <span class="badge" style="background: #f5f5f5; color: #333;">{{ strtoupper($doc['format']) }}</span>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        
        <!-- Section VII: Timeline -->
        @if(!empty($recommendation->estimated_timeline['critical_path']))
        <div class="section">
            <div class="section-title">VII. TIMELINE & JALUR KRITIS PROSES</div>
            <div class="section-content">
                <div class="info-box mb-10">
                    <strong>Estimasi Total:</strong> {{ $recommendation->estimated_timeline['minimum_days'] ?? 'N/A' }} - {{ $recommendation->estimated_timeline['maximum_days'] ?? 'N/A' }} hari kerja
                </div>
                
                <h4 style="color: #0a66c2; margin-bottom: 8px;">Jalur Kritis Tahapan:</h4>
                <ul class="custom-list">
                    @foreach($recommendation->estimated_timeline['critical_path'] as $index => $step)
                        <li><strong>Tahap {{ $index + 1 }}:</strong> {{ $step }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        
        <!-- Section VIII: Disclaimer & Penutup -->
        <div class="section mt-20">
            <div class="section-title">VIII. CATATAN PENTING & DISCLAIMER</div>
            <div class="section-content">
                <div class="warning-box">
                    <strong>⚠️ DISCLAIMER HUKUM:</strong>
                    <ul style="margin-left: 15px; margin-top: 5px;">
                        <li>Dokumen ini adalah analisis otomatis berdasarkan regulasi terkini dan database perizinan nasional.</li>
                        <li>Estimasi biaya dapat berubah sesuai kompleksitas aktual proyek, kebijakan daerah, dan kondisi lapangan.</li>
                        <li>Persyaratan dan prosedur dapat berbeda antar daerah sesuai Perda setempat.</li>
                        <li>BizMark tidak bertanggung jawab atas perubahan regulasi atau interpretasi yang berbeda dari instansi berwenang.</li>
                        <li>Untuk kepastian hukum dan konsultasi mendalam, hubungi tim konsultan kami.</li>
                    </ul>
                </div>
                
                <div class="success-box mt-20">
                    <strong>✓ LANGKAH SELANJUTNYA:</strong><br>
                    Untuk pendampingan penuh mulai dari penyusunan dokumen, koordinasi instansi, hingga izin terbit, silakan ajukan permohonan melalui portal klien BizMark atau hubungi konsultan kami di <strong>+62 812-3456-7890</strong>.
                </div>
            </div>
        </div>
        
        <!-- Digital Signature -->
        <div style="margin-top: 40px; text-align: right;">
            <div style="display: inline-block; text-align: center;">
                <p style="margin-bottom: 5px;">Jakarta, {{ $metadata['generated_date'] }}</p>
                <p style="font-weight: bold; margin-bottom: 50px;">PT BizMark Indonesia</p>
                
                <!-- Digital Signature Box with Logo -->
                <div style="width: 150px; height: 80px; border: 2px solid #005f4b; border-radius: 8px; display: inline-block; position: relative; background: linear-gradient(135deg, #f8fafe 0%, #e8f5f0 100%); margin-bottom: 10px;">
                    <!-- Mini Logo in Signature -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="35" height="35" style="position: absolute; top: 10px; left: 58px; opacity: 0.25;">
                        <path d="M 605.50 949.21 C589.72,950.54 520.05,951.06 511.00,949.91 C507.42,949.46 498.42,948.60 491.00,948.00 C483.58,947.39 473.45,946.27 468.50,945.51 C463.55,944.74 457.70,943.85 455.50,943.52 C437.48,940.87 415.88,936.60 407.50,934.04 C405.85,933.53 401.58,932.39 398.00,931.50 C390.29,929.58 389.53,929.35 371.50,923.48 C359.81,919.68 348.69,915.58 337.50,910.95 C336.40,910.49 328.08,906.53 319.00,902.13 C294.07,890.06 275.94,878.62 254.13,861.20 C245.54,854.33 222.88,831.56 216.62,823.50 C204.01,807.25 198.10,797.90 188.54,779.00 C182.55,767.18 181.00,763.88 181.00,762.93 C181.00,762.45 179.90,759.68 178.56,756.78 C174.87,748.79 169.02,730.95 166.99,721.50 C166.52,719.30 165.39,714.35 164.49,710.50 C159.95,691.17 158.65,676.80 158.62,645.50 C158.59,616.54 160.17,596.96 163.81,581.00 C164.37,578.53 165.36,573.80 165.99,570.50 C167.57,562.29 168.43,558.88 171.43,548.94 C172.84,544.23 174.00,539.65 174.00,538.75 C174.00,537.85 174.45,536.84 175.00,536.50 C175.55,536.16 176.00,534.63 176.00,533.11 C176.00,531.58 176.31,530.02 176.69,529.65 C177.37,528.96 178.74,525.43 181.58,517.00 C183.33,511.81 183.55,511.28 188.39,500.29 C190.37,495.77 192.01,491.72 192.02,491.29 C192.04,490.21 204.93,464.77 208.89,458.00 C210.66,454.98 212.36,451.71 212.67,450.75 C212.98,449.79 213.63,449.00 214.12,449.00 C214.60,449.00 215.00,448.55 215.00,448.01 C215.00,446.81 232.22,419.75 235.68,415.50 C237.03,413.85 240.35,409.58 243.07,406.00 C245.79,402.42 250.37,396.58 253.25,393.00 C260.07,384.54 299.61,345.01 308.00,338.27 C320.40,328.31 322.77,326.48 328.47,322.50 C331.62,320.30 334.71,318.02 335.35,317.44 C336.95,315.96 346.87,309.79 350.50,308.02 C352.15,307.21 354.67,305.61 356.10,304.47 C357.53,303.32 361.36,301.11 364.60,299.57 C367.85,298.02 372.08,295.70 374.00,294.42 C375.92,293.13 384.25,289.12 392.50,285.49 C400.75,281.87 408.40,278.50 409.50,278.01 C412.61,276.62 430.98,270.42 436.00,269.08 C438.48,268.41 441.85,267.46 443.50,266.97 C448.48,265.50 465.17,261.77 470.50,260.94 C480.30,259.41 488.69,258.09 502.50,255.90 C514.89,253.94 520.69,253.66 553.00,253.51 C587.40,253.35 592.60,253.06 597.50,251.01 C598.60,250.54 600.40,249.94 601.50,249.67 C603.52,249.17 608.80,247.37 612.50,245.92 C613.60,245.49 615.85,244.93 617.50,244.68 C619.15,244.43 623.08,243.51 626.24,242.63 C647.40,236.74 680.11,234.13 710.88,235.87 C729.06,236.90 739.56,237.98 744.50,239.33 C745.60,239.63 750.10,240.64 754.50,241.58 C761.57,243.09 764.68,243.85 775.00,246.57 C779.63,247.79 797.46,254.13 802.00,256.17 C804.47,257.28 808.08,258.87 810.00,259.70 C815.54,262.09 831.13,270.10 835.23,272.66 C837.28,273.95 839.19,275.00 839.47,275.00 C840.42,275.00 862.46,289.91 867.15,293.72 C869.71,295.80 874.21,299.44 877.15,301.82 C884.59,307.81 906.42,329.46 911.14,335.50 C913.28,338.25 917.06,342.98 919.54,346.00 C938.04,368.59 958.98,409.35 967.06,438.50 C970.85,452.18 973.34,462.36 974.04,467.00 C974.49,470.02 975.59,476.95 976.49,482.39 C977.51,488.56 977.76,492.64 977.18,493.22 C976.59,493.81 974.78,493.42 972.37,492.19 C969.05,490.49 962.85,488.30 949.50,484.09 C942.43,481.85 913.15,474.79 895.50,471.06 C880.72,467.93 867.28,465.05 860.50,463.54 C857.20,462.81 852.25,461.73 849.50,461.14 C839.89,459.09 821.47,454.92 818.00,454.01 C816.08,453.50 807.97,451.47 800.00,449.48 C786.72,446.18 779.32,444.16 767.00,440.45 C727.28,428.49 687.50,411.89 666.79,398.64 C663.66,396.64 660.81,395.00 660.45,395.00 C659.57,395.00 644.80,384.59 638.00,379.19 C621.79,366.30 596.00,337.34 596.00,332.02 C596.00,331.46 595.66,331.00 595.25,330.99 C594.84,330.99 594.01,329.98 593.42,328.74 C592.83,327.51 590.79,323.58 588.89,320.00 C585.43,313.48 581.18,302.97 579.00,295.50 C578.35,293.30 577.26,289.70 576.57,287.50 L 575.32 283.50 L 560.91 283.17 C552.98,282.99 542.00,283.34 536.50,283.95 C525.87,285.12 497.85,289.58 492.00,291.03 C490.08,291.51 483.77,293.07 478.00,294.51 C466.36,297.39 465.17,297.74 453.00,301.89 C427.24,310.65 400.46,323.77 380.01,337.65 C362.07,349.83 357.08,353.92 342.45,368.50 C331.30,379.61 326.29,385.52 313.20,403.00 C307.47,410.66 293.21,437.36 289.17,448.00 C285.00,459.00 280.25,474.74 274.99,495.00 C273.49,500.81 269.29,522.03 268.18,529.50 C267.60,533.35 266.64,539.88 266.03,544.00 C260.62,580.63 262.68,625.62 271.18,656.50 C278.91,684.55 290.99,708.65 308.04,730.00 C315.73,739.64 331.39,755.16 340.87,762.54 C344.38,765.27 348.72,768.74 350.51,770.25 C352.30,771.76 354.07,773.00 354.44,773.00 C354.81,773.00 357.90,774.91 361.30,777.24 C368.61,782.25 375.84,786.26 389.50,792.89 C403.55,799.70 410.89,803.00 412.04,803.00 C412.58,803.00 413.81,803.43 414.76,803.95 C416.89,805.10 428.05,809.32 433.50,811.03 C435.70,811.71 438.40,812.63 439.50,813.06 C443.81,814.74 458.20,819.00 459.58,819.00 C460.37,819.00 461.81,819.40 462.76,819.88 C465.51,821.28 481.49,825.04 497.00,827.95 C499.48,828.41 504.88,829.54 509.00,830.45 C516.14,832.04 521.33,832.80 539.50,834.91 C587.75,840.52 600.62,841.37 637.00,841.38 C662.82,841.38 673.82,840.98 685.00,839.62 C727.46,834.47 755.88,824.96 775.12,809.46 C791.40,796.35 799.00,777.42 796.13,757.14 C794.50,745.64 792.04,737.86 786.02,725.25 C783.32,719.61 781.38,715.00 781.70,715.00 C782.48,715.00 793.93,724.96 802.36,732.98 C810.04,740.28 820.38,752.44 824.06,758.50 C830.41,768.93 834.86,777.83 836.09,782.50 C836.52,784.15 837.79,788.88 838.92,793.00 C843.39,809.37 840.81,829.00 832.00,845.77 C819.05,870.41 794.43,891.87 758.09,910.21 C736.81,920.95 720.30,927.12 688.00,936.43 C679.10,938.99 667.07,941.31 649.04,943.92 C644.94,944.51 640.89,945.20 640.04,945.45 C637.93,946.07 621.22,947.89 605.50,949.21 Z" fill="rgba(0,80,58,1)"/>
                    </svg>
                    <span style="position: absolute; bottom: 8px; left: 0; right: 0; color: #005f4b; font-size: 8pt; font-style: italic; font-weight: 600;">Digital Signature</span>
                </div>
                
                <p style="font-weight: bold; border-top: 2px solid #333; display: inline-block; padding-top: 5px;">Sistem Analisis BizMark</p>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <!-- Mini Logo in Footer -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="14" height="14" style="vertical-align: middle; margin-right: 5px;">
                    <path d="M 605.50 949.21 C589.72,950.54 520.05,951.06 511.00,949.91 C507.42,949.46 498.42,948.60 491.00,948.00 C483.58,947.39 473.45,946.27 468.50,945.51 C463.55,944.74 457.70,943.85 455.50,943.52 C437.48,940.87 415.88,936.60 407.50,934.04 C405.85,933.53 401.58,932.39 398.00,931.50 C390.29,929.58 389.53,929.35 371.50,923.48 C359.81,919.68 348.69,915.58 337.50,910.95 C336.40,910.49 328.08,906.53 319.00,902.13 C294.07,890.06 275.94,878.62 254.13,861.20 C245.54,854.33 222.88,831.56 216.62,823.50 C204.01,807.25 198.10,797.90 188.54,779.00 C182.55,767.18 181.00,763.88 181.00,762.93 C181.00,762.45 179.90,759.68 178.56,756.78 C174.87,748.79 169.02,730.95 166.99,721.50 C166.52,719.30 165.39,714.35 164.49,710.50 C159.95,691.17 158.65,676.80 158.62,645.50 C158.59,616.54 160.17,596.96 163.81,581.00 C164.37,578.53 165.36,573.80 165.99,570.50 C167.57,562.29 168.43,558.88 171.43,548.94 C172.84,544.23 174.00,539.65 174.00,538.75 C174.00,537.85 174.45,536.84 175.00,536.50 C175.55,536.16 176.00,534.63 176.00,533.11 C176.00,531.58 176.31,530.02 176.69,529.65 C177.37,528.96 178.74,525.43 181.58,517.00 C183.33,511.81 183.55,511.28 188.39,500.29 C190.37,495.77 192.01,491.72 192.02,491.29 C192.04,490.21 204.93,464.77 208.89,458.00 C210.66,454.98 212.36,451.71 212.67,450.75 C212.98,449.79 213.63,449.00 214.12,449.00 C214.60,449.00 215.00,448.55 215.00,448.01 C215.00,446.81 232.22,419.75 235.68,415.50 C237.03,413.85 240.35,409.58 243.07,406.00 C245.79,402.42 250.37,396.58 253.25,393.00 C260.07,384.54 299.61,345.01 308.00,338.27 C320.40,328.31 322.77,326.48 328.47,322.50 C331.62,320.30 334.71,318.02 335.35,317.44 C336.95,315.96 346.87,309.79 350.50,308.02 C352.15,307.21 354.67,305.61 356.10,304.47 C357.53,303.32 361.36,301.11 364.60,299.57 C367.85,298.02 372.08,295.70 374.00,294.42 C375.92,293.13 384.25,289.12 392.50,285.49 C400.75,281.87 408.40,278.50 409.50,278.01 C412.61,276.62 430.98,270.42 436.00,269.08 C438.48,268.41 441.85,267.46 443.50,266.97 C448.48,265.50 465.17,261.77 470.50,260.94 C480.30,259.41 488.69,258.09 502.50,255.90 C514.89,253.94 520.69,253.66 553.00,253.51 C587.40,253.35 592.60,253.06 597.50,251.01 C598.60,250.54 600.40,249.94 601.50,249.67 C603.52,249.17 608.80,247.37 612.50,245.92 C613.60,245.49 615.85,244.93 617.50,244.68 C619.15,244.43 623.08,243.51 626.24,242.63 C647.40,236.74 680.11,234.13 710.88,235.87 C729.06,236.90 739.56,237.98 744.50,239.33 C745.60,239.63 750.10,240.64 754.50,241.58 C761.57,243.09 764.68,243.85 775.00,246.57 C779.63,247.79 797.46,254.13 802.00,256.17 C804.47,257.28 808.08,258.87 810.00,259.70 C815.54,262.09 831.13,270.10 835.23,272.66 C837.28,273.95 839.19,275.00 839.47,275.00 C840.42,275.00 862.46,289.91 867.15,293.72 C869.71,295.80 874.21,299.44 877.15,301.82 C884.59,307.81 906.42,329.46 911.14,335.50 C913.28,338.25 917.06,342.98 919.54,346.00 C938.04,368.59 958.98,409.35 967.06,438.50 C970.85,452.18 973.34,462.36 974.04,467.00 C974.49,470.02 975.59,476.95 976.49,482.39 C977.51,488.56 977.76,492.64 977.18,493.22 C976.59,493.81 974.78,493.42 972.37,492.19 C969.05,490.49 962.85,488.30 949.50,484.09 C942.43,481.85 913.15,474.79 895.50,471.06 C880.72,467.93 867.28,465.05 860.50,463.54 C857.20,462.81 852.25,461.73 849.50,461.14 C839.89,459.09 821.47,454.92 818.00,454.01 C816.08,453.50 807.97,451.47 800.00,449.48 C786.72,446.18 779.32,444.16 767.00,440.45 C727.28,428.49 687.50,411.89 666.79,398.64 C663.66,396.64 660.81,395.00 660.45,395.00 C659.57,395.00 644.80,384.59 638.00,379.19 C621.79,366.30 596.00,337.34 596.00,332.02 C596.00,331.46 595.66,331.00 595.25,330.99 C594.84,330.99 594.01,329.98 593.42,328.74 C592.83,327.51 590.79,323.58 588.89,320.00 C585.43,313.48 581.18,302.97 579.00,295.50 C578.35,293.30 577.26,289.70 576.57,287.50 L 575.32 283.50 L 560.91 283.17 C552.98,282.99 542.00,283.34 536.50,283.95 C525.87,285.12 497.85,289.58 492.00,291.03 C490.08,291.51 483.77,293.07 478.00,294.51 C466.36,297.39 465.17,297.74 453.00,301.89 C427.24,310.65 400.46,323.77 380.01,337.65 C362.07,349.83 357.08,353.92 342.45,368.50 C331.30,379.61 326.29,385.52 313.20,403.00 C307.47,410.66 293.21,437.36 289.17,448.00 C285.00,459.00 280.25,474.74 274.99,495.00 C273.49,500.81 269.29,522.03 268.18,529.50 C267.60,533.35 266.64,539.88 266.03,544.00 C260.62,580.63 262.68,625.62 271.18,656.50 C278.91,684.55 290.99,708.65 308.04,730.00 C315.73,739.64 331.39,755.16 340.87,762.54 C344.38,765.27 348.72,768.74 350.51,770.25 C352.30,771.76 354.07,773.00 354.44,773.00 C354.81,773.00 357.90,774.91 361.30,777.24 C368.61,782.25 375.84,786.26 389.50,792.89 C403.55,799.70 410.89,803.00 412.04,803.00 C412.58,803.00 413.81,803.43 414.76,803.95 C416.89,805.10 428.05,809.32 433.50,811.03 C435.70,811.71 438.40,812.63 439.50,813.06 C443.81,814.74 458.20,819.00 459.58,819.00 C460.37,819.00 461.81,819.40 462.76,819.88 C465.51,821.28 481.49,825.04 497.00,827.95 C499.48,828.41 504.88,829.54 509.00,830.45 C516.14,832.04 521.33,832.80 539.50,834.91 C587.75,840.52 600.62,841.37 637.00,841.38 C662.82,841.38 673.82,840.98 685.00,839.62 C727.46,834.47 755.88,824.96 775.12,809.46 C791.40,796.35 799.00,777.42 796.13,757.14 C794.50,745.64 792.04,737.86 786.02,725.25 C783.32,719.61 781.38,715.00 781.70,715.00 C782.48,715.00 793.93,724.96 802.36,732.98 C810.04,740.28 820.38,752.44 824.06,758.50 C830.41,768.93 834.86,777.83 836.09,782.50 C836.52,784.15 837.79,788.88 838.92,793.00 C843.39,809.37 840.81,829.00 832.00,845.77 C819.05,870.41 794.43,891.87 758.09,910.21 C736.81,920.95 720.30,927.12 688.00,936.43 C679.10,938.99 667.07,941.31 649.04,943.92 C644.94,944.51 640.89,945.20 640.04,945.45 C637.93,946.07 621.22,947.89 605.50,949.21 Z" fill="rgba(0,80,58,1)"/>
                </svg>
                Dokumen No: {{ $metadata['document_number'] }} | Digenerate secara otomatis oleh sistem BizMark
            </div>
            <div class="footer-right">
                Halaman <span class="pagenum"></span> | www.bizmark.id
            </div>
        </div>
    </div>
    
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 30;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
