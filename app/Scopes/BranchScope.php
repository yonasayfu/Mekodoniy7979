<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Skip applying scope to User model to avoid recursion during authentication
        if ($model instanceof \App\Models\User) {
            return;
        }

        if (Auth::check() && Auth::user()->hasRole('Branch Admin')) {
            $builder->where('branch_id', Auth::user()->branch_id);
        }
    }
}
