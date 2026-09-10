<?php

namespace App\Http\Middleware;

use App\Domain\Organization\Services\OrganizationService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'current_organization' => fn () => $this->getCurrentOrganization($request),
                'organizations' => fn () => $this->getUserOrganizations($request),
            ],
        ];
    }

    private function getCurrentOrganization(Request $request): ?array
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        $organizationId = session('organization_id');
        if (!$organizationId) {
            return null;
        }

        $organizations = app(OrganizationService::class)->getUserOrganizations($user);
        $org = $organizations->firstWhere('id', $organizationId);

        return $org ? [
            'id' => $org->id,
            'name' => $org->name,
            'slug' => $org->slug,
        ] : null;
    }

    private function getUserOrganizations(Request $request): array
    {
        $user = $request->user();
        if (!$user) {
            return [];
        }

        $organizations = app(OrganizationService::class)->getUserOrganizations($user);

        return $organizations->map(fn ($org) => [
            'id' => $org->id,
            'name' => $org->name,
            'slug' => $org->slug,
        ])->toArray();
    }
}
