<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\Client;
use App\Models\User;

class ApiKeyPolicy
{
    public function viewAny(User|Client $user): bool
    {
        return true;
    }

    public function view(User|Client $user, ApiKey $apiKey): bool
    {
        if ($user instanceof Client) {
            return $user->id === $apiKey->client_id;
        }

        return true; // admin
    }

    public function create(User|Client $user): bool
    {
        return true;
    }

    public function delete(User|Client $user, ApiKey $apiKey): bool
    {
        if ($user instanceof Client) {
            return $user->id === $apiKey->client_id;
        }

        return true; // admin
    }

    public function update(User|Client $user, ApiKey $apiKey): bool
    {
        if ($user instanceof Client) {
            return $user->id === $apiKey->client_id;
        }

        return true; // admin
    }
}
