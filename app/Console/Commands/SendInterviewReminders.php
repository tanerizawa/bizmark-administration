<?php

namespace App\Console\Commands;

use App\Mail\InterviewReminderMail;
use App\Models\InterviewSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interviews:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for interviews scheduled within the next 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for interviews scheduled within next 24 hours...');

        // Get interviews scheduled between now+23 hours and now+25 hours
        // This 2-hour window ensures we catch them even if command runs slightly off schedule
        $startTime = now()->addHours(23);
        $endTime = now()->addHours(25);

        $interviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
            ->where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$startTime, $endTime])
            ->whereNull('reminder_sent_at') // Don't send duplicate reminders
            ->get();

        if ($interviews->isEmpty()) {
            $this->info('No interviews found that need reminders.');
            return 0;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($interviews as $interview) {
            try {
                Mail::to($interview->jobApplication->email)
                    ->send(new InterviewReminderMail($interview));

                // Mark reminder as sent
                $interview->update(['reminder_sent_at' => now()]);

                $successCount++;
                $this->info("✓ Reminder sent to {$interview->jobApplication->full_name} ({$interview->jobApplication->email})");
            } catch (\Exception $e) {
                $failCount++;
                $this->error("✗ Failed to send reminder to {$interview->jobApplication->email}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("- Total interviews found: {$interviews->count()}");
        $this->info("- Reminders sent successfully: {$successCount}");
        if ($failCount > 0) {
            $this->warn("- Failed to send: {$failCount}");
        }

        return 0;
    }
}
