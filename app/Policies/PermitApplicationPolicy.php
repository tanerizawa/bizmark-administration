<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\PermitApplication;
use App\Models\User;

class PermitApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('permits.manage');
    }

    public function view(User $user, PermitApplication $application): bool
    {
        return $user->hasPermission('permits.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('permits.manage');
    }

    public function update(User $user, PermitApplication $application): bool
    {
        return $user->hasPermission('permits.manage');
    }

    public function delete(User $user, PermitApplication $application): bool
    {
        return $user->hasPermission('permits.manage')
            && $application->status === 'draft';
    }

    public function restore(User $user, PermitApplication $application): bool
    {
        return $user->hasPermission('permits.manage');
    }

    public function forceDelete(User $user, PermitApplication $application): bool
    {
        return $user->hasPermission('permits.manage');
    }

    /**
     * Client-side: view own application.
     */
    public function viewAsClient(?Client $client, PermitApplication $application): bool
    {
        return $client !== null && $client->id === $application->client_id;
    }

    /**
     * Client-side: cancel own draft application.
     */
    public function cancelAsClient(?Client $client, PermitApplication $application): bool
    {
        return $client !== null
            && $client->id === $application->client_id
            && in_array($application->status, ['draft', 'submitted']);
    }
}
