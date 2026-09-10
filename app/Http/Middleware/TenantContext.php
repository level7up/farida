<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $organizationId = session('organization_id');

        if ($organizationId) {
            request()->attributes->set('organization_id', $organizationId);
        }

        return $next($request);
    }
}
