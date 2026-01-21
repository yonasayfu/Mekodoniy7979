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
        return $user->hasAnyPermission(['case_notes.view', 'case_notes.manage']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseNote $caseNote): bool
    {
        // Users can view if they have permission and the note is from their branch
        if (! $user->hasAnyPermission(['case_notes.view', 'case_notes.manage'])) {
            return false;
        }

        return $this->userSharesBranch($user, $caseNote);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('case_notes.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CaseNote $caseNote): bool
    {
        if (! $user->hasPermissionTo('case_notes.manage')) {
            return false;
        }

        if ($this->userHasGlobalAccess($user)) {
            return true;
        }

        return $user->id === $caseNote->created_by
            && $this->userSharesBranch($user, $caseNote);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseNote $caseNote): bool
    {
        // Only the author, admin, or users with delete permission can delete
        if ($this->userHasGlobalAccess($user) && $user->hasPermissionTo('case_notes.manage')) {
            return true;
        }

        if (! $this->userSharesBranch($user, $caseNote)) {
            return false;
        }

        return $user->id === $caseNote->created_by
            || $user->hasPermissionTo('case_notes.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseNote $caseNote): bool
    {
        if ($this->userHasGlobalAccess($user)) {
            return $user->hasPermissionTo('case_notes.manage');
        }

        return $user->hasPermissionTo('case_notes.manage')
            && $this->userSharesBranch($user, $caseNote);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseNote $caseNote): bool
    {
        return $this->userHasGlobalAccess($user)
            && $user->hasPermissionTo('case_notes.manage');
    }

    protected function userHasGlobalAccess(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    protected function userSharesBranch(User $user, CaseNote $caseNote): bool
    {
        if ($this->userHasGlobalAccess($user)) {
            return true;
        }

        if (! $user->branch_id || ! $caseNote->branch_id) {
            return false;
        }

        return $user->branch_id === $caseNote->branch_id;
    }
}
