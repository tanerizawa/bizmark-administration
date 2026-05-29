<?php

namespace App\Policies;

use App\Models\ChecklistGeneration;
use App\Models\Client;
use App\Models\User;

class ChecklistGenerationPolicy
{
    public function viewAny(User|Client|null $user): bool
    {
        return true; // checklist tool is public
    }

    public function view(User|Client|null $user, ChecklistGeneration $checklist): bool
    {
        // Authenticated client can see their own; admin sees all; public can access by route
        if ($user instanceof Client) {
            return $user->email === $checklist->requester_email || $checklist->requester_email === null;
        }

        if ($user instanceof User) {
            return true; // admin
        }

        return true; // public tool — access by ID is gated by controller
    }

    public function download(User|Client|null $user, ChecklistGeneration $checklist): bool
    {
        return true; // freely downloadable
    }
}
