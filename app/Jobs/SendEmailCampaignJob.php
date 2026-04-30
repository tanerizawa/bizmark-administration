<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    public $timeout = 600;

    public $backoff = [60, 300];

    private const BATCH_SIZE = 50;

    private const DELAY_MS = 200;

    public function __construct(
        public readonly int $campaignId,
    ) {
        $this->onQueue('email');
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->campaignId)];
    }

    public function handle(): void
    {
        $campaign = EmailCampaign::findOrFail($this->campaignId);

        if (! in_array($campaign->status, ['draft', 'scheduled'])) {
            Log::warning('SendEmailCampaignJob: skipped — invalid status', [
                'campaign_id' => $this->campaignId,
                'status' => $campaign->status,
            ]);

            return;
        }

        $campaign->update(['status' => 'sending', 'sent_at' => now()]);

        Log::info('SendEmailCampaignJob: started', [
            'campaign_id' => $campaign->id,
            'name' => $campaign->name,
            'total_recipients' => $campaign->total_recipients,
        ]);

        $sentCount = 0;
        $failedCount = 0;

        try {
            $recipients = $this->getRecipients($campaign);

            foreach ($recipients->chunk(self::BATCH_SIZE) as $batch) {
                foreach ($batch as $recipient) {
                    try {
                        Mail::html($campaign->content, function ($message) use ($campaign, $recipient) {
                            $message->to($recipient->email, $recipient->name ?? null)
                                ->subject($campaign->subject)
                                ->from(
                                    config('mail.from.address'),
                                    config('mail.from.name')
                                );
                        });

                        EmailLog::create([
                            'campaign_id' => $campaign->id,
                            'recipient_email' => $recipient->email,
                            'status' => 'sent',
                        ]);

                        $sentCount++;
                    } catch (\Exception $e) {
                        Log::warning('SendEmailCampaignJob: recipient failed', [
                            'email' => $recipient->email,
                            'error' => $e->getMessage(),
                        ]);

                        EmailLog::create([
                            'campaign_id' => $campaign->id,
                            'recipient_email' => $recipient->email,
                            'status' => 'failed',
                        ]);

                        $failedCount++;
                    }
                }

                usleep(self::DELAY_MS * 1000);
            }

            $campaign->update([
                'status' => 'sent',
                'sent_count' => $sentCount,
            ]);

            Log::info('SendEmailCampaignJob: completed', [
                'campaign_id' => $campaign->id,
                'sent' => $sentCount,
                'failed' => $failedCount,
            ]);
        } catch (\Exception $e) {
            $campaign->update(['status' => 'draft']);

            Log::error('SendEmailCampaignJob: failed', [
                'campaign_id' => $campaign->id,
                'sent_so_far' => $sentCount,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        EmailCampaign::where('id', $this->campaignId)
            ->where('status', 'sending')
            ->update(['status' => 'draft']);

        Log::error('SendEmailCampaignJob: permanently failed', [
            'campaign_id' => $this->campaignId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function getRecipients(EmailCampaign $campaign)
    {
        return \App\Models\EmailSubscriber::query()
            ->where('status', 'active')
            ->when($campaign->recipient_type === 'tags' && ! empty($campaign->recipient_tags), function ($q) use ($campaign) {
                foreach ($campaign->recipient_tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            })
            ->get();
    }
}
