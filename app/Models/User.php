<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'avatar',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the role of the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roleNames)
    {
        return $this->role && in_array($this->role->name, $roleNames);
    }

    /**
     * Check if user has a specific permission
     * Rename to hasPermission to avoid conflict with Laravel's can() method
     */
    public function hasPermission($permissionName)
    {
        return $this->role && $this->role->hasPermission($permissionName);
    }

    /**
     * Override Laravel's can() method to check custom permissions
     * @param mixed $abilities
     * @param array|mixed $arguments
     * @return bool
     */
    public function can($abilities, $arguments = []): bool
    {
        // If it's a string, check our custom permission system
        if (is_string($abilities)) {
            return $this->hasPermission($abilities);
        }
        
        // Otherwise, fall back to parent implementation
        return parent::can($abilities, $arguments);
    }

    /**
     * Get the tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    /**
     * Alias for assignedTasks (for dashboard compatibility)
     */
    public function tasks()
    {
        return $this->assignedTasks();
    }
}
