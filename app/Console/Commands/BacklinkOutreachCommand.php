<?php

namespace App\Console\Commands;

use App\Models\BacklinkTarget;
use App\Models\BacklinkOutreach;
use App\Services\AiEmailGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class BacklinkOutreachCommand extends Command
{
    protected $signature = 'backlink:outreach
                          {--target= : Specific target ID to contact}
                          {--priority= : Filter by priority (high, medium, low)}
                          {--type=initial : Type of outreach (initial, follow_up, thank_you)}
                          {--limit=5 : Maximum number of targets to contact}
                          {--ai : Use AI to generate personalized emails}
                          {--test-email= : Send test email to this address instead}
                          {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send outreach emails to backlink targets';

    public function handle()
    {
        $this->info('🚀 Backlink Outreach Automation Started');
        $this->newLine();

        // Check for test mode
        $testEmail = $this->option('test-email');
        if ($testEmail) {
            $this->warn('🧪 TEST MODE - Sending to: ' . $testEmail);
            $this->newLine();
        }

        // Check for dry run
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No emails will be sent');
            $this->newLine();
        }

        // Build query
        $query = BacklinkTarget::query();

        // Filter by specific target
        if ($targetId = $this->option('target')) {
            $query->where('id', $targetId);
        }

        // Filter by priority
        if ($priority = $this->option('priority')) {
            $query->where('priority', $priority);
        }

        // Get targets that haven't been contacted recently
        $type = $this->option('type');
        if ($type === 'initial') {
            // Only targets never contacted
            $query->whereDoesntHave('outreaches');
        } elseif ($type === 'follow_up') {
            // Targets contacted but no response
            $query->whereHas('outreaches', function($q) {
                $q->where('status', 'sent')
                  ->whereNull('responded_at')
                  ->where('sent_at', '<=', now()->subDays(7));
            });
        }

        $targets = $query->limit($this->option('limit'))->get();

        $this->info('📧 Found ' . $targets->count() . ' target(s) to contact');
        $this->newLine();

        $sent = 0;
        $failed = 0;

        foreach ($targets as $target) {
            $this->line('Processing: ' . $target->website_name);

            try {
                // Generate email
                $emailData = $this->generateEmail($target, $type);

                // Send email
                if ($testEmail) {
                    $this->sendTestEmail($testEmail, $target, $emailData);
                    $this->info('  📧 Sending test email to: ' . $testEmail);
                } elseif ($dryRun) {
                    $this->info('  → Would send to: ' . $target->contact_email);
                    $this->line('  → Subject: ' . $emailData['subject']);
                    $this->line('  → Preview: ' . substr(strip_tags($emailData['message']), 0, 100) . '...');
                } else {
                    $this->sendEmail($target, $emailData);
                }

                $this->info('  ✓ Email sent successfully');
                $sent++;

                // Rate limiting
                if ($targets->count() > 1) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                $this->error('  ✗ Failed: ' . $e->getMessage());
                $failed++;
            }

            $this->newLine();
        }

        // Summary
        $this->info('📊 Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Targets Processed', $targets->count()],
                ['Emails Sent', $sent],
                ['Failed', $failed],
            ]
        );

        if ($dryRun) {
            $this->warn('Run without --dry-run to actually send emails');
        }

        $this->info('✅ Outreach automation completed!');

        return 0;
    }

    private function generateEmail($target, $type)
    {
        $useAI = $this->option('ai');

        if ($useAI) {
            $aiGenerator = app(AiEmailGenerator::class);
            $emailData = $aiGenerator->generatePersonalizedEmail($target, $type);
            
            $this->line('  🤖 AI Generated (' . $emailData['personalization_score'] . '% personalized)');
            
            return $emailData;
        }

        // Fallback to template
        $templates = $this->getEmailTemplates();
        $template = $templates[$type] ?? $templates['initial'];

        return [
            'subject' => str_replace('{website_name}', $target->website_name, $template['subject']),
            'message' => str_replace(
                ['{website_name}', '{category}'],
                [$target->website_name, $target->category],
                $template['message']
            ),
            'generated_by' => 'Template',
            'personalization_score' => 30,
        ];
    }

    private function sendEmail($target, $emailData)
    {
        Mail::send([], [], function ($message) use ($target, $emailData) {
            $message->to($target->contact_email)
                    ->subject($emailData['subject'])
                    ->from('cs@bizmark.id', 'Bizmark.ID')
                    ->html($emailData['message']);
        });

        // Record outreach
        BacklinkOutreach::create([
            'backlink_target_id' => $target->id,
            'subject' => $emailData['subject'],
            'message' => $emailData['message'],
            'type' => $this->option('type'),
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    private function sendTestEmail($testEmail, $target, $emailData)
    {
        // Send actual email content without test wrapper
        Mail::send([], [], function ($message) use ($testEmail, $emailData) {
            $message->to($testEmail)
                    ->subject($emailData['subject'])
                    ->from('cs@bizmark.id', 'Bizmark.ID')
                    ->html($emailData['message']);
        });
    }

    private function getEmailTemplates(): array
    {
        return [
            'initial' => [
                'subject' => 'Partnership Opportunity: {website_name} x Bizmark.ID',
                'message' => "Hi Team,<br><br>I hope this email finds you well!<br><br>My name is from Bizmark team, and I'm reaching out because I'm a big fan of {website_name}. Your content on {category} is truly valuable.<br><br>We run Bizmark.ID, a platform helping Indonesian businesses with permits, licensing, and business management.<br><br>I'd love to explore potential partnership opportunities:<br><br>1. Guest Post Exchange<br>2. Resource Link<br>3. Content Collaboration<br><br>Would you be open to discussing this further?<br><br>Best regards,<br>Bizmark.ID Team<br>cs@bizmark.id",
            ],
            'follow_up' => [
                'subject' => 'Following up: Partnership Opportunity with {website_name}',
                'message' => "Hi Team,<br><br>I wanted to follow up on my previous email about potential partnership between {website_name} and Bizmark.ID.<br><br>I understand you're busy, but I believe this could be mutually beneficial.<br><br>Would you be interested in learning more?<br><br>Best regards,<br>Bizmark.ID Team",
            ],
        ];
    }
}
