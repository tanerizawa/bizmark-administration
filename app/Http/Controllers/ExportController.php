<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportController extends Controller
{
    /**
     * Export projects to Excel
     */
    public function exportProjects(Request $request)
    {
        // Get projects with relationships
        $query = Project::with(['status', 'institution']);

        // Apply filters if provided
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proyek');

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Proyek',
            'C1' => 'Nama Klien',
            'D1' => 'Institusi',
            'E1' => 'Status',
            'F1' => 'Budget',
            'G1' => 'Tanggal Mulai',
            'H1' => 'Deadline',
            'I1' => 'Kontak Klien',
            'J1' => 'Alamat Klien',
            'K1' => 'Progress (%)',
            'L1' => 'Dibuat Tanggal',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A84FF'], // Apple Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Fill data
        $row = 2;
        foreach ($projects as $index => $project) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $project->name);
            $sheet->setCellValue('C' . $row, $project->client_name ?? '-');
            $sheet->setCellValue('D' . $row, $project->institution->name ?? '-');
            $sheet->setCellValue('E' . $row, $project->status->name ?? '-');
            $sheet->setCellValue('F' . $row, $project->budget ?? 0);
            $sheet->setCellValue('G' . $row, $project->start_date ? $project->start_date->format('d/m/Y') : '-');
            $sheet->setCellValue('H' . $row, $project->deadline ? $project->deadline->format('d/m/Y') : '-');
            $sheet->setCellValue('I' . $row, $project->client_contact ?? '-');
            $sheet->setCellValue('J' . $row, $project->client_address ?? '-');
            $sheet->setCellValue('K' . $row, ($project->progress_percentage ?? 0) . '%');
            $sheet->setCellValue('L' . $row, $project->created_at->format('d/m/Y H:i'));

            $row++;
        }

        // Format currency column
        $sheet->getStyle('F2:F' . ($row - 1))->getNumberFormat()
            ->setFormatCode('Rp #,##0');

        // Auto-size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to data
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];
        $sheet->getStyle('A1:L' . ($row - 1))->applyFromArray($dataStyle);

        // Alternating row colors
        for ($i = 2; $i < $row; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':L' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F5F5F7'); // Light gray
            }
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Generate filename with timestamp
        $filename = 'Proyek_Export_' . date('Y-m-d_His') . '.xlsx';

        // Save file to temporary location
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Return download response
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export project details with tasks and documents
     */
    public function exportProjectDetails($id)
    {
        $project = Project::with(['status', 'institution', 'tasks', 'documents'])->findOrFail($id);

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Project Info
        $infoSheet = $spreadsheet->getActiveSheet();
        $infoSheet->setTitle('Info Proyek');

        $infoSheet->setCellValue('A1', 'INFORMASI PROYEK');
        $infoSheet->mergeCells('A1:B1');
        $infoSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $infoSheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $info = [
            ['Nama Proyek', $project->name],
            ['Nama Klien', $project->client_name ?? '-'],
            ['Institusi', $project->institution->name ?? '-'],
            ['Status', $project->status->name ?? '-'],
            ['Budget', 'Rp ' . number_format($project->budget ?? 0, 0, ',', '.')],
            ['Tanggal Mulai', $project->start_date ? $project->start_date->format('d/m/Y') : '-'],
            ['Deadline', $project->deadline ? $project->deadline->format('d/m/Y') : '-'],
            ['Kontak Klien', $project->client_contact ?? '-'],
            ['Alamat Klien', $project->client_address ?? '-'],
            ['Progress', ($project->progress_percentage ?? 0) . '%'],
            ['Deskripsi', $project->description ?? '-'],
        ];

        $row = 3;
        foreach ($info as $item) {
            $infoSheet->setCellValue('A' . $row, $item[0]);
            $infoSheet->setCellValue('B' . $row, $item[1]);
            $infoSheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
        }

        $infoSheet->getColumnDimension('A')->setWidth(20);
        $infoSheet->getColumnDimension('B')->setWidth(50);

        // Sheet 2: Tasks
        if ($project->tasks->count() > 0) {
            $taskSheet = $spreadsheet->createSheet();
            $taskSheet->setTitle('Task');

            $taskHeaders = ['No', 'Judul Task', 'Status', 'Priority', 'Deadline'];
            $col = 'A';
            foreach ($taskHeaders as $header) {
                $taskSheet->setCellValue($col . '1', $header);
                $taskSheet->getStyle($col . '1')->getFont()->setBold(true);
                $col++;
            }

            $row = 2;
            foreach ($project->tasks as $index => $task) {
                $taskSheet->setCellValue('A' . $row, $index + 1);
                $taskSheet->setCellValue('B' . $row, $task->title);
                $taskSheet->setCellValue('C' . $row, $task->status ?? '-');
                $taskSheet->setCellValue('D' . $row, $task->priority ?? '-');
                $taskSheet->setCellValue('E' . $row, $task->due_date ? $task->due_date->format('d/m/Y') : '-');
                $row++;
            }

            foreach (range('A', 'E') as $col) {
                $taskSheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Sheet 3: Documents
        if ($project->documents->count() > 0) {
            $docSheet = $spreadsheet->createSheet();
            $docSheet->setTitle('Dokumen');

            $docHeaders = ['No', 'Judul Dokumen', 'Kategori', 'Tipe File', 'Ukuran', 'Tanggal Upload'];
            $col = 'A';
            foreach ($docHeaders as $header) {
                $docSheet->setCellValue($col . '1', $header);
                $docSheet->getStyle($col . '1')->getFont()->setBold(true);
                $col++;
            }

            $row = 2;
            foreach ($project->documents as $index => $doc) {
                $docSheet->setCellValue('A' . $row, $index + 1);
                $docSheet->setCellValue('B' . $row, $doc->title);
                $docSheet->setCellValue('C' . $row, $doc->category ?? '-');
                $docSheet->setCellValue('D' . $row, $doc->document_type ?? '-');
                $docSheet->setCellValue('E' . $row, $this->formatFileSize($doc->file_size ?? 0));
                $docSheet->setCellValue('F' . $row, $doc->created_at->format('d/m/Y H:i'));
                $row++;
            }

            foreach (range('A', 'F') as $col) {
                $docSheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Generate filename
        $filename = 'Detail_Proyek_' . str_replace(' ', '_', $project->name) . '_' . date('Y-m-d') . '.xlsx';

        // Save and download
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
