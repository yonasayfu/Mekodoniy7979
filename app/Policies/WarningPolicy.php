<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warning;
use Illuminate\Auth\Access\Response;

class WarningPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('warnings.view') || $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Warning $warning): bool
    {
        return $user->can('warnings.view') || $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('warnings.issue') || $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Warning $warning): bool
    {
        // Only allow management roles to update warnings
        return $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Warning $warning): bool
    {
        // Only allow management roles to delete warnings
        return $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Warning $warning): bool
    {
        return $user->can('warnings.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Warning $warning): bool
    {
        return $user->can('warnings.manage');
    }
}
