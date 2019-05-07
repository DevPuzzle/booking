<?php

namespace App\Policies;

use App\Page;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function view(User $user, Page $page)
    {
        if ($user->is_admin) {
            return true;
        }
        if ($page->roles->count() && !$page->roles->where('id', $user->role_id)->count()) {
            return false;
        }
        if ($page->regions->count() && !$page->regions->where('id', $user->region_id)->count()) {
            return false;
        }
        return true;
    }
}
