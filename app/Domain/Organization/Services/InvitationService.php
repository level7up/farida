<?php

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Invitation;
use App\Domain\Organization\Models\Membership;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvitationService
{
    public function createInvitation(Organization $organization, User $inviter, array $data): Invitation
    {
        Invitation::where('email', $data['email'])
            ->where('organization_id', $organization->id)
            ->whereNull('accepted_at')
            ->update(['accepted_at' => now()]);

        return Invitation::create([
            'email' => $data['email'],
            'role' => $data['role'],
            'token' => Invitation::generateToken(),
            'organization_id' => $organization->id,
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function acceptInvitation(string $token, User $user): bool
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->first();

        if (!$invitation || $invitation->isExpired()) {
            return false;
        }

        if ($invitation->email !== $user->email) {
            return false;
        }

        return DB::transaction(function () use ($invitation, $user) {
            $invitation->update(['accepted_at' => now()]);

            Membership::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'organization_id' => $invitation->organization_id,
                ],
                [
                    'role' => $invitation->role,
                ]
            );

            return true;
        });
    }

    public function getOrganizationInvitations(Organization $organization): \Illuminate\Database\Eloquent\Collection
    {
        return $organization->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();
    }
}
