<?php

namespace App\Domain\Organization\Controllers;

use App\Domain\Organization\Models\Invitation;
use App\Domain\Organization\Services\InvitationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvitationAcceptController extends Controller
{
    public function __construct(
        private InvitationService $invitationService
    ) {}

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->with('organization')
            ->firstOrFail();

        return Inertia::render('Invitations/Accept', [
            'invitation' => $invitation,
            'user' => auth()->user(),
        ]);
    }

    public function accept(Request $request, string $token)
    {
        $success = $this->invitationService->acceptInvitation($token, $request->user());

        if (!$success) {
            return back()->withErrors(['invitation' => 'Unable to accept this invitation.']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Invitation accepted successfully.');
    }
}
