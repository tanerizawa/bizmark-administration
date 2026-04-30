<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('hrm.view');
    }

    public function view(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('hrm.view');
    }

    public function update(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('hrm.edit');
    }

    public function delete(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('hrm.delete');
    }

    public function updateStatus(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('hrm.edit');
    }
}
