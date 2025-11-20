<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Lamaran - Bizmark.ID</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #F3F4F6; line-height: 1.6;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F3F4F6; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); max-width: 600px;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0077B5 0%, #005582 100%); padding: 32px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                Bizmark.ID Career
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                                Konsultan Perizinan & Lingkungan Terpercaya
                            </p>
                        </td>
                    </tr>

                    <!-- Status Banner -->
                    <tr>
                        <td style="padding: 0;">
                            @php
                                $statusColors = [
                                    'pending' => [
                                        'bg' => '#FEF3C7',
                                        'text' => '#92400E',
                                        'icon' => '⏳'
                                    ],
                                    'reviewed' => [
                                        'bg' => '#DBEAFE',
                                        'text' => '#1E40AF',
                                        'icon' => '👁️'
                                    ],
                                    'interview' => [
                                        'bg' => '#E9D5FF',
                                        'text' => '#6B21A8',
                                        'icon' => '🤝'
                                    ],
                                    'accepted' => [
                                        'bg' => '#D1FAE5',
                                        'text' => '#065F46',
                                        'icon' => '🎉'
                                    ],
                                    'rejected' => [
                                        'bg' => '#FEE2E2',
                                        'text' => '#991B1B',
                                        'icon' => '📋'
                                    ],
                                ];

                                $statusLabels = [
                                    'pending' => 'Lamaran Sedang Diproses',
                                    'reviewed' => 'Lamaran Telah Direview',
                                    'interview' => 'Undangan Interview',
                                    'accepted' => 'Lamaran Diterima',
                                    'rejected' => 'Lamaran Tidak Dapat Dilanjutkan',
                                ];

                                $statusInfo = $statusColors[$newStatus] ?? ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => 'ℹ️'];
                            @endphp
                            
                            <div style="background-color: {{ $statusInfo['bg'] }}; padding: 24px 30px; text-align: center;">
                                <div style="font-size: 42px; margin-bottom: 8px;">{{ $statusInfo['icon'] }}</div>
                                <h2 style="margin: 0; color: {{ $statusInfo['text'] }}; font-size: 20px; font-weight: 700;">
                                    {{ $statusLabels[$newStatus] ?? 'Status Diperbarui' }}
                                </h2>
                            </div>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 32px 30px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 16px; color: #111827; font-size: 16px;">
                                Halo <strong>{{ $application->full_name }}</strong>,
                            </p>

                            <p style="margin: 0 0 24px; color: #4B5563; font-size: 15px; line-height: 1.6;">
                                Terima kasih atas minat Anda untuk bergabung dengan Bizmark.ID. Kami ingin memberitahu Anda tentang update status lamaran Anda.
                            </p>

                            <!-- Application Info Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">Posisi</div>
                                                    <div style="color: #111827; font-size: 16px; font-weight: 600;">{{ $application->jobVacancy->title ?? 'N/A' }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-top: 1px solid #E5E7EB;">
                                                    <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">Tanggal Melamar</div>
                                                    <div style="color: #111827; font-size: 14px;">{{ $application->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-top: 1px solid #E5E7EB;">
                                                    <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">Status</div>
                                                    <div style="color: {{ $statusInfo['text'] }}; font-size: 14px; font-weight: 600;">
                                                        {{ $statusInfo['icon'] }} {{ $statusLabels[$newStatus] ?? 'Status Diperbarui' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Status-specific Messages -->
                            @if($newStatus === 'pending')
                                <div style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px;">
                                        <strong>📋 Status Saat Ini:</strong><br>
                                        Lamaran Anda sedang dalam antrian review oleh tim HR kami. Kami akan menghubungi Anda segera setelah proses review selesai.
                                    </p>
                                </div>
                            @elseif($newStatus === 'reviewed')
                                <div style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px;">
                                        <strong>👁️ Lamaran Direview:</strong><br>
                                        Tim HR kami telah meninjau lamaran Anda. Kami akan menghubungi Anda dalam waktu dekat untuk tahap selanjutnya.
                                    </p>
                                </div>
                            @elseif($newStatus === 'interview')
                                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px;">
                                        <strong>🤝 Undangan Interview:</strong><br>
                                        Selamat! Lamaran Anda telah lolos ke tahap interview. Tim HR kami akan segera menghubungi Anda untuk mengatur jadwal interview. Mohon pastikan nomor telepon Anda aktif.
                                    </p>
                                </div>
                            @elseif($newStatus === 'accepted')
                                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px;">
                                        <strong>🎉 Selamat!</strong><br>
                                        Kami dengan senang hati menawarkan posisi <strong>{{ $application->jobVacancy->title ?? 'ini' }}</strong> kepada Anda. Tim HR kami akan segera menghubungi Anda untuk membahas detail lebih lanjut mengenai onboarding dan kontrak kerja.
                                    </p>
                                </div>
                            @elseif($newStatus === 'rejected')
                                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #92400E; font-size: 14px;">
                                        <strong>📋 Terima Kasih atas Minat Anda:</strong><br>
                                        Setelah review yang cermat, dengan berat hati kami informasikan bahwa saat ini kami tidak dapat melanjutkan lamaran Anda. Namun, kami menghargai waktu dan usaha Anda, dan kami mendorong Anda untuk melamar posisi lain yang sesuai di masa depan.
                                    </p>
                                </div>
                            @endif

                            <!-- Admin Notes (if provided) -->
                            @if($notes)
                                <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                    <div style="color: #6B7280; font-size: 13px; margin-bottom: 8px; font-weight: 600;">
                                        💬 Catatan dari Tim HR:
                                    </div>
                                    <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.6;">
                                        {{ $notes }}
                                    </p>
                                </div>
                            @endif

                            <!-- CTA Button -->
                            @if(in_array($newStatus, ['interview', 'accepted']))
                                <div style="text-align: center; margin: 32px 0;">
                                    <a href="{{ route('career.show', $application->jobVacancy->slug ?? '#') }}" 
                                       style="display: inline-block; background: linear-gradient(135deg, #0077B5 0%, #005582 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; box-shadow: 0 4px 6px rgba(0, 119, 181, 0.3);">
                                        🔗 Lihat Detail Lowongan
                                    </a>
                                </div>
                            @endif

                            <!-- Contact Info -->
                            <div style="background: #F9FAFB; border-radius: 8px; padding: 20px; margin-top: 24px;">
                                <p style="margin: 0 0 12px; color: #111827; font-weight: 600; font-size: 14px;">
                                    📞 Butuh Bantuan?
                                </p>
                                <p style="margin: 0 0 8px; color: #4B5563; font-size: 13px;">
                                    📧 Email: <a href="mailto:career@bizmark.id" style="color: #0077B5; text-decoration: none;">career@bizmark.id</a>
                                </p>
                                <p style="margin: 0; color: #4B5563; font-size: 13px;">
                                    📱 WhatsApp: <a href="https://wa.me/6281234567890" style="color: #25D366; text-decoration: none;">+62 812-3456-7890</a>
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #F9FAFB; padding: 24px 30px; text-align: center; border-top: 1px solid #E5E7EB;">
                            <p style="margin: 0 0 8px; color: #6B7280; font-size: 13px;">
                                Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                            </p>
                            <p style="margin: 0 0 16px; color: #6B7280; font-size: 13px;">
                                Untuk pertanyaan, silakan hubungi kami melalui kontak di atas.
                            </p>
                            <div style="border-top: 1px solid #E5E7EB; padding-top: 16px; margin-top: 16px;">
                                <p style="margin: 0 0 4px; color: #111827; font-weight: 600; font-size: 14px;">
                                    PT. Bizmark Indonesia
                                </p>
                                <p style="margin: 0; color: #6B7280; font-size: 12px;">
                                    Konsultan Perizinan & Lingkungan Terpercaya<br>
                                    © {{ date('Y') }} Bizmark.ID. All rights reserved.
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
