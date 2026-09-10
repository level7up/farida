<?php

namespace App\Domain\Organization\Controllers;

use App\Domain\Organization\Models\Organization;
use App\Domain\Organization\Services\InvitationService;
use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function __construct(
        private InvitationService $invitationService
    ) {}

    public function index(Organization $organization)
    {
        $invitations = $this->invitationService->getOrganizationInvitations($organization);

        return Inertia::render('Invitations/Index', [
            'organization' => $organization,
            'invitations' => $invitations,
        ]);
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'in:Owner,Admin,Manager,Staff,Accountant,Cleaning'],
        ]);

        $invitation = $this->invitationService->createInvitation(
            $organization,
            $request->user(),
            $validated
        );

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }
}
