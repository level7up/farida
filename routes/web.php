<?php

use App\Domain\Organization\Controllers\InvitationAcceptController;
use App\Domain\Organization\Controllers\InvitationController;
use App\Domain\Organization\Controllers\OrganizationController;
use App\Domain\Organization\Controllers\OrganizationSwitchController;
use App\Http\Controllers\ProfileController;
use App\Domain\Organization\Services\OrganizationService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $organizationService = app(OrganizationService::class);
    $organizations = $organizationService->getUserOrganizations($user);
    $currentOrganization = $organizations->firstWhere('id', session('organization_id'));

    return Inertia::render('Dashboard/Index', [
        'organizations' => $organizations,
        'currentOrganization' => $currentOrganization,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/organizations/create', [OrganizationController::class, 'create'])
        ->name('organizations.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])
        ->name('organizations.store');
    Route::post('/organizations/{organizationId}/switch', [OrganizationSwitchController::class, 'switch'])
        ->name('organizations.switch');

    Route::get('/organizations/{organization:slug}/invitations', [InvitationController::class, 'index'])
        ->name('invitations.index');
    Route::post('/organizations/{organization:slug}/invitations', [InvitationController::class, 'store'])
        ->name('invitations.store');
});

Route::get('/invitations/accept/{token}', [InvitationAcceptController::class, 'show'])
    ->name('invitations.show');
Route::post('/invitations/accept/{token}', [InvitationAcceptController::class, 'accept'])
    ->name('invitations.accept')
    ->middleware('auth');

require __DIR__.'/auth.php';
