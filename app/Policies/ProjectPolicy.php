<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the client can view any projects.
     * Clients can only view their own projects.
     */
    public function viewAnyAsClient(?Client $client): bool
    {
        return $client !== null;
    }

    /**
     * Determine whether the client can view the project.
     * Clients can only view projects that belong to them.
     */
    public function viewAsClient(?Client $client, Project $project): bool
    {
        if (!$client) {
            return false;
        }

        return $client->id === $project->client_id;
    }

    /**
     * Determine whether the admin user can view any projects.
     * Admins can view all projects based on their permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('projects.view');
    }

    /**
     * Determine whether the admin user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.view');
    }

    /**
     * Determine whether the admin user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('projects.create');
    }

    /**
     * Determine whether the admin user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.edit');
    }

    /**
     * Determine whether the admin user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.delete');
    }

    /**
     * Determine whether the admin user can restore the project.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.delete');
    }

    /**
     * Determine whether the admin user can permanently delete the project.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.delete');
    }
}
