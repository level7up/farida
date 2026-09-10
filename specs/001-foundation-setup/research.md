# Research: Foundation Setup

**Date**: 2026-09-08
**Feature**: 001-foundation-setup

## Research Areas

### 1. Laravel Breeze Authentication

**Decision**: Use Laravel Breeze for authentication scaffolding

**Rationale**: Breeze provides complete auth scaffolding including registration, login, email verification, and password reset. It integrates with Inertia.js and Vue 3, matching the tech stack. More lightweight than Jetstream while covering all Phase 1 requirements.

**Alternatives Considered**:
- Laravel Jetstream: More features (teams, API) but heavier; teams feature partially overlaps with custom organization logic
- Custom auth: More control but reinvents wheel; Breeze covers all FR-001 to FR-005 requirements

### 2. Multi-Tenancy Approach

**Decision**: Shared database with organization_id foreign key (Constitution Principle I)

**Rationale**: Constitution mandates tenant isolation via organization_id on all tenant tables. Single database simplifies deployment and maintenance for initial SaaS launch. Can migrate to separate databases or schema-per-tenant later if needed.

**Alternatives Considered**:
- Database-per-tenant: Stronger isolation but higher operational complexity; overkill for initial launch
- Schema-per-tenant: PostgreSQL-specific; MySQL doesn't support schemas well
- Single database with row-level security: MySQL lacks native RLS; application-level enforcement required

### 3. Organization Roles & Permissions

**Decision**: Use spatie/laravel-permission with 6 organization roles

**Rationale**: Package provides battle-tested role/permission system. Six roles (Owner, Admin, Manager, Staff, Accountant, Cleaning) cover property management hierarchy. Platform admins use separate auth guard (per clarification).

**Alternatives Considered**:
- Custom RBAC: More control but reinvents wheel; spatie handles caching, hierarchical roles
- Bouncer: Lightweight but less maintained; spatie has larger community
- Entrust: Abandoned; not recommended

### 4. Platform Admin Guard Separation

**Decision**: Separate authentication guard for platform administrators

**Rationale**: Platform admins are not organization members; they need cross-tenant access for support and administration. Separate guard prevents accidental data leakage and maintains clean separation of concerns.

**Implementation**: Laravel supports multiple auth guards via config/auth.php. Platform admin guard uses its own model (PlatformAdmin) and session driver.

### 5. Email Queue Strategy

**Decision**: Queue emails with automatic retry (silent to user)

**Rationale**: Constitution requires server-side source of truth; email failures should not block user actions. Redis queue provides reliable delivery with retry logic. User sees success message; retry happens asynchronously.

**Implementation**: Laravel's built-in mail queue with Redis driver. Configure retry count (3 attempts) and failure logging.

### 6. Session Security

**Decision**: Rate limiting on login, session invalidation on password change, secure hashing

**Rationale**: Clarification specified standard security measures. Laravel provides built-in rate limiting (throttle middleware), session invalidation (Auth::logoutOtherDevices), and bcrypt/argon2 hashing.

**Implementation**:
- Rate limiting: `throttle:5,1` middleware on login route (5 attempts per minute)
- Session invalidation: `Auth::logoutOtherDevices($request->password)` in password change
- Password hashing: bcrypt via Hash::make() (Laravel default)

### 7. Organization Switching

**Decision**: Store current organization_id in session

**Rationale**: FR-010 requires organization switching. Session-based approach is simple and performant. User selects organization from dropdown; middleware loads organization context.

**Implementation**:
- Store `organization_id` in session after login/switch
- TenantContext middleware reads session and sets current organization
- All queries scope to session organization_id

### 8. Invitation Flow

**Decision**: Email-based invitations with token expiry

**Rationale**: Standard SaaS pattern. Invitations expire after 7 days (industry standard). Tokens are single-use and stored hashed in database.

**Implementation**:
- Invitation model with email, role, token (hashed), expires_at
- Invitation email with link to accept page
- Accept page creates user account if needed, then adds membership

## Summary

All technical decisions align with constitution principles and spec requirements. No NEEDS CLARIFICATION items remain. Ready for Phase 1 design.
