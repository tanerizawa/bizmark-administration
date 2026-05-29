<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentVaultController extends Controller
{
    private const VAULT_CATEGORIES = [
        'izin_utama' => 'Izin Utama',
        'dokumen_pendukung' => 'Dokumen Pendukung',
        'laporan' => 'Laporan',
        'sertifikat' => 'Sertifikat',
        'lainnya' => 'Lainnya',
    ];

    public function index(Request $request)
    {
        $client = Auth::guard('client')->user();

        $query = Document::whereHas('project', function ($q) use ($client) {
            $q->where('client_id', $client->id);
        })
            ->where('client_visible', true)
            ->with('project');

        if ($request->filled('category')) {
            $validCats = array_keys(self::VAULT_CATEGORIES);
            if (in_array($request->category, $validCats, true)) {
                $query->where('vault_category', $request->category);
            }
        }

        if ($request->filled('project_id')) {
            // Validate project belongs to client
            $ownedIds = Project::where('client_id', $client->id)->pluck('id');
            if ($ownedIds->contains((int) $request->project_id)) {
                $query->where('project_id', $request->integer('project_id'));
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', '%'.e($request->search).'%')
                    ->orWhere('document_number', 'LIKE', '%'.e($request->search).'%');
            });
        }

        $documents = $query->orderBy('document_expires_at')->orderByDesc('created_at')->paginate(18)->withQueryString();

        $grouped = $query->get()->groupBy(fn ($d) => $d->vault_category ?? 'lainnya');

        $stats = [
            'total' => Document::whereHas('project', fn ($q) => $q->where('client_id', $client->id))->where('client_visible', true)->count(),
            'expiring' => Document::whereHas('project', fn ($q) => $q->where('client_id', $client->id))->where('client_visible', true)->whereNotNull('document_expires_at')->whereDate('document_expires_at', '<=', now()->addDays(90))->whereDate('document_expires_at', '>', now())->count(),
            'expired' => Document::whereHas('project', fn ($q) => $q->where('client_id', $client->id))->where('client_visible', true)->whereNotNull('document_expires_at')->whereDate('document_expires_at', '<', now())->count(),
        ];

        $projects = Project::where('client_id', $client->id)->orderBy('name')->get(['id', 'name']);
        $categories = self::VAULT_CATEGORIES;

        return view('client.documents.vault', compact('documents', 'grouped', 'stats', 'projects', 'categories'));
    }

    public function download(Document $document)
    {
        $client = Auth::guard('client')->user();

        // Authorize: document must belong to authenticated client AND be visible
        $belongs = Project::where('client_id', $client->id)
            ->where('id', $document->project_id)
            ->exists();

        abort_unless($belongs && $document->client_visible, 403);

        // Track download
        $document->increment('download_count');
        $document->update(['last_accessed_at' => now()]);

        // Try private disk first, then public
        if ($document->file_path && Storage::disk('private')->exists($document->file_path)) {
            return response()->download(
                Storage::disk('private')->path($document->file_path),
                $document->file_name
            );
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->download(
                Storage::disk('public')->path($document->file_path),
                $document->file_name
            );
        }

        return back()->withErrors(['file' => 'File tidak ditemukan. Hubungi tim Bizmark.']);
    }

    public function bulkDownload(Request $request)
    {
        $client = Auth::guard('client')->user();
        $ids = array_filter(array_map('intval', explode(',', $request->input('ids', ''))));

        abort_if(empty($ids) || count($ids) > 50, 422);

        $documents = Document::whereIn('id', $ids)
            ->whereHas('project', fn ($q) => $q->where('client_id', $client->id))
            ->where('client_visible', true)
            ->get();

        abort_if($documents->isEmpty(), 404);

        // Single file — redirect to regular download
        if ($documents->count() === 1) {
            return redirect()->route('client.vault.download', $documents->first());
        }

        // Multiple files — create ZIP in memory
        $zip = new \ZipArchive;
        $tmpPath = tempnam(sys_get_temp_dir(), 'vault_').'.zip';
        $zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($documents as $doc) {
            $disk = Storage::disk('private')->exists($doc->file_path ?? '') ? 'private' : 'public';
            if ($doc->file_path && Storage::disk($disk)->exists($doc->file_path)) {
                $zip->addFile(Storage::disk($disk)->path($doc->file_path), $doc->file_name ?? basename($doc->file_path));
            }
        }
        $zip->close();

        return response()->download($tmpPath, 'vault-'.now()->format('Ymd-His').'.zip')->deleteFileAfterSend(true);
    }
}
