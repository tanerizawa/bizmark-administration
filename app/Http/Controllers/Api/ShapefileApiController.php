<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ServiceInquiry;
use App\Models\ShapefileProject;
use App\Services\ShapefileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShapefileApiController extends Controller
{
    public function __construct(
        private ShapefileService $shapefileService
    ) {}

    /**
     * Generate and download a zipped shapefile from polygon data.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'coordinates' => 'required|array|min:3',
            'coordinates.*' => 'array|size:2',
            'coordinates.*.0' => 'required|numeric|between:-180,180', // lng
            'coordinates.*.1' => 'required|numeric|between:-90,90',   // lat
            'name' => 'required|string|max:100',
            'metadata' => 'nullable|array',
            'metadata.kelurahan' => 'nullable|string|max:50',
            'metadata.kecamatan' => 'nullable|string|max:50',
            'metadata.kabkota' => 'nullable|string|max:50',
            'metadata.provinsi' => 'nullable|string|max:50',
            'metadata.keterangan' => 'nullable|string|max:200',
            'area_m2' => 'nullable|numeric|min:0',
            'area_ha' => 'nullable|numeric|min:0',
            'perimeter_m' => 'nullable|numeric|min:0',
            // Lead / contact fields
            'company_name' => 'nullable|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'agreed_terms' => 'required|accepted',
            // RTRW enrichment (optional, from frontend zona query)
            'rtrw_zona' => 'nullable|string|max:200',
            'rtrw_perda' => 'nullable|string|max:100',
            'rtrw_remark' => 'nullable|string|max:500',
        ]);

        $maxPoints = config('shapefile.max_points', 500);
        if (count($validated['coordinates']) > $maxPoints) {
            return response()->json([
                'error' => "Polygon tidak boleh lebih dari {$maxPoints} titik koordinat."
            ], 422);
        }

        // Server-side check: block registered emails from unauthenticated requests
        $clientAuth = auth('client')->user();
        if (!$clientAuth && Client::where('email', $validated['email'])->exists()) {
            return response()->json([
                'error' => 'Email sudah terdaftar. Silakan login terlebih dahulu.'
            ], 403);
        }

        $coordinates = $validated['coordinates'];
        $meta = $validated['metadata'] ?? [];

        // Calculate area server-side as fallback
        $areaM2 = $validated['area_m2'] ?? $this->shapefileService->calculateArea($coordinates);
        $areaHa = $validated['area_ha'] ?? ($areaM2 / 10000);
        $perimeterM = $validated['perimeter_m'] ?? $this->shapefileService->calculatePerimeter($coordinates);

        $attributes = [
            'NAMA' => $validated['name'],
            'LUAS_M2' => round($areaM2, 2),
            'LUAS_HA' => round($areaHa, 6),
            'KELURAHAN' => $meta['kelurahan'] ?? '',
            'KECAMATAN' => $meta['kecamatan'] ?? '',
            'KABKOTA' => $meta['kabkota'] ?? '',
            'PROVINSI' => $meta['provinsi'] ?? '',
            'KETERANGAN' => $meta['keterangan'] ?? '',
            'ZONA_RTRW' => $validated['rtrw_zona'] ?? '',
            'NO_PERDA' => $validated['rtrw_perda'] ?? '',
        ];

        try {
            $zipPath = $this->shapefileService->generate($coordinates, $attributes, $validated['name']);

            // Create lead in service_inquiries for admin follow-up
            $inquiry = ServiceInquiry::create([
                'email' => $validated['email'],
                'company_name' => $validated['company_name'] ?? '-',
                'phone' => $validated['phone'],
                'contact_person' => $validated['contact_person'] ?? $validated['name'],
                'business_activity' => 'Pembuatan file SHP untuk lahan: ' . $validated['name'],
                'form_data' => [
                    'tool' => 'polygon_shp_maker',
                    'project_name' => $validated['name'],
                    'area_m2' => $areaM2,
                    'area_ha' => $areaHa,
                    'perimeter_m' => $perimeterM,
                    'num_points' => count($coordinates),
                    'location' => $meta,
                ],
                'status' => 'new',
                'priority' => 'medium',
                'source' => 'shp_maker',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            ]);

            // Save to database
            $sessionToken = $request->cookie('shp_session') ?? Str::random(40);
            ShapefileProject::create([
                'user_id' => $clientAuth?->id,
                'name' => $validated['name'],
                'company_name' => $validated['company_name'] ?? null,
                'contact_person' => $validated['contact_person'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'geojson' => [
                    'type' => 'Polygon',
                    'coordinates' => [$coordinates],
                ],
                'area_m2' => $areaM2,
                'area_ha' => $areaHa,
                'perimeter_m' => $perimeterM,
                'metadata' => $meta,
                'rtrw_zona' => $validated['rtrw_zona'] ?? null,
                'rtrw_perda' => $validated['rtrw_perda'] ?? null,
                'rtrw_remark' => $validated['rtrw_remark'] ?? null,
                'file_path' => $zipPath,
                'session_token' => $sessionToken,
                'agreed_terms_at' => now(),
                'service_inquiry_id' => $inquiry->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $fullPath = storage_path('app/' . $zipPath);
            $filename = basename($zipPath);

            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(false);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal membuat file SHP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview: calculate area without generating file.
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'coordinates' => 'required|array|min:3',
            'coordinates.*' => 'array|size:2',
            'coordinates.*.0' => 'required|numeric|between:-180,180',
            'coordinates.*.1' => 'required|numeric|between:-90,90',
        ]);

        $coords = $validated['coordinates'];
        $areaM2 = $this->shapefileService->calculateArea($coords);
        $perimeterM = $this->shapefileService->calculatePerimeter($coords);

        return response()->json([
            'area_m2' => round($areaM2, 4),
            'area_ha' => round($areaM2 / 10000, 8),
            'perimeter_m' => round($perimeterM, 4),
            'num_points' => count($coords),
        ]);
    }

    /**
     * Check if an email is already registered as a client.
     */
    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:150',
        ]);

        $exists = Client::where('email', $validated['email'])->exists();

        return response()->json([
            'registered' => $exists,
        ]);
    }
}
