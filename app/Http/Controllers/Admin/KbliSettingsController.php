<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kbli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KbliSettingsController extends Controller
{
    /**
     * Show KBLI settings page
     */
    public function index()
    {
        $totalKbli = Kbli::count();
        $kbliStats = [
            'total' => $totalKbli,
            'by_sector' => Kbli::select('sector', DB::raw('count(*) as count'))
                ->groupBy('sector')
                ->orderBy('sector')
                ->get(),
        ];

        return view('admin.settings.kbli', compact('kbliStats'));
    }

    /**
     * Import KBLI from CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'clear_existing' => 'nullable|boolean',
        ]);

        try {
            $file = $request->file('csv_file');
            $clearExisting = $request->boolean('clear_existing');

            // Clear existing data if requested
            if ($clearExisting) {
                Kbli::truncate();
            }

            // Read CSV file
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // Read header row
            
            // Normalize header (lowercase, trim)
            $header = array_map(function($col) {
                return strtolower(trim($col));
            }, $header);
            
            // Map possible column names
            $columnMap = [
                'kode' => 'code',
                'code' => 'code',
                'judul' => 'description',
                'title' => 'description',
                'description' => 'description',
                'kategori' => 'sector',
                'sector' => 'sector',
                'deskripsi' => 'notes',
                'notes' => 'notes',
                'keterangan' => 'notes',
            ];
            
            // Find column indexes
            $indexes = [];
            foreach ($header as $index => $colName) {
                if (isset($columnMap[$colName])) {
                    $indexes[$columnMap[$colName]] = $index;
                }
            }
            
            // Validate required columns exist
            if (!isset($indexes['code']) || !isset($indexes['description']) || !isset($indexes['sector'])) {
                throw new \Exception('Format CSV tidak valid. Header harus memiliki kolom: Kode/Code, Judul/Description, Kategori/Sector');
            }
            
            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::beginTransaction();

            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map data from CSV using flexible column mapping
                    $data = [
                        'code' => trim($row[$indexes['code']] ?? ''),
                        'description' => trim($row[$indexes['description']] ?? ''),
                        'sector' => trim($row[$indexes['sector']] ?? ''),
                        'notes' => isset($indexes['notes']) ? trim($row[$indexes['notes']] ?? null) : null,
                    ];

                    // Validate required fields
                    if (empty($data['code']) || empty($data['description']) || empty($data['sector'])) {
                        $skipped++;
                        continue;
                    }

                    // Create or update KBLI
                    Kbli::updateOrCreate(
                        ['code' => $data['code']],
                        $data
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row error: " . $e->getMessage();
                    $skipped++;
                }
            }

            fclose($handle);
            DB::commit();

            $message = "Import berhasil! {$imported} KBLI diimpor";
            if ($skipped > 0) {
                $message .= ", {$skipped} baris dilewati";
            }

            if (!empty($errors)) {
                Log::warning('KBLI Import Errors', $errors);
            }

            return redirect()->route('admin.settings.kbli.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KBLI Import Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.settings.kbli.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Download KBLI template CSV
     */
    public function downloadTemplate()
    {
        $filename = 'kbli_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['code', 'description', 'sector', 'notes']);
            
            // Write sample data
            fputcsv($file, ['62010', 'Aktivitas Pemrograman Komputer', 'J', 'Contoh: Pengembangan software, aplikasi web/mobile']);
            fputcsv($file, ['62020', 'Aktivitas Konsultasi Komputer', 'J', 'Contoh: Konsultan IT, system integrator']);
            fputcsv($file, ['55101', 'Hotel Bintang 5', 'I', 'Perhotelan berbintang lima']);
            fputcsv($file, ['01111', 'Pertanian Padi', 'A', 'Pertanian padi sawah dan ladang']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete all KBLI data
     */
    public function clear(Request $request)
    {
        try {
            $count = Kbli::count();
            Kbli::truncate();

            return redirect()->route('admin.settings.kbli.index')
                ->with('success', "{$count} data KBLI berhasil dihapus");
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.kbli.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export KBLI to CSV
     */
    public function export()
    {
        $filename = 'kbli_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Write header
            fputcsv($file, ['code', 'description', 'sector', 'notes']);
            
            // Write data
            Kbli::orderBy('code')->chunk(100, function($kblis) use ($file) {
                foreach ($kblis as $kbli) {
                    fputcsv($file, [
                        $kbli->code,
                        $kbli->description,
                        $kbli->sector,
                        $kbli->notes,
                    ]);
                }
            });
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
