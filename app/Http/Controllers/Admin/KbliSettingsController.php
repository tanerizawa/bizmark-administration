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
            'by_category' => Kbli::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get(),
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

                    // Expected CSV format: code, description, category, sector, notes
                    $data = [
                        'code' => trim($row[0] ?? ''),
                        'description' => trim($row[1] ?? ''),
                        'category' => trim($row[2] ?? null),
                        'sector' => trim($row[3] ?? ''),
                        'notes' => trim($row[4] ?? null),
                    ];

                    // Validate required fields
                    if (empty($data['code']) || empty($data['description'])) {
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
            fputcsv($file, ['code', 'description', 'category', 'sector', 'notes']);
            
            // Write sample data
            fputcsv($file, ['62010', 'Aktivitas Pemrograman Komputer', 'Rendah', 'J', 'Contoh: Pengembangan software, aplikasi web/mobile']);
            fputcsv($file, ['62020', 'Aktivitas Konsultasi Komputer', 'Rendah', 'J', 'Contoh: Konsultan IT, system integrator']);
            fputcsv($file, ['55101', 'Hotel Bintang 5', 'Menengah Tinggi', 'I', 'Perhotelan berbintang lima']);
            fputcsv($file, ['11010', 'Industri Minuman Keras', 'Tinggi', 'C', 'Produksi minuman beralkohol']);
            
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
            fputcsv($file, ['code', 'description', 'category', 'sector', 'notes']);
            
            // Write data
            Kbli::orderBy('code')->chunk(100, function($kblis) use ($file) {
                foreach ($kblis as $kbli) {
                    fputcsv($file, [
                        $kbli->code,
                        $kbli->description,
                        $kbli->category,
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
