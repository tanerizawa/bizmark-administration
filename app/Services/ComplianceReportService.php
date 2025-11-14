<?php

namespace App\Services;

use App\Models\ComplianceCheck;
use App\Models\DocumentDraft;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\SimpleType\Jc;

class ComplianceReportService
{
    protected PhpWord $phpWord;
    protected $section;

    public function generateReport(DocumentDraft $draft): string
    {
        $this->phpWord = new PhpWord();
        $this->defineStyles();

        // Get latest compliance check
        $check = $draft->complianceChecks()->latest()->firstOrFail();
        
        // Add section
        $this->section = $this->phpWord->addSection([
            'marginTop' => 1134, // 2cm
            'marginBottom' => 1134,
            'marginLeft' => 1701, // 3cm
            'marginRight' => 1134,
        ]);

        // Build report content
        $this->addCoverPage($draft, $check);
        $this->section->addPageBreak();
        
        $this->addExecutiveSummary($draft, $check);
        $this->section->addPageBreak();
        
        $this->addDetailedScores($check);
        $this->section->addPageBreak();
        
        $this->addIssuesByCategory($check);
        $this->section->addPageBreak();
        
        $this->addRecommendations($check);

        // Save to temp file
        $tempFile = storage_path('app/temp/compliance_report_' . $draft->id . '_' . time() . '.docx');
        
        if (!file_exists(dirname($tempFile))) {
            mkdir(dirname($tempFile), 0755, true);
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return $tempFile;
    }

    protected function defineStyles(): void
    {
        // Normal text
        $this->phpWord->addFontStyle('normalText', [
            'name' => 'Times New Roman',
            'size' => 12,
        ]);

        // Title
        $this->phpWord->addFontStyle('title', [
            'name' => 'Times New Roman',
            'size' => 18,
            'bold' => true,
            'color' => '1F1F1F',
        ]);

        // Heading 1
        $this->phpWord->addFontStyle('heading1', [
            'name' => 'Times New Roman',
            'size' => 16,
            'bold' => true,
            'color' => '1F1F1F',
        ]);

        // Heading 2
        $this->phpWord->addFontStyle('heading2', [
            'name' => 'Times New Roman',
            'size' => 14,
            'bold' => true,
            'color' => '1F1F1F',
        ]);

        // Heading 3
        $this->phpWord->addFontStyle('heading3', [
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => true,
            'color' => '1F1F1F',
        ]);

        // Paragraph
        $this->phpWord->addParagraphStyle('normalPara', [
            'alignment' => Jc::BOTH,
            'spaceBefore' => 0,
            'spaceAfter' => 200,
            'lineHeight' => 1.5,
        ]);

        $this->phpWord->addParagraphStyle('centered', [
            'alignment' => Jc::CENTER,
            'spaceBefore' => 0,
            'spaceAfter' => 200,
        ]);
    }

    protected function addCoverPage(DocumentDraft $draft, ComplianceCheck $check): void
    {
        // Title
        $this->section->addText(
            'COMPLIANCE CHECK REPORT',
            'title',
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400]
        );

        $this->section->addText(
            'UKL-UPL Document Validation',
            ['size' => 14, 'color' => '666666'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 800]
        );

        // Add some spacing
        $this->section->addTextBreak(3);

        // Score circle (text representation)
        $scoreColor = $check->overall_score >= 80 ? '34C759' : ($check->overall_score >= 70 ? 'FF9500' : 'FF3B30');
        
        $this->section->addText(
            'Overall Compliance Score',
            ['size' => 12, 'color' => '666666'],
            ['alignment' => Jc::CENTER]
        );
        
        $this->section->addText(
            round($check->overall_score, 1) . '/100',
            ['size' => 36, 'bold' => true, 'color' => $scoreColor],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400]
        );

