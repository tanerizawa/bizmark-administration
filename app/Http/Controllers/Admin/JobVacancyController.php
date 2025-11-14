<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthorizesRequests;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobVacancyController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizePermission('recruitment.manage', 'Anda tidak memiliki akses untuk mengelola lowongan kerja.');
    }

    /**
     * Display a listing of job vacancies (ADMIN).
     */
    public function index()
    {
        $vacancies = JobVacancy::withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.jobs.index', compact('vacancies'));
    }

    /**
     * Show the form for creating a new job vacancy (ADMIN).
     */
    public function create()
    {
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created job vacancy (ADMIN).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'required|array',
            'qualifications' => 'required|array',
            'benefits' => 'nullable|array',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0',
            'salary_negotiable' => 'boolean',
            'deadline' => 'nullable|date',
            'status' => 'required|in:open,closed,draft',
            'google_form_url' => 'nullable|url',
        ]);

        // Auto-generate slug from title
        $validated['slug'] = Str::slug($validated['title']);

        // Encode arrays as JSON
        $validated['responsibilities'] = json_encode($validated['responsibilities']);
        $validated['qualifications'] = json_encode($validated['qualifications']);
        $validated['benefits'] = !empty($validated['benefits']) ? json_encode($validated['benefits']) : null;

        JobVacancy::create($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil ditambahkan!');
    }

    /**
     * Display the specified job vacancy (ADMIN).
     */
    public function show($id)
    {
        $vacancy = JobVacancy::withCount('applications')->findOrFail($id);
        $recentApplications = $vacancy->applications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.jobs.show', compact('vacancy', 'recentApplications'));
    }

    /**
     * Show the form for editing a job vacancy (ADMIN).
     */
    public function edit($id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        // Decode JSON fields for form
        $vacancy->responsibilities = is_string($vacancy->responsibilities) 
            ? json_decode($vacancy->responsibilities, true) 
            : $vacancy->responsibilities;
        
        $vacancy->qualifications = is_string($vacancy->qualifications) 
            ? json_decode($vacancy->qualifications, true) 
            : $vacancy->qualifications;
        
        $vacancy->benefits = is_string($vacancy->benefits) 
            ? json_decode($vacancy->benefits, true) 
            : $vacancy->benefits;

        return view('admin.jobs.edit', compact('vacancy'));
    }

    /**
     * Update a job vacancy (ADMIN).
     */
    public function update(Request $request, $id)
    {
        $vacancy = JobVacancy::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'required|array',
            'qualifications' => 'required|array',
            'benefits' => 'nullable|array',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0',
            'salary_negotiable' => 'boolean',
            'deadline' => 'nullable|date',
            'status' => 'required|in:open,closed,draft',
            'google_form_url' => 'nullable|url',
        ]);

        // Update slug if title changed
        if ($vacancy->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Encode arrays as JSON
        $validated['responsibilities'] = json_encode($validated['responsibilities']);
        $validated['qualifications'] = json_encode($validated['qualifications']);
        $validated['benefits'] = !empty($validated['benefits']) ? json_encode($validated['benefits']) : null;

        $vacancy->update($validated);

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil diperbarui!');
    }

    /**
     * Soft delete a job vacancy (ADMIN).
     */
    public function destroy($id)
    {
        $vacancy = JobVacancy::findOrFail($id);
        $vacancy->delete();

        return redirect()->route('admin.jobs.index')
            ->with('success', 'Lowongan pekerjaan berhasil dihapus!');
    }
}
