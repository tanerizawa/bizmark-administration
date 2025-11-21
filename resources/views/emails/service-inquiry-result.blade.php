<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis Perizinan</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px;">
                
                <!-- Container -->
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0077B5 0%, #005582 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                                Analisis Selesai!
                            </h1>
                            <p style="margin: 10px 0 0; color: #e7f3f8; font-size: 16px;">
                                Hasil analisis perizinan untuk {{ $inquiry->company_name }}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #374151; line-height: 1.6;">
                                Halo <strong>{{ $inquiry->contact_person }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #374151; line-height: 1.6;">
                                Terima kasih telah menggunakan Analisis AI Perizinan Bizmark.ID! 
                                Berikut hasil analisis lengkap untuk usaha Anda:
                            </p>
                            
                            <!-- Summary Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0; background-color: #e7f3f8; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="margin: 0 0 15px; font-size: 20px; color: #0077B5;">
                                            Ringkasan Analisis
                                        </h2>
                                        
                                        @php
                                            $totalCost = $analysis['total_estimated_cost'] ?? [];
                                        @endphp
                                        
                                        <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px;">
                                                    <strong>Total Estimasi Biaya:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #0077B5; font-size: 16px; font-weight: bold; text-align: right;">
                                                    @if(isset($totalCost['min']) && isset($totalCost['max']))
                                                        Rp {{ number_format($totalCost['min'] / 1000000, 0) }}-{{ number_format($totalCost['max'] / 1000000, 0) }} Jt
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px;">
                                                    <strong>Timeline Estimasi:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #0077B5; font-size: 16px; font-weight: bold; text-align: right;">
                                                    {{ $analysis['total_estimated_timeline'] ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #374151; font-size: 14px;">
                                                    <strong>Kompleksitas:</strong>
                                                </td>
                                                <td style="padding: 8px 0; color: #0077B5; font-size: 16px; font-weight: bold; text-align: right;">
                                                    {{ $analysis['complexity_score'] ?? '0' }}/10
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Recommended Permits -->
                            @php
                                $permits = $analysis['recommended_permits'] ?? [];
                            @endphp
                            
                            @if(count($permits) > 0)
                            <h2 style="margin: 30px 0 15px; font-size: 20px; color: #0077B5;">
                                Izin yang Direkomendasikan
                            </h2>
                            
                            @foreach($permits as $index => $permit)
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 15px 0; background-color: #f9fafb; border-left: 4px solid 
                                @if($permit['priority'] === 'critical') #ef4444
                                @elseif($permit['priority'] === 'high') #f59e0b
                                @else #3b82f6
                                @endif
                                ; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 15px 20px;">
                                        <div style="margin-bottom: 8px;">
                                            <span style="font-weight: bold; color: #111827; font-size: 16px;">
                                                {{ $index + 1 }}. {{ $permit['name'] }}
                                            </span>
                                            <span style="display: inline-block; padding: 2px 8px; margin-left: 8px; font-size: 11px; font-weight: bold; border-radius: 12px; color: #ffffff; background-color: 
                                                @if($permit['priority'] === 'critical') #ef4444
                                                @elseif($permit['priority'] === 'high') #f59e0b
                                                @else #3b82f6
                                                @endif
                                                ;">
                                                {{ strtoupper($permit['priority']) }}
                                            </span>
                                        </div>
                                        <p style="margin: 8px 0; color: #4b5563; font-size: 14px; line-height: 1.5;">
                                            {{ $permit['description'] }}
                                        </p>
                                        <div style="margin-top: 10px; font-size: 13px; color: #6b7280;">
                                            <strong>Timeline:</strong> {{ $permit['estimated_timeline'] }} 
                                            @if(isset($permit['total_cost_range']))
                                            · <strong>Biaya:</strong> {{ $permit['total_cost_range'] }}
                                            @elseif(isset($permit['estimated_cost_range']))
                                            · <strong>Biaya:</strong> {{ $permit['estimated_cost_range'] }}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                            @endif
                            
                            <!-- Risk Factors -->
                            @php
                                $riskFactors = $analysis['risk_factors'] ?? [];
                            @endphp
                            
                            @if(count($riskFactors) > 0)
                            <h2 style="margin: 30px 0 15px; font-size: 20px; color: #0077B5;">
                                Faktor Risiko & Perhatian
                            </h2>
                            <ul style="margin: 0; padding-left: 20px; color: #374151; font-size: 14px; line-height: 1.8;">
                                @foreach($riskFactors as $risk)
                                <li>{{ $risk }}</li>
                                @endforeach
                            </ul>
                            @endif
                            
                            <!-- Next Steps -->
                            @php
                                $nextSteps = $analysis['next_steps'] ?? [];
                            @endphp
                            
                            @if(count($nextSteps) > 0)
                            <h2 style="margin: 30px 0 15px; font-size: 20px; color: #0077B5;">
                                Langkah Selanjutnya
                            </h2>
                            <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 14px; line-height: 1.8;">
                                @foreach($nextSteps as $step)
                                <li style="margin-bottom: 8px;">{{ $step }}</li>
                                @endforeach
                            </ol>
                            @endif
                            
                            <!-- CTA Button -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 40px 0 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $resultUrl }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #0077B5 0%, #005582 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 119, 181, 0.3);">
                                            Lihat Analisis Lengkap »
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Upgrade Notice -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0; background-color: #fffbeb; border: 2px solid #fbbf24; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 10px; font-size: 18px; color: #92400e;">
                                            Upgrade ke Portal Lengkap
                                        </h3>
                                        <p style="margin: 0 0 15px; color: #78350f; font-size: 14px; line-height: 1.6;">
                                            Dapatkan fitur premium dengan mendaftar ke portal kami:
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #78350f; font-size: 14px; line-height: 1.8;">
                                            <li>Dokumen checklist detail per izin</li>
                                            <li>Timeline breakdown dengan milestone</li>
                                            <li>Pendampingan konsultan bersertifikat</li>
                                            <li>Portal monitoring real-time 24/7</li>
                                            <li>Update peraturan terbaru</li>
                                        </ul>
                                        <div style="margin-top: 15px; text-align: center;">
                                            <a href="{{ route('client.register') }}" style="display: inline-block; padding: 12px 30px; background-color: #0077B5; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: bold; border-radius: 8px;">
                                                Daftar Sekarang »
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Contact -->
                            <p style="margin: 30px 0 10px; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                Butuh bantuan lebih lanjut?
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                                <strong>Hubungi kami via WhatsApp:</strong> 
                                <a href="https://wa.me/6283879602855" style="color: #0077B5; text-decoration: none; font-weight: bold;">+62 838-7960-2855</a>
                            </p>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #001820; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 10px; color: #cce7f1; font-size: 14px; font-weight: bold;">
                                Bizmark<span style="color: #F2CD49;">.ID</span>
                            </p>
                            <p style="margin: 0 0 15px; color: #99cfe3; font-size: 12px;">
                                Platform Perizinan Digital
                            </p>
                            <p style="margin: 0; color: #99cfe3; font-size: 11px;">
                                No. Inquiry: {{ $inquiry->inquiry_number }} · {{ $inquiry->created_at->format('d M Y') }}
                            </p>
                            <div style="margin-top: 15px;">
                                <a href="{{ route('privacy.policy') }}" style="color: #99cfe3; text-decoration: none; font-size: 11px; margin: 0 8px;">Kebijakan Privasi</a>
                                <span style="color: #99cfe3;">·</span>
                                <a href="{{ route('terms.conditions') }}" style="color: #99cfe3; text-decoration: none; font-size: 11px; margin: 0 8px;">Syarat & Ketentuan</a>
                            </div>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
