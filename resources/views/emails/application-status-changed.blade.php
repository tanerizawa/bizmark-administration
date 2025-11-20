<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Aplikasi</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header with LinkedIn Blue Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0077B5 0%, #005582 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                Bizmark.ID
                            </h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                                Konsultan Perizinan Usaha Terpercaya
                            </p>
                        </td>
                    </tr>

                    <!-- Status Change Banner -->
                    <tr>
                        <td style="padding: 0;">
                            @php
                                $statusColors = [
                                    'under_review' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'icon' => '🔍'],
                                    'document_incomplete' => ['bg' => '#FED7AA', 'text' => '#9A3412', 'icon' => '📄'],
                                    'quoted' => ['bg' => '#E9D5FF', 'text' => '#6B21A8', 'icon' => '💰'],
                                    'quotation_accepted' => ['bg' => '#D1FAE5', 'text' => '#065F46', 'icon' => '✅'],
                                    'payment_pending' => ['bg' => '#FEF3C7', 'text' => '#92400E', 'icon' => '⏳'],
                                    'payment_verified' => ['bg' => '#CCFBF1', 'text' => '#115E59', 'icon' => '✓'],
                                    'in_progress' => ['bg' => '#DBEAFE', 'text' => '#1E40AF', 'icon' => '🚀'],
                                    'completed' => ['bg' => '#D1FAE5', 'text' => '#065F46', 'icon' => '🎉'],
                                    'cancelled' => ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => '❌'],
                                ];
                                
                                $statusInfo = $statusColors[$newStatus] ?? ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => 'ℹ️'];
                                
                                $statusLabels = [
                                    'under_review' => 'Aplikasi Sedang Ditinjau',
                                    'document_incomplete' => 'Dokumen Perlu Dilengkapi',
                                    'quoted' => 'Penawaran Harga Tersedia',
                                    'quotation_accepted' => 'Penawaran Diterima',
                                    'payment_pending' => 'Menunggu Pembayaran',
                                    'payment_verified' => 'Pembayaran Terverifikasi',
                                    'in_progress' => 'Proses Perizinan Dimulai',
                                    'completed' => 'Izin Selesai Diproses',
                                    'cancelled' => 'Aplikasi Dibatalkan',
                                ];
                            @endphp
                            <div style="background-color: {{ $statusInfo['bg'] }}; padding: 24px 30px; text-align: center; border-bottom: 3px solid {{ $statusInfo['text'] }};">
                                <div style="font-size: 42px; margin-bottom: 8px;">{{ $statusInfo['icon'] }}</div>
                                <h2 style="margin: 0; color: {{ $statusInfo['text'] }}; font-size: 22px; font-weight: 700;">
                                    {{ $statusLabels[$newStatus] ?? 'Status Diperbarui' }}
                                </h2>
                            </div>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 24px 0; color: #111827; font-size: 16px; line-height: 1.6;">
                                Kepada Yth. <strong>{{ $application->client->name ?? 'Client' }}</strong>,
                            </p>

                            <p style="margin: 0 0 24px 0; color: #4B5563; font-size: 15px; line-height: 1.7;">
                                Status aplikasi perizinan Anda telah diperbarui. Berikut adalah detail terbaru:
                            </p>

                            <!-- Application Info Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="8" cellspacing="0">
                                            <tr>
                                                <td style="color: #6B7280; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    Nomor Aplikasi
                                                </td>
                                                <td style="color: #111827; font-size: 15px; font-weight: 700; text-align: right;">
                                                    {{ $application->application_number }}
                                                </td>
                                            </tr>
                                            @if($application->permitType)
                                            <tr>
                                                <td style="color: #6B7280; font-size: 13px; padding-top: 12px;">
                                                    Jenis Izin
                                                </td>
                                                <td style="color: #374151; font-size: 14px; text-align: right; padding-top: 12px;">
                                                    {{ $application->permitType->name }}
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="color: #6B7280; font-size: 13px; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                                                    Status Sebelumnya
                                                </td>
                                                <td style="color: #6B7280; font-size: 14px; text-align: right; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                                                    {{ ucwords(str_replace('_', ' ', $previousStatus)) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6B7280; font-size: 13px; font-weight: 600; padding-top: 8px;">
                                                    Status Saat Ini
                                                </td>
                                                <td style="color: #0077B5; font-size: 16px; font-weight: 700; text-align: right; padding-top: 8px;">
                                                    {{ $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus)) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #6B7280; font-size: 13px; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                                                    Tanggal Update
                                                </td>
                                                <td style="color: #374151; font-size: 14px; text-align: right; padding-top: 12px; border-top: 1px solid #E5E7EB;">
                                                    {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Status-specific Messages -->
                            @if($newStatus === 'under_review')
                                <div style="background-color: #EFF6FF; border-left: 4px solid #3B82F6; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px; line-height: 1.6;">
                                        <strong>📋 Langkah Selanjutnya:</strong><br>
                                        Tim kami sedang meninjau dokumen dan detail aplikasi Anda. Kami akan segera menginformasikan jika ada dokumen tambahan yang diperlukan atau memproses quotation.
                                    </p>
                                </div>
                            @elseif($newStatus === 'document_incomplete')
                                <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #92400E; font-size: 14px; line-height: 1.6;">
                                        <strong>⚠️ Perlu Tindakan:</strong><br>
                                        Dokumen Anda memerlukan perbaikan atau pelengkapan. Silakan login ke portal client untuk melihat detail dan upload dokumen yang diperlukan.
                                    </p>
                                </div>
                            @elseif($newStatus === 'quoted')
                                <div style="background-color: #F5F3FF; border-left: 4px solid #8B5CF6; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #6B21A8; font-size: 14px; line-height: 1.6;">
                                        <strong>💰 Penawaran Tersedia:</strong><br>
                                        Quotation untuk aplikasi Anda sudah tersedia. Silakan review dan konfirmasi untuk melanjutkan proses perizinan.
                                    </p>
                                </div>
                            @elseif($newStatus === 'payment_pending')
                                <div style="background-color: #FEF3C7; border-left: 4px solid #F59E0B; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #92400E; font-size: 14px; line-height: 1.6;">
                                        <strong>💳 Menunggu Pembayaran:</strong><br>
                                        Silakan lakukan pembayaran sesuai detail yang tertera di portal. Setelah pembayaran dikonfirmasi, kami akan segera memproses aplikasi Anda.
                                    </p>
                                </div>
                            @elseif($newStatus === 'payment_verified')
                                <div style="background-color: #D1FAE5; border-left: 4px solid #10B981; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px; line-height: 1.6;">
                                        <strong>✅ Pembayaran Diterima:</strong><br>
                                        Terima kasih! Pembayaran Anda telah terverifikasi. Tim kami akan segera memulai proses perizinan.
                                    </p>
                                </div>
                            @elseif($newStatus === 'in_progress')
                                <div style="background-color: #DBEAFE; border-left: 4px solid #0077B5; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #1E40AF; font-size: 14px; line-height: 1.6;">
                                        <strong>🚀 Proses Dimulai:</strong><br>
                                        Aplikasi perizinan Anda sedang diproses. Kami akan memberikan update berkala melalui portal client.
                                    </p>
                                </div>
                            @elseif($newStatus === 'completed')
                                <div style="background-color: #D1FAE5; border-left: 4px solid #10B981; padding: 16px 20px; border-radius: 6px; margin-bottom: 24px;">
                                    <p style="margin: 0; color: #065F46; font-size: 14px; line-height: 1.6;">
                                        <strong>🎉 Selamat!</strong><br>
                                        Izin Anda telah selesai diproses. Dokumen izin dapat diunduh melalui portal client.
                                    </p>
                                </div>
                            @endif

                            <!-- Admin Notes (if any) -->
                            @if($notes)
                                <div style="background-color: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
                                    <p style="margin: 0 0 8px 0; color: #6B7280; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                        💬 Catatan dari Tim
                                    </p>
                                    <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.7;">
                                        {{ $notes }}
                                    </p>
                                </div>
                            @endif

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('client.applications.show', $application->id) }}" 
                                           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #0077B5, #005582); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 600; box-shadow: 0 4px 6px rgba(0, 119, 181, 0.3);">
                                            🔗 Lihat Detail Aplikasi
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Closing -->
                            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #E5E7EB;">
                                <p style="margin: 0 0 12px 0; color: #374151; font-size: 14px; line-height: 1.6;">
                                    Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami melalui:
                                </p>
                                <table cellpadding="0" cellspacing="0" style="margin: 0;">
                                    <tr>
                                        <td style="padding: 4px 0;">
                                            <span style="color: #6B7280; font-size: 14px;">📧 Email:</span>
                                            <a href="mailto:info@bizmark.id" style="color: #0077B5; text-decoration: none; font-weight: 600; margin-left: 8px;">
                                                info@bizmark.id
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 4px 0;">
                                            <span style="color: #6B7280; font-size: 14px;">📱 WhatsApp:</span>
                                            <a href="https://wa.me/6281234567890" style="color: #0077B5; text-decoration: none; font-weight: 600; margin-left: 8px;">
                                                +62 812-3456-7890
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin: 24px 0 0 0; color: #4B5563; font-size: 14px; line-height: 1.6;">
                                Hormat kami,<br>
                                <strong style="color: #0077B5;">Tim Bizmark.ID</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F9FAFB; padding: 24px 30px; border-top: 1px solid #E5E7EB;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 8px 0; color: #6B7280; font-size: 12px;">
                                            © {{ date('Y') }} Bizmark.ID - Konsultan Perizinan Usaha Terpercaya
                                        </p>
                                        <p style="margin: 0; color: #9CA3AF; font-size: 11px;">
                                            Email ini dikirim secara otomatis, mohon tidak membalas email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
