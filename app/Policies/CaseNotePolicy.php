<?php

namespace App\Policies;

use App\Models\CaseNote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CaseNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_case_notes', 'manage_case_notes']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseNote $caseNote): bool
    {
        // Users can view if they have permission and the note is from their branch
        return $user->hasAnyPermission(['view_case_notes', 'manage_case_notes'])
            && $user->branch_id === $caseNote->branch_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage_case_notes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseNote $caseNote): bool
    {
        // Only the author or admin can update
        return ($user->id === $caseNote->created_by || $user->hasRole('admin'))
            && $user->hasPermissionTo('manage_case_notes')
            && $user->branch_id === $caseNote->branch_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseNote $caseNote): bool
    {
        // Only the author, admin, or users with delete permission can delete
        return ($user->id === $caseNote->created_by 
                || $user->hasRole('admin') 
                || $user->hasPermissionTo('delete_case_notes'))
            && $user->branch_id === $caseNote->branch_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseNote $caseNote): bool
    {
        return $user->hasRole('admin') 
            || ($user->hasPermissionTo('manage_case_notes') 
                && $user->branch_id === $caseNote->branch_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseNote $caseNote): bool
    {
        return $user->hasRole('admin');
    }
}
