<?php

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Membership;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    public function createOrganization(User $user, array $data): Organization
    {
        return DB::transaction(function () use ($user, $data) {
            $organization = Organization::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'owner_id' => $user->id,
            ]);

            $organization->members()->attach($user->id, [
                'role' => 'Owner',
            ]);

            session(['organization_id' => $organization->id]);

            return $organization;
        });
    }

    public function switchOrganization(User $user, int $organizationId): bool
    {
        $membership = Membership::where('user_id', $user->id)
            ->where('organization_id', $organizationId)
            ->first();

        if (!$membership) {
            return false;
        }

        session(['organization_id' => $organizationId]);

        return true;
    }

    public function getUserOrganizations(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $user->organizations()->get();
    }
}
