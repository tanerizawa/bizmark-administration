<?php

namespace App\Http\Controllers;

use App\Models\ChecklistGeneration;
use App\Services\ChecklistGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChecklistGeneratorController extends Controller
{
    public function __construct(protected ChecklistGeneratorService $service) {}

    public function index()
    {
        return view('tools.checklist-generator.index');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'kbli_code' => ['required', 'string', 'regex:/^\d{5}$/', 'max:10'],
            'permit_type' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'business_scale' => ['required', 'in:mikro,kecil,menengah,besar'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        try {
            $record = $this->service->generate(
                kbliCode: $validated['kbli_code'],
                permitType: $validated['permit_type'],
                city: $validated['city'],
                businessScale: $validated['business_scale'],
                requesterEmail: $validated['email'] ?? null,
                ipAddress: $request->ip(),
            );

            return redirect()->route('checklist.result', $record->id);
        } catch (\Throwable $e) {
            Log::error('ChecklistGeneratorController generate error', ['error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Terjadi kesalahan saat membuat checklist. Silakan coba lagi.'])->withInput();
        }
    }

    public function result(ChecklistGeneration $checklist)
    {
        return view('tools.checklist-generator.result', compact('checklist'));
    }

    public function download(ChecklistGeneration $checklist)
    {
        try {
            $relativePath = $this->service->generatePdf($checklist);

            return response()->download(storage_path('app/public/'.$relativePath))->deleteFileAfterSend(false);
        } catch (\Throwable $e) {
            Log::error('ChecklistGeneratorController download error', ['id' => $checklist->id, 'error' => $e->getMessage()]);

            return back()->withErrors(['general' => 'Gagal membuat PDF. Silakan coba lagi.']);
        }
    }
}
