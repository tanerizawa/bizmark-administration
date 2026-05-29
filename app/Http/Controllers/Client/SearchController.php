<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Kbli;
use App\Models\PermitApplication;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Command palette live search endpoint.
     * Returns matching applications, projects, and services (KBLI).
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        $results = [];

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $client = Auth::guard('client')->user();
        $clientId = $client?->id;
        $like = '%'.$this->escapeLike($q).'%';
        $startsWith = $this->escapeLike($q).'%';

        // Search applications
        if ($clientId) {
            $apps = PermitApplication::query()
                ->where('client_id', $clientId)
                ->where(function ($query) use ($like, $startsWith) {
                    $query->where('application_number', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhereHas('permitType', function ($permitTypeQuery) use ($like, $startsWith) {
                            $permitTypeQuery->where('name', 'like', $like)
                                ->orWhere('code', 'like', $startsWith);
                        });
                })
                ->with('permitType')
                ->latest('updated_at')
                ->limit(4)
                ->get();

            foreach ($apps as $app) {
                $results[] = [
                    'id' => 'app-'.$app->id,
                    'label' => $app->application_number,
                    'meta' => trim(($app->permitType?->name ?? 'Permohonan').' • '.str($app->status)->replace('_', ' ')->title()),
                    'group' => 'Permohonan',
                    'icon' => 'fas fa-file-signature',
                    'url' => route('client.applications.show', $app->id),
                ];
            }
        }

        // Search projects
        if ($clientId) {
            $projects = Project::query()
                ->where('client_id', $clientId)
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhereHas('status', fn ($statusQuery) => $statusQuery->where('name', 'like', $like))
                        ->orWhereHas('permitApplication', fn ($appQuery) => $appQuery->where('application_number', 'like', $like));
                })
                ->with(['status', 'permitApplication'])
                ->withCount('documents')
                ->latest('updated_at')
                ->limit(4)
                ->get();

            foreach ($projects as $project) {
                $results[] = [
                    'id' => 'proj-'.$project->id,
                    'label' => $project->name,
                    'meta' => trim(($project->status?->name ?? 'Proyek').' • '.$project->documents_count.' dokumen'),
                    'group' => 'Proyek',
                    'icon' => 'fas fa-diagram-project',
                    'url' => route('client.projects.show', $project->id),
                ];
            }
        }

        // Search documents
        if ($clientId) {
            $documents = Document::query()
                ->where('client_visible', true)
                ->whereHas('project', function ($query) use ($clientId, $like) {
                    $query->where('client_id', $clientId)
                        ->where(function ($projectQuery) use ($like) {
                            $projectQuery->where('name', 'like', $like)
                                ->orWhere('client_name', 'like', $like);
                        });
                })
                ->orWhere(function ($query) use ($clientId, $like) {
                    $query->where('client_visible', true)
                        ->whereHas('project', fn ($projectQuery) => $projectQuery->where('client_id', $clientId))
                        ->where(function ($documentQuery) use ($like) {
                            $documentQuery->where('title', 'like', $like)
                                ->orWhere('file_name', 'like', $like)
                                ->orWhere('document_number', 'like', $like)
                                ->orWhere('vault_category', 'like', $like);
                        });
                })
                ->with('project:id,name')
                ->latest('updated_at')
                ->limit(4)
                ->get();

            foreach ($documents as $document) {
                $results[] = [
                    'id' => 'doc-'.$document->id,
                    'label' => $document->title ?: ($document->file_name ?: 'Dokumen'),
                    'meta' => trim(($document->project?->name ?? 'Document Vault').' • '.($document->document_number ?: ($document->file_name ?: 'Dokumen klien'))),
                    'group' => 'Dokumen',
                    'icon' => 'fas fa-file-lines',
                    'url' => route('client.vault.index', ['search' => $document->title ?: $document->document_number ?: $document->file_name]),
                ];
            }
        }

        // Search KBLI / services
        $kblis = Kbli::query()
            ->where(function ($query) use ($like, $startsWith) {
                $query->where('code', 'like', $startsWith)
                    ->orWhere('description', 'like', $like);
            })
            ->limit(4)
            ->get();

        foreach ($kblis as $kbli) {
            $results[] = [
                'id' => 'kbli-'.$kbli->code,
                'label' => $kbli->code.' — '.$kbli->description,
                'meta' => 'KBLI • Rekomendasi izin dan estimasi layanan',
                'group' => 'Katalog Layanan',
                'icon' => 'fas fa-layer-group',
                'url' => route('client.services.show', $kbli->code),
            ];
        }

        return response()->json(['results' => $results]);
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
