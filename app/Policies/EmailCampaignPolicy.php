<?php

namespace App\Policies;

use App\Models\EmailCampaign;
use App\Models\User;

class EmailCampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('email.view');
    }

    public function view(User $user, EmailCampaign $campaign): bool
    {
        return $user->hasPermission('email.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('email.create');
    }

    public function update(User $user, EmailCampaign $campaign): bool
    {
        return $user->hasPermission('email.edit');
    }

    public function delete(User $user, EmailCampaign $campaign): bool
    {
        return $user->hasPermission('email.delete');
    }

    public function send(User $user, EmailCampaign $campaign): bool
    {
        return $user->hasPermission('email.send');
    }
}
