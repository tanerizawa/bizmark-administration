<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    public $backoff = [30, 60, 120];

    public function __construct(
        public readonly string $modelType,
        public readonly int $modelId,
        public readonly string $view,
        public readonly array $data = [],
        public readonly string $disk = 'local',
        public readonly ?string $notifyEmail = null,
    ) {
        $this->onQueue('documents');
    }

    public function handle(): void
    {
        Log::info('GeneratePdfJob: starting', [
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'view' => $this->view,
        ]);

        try {
            $html = view($this->view, $this->data)->render();

            $filename = sprintf(
                '%s-%d-%s.pdf',
                str_replace(['\\', '/'], '-', $this->modelType),
                $this->modelId,
                now()->format('Ymd_His')
            );

            $path = 'pdfs/'.$filename;

            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml($html);
                Storage::disk($this->disk)->put($path, $pdf->output());
            } else {
                Storage::disk($this->disk)->put($path, $html);
                Log::warning('GeneratePdfJob: DomPDF not installed, stored HTML fallback', ['path' => $path]);
            }

            Log::info('GeneratePdfJob: completed', ['path' => $path]);

            if ($this->notifyEmail) {
                \Mail::raw("Your PDF report is ready: {$path}", function ($message) {
                    $message->to($this->notifyEmail)
                        ->subject('PDF Report Ready');
                });
            }
        } catch (\Exception $e) {
            Log::error('GeneratePdfJob: failed', [
                'model_type' => $this->modelType,
                'model_id' => $this->modelId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePdfJob: permanently failed', [
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'error' => $exception->getMessage(),
        ]);
    }
}