        $this->section->addText(
            $check->status_label,
            ['size' => 14, 'bold' => true, 'color' => $scoreColor],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 800]
        );

        // Document info
        $this->section->addTextBreak(2);
        
        $table = $this->section->addTable([
            'borderSize' => 6,
            'borderColor' => 'CCCCCC',
            'width' => 100 * 50,
            'unit' => 'pct',
        ]);

        $infoData = [
            ['Document Title', $draft->title],
            ['Project', $draft->project->name ?? 'N/A'],
            ['Created By', $draft->creator->name ?? 'N/A'],
            ['Created At', $draft->created_at->format('d F Y H:i')],
            ['Checked At', $check->checked_at->format('d F Y H:i')],
            ['Total Issues', $check->total_issues],
            ['Critical Issues', $check->critical_issues],
            ['Warning Issues', $check->warning_issues],
            ['Info Issues', $check->info_issues],
        ];

        foreach ($infoData as $row) {
            $table->addRow();
            $table->addCell(4000)->addText($row[0], ['bold' => true], 'normalPara');
            $table->addCell(6000)->addText($row[1], 'normalText', 'normalPara');
        }
    }

    protected function addExecutiveSummary(DocumentDraft $draft, ComplianceCheck $check): void
    {
        $this->section->addText('EXECUTIVE SUMMARY', 'heading1');
        $this->section->addTextBreak(1);

        // Summary text
        $complianceService = new UKLUPLComplianceService();
        $summary = $complianceService->getComplianceSummary($check);
        
        $this->section->addText($summary, 'normalText', 'normalPara');
        $this->section->addTextBreak(1);

        // Key findings
        $this->section->addText('Key Findings:', 'heading3');
        $this->section->addTextBreak(1);

        if ($check->critical_issues > 0) {
            $this->section->addText(
                '❌ ' . $check->critical_issues . ' Critical Issues Found',
                ['size' => 12, 'color' => 'FF3B30', 'bold' => true],
                'normalPara'
            );
            $this->section->addText(
                'These issues must be addressed before document approval.',
                ['size' => 11, 'color' => '666666'],
                'normalPara'
            );
            $this->section->addTextBreak(1);
        }

        if ($check->warning_issues > 0) {
            $this->section->addText(
                '⚠️ ' . $check->warning_issues . ' Warnings Found',
                ['size' => 12, 'color' => 'FF9500', 'bold' => true],
                'normalPara'
            );
            $this->section->addText(
                'These issues should be reviewed for document quality improvement.',
                ['size' => 11, 'color' => '666666'],
                'normalPara'
            );
            $this->section->addTextBreak(1);
        }

        if ($check->info_issues > 0) {
            $this->section->addText(
                'ℹ️ ' . $check->info_issues . ' Info Items',
                ['size' => 12, 'color' => '007AFF', 'bold' => true],
                'normalPara'
            );
            $this->section->addText(
                'These are suggestions for document enhancement.',
                ['size' => 11, 'color' => '666666'],
                'normalPara'
            );
        }
    }

    protected function addDetailedScores(ComplianceCheck $check): void
    {
        $this->section->addText('DETAILED SCORES', 'heading1');
        $this->section->addTextBreak(1);

        $scores = [
            'Structure' => $check->structure_score,
            'Compliance' => $check->compliance_score,
            'Formatting' => $check->formatting_score,
            'Completeness' => $check->completeness_score,
        ];

        foreach ($scores as $category => $score) {
            $color = $score >= 80 ? '34C759' : ($score >= 70 ? 'FF9500' : 'FF3B30');
            
            $this->section->addText($category, 'heading3');
            $this->section->addText(
                round($score, 1) . '/100',
                ['size' => 18, 'bold' => true, 'color' => $color],
                'normalPara'
            );
            
            // Progress bar simulation
            $percentage = round($score);
            $filled = str_repeat('█', intval($percentage / 5));
            $empty = str_repeat('░', 20 - intval($percentage / 5));
            
            $this->section->addText(
                $filled . $empty . ' ' . $percentage . '%',
                ['name' => 'Consolas', 'size' => 10, 'color' => $color],
                'normalPara'
            );
            
            $this->section->addTextBreak(1);
        }
    }

    protected function addIssuesByCategory(ComplianceCheck $check): void
    {
        $this->section->addText('ISSUES DETAIL', 'heading1');
        $this->section->addTextBreak(1);

        if (empty($check->issues)) {
            $this->section->addText(
                '✅ No issues found! Document is in excellent condition.',
                ['size' => 12, 'color' => '34C759', 'bold' => true],
                'normalPara'
            );
            return;
        }

        $issuesByCategory = [];
        foreach ($check->issues as $issue) {
            $category = $issue['category'] ?? 'general';
            if (!isset($issuesByCategory[$category])) {
                $issuesByCategory[$category] = [];
            }
            $issuesByCategory[$category][] = $issue;
        }

        foreach ($issuesByCategory as $category => $issues) {
            $this->section->addText(
                strtoupper($category),
                'heading2'
            );
            $this->section->addTextBreak(1);

            foreach ($issues as $index => $issue) {
                $severityIcon = [
                    'critical' => '❌',
                    'warning' => '⚠️',
                    'info' => 'ℹ️',
                ][$issue['severity']] ?? '•';

                $severityColor = [
                    'critical' => 'FF3B30',
                    'warning' => 'FF9500',
                    'info' => '007AFF',
                ][$issue['severity']] ?? '666666';

                // Issue title
                $this->section->addText(
                    ($index + 1) . '. ' . $severityIcon . ' ' . $issue['message'],
                    ['size' => 12, 'bold' => true, 'color' => $severityColor],
                    'normalPara'
                );

                // Location
                $this->section->addText(
                    'Location: ' . $issue['location'],
                    ['size' => 11, 'italic' => true, 'color' => '666666'],
                    ['indentation' => ['left' => 360]]
                );

                // Suggestion
                $this->section->addText(
                    '💡 Fix: ' . $issue['suggestion'],
                    ['size' => 11, 'color' => '007AFF'],
                    ['indentation' => ['left' => 360], 'spaceAfter' => 300]
                );
            }

            $this->section->addTextBreak(1);
        }
    }

    protected function addRecommendations(ComplianceCheck $check): void
    {
        $this->section->addText('RECOMMENDATIONS', 'heading1');
        $this->section->addTextBreak(1);

        if ($check->overall_score >= 80) {
            $this->section->addText(
                '✅ Document Quality: Excellent',
                ['size' => 12, 'bold' => true, 'color' => '34C759'],
                'normalPara'
            );
            $this->section->addText(
                'The document meets compliance standards and is ready for approval. Minor improvements suggested above can further enhance document quality.',
                'normalText',
                'normalPara'
            );
        } elseif ($check->overall_score >= 70) {
            $this->section->addText(
                '⚠️ Document Quality: Good with Improvements Needed',
                ['size' => 12, 'bold' => true, 'color' => 'FF9500'],
                'normalPara'
            );
            $this->section->addText(
                'The document is approaching compliance standards. Address the identified issues to meet the 80% threshold for approval.',
                'normalText',
                'normalPara'
            );
        } else {
            $this->section->addText(
                '❌ Document Quality: Requires Significant Revision',
                ['size' => 12, 'bold' => true, 'color' => 'FF3B30'],
                'normalPara'
            );
            $this->section->addText(
                'The document does not meet compliance standards. Critical issues must be resolved before proceeding with approval process.',
                'normalText',
                'normalPara'
            );
        }

        $this->section->addTextBreak(1);

        // Priority actions
        $this->section->addText('Priority Actions:', 'heading3');
        $this->section->addTextBreak(1);

        $criticalIssues = array_filter($check->issues ?? [], fn($i) => ($i['severity'] ?? '') === 'critical');
        
        if (!empty($criticalIssues)) {
            $this->section->addText(
                '1. Address all ' . count($criticalIssues) . ' critical issues',
                ['size' => 11, 'bold' => true],
                'normalPara'
            );
            foreach (array_slice($criticalIssues, 0, 3) as $issue) {
                $this->section->addText(
                    '   • ' . ($issue['message'] ?? 'Unknown issue'),
                    ['size' => 10],
                    ['indentation' => ['left' => 360]]
                );
            }
            $this->section->addTextBreak(1);
        }

        $this->section->addText(
            '2. Run compliance check again after making changes',
            ['size' => 11],
            'normalPara'
        );

        $this->section->addText(
            '3. Aim for 80+ score before requesting approval',
            ['size' => 11],
            'normalPara'
        );

        // Footer
        $this->section->addTextBreak(2);
        $this->section->addText(
            '---',
            ['size' => 10],
            'centered'
        );
        $this->section->addText(
            'Generated by Bizmark.id Compliance System',
            ['size' => 10, 'italic' => true, 'color' => '999999'],
            'centered'
        );
        $this->section->addText(
            'Report Date: ' . now()->format('d F Y H:i:s'),
            ['size' => 9, 'color' => '999999'],
            'centered'
        );
    }

    /**
     * Download and delete the report file
     */
    public function downloadReport(DocumentDraft $draft): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filePath = $this->generateReport($draft);
        $fileName = 'Compliance_Report_' . $draft->id . '_' . date('Ymd') . '.docx';

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}
