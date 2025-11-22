<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use App\Models\Client;
use App\Models\Institution;
use App\Models\PermitApplication;

class SearchController extends Controller
{
    /**
     * Global search across multiple models
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'message' => 'Masukkan minimal 2 karakter untuk mencari'
            ]);
        }

        $results = [
            'projects' => $this->searchProjects($query),
            'tasks' => $this->searchTasks($query),
            'documents' => $this->searchDocuments($query),
            'clients' => $this->searchClients($query),
            'institutions' => $this->searchInstitutions($query),
            'permits' => $this->searchPermits($query),
        ];

        // Count total results
        $totalResults = collect($results)->sum(fn($items) => count($items));

        return response()->json([
            'query' => $query,
            'total' => $totalResults,
            'results' => $results,
        ]);
    }

    private function searchProjects($query)
    {
        return Project::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('description', 'ILIKE', "%{$query}%")
            ->orWhere('client_name', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'client_name'])
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->name,
                    'subtitle' => $project->client_name ?? 'Tidak ada klien',
                    'icon' => 'fa-project-diagram',
                    'color' => 'apple-green',
                    'url' => route('projects.show', $project->id),
                    'type' => 'Proyek',
                ];
            });
    }

    private function searchTasks($query)
    {
        return Task::where('title', 'ILIKE', "%{$query}%")
            ->orWhere('description', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'status'])
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'subtitle' => 'Status: ' . ucfirst($task->status),
                    'icon' => 'fa-tasks',
                    'color' => 'apple-orange',
                    'url' => route('tasks.show', $task->id),
                    'type' => 'Task',
                ];
            });
    }

    private function searchDocuments($query)
    {
        return Document::where('title', 'ILIKE', "%{$query}%")
            ->orWhere('description', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'category'])
            ->map(function($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'subtitle' => $document->category ?? 'Dokumen',
                    'icon' => 'fa-file-alt',
                    'color' => 'apple-blue',
                    'url' => route('documents.show', $document->id),
                    'type' => 'Dokumen',
                ];
            });
    }

    private function searchClients($query)
    {
        return Client::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('email', 'ILIKE', "%{$query}%")
            ->orWhere('phone', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'title' => $client->name,
                    'subtitle' => $client->email,
                    'icon' => 'fa-users',
                    'color' => 'apple-purple',
                    'url' => route('clients.show', $client->id),
                    'type' => 'Klien',
                ];
            });
    }

    private function searchInstitutions($query)
    {
        return Institution::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('address', 'ILIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'type'])
            ->map(function($institution) {
                return [
                    'id' => $institution->id,
                    'title' => $institution->name,
                    'subtitle' => $institution->type ?? 'Instansi',
                    'icon' => 'fa-building',
                    'color' => 'apple-blue',
                    'url' => route('institutions.show', $institution->id),
                    'type' => 'Instansi',
                ];
            });
    }

    private function searchPermits($query)
    {
        return PermitApplication::where('application_number', 'ILIKE', "%{$query}%")
            ->orWhereHas('client', function($q) use ($query) {
                $q->where('name', 'ILIKE', "%{$query}%");
            })
            ->limit(5)
            ->with('client:id,name')
            ->get(['id', 'application_number', 'client_id', 'status'])
            ->map(function($permit) {
                return [
                    'id' => $permit->id,
                    'title' => $permit->application_number,
                    'subtitle' => $permit->client ? $permit->client->name : 'N/A',
                    'icon' => 'fa-file-certificate',
                    'color' => 'apple-purple',
                    'url' => route('admin.permit-applications.show', $permit->id),
                    'type' => 'Perizinan',
                ];
            });
    }
}
