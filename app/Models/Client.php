<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Client extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'name',
        'company_name',
        'industry',
        'contact_person',
        'email',
        'password',
        'phone',
        'mobile',
        'address',
        'city',
        'province',
        'postal_code',
        'npwp',
        'tax_name',
        'tax_address',
        'client_type',
        'status',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the email address for password reset.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Get all projects for this client
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get active projects count
     */
    public function activeProjectsCount()
    {
        return $this->projects()->whereHas('status', function ($query) {
            $query->where('name', '!=', 'Selesai');
        })->count();
    }

    /**
     * Get total project value
     */
    public function getTotalProjectValueAttribute()
    {
        return $this->projects()->sum('contract_value') ?? 0;
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute()
    {
        return $this->projects()->sum('down_payment') ?? 0;
    }

    /**
     * Scope for active clients
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for company clients
     */
    public function scopeCompany($query)
    {
        return $query->where('client_type', 'company');
    }
}
