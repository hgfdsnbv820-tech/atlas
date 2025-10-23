<?php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPolicy
{
    use HandlesAuthorization;

    public function before(?User $user, $ability = null)
    {
        if ($user && ($user->is_admin ?? false)) {
            return true;
        }
    }

    public function viewAny(?User $user): bool
    {
        return true; // public listing; controllers may filter
    }

    public function view(?User $user, Community $community): bool
    {
        if ($community->is_public) {
            return true;
        }
        if ($user === null) {
            return false;
        }
        return $community->communityMembers()->where('users.id', $user->id)->exists();
    }

    public function create(?User $user): bool
    {
        return $user !== null; // any authenticated user can create; controller may set creator as admin
    }

    public function update(?User $user, Community $community): bool
    {
        if ($user === null) return false;
        return $community->admins()->where('users.id', $user->id)->exists();
    }

    public function delete(?User $user, Community $community): bool
    {
        if ($user === null) return false;
        return $community->admins()->where('users.id', $user->id)->exists();
    }

    public function join(?User $user, Community $community): bool
    {
        return $user !== null;
    }

    public function leave(?User $user, Community $community): bool
    {
        return $user !== null;
    }
}
