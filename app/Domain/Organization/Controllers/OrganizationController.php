<?php

namespace App\Domain\Organization\Controllers;

use App\Domain\Organization\Services\OrganizationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function __construct(
        private OrganizationService $organizationService
    ) {}

    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:organizations,slug'],
        ]);

        $this->organizationService->createOrganization($request->user(), $validated);

        return redirect()->route('dashboard')
            ->with('success', 'Organization created successfully.');
    }
}
