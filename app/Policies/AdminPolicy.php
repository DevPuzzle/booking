<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the given is admin
     *
     * @param  \App\User $user
     * @return bool
     */
    public function admin(User $user)
    {
        return $user->role == 'administrator' || $user->role == 'mngr';
    }
}
