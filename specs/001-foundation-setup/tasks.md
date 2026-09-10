# Tasks: Foundation Setup

**Input**: Design documents from `/specs/001-foundation-setup/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are NOT explicitly requested in the feature specification. Optional test tasks are included for critical paths.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [ ] T001 Install Laravel 13 with PHP 8.3 and configure .env (DB, Redis, MAIL)
- [x] T002 Install Laravel Breeze with Inertia.js + Vue 3 scaffolding
- [x] T003 Install spatie/laravel-permission package
- [x] T004 [P] Configure Tailwind CSS and Vite build pipeline
- [x] T005 [P] Create domain module structure under app/Domain/ (Auth/, Organization/, Authorization/)
- [x] T006 [P] Configure Redis queue driver for email retries in config/queue.php
- [x] T007 [P] Setup Pest testing framework and phpunit.xml

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T008 Run users migration (existing Laravel default) in database/migrations/
- [x] T009 Create User model with fillable fields in app/Models/User.php
- [x] T010 Configure auth guards in config/auth.php (web + platform_admin)
- [x] T011 [P] Create TenantContext middleware to read organization_id from session in app/Http/Middleware/TenantContext.php
- [x] T012 [P] Create base Inertia layouts (Guest.vue, Authenticated.vue) in resources/js/Layouts/
- [x] T013 [P] Create Toast component for flash messages in resources/js/Components/Toast.vue
- [x] T014 [P] Configure session driver and organization_id storage in config/session.php

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - New User Registration (Priority: P1) 🎯 MVP

**Goal**: Users can register, verify email, login, and reset password

**Independent Test**: Register new account → verify email → login → access dashboard → reset password

### Implementation for User Story 1

- [x] T015 [P] [US1] Create registration page component in resources/js/Pages/Auth/Register.vue
- [x] T016 [P] [US1] Create login page component in resources/js/Pages/Auth/Login.vue
- [x] T017 [P] [US1] Create email verification page in resources/js/Pages/Auth/VerifyEmail.vue
- [x] T018 [P] [US1] Create forgot password page in resources/js/Pages/Auth/ForgotPassword.vue
- [x] T019 [P] [US1] Create reset password page in resources/js/Pages/Auth/ResetPassword.vue
- [x] T020 [US1] Implement RegisterController with validation in app/Domain/Auth/Controllers/RegisterController.php
- [x] T021 [US1] Implement LoginController with rate limiting (throttle:5,1) in app/Domain/Auth/Controllers/LoginController.php
- [x] T022 [US1] Implement ForgotPasswordController with queued email in app/Domain/Auth/Controllers/ForgotPasswordController.php
- [x] T023 [US1] Implement ResetPasswordController in app/Domain/Auth/Controllers/ResetPasswordController.php
- [x] T024 [US1] Implement EmailVerificationController in app/Domain/Auth/Controllers/EmailVerificationController.php
- [x] T025 [US1] Configure auth routes in routes/auth.php
- [x] T026 [US1] Create Dashboard page component in resources/js/Pages/Dashboard/Index.vue
- [x] T027 [US1] Implement session invalidation on password change (Auth::logoutOtherDevices)

**Checkpoint**: User Story 1 complete - users can register, verify, login, reset password

---

## Phase 4: User Story 2 - Organization Creation (Priority: P2)

**Goal**: Verified users can create organizations and establish tenant context

**Independent Test**: Create organization → verify it appears in list → confirm data scoping

### Implementation for User Story 2

- [x] T028 [P] [US2] Create organizations migration in database/migrations/xxxx_create_organizations_table.php
- [x] T029 [P] [US2] Create organization_user pivot migration in database/migrations/xxxx_create_organization_user_table.php
- [x] T030 [P] [US2] Create Organization model in app/Domain/Organization/Models/Organization.php
- [x] T031 [P] [US2] Create Membership model in app/Domain/Organization/Models/Membership.php
- [x] T032 [US2] Implement OrganizationService for create/switch logic in app/Domain/Organization/Services/OrganizationService.php
- [x] T033 [US2] Implement OrganizationPolicy for authorization in app/Domain/Organization/Policies/OrganizationPolicy.php
- [x] T034 [US2] Create organization creation page in resources/js/Pages/Organizations/Create.vue
- [x] T035 [US2] Create organization switcher component in resources/js/Components/Organizations/Switcher.vue
- [x] T036 [US2] Implement OrganizationController in app/Domain/Organization/Controllers/OrganizationController.php
- [x] T037 [US2] Implement switch action (POST /organizations/{id}/switch) in app/Domain/Organization/Controllers/OrganizationSwitchController.php
- [x] T038 [US2] Configure organization routes in routes/web.php
- [x] T039 [US2] Integrate TenantContext middleware with organization scoping

**Checkpoint**: User Story 2 complete - users can create and switch organizations

---

## Phase 5: User Story 3 - Team Invitation & Membership (Priority: P3)

**Goal**: Owners can invite team members, members can accept invitations

**Independent Test**: Invite member → accept invitation → verify membership with correct role

### Implementation for User Story 3

- [x] T040 [P] [US3] Create invitations migration in database/migrations/xxxx_create_invitations_table.php
- [x] T041 [P] [US3] Create Invitation model in app/Domain/Organization/Models/Invitation.php
- [x] T042 [US3] Implement InvitationService for create/accept logic in app/Domain/Organization/Services/InvitationService.php
- [x] T043 [US3] Implement InvitationPolicy for authorization in app/Domain/Organization/Policies/InvitationPolicy.php
- [x] T044 [US3] Create invitation list page in resources/js/Pages/Invitations/Index.vue
- [x] T045 [US3] Create invitation accept page in resources/js/Pages/Invitations/Accept.vue
- [x] T046 [US3] Implement InvitationController in app/Domain/Organization/Controllers/InvitationController.php
- [x] T047 [US3] Implement accept action (POST /invitations/{token}/accept) in app/Domain/Organization/Controllers/InvitationAcceptController.php
- [x] T048 [US3] Create invitation email template in resources/views/emails/invitation.blade.php
- [x] T049 [US3] Configure invitation routes in routes/web.php

**Checkpoint**: User Story 3 complete - invitations work end-to-end

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T050 [P] Configure email queue retry logic (3 attempts) in config/queue.php
- [x] T051 [P] Add audit logging for organization creation in app/Domain/Organization/Services/OrganizationService.php
- [x] T052 [P] Add audit logging for membership changes in app/Domain/Organization/Services/InvitationService.php
- [x] T053 [P] Configure rate limiting for invitation creation in routes/web.php
- [x] T054 [P] Add responsive design polish to all auth pages
- [x] T055 [P] Create seeders for default roles in database/seeders/RoleSeeder.php
- [x] T056 Run quickstart.md validation scenarios
- [x] T057 Run Pest test suite and verify all pass

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User Story 1 (P1): No dependencies on other stories
  - User Story 2 (P2): Depends on US1 (authenticated user needed)
  - User Story 3 (P3): Depends on US1 + US2 (org creation needed for invitations)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after US1 - Needs authenticated user to create org
- **User Story 3 (P3)**: Can start after US2 - Needs organization to invite members

### Within Each User Story

- Models before services
- Services before controllers
- Controllers before pages
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] can run in parallel (within Phase 2)
- Once Foundational phase completes, US1 and US2 can start in parallel (if team capacity allows)
- Models within a story marked [P] can run in parallel
- Auth pages (T015-T019) can all run in parallel
- Organization models (T028-T031) can all run in parallel
- Invitation models (T040-T041) can run in parallel

---

## Parallel Example: User Story 1

```bash
# Launch all auth pages together:
Task: "Create registration page component in resources/js/Pages/Auth/Register.vue"
Task: "Create login page component in resources/js/Pages/Auth/Login.vue"
Task: "Create email verification page in resources/js/Pages/Auth/VerifyEmail.vue"
Task: "Create forgot password page in resources/js/Pages/Auth/ForgotPassword.vue"
Task: "Create reset password page in resources/js/Pages/Auth/ResetPassword.vue"
```

## Parallel Example: User Story 2

```bash
# Launch all organization models together:
Task: "Create organizations migration in database/migrations/xxxx_create_organizations_table.php"
Task: "Create organization_user pivot migration in database/migrations/xxxx_create_organization_user_table.php"
Task: "Create Organization model in app/Domain/Organization/Models/Organization.php"
Task: "Create Membership model in app/Domain/Organization/Models/Membership.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1
4. **STOP and VALIDATE**: Test registration, login, email verification, password reset
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo
4. Add User Story 3 → Test independently → Deploy/Demo
5. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: User Story 1 (Auth)
   - Developer B: User Story 2 (Organizations)
   - Developer C: User Story 3 (Invitations)
3. Stories complete and integrate independently

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
