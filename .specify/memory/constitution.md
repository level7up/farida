<!-- Sync Impact Report
Version Change: 0.0.0 → 1.0.0
Modified Principles: Initial creation from IMPLEMENTATION_PLAN
Added Sections: Core Principles (6), Tech Stack, Development Workflow, Critical Rules, Governance
Removed Sections: None
Follow-up TODOs: None
-->

# Farida Constitution

## Core Principles

### I. Tenant Isolation (NON-NEGOTIABLE)

Every tenant table MUST include `organization_id` as a foreign key. All queries MUST be scoped to the current organization context. Never trust browser-provided organization IDs — always resolve from authenticated session. Tenant boundaries are enforced via middleware and query scopes, not just code organization.

**Rationale**: Prevents cross-tenant data leakage in a multi-tenant SaaS application where organizations share the same database.

### II. Server is Source of Truth

All money calculations and availability checks MUST happen server-side. No Vue-side or client-side price calculations. Frontend displays data but never computes financial or booking-critical logic.

**Rationale**: Prevents price manipulation, ensures consistency, and maintains audit integrity for financial operations.

### III. Append-Only Financial Records

Payment amounts MUST NOT be edited after creation. Use refund records to correct errors. All financial mutations are recorded immutably.

**Rationale**: Maintains financial audit trail integrity and enables accurate reconciliation and reporting.

### IV. Transactional Booking Creation

Booking creation MUST use database transactions with locking to prevent double-bookings. Validate availability within the transaction before committing.

**Rationale**: Prevents race conditions where multiple users could book the same property for overlapping dates simultaneously.

### V. Thin Controllers, Fat Services

Business logic MUST reside in service classes, not controllers. Controllers handle HTTP concerns only. Services contain domain logic and coordinate between models, policies, and external systems.

**Rationale**: Improves testability, reusability, and maintainability by separating concerns cleanly.

### VI. Audit Trail for Mutations

Important mutations (bookings, payments, property changes) MUST be logged with user, organization, action, entity, old/new values, IP, and user agent. Audit logs are append-only and tamper-resistant.

**Rationale**: Enables accountability, debugging, compliance, and forensic analysis of system changes.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Vue 3 + Inertia + TypeScript |
| Styling | Tailwind CSS |
| Database | MySQL |
| Cache/Queue | Redis |
| Storage | S3-compatible (MinIO locally) |
| Auth | Laravel Breeze/Sanctum |
| Authorization | spatie/laravel-permission |
| Testing | Pest |
| Build | Vite |

## Development Workflow

Build vertically — each feature is built end-to-end before moving to the next:

1. Migration
2. Model
3. Policy
4. Service
5. Request validation
6. Controller
7. Inertia page
8. Vue components
9. Tests
10. Audit/event handling

This keeps every milestone usable and reduces unfinished modules.

## Critical Rules

1. Tenant isolation is mandatory — `organization_id` on all tenant tables
2. Server is source of truth for money and availability
3. Financial records are append-only — use refunds, never edit
4. Booking creation is transactional — DB locks prevent double bookings
5. Business logic in services, not controllers
6. Audit trail for important mutations

## Governance

This constitution supersedes all other development practices. Amendments require:
- Documentation of the change rationale
- Version bump following semantic versioning (MAJOR for principle removals, MINOR for additions, PATCH for clarifications)
- Migration plan for existing code

All PRs and code reviews MUST verify compliance with these principles. Complexity MUST be justified. Use `docs/IMPLEMENTATION_PLAN.md` for runtime development guidance.

**Version**: 1.0.0 | **Ratified**: 2026-09-08 | **Last Amended**: 2026-09-08
