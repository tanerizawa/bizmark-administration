<?php

namespace App\Models;

use App\Observers\NavCountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([NavCountObserver::class])]
class Institution extends Model
{
    protected $fillable = [
        'name',
        'type',
        'address',
        'phone',
        'email',
        'contact_person',
        'contact_position',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'institution_id');
    }

    public function permitTypes(): HasMany
    {
        return $this->hasMany(PermitType::class, 'institution_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
