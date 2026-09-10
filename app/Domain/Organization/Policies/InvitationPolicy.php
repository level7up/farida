<?php

namespace App\Domain\Organization\Policies;

use App\Domain\Organization\Models\Invitation;
use App\Domain\Organization\Models\Organization;
use App\Models\User;

class InvitationPolicy
{
    public function viewAny(User $user, Organization $organization): bool
    {
        return $organization->members()->where('users.id', $user->id)->exists();
    }

    public function create(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id ||
            $organization->members()
                ->where('users.id', $user->id)
                ->wherePivot('role', 'Admin')
                ->exists();
    }

    public function accept(User $user, Invitation $invitation): bool
    {
        return $invitation->email === $user->email && $invitation->isPending();
    }
}
