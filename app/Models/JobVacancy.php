<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'position',
        'description',
        'responsibilities',
        'qualifications',
        'benefits',
        'employment_type',
        'location',
        'salary_min',
        'salary_max',
        'salary_negotiable',
        'deadline',
        'status',
        'google_form_url',
        'applications_count',
    ];

    protected $casts = [
        'deadline' => 'date',
        'salary_negotiable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vacancy) {
            if (empty($vacancy->slug)) {
                $vacancy->slug = Str::slug($vacancy->title);
            }
        });
    }

    /**
     * Get all applications for this vacancy.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get applications with specific status.
     */
    public function pendingApplications()
    {
        return $this->hasMany(JobApplication::class)->where('status', 'pending');
    }

    /**
     * Check if vacancy is still open.
     */
    public function isOpen()
    {
        return $this->status === 'open' && 
               ($this->deadline === null || $this->deadline->isFuture());
    }

    /**
     * Get formatted salary range.
     */
    public function getSalaryRangeAttribute()
    {
        if (!$this->salary_min && !$this->salary_max) {
            return $this->salary_negotiable ? 'Negotiable' : 'Not specified';
        }

        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . 
                   ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        }

        return 'Rp ' . number_format($this->salary_min ?? $this->salary_max, 0, ',', '.');
    }

    /**
     * Scope to get only open vacancies.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open')
                     ->where(function ($q) {
                         $q->whereNull('deadline')
                           ->orWhere('deadline', '>=', now());
                     });
    }
}
