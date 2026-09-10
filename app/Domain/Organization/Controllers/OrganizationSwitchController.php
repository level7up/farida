<?php

namespace App\Domain\Organization\Controllers;

use App\Domain\Organization\Services\OrganizationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationSwitchController extends Controller
{
    public function __construct(
        private OrganizationService $organizationService
    ) {}

    public function switch(Request $request, int $organizationId)
    {
        $success = $this->organizationService->switchOrganization($request->user(), $organizationId);

        if (!$success) {
            return back()->withErrors(['organization' => 'Unable to switch to that organization.']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Organization switched successfully.');
    }
}
