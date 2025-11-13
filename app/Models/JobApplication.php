<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_vacancy_id',
        'full_name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'address',
        'education_level',
        'major',
        'institution',
        'graduation_year',
        'gpa',
        'work_experience',
        'has_experience_ukl_upl',
        'skills',
        'cv_path',
        'portfolio_path',
        'cover_letter',
        'expected_salary',
        'available_from',
        'status',
        'notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $hidden = [
        'reviewed_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'available_from' => 'date',
        'work_experience' => 'array',
        'skills' => 'array',
        'has_experience_ukl_upl' => 'boolean',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the job vacancy for this application.
     */
    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    /**
     * Get the admin who reviewed this application.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'reviewed' => 'bg-blue-100 text-blue-800',
            'interview' => 'bg-purple-100 text-purple-800',
            'accepted' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'reviewed' => 'Direview',
            'interview' => 'Interview',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    /**
     * Mutator for CV upload.
     */
    public function setCvPathAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->attributes['cv_path'] = $value->store('applications/cv', 'public');
        } else {
            $this->attributes['cv_path'] = $value;
        }
    }

    /**
     * Mutator for portfolio upload.
     */
    public function setPortfolioPathAttribute($value)
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->attributes['portfolio_path'] = $value->store('applications/portfolio', 'public');
        } else {
            $this->attributes['portfolio_path'] = $value;
        }
    }
}
