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
                            <img src="https://bizmark.id/images/logo-white.png" alt="Bizmark.ID" style="height: 40px; margin-bottom: 12px;">
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
                                        'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="#92400E"/></svg>'
                                    ],
                                    'reviewed' => [
                                        'bg' => '#DBEAFE',
                                        'text' => '#1E40AF',
                                        'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#1E40AF"/></svg>'
                                    ],
                                    'interview' => [
                                        'bg' => '#E9D5FF',
                                        'text' => '#6B21A8',
                                        'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill="#6B21A8"/></svg>'
                                    ],
                                    'accepted' => [
                                        'bg' => '#D1FAE5',
                                        'text' => '#065F46',
                                        'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="#065F46"/></svg>'
                                    ],
                                    'rejected' => [
                                        'bg' => '#FEE2E2',
                                        'text' => '#991B1B',
                                        'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="#991B1B"/></svg>'
                                    ],
                                ];

                                $statusLabels = [
                                    'pending' => 'Lamaran Sedang Diproses',
                                    'reviewed' => 'Lamaran Telah Direview',
                                    'interview' => 'Undangan Interview',
                                    'accepted' => 'Lamaran Diterima',
                                    'rejected' => 'Lamaran Tidak Dapat Dilanjutkan',
                                ];

                                $statusInfo = $statusColors[$newStatus] ?? ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#374151"/></svg>'];
                            @endphp
                            
                            <div style="background-color: {{ $statusInfo['bg'] }}; padding: 24px 30px; text-align: center;">
                                <div style="margin-bottom: 12px;">{!! $statusInfo['icon'] !!}</div>
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
                                                        {{ $statusLabels[$newStatus] ?? 'Status Diperbarui' }}
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
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px; line-height: 1.6;">
                                        <strong style="display: block; margin-bottom: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" fill="#1E40AF"/></svg>
                                            Status Saat Ini:
                                        </strong>
                                        Lamaran Anda sedang dalam antrian review oleh tim HR kami. Kami akan menghubungi Anda segera setelah proses review selesai.
                                    </p>
                                </div>
                            @elseif($newStatus === 'reviewed')
                                <div style="background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px; line-height: 1.6;">
                                        <strong style="display: block; margin-bottom: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z" fill="#1E40AF"/></svg>
                                            Lamaran Direview:
                                        </strong>
                                        Tim HR kami telah meninjau lamaran Anda. Kami akan menghubungi Anda dalam waktu dekat untuk tahap selanjutnya.
                                    </p>
                                </div>
                            @elseif($newStatus === 'interview')
                                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px; line-height: 1.6;">
                                        <strong style="display: block; margin-bottom: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3z" fill="#065F46"/></svg>
                                            Undangan Interview:
                                        </strong>
                                        Selamat! Lamaran Anda telah lolos ke tahap interview. Tim HR kami akan segera menghubungi Anda untuk mengatur jadwal interview. Mohon pastikan nomor telepon Anda aktif.
                                    </p>
                                </div>
                            @elseif($newStatus === 'accepted')
                                <div style="background: #F0FDF4; border-left: 4px solid #10B981; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px; line-height: 1.6;">
                                        <strong style="display: block; margin-bottom: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" fill="#065F46"/></svg>
                                            Selamat!
                                        </strong>
                                        Kami dengan senang hati menawarkan posisi <strong>{{ $application->jobVacancy->title ?? 'ini' }}</strong> kepada Anda. Tim HR kami akan segera menghubungi Anda untuk membahas detail lebih lanjut mengenai onboarding dan kontrak kerja.
                                    </p>
                                </div>
                            @elseif($newStatus === 'rejected')
                                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #92400E; font-size: 14px; line-height: 1.6;">
                                        <strong style="display: block; margin-bottom: 8px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" fill="#92400E"/></svg>
                                            Terima Kasih atas Minat Anda:
                                        </strong>
                                        Setelah review yang cermat, dengan berat hati kami informasikan bahwa saat ini kami tidak dapat melanjutkan lamaran Anda. Namun, kami menghargai waktu dan usaha Anda, dan kami mendorong Anda untuk melamar posisi lain yang sesuai di masa depan.
                                    </p>
                                </div>
                            @endif

                            <!-- Admin Notes (if provided) -->
                            @if($notes)
                                <div style="background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                                    <div style="color: #6B7280; font-size: 13px; margin-bottom: 8px; font-weight: 600;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" fill="#6B7280"/></svg>
                                        Catatan dari Tim HR:
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
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 8px;"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z" fill="white"/></svg>
                                        Lihat Detail Lowongan
                                    </a>
                                </div>
                            @endif

                            <!-- Contact Info -->
                            <div style="background: #F9FAFB; border-radius: 8px; padding: 20px; margin-top: 24px;">
                                <p style="margin: 0 0 12px; color: #111827; font-weight: 600; font-size: 14px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 8px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57V12c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57V12c0-5.52-4.48-10-10-10zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z" fill="#111827"/></svg>
                                    Butuh Bantuan?
                                </p>
                                <p style="margin: 0 0 8px; color: #4B5563; font-size: 13px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="#4B5563"/></svg>
                                    Email: <a href="mailto:cs@bizmark.id" style="color: #0077B5; text-decoration: none;">cs@bizmark.id</a>
                                </p>
                                <p style="margin: 0; color: #4B5563; font-size: 13px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path d="M16.5 3C13.6 3 11.2 5.3 11 8.2c0 .1-.1.2-.1.3h1.9c.1-1.5 1.4-2.8 3-2.8 1.7 0 3 1.3 3 3s-1.3 3-3 3c-.5 0-1-.1-1.4-.3l-.9 1.7c.7.3 1.5.5 2.3.5 2.8 0 5-2.2 5-5s-2.2-5-5-5zm-1 9h-2v2h2v-2zm-5.4 4.5c-.3-.1-.5-.3-.7-.5l-1.4 1.4c.4.5.9.8 1.5 1.1l.6-2zm-3.1-3.7c-.2-.3-.3-.5-.3-.8h-2c0 .6.2 1.2.5 1.7l1.8-1zm-.5-2.8h2c0-.3.1-.5.3-.8l-1.8-1c-.3.5-.5 1.1-.5 1.8zm2 4.5c.2.3.5.5.7.7l1.4-1.4c-.2-.2-.4-.4-.5-.7l-1.6.4z" fill="#25D366"/></svg>
                                    WhatsApp: <a href="https://wa.me/628387960285" style="color: #25D366; text-decoration: none;">+62 838-7960-285</a>
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
