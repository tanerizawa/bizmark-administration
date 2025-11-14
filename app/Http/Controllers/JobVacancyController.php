<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    /**
     * Display a listing of open job vacancies (PUBLIC).
     */
    public function index()
    {
        $vacancies = JobVacancy::open()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('career.index', compact('vacancies'));
    }

    /**
     * Display the specified vacancy (PUBLIC).
     */
    public function show($slug)
    {
        $vacancy = JobVacancy::where('slug', $slug)->firstOrFail();

        // Parse JSON fields for display
        $responsibilities = is_string($vacancy->responsibilities) 
            ? json_decode($vacancy->responsibilities, true) 
            : $vacancy->responsibilities;
            
        $qualifications = is_string($vacancy->qualifications) 
            ? json_decode($vacancy->qualifications, true) 
            : $vacancy->qualifications;
            
        $benefits = is_string($vacancy->benefits) 
            ? json_decode($vacancy->benefits, true) 
            : $vacancy->benefits;

        return view('career.show', compact('vacancy', 'responsibilities', 'qualifications', 'benefits'));
    }
}
