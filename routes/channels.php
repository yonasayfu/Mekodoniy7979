<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, int $id) {
    return (int) $user->id === $id;
});

Broadcast::channel('branch.{branchId}.pledges', function (User $user, int $branchId) {
    $allowedRoles = ['Super Admin', 'Admin', 'Branch Admin', 'Manager', 'Branch Coordinator', 'Finance Officer'];

    if ($branchId === 0) {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    return $user->branch_id === $branchId && $user->hasAnyRole($allowedRoles);
});

Broadcast::channel('branch.{branchId}.sponsorships', function (User $user, int $branchId) {
    $allowedRoles = ['Super Admin', 'Admin', 'Branch Admin', 'Manager', 'Branch Coordinator', 'Finance Officer'];

    if ($branchId === 0) {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    return $user->branch_id === $branchId && $user->hasAnyRole($allowedRoles);
});
