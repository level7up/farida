# Implementation Plan: Foundation Setup

**Branch**: `001-foundation-setup` | **Date**: 2026-09-08 | **Spec**: [spec.md](spec.md)

**Input**: Feature specification from `/specs/001-foundation-setup/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command; its definition describes the execution workflow.

## Summary

Foundation Setup establishes the authentication system, organization tenancy, and team membership for the Farida property management SaaS. Users can register, verify email, create organizations, and invite team members with role-based access. Platform administrators use a separate authentication guard.

## Technical Context

**Language/Version**: PHP 8.3, Laravel 13

**Primary Dependencies**: Laravel Breeze (auth scaffolding), spatie/laravel-permission (roles/permissions), Inertia.js + Vue 3 (frontend)

**Storage**: MySQL (primary database), Redis (cache/queue for email retries)

**Testing**: Pest (PHP testing framework)

**Target Platform**: Web application (responsive, mobile-friendly)

**Project Type**: Web application (monolithic Laravel with domain modules)

**Performance Goals**: 100 concurrent registrations, <3min registration flow, <2min password reset

**Constraints**: Tenant isolation mandatory (organization_id on all tables), server-side source of truth

**Scale/Scope**: Multi-tenant SaaS, 6 organization roles, platform admin guard separation

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Status | Notes |
|-----------|--------|-------|
| I. Tenant Isolation | ✅ PASS | organization_id required on all tenant tables |
| II. Server is Source of Truth | ✅ PASS | No client-side financial logic in scope |
| III. Append-Only Financial Records | ⚪ N/A | No financial records in Phase 1 |
| IV. Transactional Booking Creation | ⚪ N/A | No bookings in Phase 1 |
| V. Thin Controllers, Fat Services | ✅ PASS | Service layer architecture planned |
| VI. Audit Trail for Mutations | ✅ PASS | Audit logging for org creation, membership changes |

## Project Structure

### Documentation (this feature)

```text
specs/001-foundation-setup/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output (/speckit.tasks command)
```

### Source Code (repository root)

```text
app/
├── Domain/
│   ├── Auth/              # Registration, login, email verification
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Events/
│   │   └── Exceptions/
│   ├── Organization/      # Tenants, memberships, invitations
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Policies/
│   │   ├── Events/
│   │   ├── Exceptions/
│   │   └── DTOs/
│   └── Authorization/     # Roles, permissions, policies
│       ├── Models/
│       ├── Services/
│       └── Policies/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/                # Shared Eloquent models
└── Services/              # Cross-domain services

resources/
├── js/
│   ├── Pages/             # Inertia page components
│   ├── Components/        # Reusable Vue components
│   └── Layouts/           # Base layouts
└── views/                 # Blade templates (email, minimal)

database/
├── migrations/
└── seeders/

routes/
├── web.php
└── auth.php

tests/
├── Unit/
├── Feature/
└── Integration/
```

**Structure Decision**: Monolithic Laravel app with domain modules under `app/Domain/`. Each domain (Auth, Organization, Authorization) follows internal structure: Models, Services, Events, Exceptions, Policies, DTOs. Frontend uses Vue 3 + Inertia for SPA-like experience.
