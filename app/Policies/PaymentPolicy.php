<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('finances.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finances.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('finances.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finances.edit');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finances.delete');
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finances.delete');
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->hasPermission('finances.delete');
    }
}
