<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;
use App\Models\User;

class EmailSystemSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample subscribers
        $subscribers = [
            [
                'email' => 'subscriber1@example.com',
                'name' => 'John Doe',
                'status' => 'active',
                'source' => 'landing_page',
                'tags' => ['customer', 'active'],
            ],
            [
                'email' => 'subscriber2@example.com',
                'name' => 'Jane Smith',
                'status' => 'active',
                'source' => 'landing_page',
                'tags' => ['prospect'],
            ],
            [
                'email' => 'subscriber3@example.com',
                'name' => 'Bob Johnson',
                'status' => 'active',
                'source' => 'manual',
                'tags' => ['customer', 'vip'],
            ],
        ];

        foreach ($subscribers as $subscriber) {
            EmailSubscriber::create($subscriber);
        }

        // Create sample email templates
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Selamat Datang di Bizmark.ID!',
                'category' => 'transactional',
                'content' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <h1 style="color: #2563eb;">Selamat Datang, {{name}}!</h1>
                        <p>Terima kasih telah berlangganan newsletter Bizmark.ID.</p>
                        <p>Kami akan mengirimkan update terbaru tentang:</p>
                        <ul>
                            <li>Peraturan perizinan terbaru</li>
                            <li>Tips & trik pengurusan izin</li>
                            <li>Promosi layanan konsultasi</li>
                        </ul>
                        <p>Jika ada pertanyaan, jangan ragu untuk menghubungi kami.</p>
                        <p>Salam hangat,<br><strong>Tim Bizmark.ID</strong></p>
                    </div>
                ',
                'variables' => ['name', 'email', 'unsubscribe_url'],
                'is_active' => true,
            ],
            [
                'name' => 'Monthly Newsletter',
                'subject' => 'Newsletter Bulanan - {{month}} {{year}}',
                'category' => 'newsletter',
                'content' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background: linear-gradient(to right, #2563eb, #7c3aed); padding: 30px; text-align: center;">
                            <h1 style="color: white; margin: 0;">Bizmark.ID Newsletter</h1>
                            <p style="color: #e0e7ff; margin: 5px 0;">{{month}} {{year}}</p>
                        </div>
                        <div style="padding: 30px; background: #f8fafc;">
                            <h2 style="color: #1e293b;">Halo {{name}}!</h2>
                            <p>Berikut update penting bulan ini:</p>
                            
                            <div style="background: white; padding: 20px; border-radius: 8px; margin: 15px 0;">
                                <h3 style="color: #2563eb;">📋 Peraturan Baru</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </div>
                            
                            <div style="background: white; padding: 20px; border-radius: 8px; margin: 15px 0;">
                                <h3 style="color: #2563eb;">💡 Tips Perizinan</h3>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                            </div>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="https://bizmark.id" style="background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block;">
                                    Kunjungi Website
                                </a>
                            </div>
                        </div>
                        <div style="background: #1e293b; padding: 20px; text-align: center; color: #94a3b8; font-size: 12px;">
                            <p>© 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri</p>
                            <p><a href="{{unsubscribe_url}}" style="color: #60a5fa;">Unsubscribe</a></p>
                        </div>
                    </div>
                ',
                'variables' => ['name', 'email', 'month', 'year', 'unsubscribe_url'],
                'is_active' => true,
            ],
            [
                'name' => 'Promotion Email',
                'subject' => '🎉 Promo Spesial - Diskon Konsultasi Perizinan!',
                'category' => 'promotional',
                'content' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #fff;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;">
                            <h1 style="color: white; font-size: 32px; margin: 0;">🎉 PROMO SPESIAL!</h1>
                            <p style="color: #e0e7ff; font-size: 18px; margin: 10px 0;">Diskon hingga 30% Konsultasi Perizinan</p>
                        </div>
                        <div style="padding: 40px;">
                            <p style="font-size: 16px; color: #333;">Halo {{name}},</p>
                            <p>Kami punya kabar gembira untuk Anda!</p>
                            
                            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; margin: 20px 0;">
                                <h3 style="color: #92400e; margin: 0 0 10px 0;">Diskon 30% untuk layanan:</h3>
                                <ul style="color: #92400e; margin: 0;">
                                    <li>Konsultasi Perizinan Lingkungan</li>
                                    <li>Pengurusan IMB & PBG</li>
                                    <li>Perizinan Operasional</li>
                                </ul>
                            </div>
                            
                            <p>Promo berlaku sampai <strong>31 Desember 2025</strong></p>
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="https://wa.me/6283879602855?text=Saya%20tertarik%20dengan%20promo%20konsultasi" 
                                   style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px;">
                                    Hubungi Kami Sekarang
                                </a>
                            </div>
                            
                            <p style="color: #666; font-size: 14px; text-align: center;">Atau hubungi: <strong>+62 838 7960 2855</strong></p>
                        </div>
                        <div style="background: #f3f4f6; padding: 20px; text-align: center;">
                            <p style="color: #6b7280; font-size: 12px; margin: 0;">
                                © 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri<br>
                                <a href="{{unsubscribe_url}}" style="color: #9ca3af;">Unsubscribe</a>
                            </p>
                        </div>
                    </div>
                ',
                'variables' => ['name', 'email', 'unsubscribe_url'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }

        $this->command->info('✅ Email system seeded successfully!');
        $this->command->info('📧 Created ' . count($subscribers) . ' subscribers');
        $this->command->info('📄 Created ' . count($templates) . ' email templates');
    }
}
