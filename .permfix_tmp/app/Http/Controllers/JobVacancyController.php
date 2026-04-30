<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    /**
     * Display a listing of open job vacancies (PUBLIC).
     */
    public function index(Request $request)
    {
        $vacancies = JobVacancy::open()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Detect mobile
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        $view = $isMobile ? 'career.mobile-index' : 'career.index';
        
        return view($view, compact('vacancies'));
    }

    /**
     * Display the specified vacancy (PUBLIC).
     */
    public function show(Request $request, $slug)
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

        // Detect mobile
        $isMobile = $request->header('User-Agent') && 
                   (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $request->header('User-Agent')));
        
        $view = $isMobile ? 'career.mobile-show' : 'career.show';
        
        return view($view, compact('vacancy', 'responsibilities', 'qualifications', 'benefits'));
    }
}
