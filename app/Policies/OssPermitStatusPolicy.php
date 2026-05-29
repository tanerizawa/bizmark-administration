<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\OssPermitStatus;
use App\Models\User;

class OssPermitStatusPolicy
{
    public function viewAny(User|Client $user): bool
    {
        return true;
    }

    public function view(User|Client $user, OssPermitStatus $status): bool
    {
        if ($user instanceof Client) {
            return $user->id === $status->client_id;
        }

        return true; // admin
    }

    public function create(User|Client $user): bool
    {
        return true;
    }

    public function delete(User|Client $user, OssPermitStatus $status): bool
    {
        if ($user instanceof Client) {
            return $user->id === $status->client_id;
        }

        return true; // admin
    }

    public function refresh(User|Client $user, OssPermitStatus $status): bool
    {
        if ($user instanceof Client) {
            return $user->id === $status->client_id;
        }

        return true; // admin
    }
}
