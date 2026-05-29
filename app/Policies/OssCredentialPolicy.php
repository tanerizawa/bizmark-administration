<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\OssCredential;
use App\Models\User;

class OssCredentialPolicy
{
    public function viewAny(User|Client $user): bool
    {
        return true;
    }

    public function view(User|Client $user, OssCredential $credential): bool
    {
        if ($user instanceof Client) {
            return $user->id === $credential->client_id;
        }

        return true; // admin
    }

    public function create(User|Client $user): bool
    {
        return true;
    }

    public function update(User|Client $user, OssCredential $credential): bool
    {
        if ($user instanceof Client) {
            return $user->id === $credential->client_id;
        }

        return true; // admin
    }

    public function delete(User|Client $user, OssCredential $credential): bool
    {
        if ($user instanceof Client) {
            return $user->id === $credential->client_id;
        }

        return true; // admin
    }
}
