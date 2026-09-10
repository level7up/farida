<?php

namespace App\Domain\Organization\Policies;

use App\Domain\Organization\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Organization $organization): bool
    {
        return $this->isOwner($user, $organization) || $this->isAdmin($user, $organization);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->isOwner($user, $organization);
    }

    public function isOwner(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id;
    }

    public function isAdmin(User $user, Organization $organization): bool
    {
        return $organization->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'Admin')
            ->exists();
    }
}
