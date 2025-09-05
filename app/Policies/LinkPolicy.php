<?php
namespace App\Policies;

use App\Models\Link;
use App\Models\User;

class LinkPolicy
{
    /**
     * Determine whether the user can view the link analytics.
     */
    public function view(User $user, Link $link): bool
    {
        return $link->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the link.
     */
    public function update(User $user, Link $link): bool
    {
        return $link->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the link.
     */
    public function delete(User $user, Link $link): bool
    {
        return $link->user_id === $user->id;
    }
}
