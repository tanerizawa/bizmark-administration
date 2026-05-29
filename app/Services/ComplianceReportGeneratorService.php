<?php

namespace App\Services;

use App\Models\ComplianceReport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * P10 — Generates compliance report PDF using AI content fill + DomPDF.
 */
class ComplianceReportGeneratorService
{
    public function __construct(private OpenRouterService $ai) {}

    /**
     * Generate PDF for a ComplianceReport, store in storage/app/compliance-reports/.
     * Returns the storage path.
     */
    public function generate(ComplianceReport $report): string
    {
        $template = $report->template;
        $project = $report->project;
        $inputData = $report->input_data;

        // Step 1: AI fills in the template with the client's data
        $filledContent = $this->fillTemplateWithAi($template->template_content, $inputData, $project);

        // Step 2: Wrap in HTML for PDF rendering
        $html = $this->buildHtml($filledContent, $report, $template);

        // Step 3: Generate PDF via DomPDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $filename = 'compliance-reports/'.Str::slug($template->name).'-'.$report->id.'-'.now()->format('Ymd').'.pdf';
        Storage::put($filename, $pdf->output());

        return $filename;
    }

    private function fillTemplateWithAi(string $templateContent, array $inputData, $project): string
    {
        $dataJson = json_encode($inputData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $projName = $project->name ?? 'Proyek';

        $result = $this->ai->chat([
            ['role' => 'system', 'content' => 'Anda adalah ahli lingkungan yang mengisi laporan UKL-UPL/perizinan lingkungan Indonesia. Gunakan bahasa Indonesia formal dan teknis. Isi template dengan data yang diberikan secara akurat dan lengkap.'],
            ['role' => 'user', 'content' => "Proyek: $projName\n\nData Input:\n$dataJson\n\nTemplate:\n$templateContent\n\nIsi template di atas dengan data yang diberikan. Ganti semua placeholder [XXX] atau {field} dengan nilai yang sesuai. Jika data tidak tersedia, tulis 'Data tidak tersedia'. Kembalikan HANYA konten HTML yang sudah diisi, tanpa markdown."],
        ]);

        return $result['success'] ? $result['content'] : $templateContent;
    }

    private function buildHtml(string $content, ComplianceReport $report, $template): string
    {
        $title = $template->name;
        $period = $report->period_start->format('d M Y').' – '.$report->period_end->format('d M Y');
        $project = $report->project->name ?? '';
        $genDate = now()->format('d M Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 0; padding: 0; color: #1a1a1a; }
  .cover { text-align: center; padding: 3cm 2cm; }
  .cover h1 { font-size: 18pt; font-weight: bold; text-transform: uppercase; border-bottom: 3px solid #000; padding-bottom: 1rem; }
  .cover .meta { margin-top: 2rem; font-size: 13pt; line-height: 2; }
  .content { padding: 2cm; }
  h2 { font-size: 14pt; border-bottom: 1px solid #333; padding-bottom: .25rem; margin-top: 1.5rem; }
  h3 { font-size: 13pt; margin-top: 1rem; }
  table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 11pt; }
  th, td { border: 1px solid #666; padding: .4rem .6rem; vertical-align: top; }
  th { background: #e8e8e8; font-weight: bold; }
  .footer { text-align: right; font-size: 10pt; color: #666; border-top: 1px solid #ccc; padding-top: .5rem; margin-top: 2rem; }
  p { line-height: 1.6; text-align: justify; }
</style>
</head>
<body>
<div class="cover">
  <h1>$title</h1>
  <div class="meta">
    <strong>Proyek:</strong> $project<br>
    <strong>Periode:</strong> $period<br>
    <strong>Tanggal Dibuat:</strong> $genDate
  </div>
</div>
<div class="content">
  $content
  <div class="footer">Dibuat oleh sistem Bizmark.id · $genDate</div>
</div>
</body>
</html>
HTML;
    }
}
