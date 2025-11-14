<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class EmailSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = $this->getCurrentSettings();
        return view('admin.email.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mailgun_domain' => 'nullable|string',
            'mailgun_secret' => 'nullable|string',
            'mailgun_endpoint' => 'nullable|string',
        ]);

        try {
            $envUpdates = [
                'MAIL_MAILER' => $request->mail_mailer,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => $request->mail_from_name,
            ];

            // Add Mailgun-specific settings
            if ($request->mail_mailer === 'mailgun') {
                $envUpdates['MAILGUN_DOMAIN'] = $request->mailgun_domain ?? 'mg.bizmark.id';
                $envUpdates['MAILGUN_SECRET'] = $request->mailgun_secret ?? '';
                $envUpdates['MAILGUN_ENDPOINT'] = $request->mailgun_endpoint ?? 'api.eu.mailgun.net';
            }
            
            // Add SMTP-specific settings
            if ($request->mail_mailer === 'smtp') {
                $envUpdates['MAIL_HOST'] = $request->mail_host ?? '127.0.0.1';
                $envUpdates['MAIL_PORT'] = $request->mail_port ?? '587';
                $envUpdates['MAIL_USERNAME'] = $request->mail_username;
                $envUpdates['MAIL_PASSWORD'] = $request->mail_password;
                $envUpdates['MAIL_ENCRYPTION'] = $request->mail_encryption ?? 'tls';
            }

            $this->updateEnvFile($envUpdates);

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('admin.email.settings.index')
                ->with('success', 'Email settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            \Mail::raw('This is a test email from Bizmark.id Email System.', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email from Bizmark.id');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCurrentSettings()
    {
        return [
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];
    }

    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            
            // Handle values with spaces or special characters
            if (str_contains($value, ' ') || str_contains($value, '#')) {
                $value = '"' . $value . '"';
            }

            // Check if key exists
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }
}
