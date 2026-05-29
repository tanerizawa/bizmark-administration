<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\ComplianceReport;
use App\Models\User;

class ComplianceReportPolicy
{
    public function viewAny(User|Client $user): bool
    {
        return true;
    }

    public function view(User|Client $user, ComplianceReport $report): bool
    {
        if ($user instanceof Client) {
            return $user->id === $report->generated_by;
        }

        return true; // admin
    }

    public function create(User|Client $user): bool
    {
        return true;
    }

    public function download(User|Client $user, ComplianceReport $report): bool
    {
        if ($user instanceof Client) {
            return $user->id === $report->generated_by && $report->status === 'ready';
        }

        return true; // admin
    }

    public function delete(User|Client $user, ComplianceReport $report): bool
    {
        if ($user instanceof Client) {
            return $user->id === $report->generated_by;
        }

        return true; // admin
    }
}
