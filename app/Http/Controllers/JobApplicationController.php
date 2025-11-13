<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class JobApplicationController extends Controller
{
    /**
     * Show application form for a specific vacancy (PUBLIC).
     */
    public function create($vacancy_id)
    {
        $vacancy = JobVacancy::findOrFail($vacancy_id);

        if (!$vacancy->isOpen()) {
            return redirect()->route('career.index')
                ->with('error', 'Maaf, lowongan ini sudah ditutup.');
        }

        return view('career.apply', compact('vacancy'));
    }

    /**
     * Store application submission (PUBLIC).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = JobApplication::where('job_vacancy_id', $request->job_vacancy_id)
                        ->where('email', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Email ini sudah terdaftar untuk posisi ini. Setiap email hanya dapat mendaftar satu kali.');
                    }
                },
            ],
            'phone' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:Pria,Wanita',
            'address' => 'nullable|string',
            
            'education_level' => 'required|in:D3,S1,S2,S3',
            'major' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'graduation_year' => 'nullable|integer|min:1980|max:' . (date('Y') + 5),
            'gpa' => 'nullable|numeric|min:0|max:4',
            
            'work_experience' => 'nullable|string',
            'has_experience_ukl_upl' => 'boolean',
            'skills' => 'nullable|string',
            
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'portfolio' => 'nullable|file|mimes:pdf,doc,docx,zip|max:5120',
            'cover_letter' => 'nullable|string|max:2000',
            
            'expected_salary' => 'nullable|integer|min:0',
            'available_from' => 'nullable|date',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'cv.required' => 'CV wajib diunggah.',
            'cv.mimes' => 'CV harus berformat PDF, DOC, atau DOCX.',
            'cv.max' => 'Ukuran CV maksimal 2MB.',
            'portfolio.mimes' => 'Portfolio harus berformat PDF, DOC, DOCX, atau ZIP.',
            'portfolio.max' => 'Ukuran portfolio maksimal 5MB.',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('cv')) {
                $validated['cv_path'] = $request->file('cv')->store('applications/cv', 'public');
            }

            if ($request->hasFile('portfolio')) {
                $validated['portfolio_path'] = $request->file('portfolio')->store('applications/portfolio', 'public');
            }

            // Parse JSON fields
            if (!empty($validated['work_experience'])) {
                $validated['work_experience'] = json_decode($validated['work_experience'], true);
            }

            if (!empty($validated['skills'])) {
                $validated['skills'] = json_decode($validated['skills'], true);
            }

            // Remove cv/portfolio from validated (already set as cv_path/portfolio_path)
            unset($validated['cv'], $validated['portfolio']);

            // Create application
            $application = JobApplication::create($validated);

            // Increment applications count
            $vacancy = JobVacancy::find($validated['job_vacancy_id']);
            $vacancy->increment('applications_count');

            // TODO: Send email notifications
            // Mail::to($validated['email'])->send(new ApplicationReceived($application));
            // Mail::to(config('mail.admin'))->send(new NewApplicationAlert($application));

            return redirect()->route('career.show', $vacancy->slug)
                ->with('success', 'Lamaran Anda berhasil dikirim! Kami akan menghubungi Anda segera.');

        } catch (\Exception $e) {
            // If error, provide Google Form fallback
            $vacancy = JobVacancy::find($request->job_vacancy_id);
            
            if ($vacancy && $vacancy->google_form_url) {
                return redirect($vacancy->google_form_url)
                    ->with('info', 'Terjadi kesalahan. Silakan lengkapi lamaran melalui Google Form.');
            }

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
