# Quickstart Validation: Foundation Setup

**Date**: 2026-09-08
**Feature**: 001-foundation-setup

## Prerequisites

- PHP 8.3+ installed
- MySQL 8.0+ running
- Redis running
- Node.js 18+ installed
- Composer installed

## Setup Commands

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Set database credentials in .env
# DB_DATABASE=farida
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

## Validation Scenarios

### Scenario 1: User Registration Flow

**Test**: New user can register and verify email

**Steps**:
1. Navigate to http://localhost:8000/register
2. Fill registration form (name, email, password, password_confirmation)
3. Submit form
4. Check email for verification link
5. Click verification link
6. Login with credentials

**Expected Outcome**:
- User account created with unverified status
- Verification email sent
- After clicking link, email_verified_at is set
- User can login and access dashboard

**Validation Command**:
```bash
php artisan tinker --execute="App\Models\User::latest()->first()"
```

---

### Scenario 2: Login Security

**Test**: Rate limiting and session security

**Steps**:
1. Navigate to http://localhost:8000/login
2. Attempt login with wrong password 5 times rapidly
3. Verify rate limit message appears
4. Login with correct credentials
5. Change password in profile
6. Verify other sessions are invalidated

**Expected Outcome**:
- After 5 failed attempts, user is rate limited
- Password change logs out other devices
- Session invalidation works correctly

**Validation Command**:
```bash
php artisan tinker --execute="Auth::user()->sessions()->count()"
```

---

### Scenario 3: Organization Creation

**Test**: Verified user can create organization

**Steps**:
1. Login as verified user
2. Navigate to http://localhost:8000/organizations/create
3. Fill organization name
4. Submit form
5. Verify redirect to dashboard
6. Verify organization appears in switcher

**Expected Outcome**:
- Organization created with user as Owner
- User becomes member with Owner role
- Dashboard shows organization context

**Validation Command**:
```bash
php artisan tinker --execute="App\Models\Organization::with('members')->first()"
```

---

### Scenario 4: Team Invitation

**Test**: Owner can invite team members

**Steps**:
1. Login as organization Owner
2. Navigate to invitations page
3. Click "Invite Member"
4. Enter email and select role (e.g., Manager)
5. Submit invitation
6. Check invitee email for invitation
7. Invitee clicks link and accepts

**Expected Outcome**:
- Invitation created with token and expiry
- Invitation email sent
- Invitee can accept and becomes member with assigned role

**Validation Command**:
```bash
php artisan tinker --execute="App\Models\Invitation::latest()->first()"
```

---

### Scenario 5: Organization Switching

**Test**: User with multiple organizations can switch

**Steps**:
1. Login as user with 2+ organizations
2. Click organization switcher in top bar
3. Select different organization
4. Verify page refreshes with new context

**Expected Outcome**:
- Session updates with new organization_id
- All data queries scope to new organization
- UI reflects current organization

**Validation Command**:
```bash
php artisan tinker --execute="session('organization_id')"
```

---

### Scenario 6: Tenant Isolation

**Test**: Data is scoped to organization

**Steps**:
1. Login as user in Organization A
2. Create some data (will be Phase 2+, but verify query scoping)
3. Switch to Organization B
4. Verify data from Organization A is not visible

**Expected Outcome**:
- All queries include organization_id filter
- No cross-tenant data leakage

**Validation Command**:
```bash
# Verify middleware is applied
php artisan route:list --middleware=tenant
```

---

## Test Commands

```bash
# Run all tests
php artisan test

# Run feature tests for auth
php artisan test --filter=Auth

# Run feature tests for organizations
php artisan test --filter=Organization

# Run unit tests
php artisan test --unit

# Run specific test file
php artisan test tests/Feature/Auth/RegistrationTest.php
```

## Success Criteria Validation

| Criterion | Command | Expected |
|-----------|---------|----------|
| SC-001: Registration <3min | Manual timing | Pass |
| SC-002: Password reset <2min | Manual timing | Pass |
| SC-003: Org creation <1min | Manual timing | Pass |
| SC-004: Invitation acceptance >80% | Analytics | Pass |
| SC-005: 100 concurrent registrations | Load test | Pass |
| SC-006: No cross-tenant leakage | Security test | Pass |

## Troubleshooting

### Email not sending
- Check MAIL_MAILER in .env is set to smtp or log
- For development, use `MAIL_MAILER=log` to log emails to storage/logs

### Migration errors
- Run `php artisan migrate:fresh` to reset (WARNING: destroys data)
- Ensure database exists and credentials are correct

### Queue not processing
- Start queue worker: `php artisan queue:work`
- Check Redis is running: `redis-cli ping`
