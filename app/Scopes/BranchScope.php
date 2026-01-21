<?php

namespace App\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BranchScope implements Scope
{
    protected static array $tableBranchColumnCache = [];

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

        if (! Auth::check()) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $this->shouldScopeForUser($user)) {
            return;
        }

        if (! $this->modelHasBranchColumn($model)) {
            return;
        }

        $builder->where(
            $model->getTable().'.branch_id',
            $user->branch_id
        );
    }

    protected function shouldScopeForUser(User $user): bool
    {
        if (! $user->branch_id) {
            return false;
        }

        if ($user->hasAnyRole([
            'Super Admin',
            'Admin',
            'Auditor',
            'Reporting Analyst',
        ])) {
            return false;
        }

        return true;
    }

    protected function modelHasBranchColumn(Model $model): bool
    {
        $table = $model->getTable();

        if (! array_key_exists($table, self::$tableBranchColumnCache)) {
            self::$tableBranchColumnCache[$table] = Schema::hasColumn($table, 'branch_id');
        }

        return self::$tableBranchColumnCache[$table];
    }
}
