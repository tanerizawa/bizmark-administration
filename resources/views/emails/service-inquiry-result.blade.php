<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis Perizinan</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0077B5 0%, #005582 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                ✅ Hasil Analisis Perizinan
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #e7f3f8; font-size: 16px;">
                                Powered by AI - Bizmark.ID
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Halo <strong>{{ $inquiry->contact_person }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Terima kasih telah menggunakan layanan <strong>Analisis AI Perizinan Bizmark.ID</strong>! 
                                Berikut hasil analisis untuk <strong>{{ $inquiry->company_name }}</strong>:
                            </p>
                        </td>
                    </tr>

                    <!-- Summary Box -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #e7f3f8 0%, #cce7f1 100%); border-radius: 8px; padding: 20px;">
                                <tr>
                                    <td style="padding: 10px; text-align: center; border-right: 1px solid rgba(0, 119, 181, 0.2);">
                                        <div style="font-size: 12px; color: #005582; font-weight: 600; margin-bottom: 5px;">ESTIMASI BIAYA</div>
                                        <div style="font-size: 18px; font-weight: bold; color: #0077B5;">
                                            Rp {{ number_format($analysis['total_estimated_cost']['min'] ?? 0, 0, ',', '.') }} - 
                                            Rp {{ number_format($analysis['total_estimated_cost']['max'] ?? 0, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td style="padding: 10px; text-align: center; border-right: 1px solid rgba(0, 119, 181, 0.2);">
                                        <div style="font-size: 12px; color: #005582; font-weight: 600; margin-bottom: 5px;">TIMELINE</div>
                                        <div style="font-size: 18px; font-weight: bold; color: #0077B5;">
                                            {{ $analysis['total_estimated_timeline'] ?? '-' }}
                                        </div>
                                    </td>
                                    <td style="padding: 10px; text-align: center;">
                                        <div style="font-size: 12px; color: #005582; font-weight: 600; margin-bottom: 5px;">KOMPLEKSITAS</div>
                                        <div style="font-size: 18px; font-weight: bold; color: #0077B5;">
                                            {{ $analysis['complexity_score'] ?? '-' }}/10
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Recommended Permits -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #111827; font-weight: bold;">
                                🎯 Izin yang Direkomendasikan
                            </h2>
                            @foreach($analysis['recommended_permits'] ?? [] as $index => $permit)
                            <div style="margin-bottom: 15px; padding: 15px; border-left: 4px solid 
                                @if($permit['priority'] === 'critical') #EF4444
                                @elseif($permit['priority'] === 'high') #F97316
                                @else #3B82F6
                                @endif; 
                                background-color: 
                                @if($permit['priority'] === 'critical') #FEF2F2
                                @elseif($permit['priority'] === 'high') #FFF7ED
                                @else #EFF6FF
                                @endif; 
                                border-radius: 6px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <strong style="font-size: 16px; color: #111827; flex: 1;">
                                        {{ $index + 1 }}. {{ $permit['name'] }}
                                    </strong>
                                    <span style="font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 12px; 
                                        @if($permit['priority'] === 'critical') background-color: #FEE2E2; color: #991B1B;
                                        @elseif($permit['priority'] === 'high') background-color: #FFEDD5; color: #9A3412;
                                        @else background-color: #DBEAFE; color: #1E40AF;
                                        @endif">
                                        @if($permit['priority'] === 'critical') WAJIB
                                        @elseif($permit['priority'] === 'high') PENTING
                                        @else PERLU
                                        @endif
                                    </span>
                                </div>
                                <p style="margin: 0 0 8px 0; font-size: 14px; color: #4B5563; line-height: 1.5;">
                                    {{ $permit['description'] }}
                                </p>
                                <div style="font-size: 12px; color: #6B7280;">
                                    ⏱️ {{ $permit['estimated_timeline'] }} • 💰 {{ $permit['estimated_cost_range'] }}
                                </div>
                            </div>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Risk Factors -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #111827; font-weight: bold;">
                                ⚠️ Faktor Risiko
                            </h2>
                            @foreach($analysis['risk_factors'] ?? [] as $risk)
                            <div style="margin-bottom: 8px; padding-left: 20px; position: relative;">
                                <span style="position: absolute; left: 0; color: #F97316;">⚠️</span>
                                <span style="font-size: 14px; color: #374151; line-height: 1.6;">{{ $risk }}</span>
                            </div>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Next Steps -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #111827; font-weight: bold;">
                                📌 Langkah Selanjutnya
                            </h2>
                            @foreach($analysis['next_steps'] ?? [] as $index => $step)
                            <div style="margin-bottom: 10px; display: flex; align-items-start;">
                                <div style="width: 24px; height: 24px; background-color: #0077B5; color: white; border-radius: 50%; text-align: center; line-height: 24px; font-size: 12px; font-weight: bold; margin-right: 12px; flex-shrink: 0;">
                                    {{ $index + 1 }}
                                </div>
                                <span style="font-size: 14px; color: #374151; line-height: 1.6; padding-top: 2px;">{{ $step }}</span>
                            </div>
                            @endforeach
                        </td>
                    </tr>

                    <!-- Disclaimer -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; border-radius: 6px;">
                                <p style="margin: 0; font-size: 13px; color: #78350F; line-height: 1.6;">
                                    <strong>ℹ️ Catatan Penting:</strong><br>
                                    {{ $analysis['limitations'] ?? 'Analisis ini bersifat umum. Untuk analisis detail, silakan daftar ke portal kami.' }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #0077B5 0%, #005582 100%); border-radius: 8px; padding: 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 22px; color: #ffffff; font-weight: bold;">
                                            🚀 Siap Memulai?
                                        </h3>
                                        <p style="margin: 0 0 20px 0; font-size: 14px; color: #e7f3f8; line-height: 1.6;">
                                            Daftar sekarang untuk analisis lengkap, dokumen checklist detail,<br>
                                            dan pendampingan konsultan bersertifikat!
                                        </p>
                                        <a href="{{ route('client.register') }}" style="display: inline-block; padding: 15px 40px; background-color: #F2CD49; color: #111827; font-weight: bold; font-size: 16px; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                            ✨ Daftar Portal Lengkap
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- View Online -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6B7280;">
                                Lihat hasil lengkap secara online:
                            </p>
                            <a href="{{ $resultUrl }}" style="color: #0077B5; font-size: 14px; text-decoration: underline;">
                                {{ $resultUrl }}
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F9FAFB; padding: 30px 40px; text-align: center; border-top: 1px solid #E5E7EB;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6B7280;">
                                Butuh bantuan? Hubungi kami:
                            </p>
                            <p style="margin: 0 0 15px 0;">
                                <a href="https://wa.me/6283879602855" style="color: #0077B5; font-weight: 600; text-decoration: none; font-size: 14px;">
                                    📱 WhatsApp: +62 838-7960-2855
                                </a>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9CA3AF;">
                                © {{ date('Y') }} Bizmark.ID - Platform Perizinan Digital<br>
                                Email ini dikirim otomatis, mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
