<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('reports.view');
    }

    /**
     * Determine whether the user can generate an impact book for a given user.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $targetUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function generateImpactBook(User $currentUser, User $targetUser)
    {
        if ($currentUser->can('reports.generate_impact_book')) {
            return true;
        }

        return $currentUser->id === $targetUser->id;
    }
}
