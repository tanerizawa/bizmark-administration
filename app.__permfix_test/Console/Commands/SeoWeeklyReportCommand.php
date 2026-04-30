<?php

namespace App\Console\Commands;

use App\Services\SeoReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SeoWeeklyReportCommand extends Command
{
    protected $signature = 'seo:weekly-report
        {--monthly : Generate monthly report instead}
        {--email : Send report via email to admin}';

    protected $description = 'Generate weekly/monthly SEO performance report';

    public function handle(SeoReportService $service): int
    {
        $isMonthly = $this->option('monthly');
        $period = $isMonthly ? 'Monthly' : 'Weekly';

        $this->info("📋 Generating {$period} SEO Report...");
        $this->newLine();

        $report = $isMonthly
            ? $service->generateMonthlyReport()
            : $service->generateWeeklyReport();

        $m = $report->metrics;

        $this->table(['Metric', 'Value'], [
            ['Period', "{$report->period_start->format('d M')} — {$report->period_end->format('d M Y')}"],
            ['Total Published', $m['total_published']],
            ['New Articles', $m['new_articles']],
            ['Period Views', number_format($m['period_views'])],
            ['Views Growth', ($m['views_growth_pct'] >= 0 ? '+' : '').$m['views_growth_pct'].'%'],
            ['Cumulative Views', number_format($m['cumulative_views'])],
            ['Avg SEO Score', $m['avg_seo_score']],
            ['Excellent SEO (80+)', $m['excellent_seo_count']],
            ['Needs Work (<60)', $m['needs_work_count']],
            ['Sitemap URLs', $m['sitemap_urls']],
            ['Keyword Clusters', $m['keyword_clusters']],
            ['Topic Clusters', $m['topic_clusters']],
        ]);

        // Top articles
        if (! empty($report->top_articles)) {
            $this->newLine();
            $this->info('🏆 Top Articles:');
            foreach ($report->top_articles as $i => $art) {
                $this->line('   '.($i + 1).". {$art['title']} — {$art['views']} views");
            }
        }

        // Alerts
        if (! empty($report->alerts)) {
            $this->newLine();
            $this->warn('🚨 Alerts:');
            foreach ($report->alerts as $alert) {
                $icon = $alert['level'] === 'warning' ? '⚠️' : 'ℹ️';
                $this->line("   {$icon} {$alert['message']}");
            }
        }

        // Email
        if ($this->option('email')) {
            $this->sendEmailReport($report);
        }

        $this->newLine();
        $this->info("✅ {$period} Report #{$report->id} generated");

        return self::SUCCESS;
    }

    protected function sendEmailReport($report): void
    {
        try {
            $m = $report->metrics;
            $subject = "[Bizmark SEO] {$report->period} Report: {$report->period_start->format('d M')} — {$report->period_end->format('d M Y')}";

            $body = "SEO {$report->period} Report\n";
            $body .= "Period: {$report->period_start->format('d M Y')} — {$report->period_end->format('d M Y')}\n\n";
            $body .= 'Views: '.number_format($m['period_views'])." (Growth: {$m['views_growth_pct']}%)\n";
            $body .= "New Articles: {$m['new_articles']}\n";
            $body .= "Avg SEO Score: {$m['avg_seo_score']}\n";
            $body .= "Sitemap URLs: {$m['sitemap_urls']}\n\n";

            if (! empty($report->alerts)) {
                $body .= "ALERTS:\n";
                foreach ($report->alerts as $alert) {
                    $body .= "- {$alert['message']}\n";
                }
            }

            Mail::raw($body, function ($message) use ($subject) {
                $message->to('cs@bizmark.id')
                    ->subject($subject);
            });

            $report->update(['emailed' => true]);
            $this->info('   📧 Report emailed to cs@bizmark.id');
        } catch (\Exception $e) {
            Log::error('SEO report email failed', ['error' => $e->getMessage()]);
            $this->warn("   ⚠️ Email failed: {$e->getMessage()}");
        }
    }
}
