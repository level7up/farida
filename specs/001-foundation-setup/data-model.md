# Data Model: Foundation Setup

**Date**: 2026-09-08
**Feature**: 001-foundation-setup

## Entities

### User

**Purpose**: Authenticated person with email, password, and profile information

| Field | Type | Constraints | Notes |
|-------|------|-------------|-------|
| id | bigint | PK, auto-increment | |
| name | varchar(255) | required | Display name |
| email | varchar(255) | required, unique | Login identifier |
| email_verified_at | timestamp | nullable | Null = unverified |
| password | varchar(255) | required | bcrypt/argon2 hash |
| remember_token | varchar(100) | nullable | Remember me token |
| created_at | timestamp | required | |
| updated_at | timestamp | required | |

**State Transitions**:
- Unverified → Verified (email verification link clicked)
- Verified → Unverified (password reset requested, optional)

**Relationships**:
- hasMany Organization (via membership)
- hasMany Invitation (as inviter)

**Validation Rules**:
- email: required, email, max:255, unique:users,email
- password: required, min:8, confirmed

---

### Organization

**Purpose**: Tenant boundary representing a property management company

| Field | Type | Constraints | Notes |
|-------|------|-------------|-------|
| id | bigint | PK, auto-increment | |
| name | varchar(255) | required | Organization display name |
| slug | varchar(255) | required, unique | URL-friendly identifier |
| owner_id | bigint | FK → users.id | Organization creator |
| created_at | timestamp | required | |
| updated_at | timestamp | required | |

**Constraints**:
- name must be unique per owner (prevent duplicate orgs)
- slug must be globally unique (used in URLs)

**Relationships**:
- belongsTo User (owner)
- hasMany Membership
- hasMany Invitation

**Validation Rules**:
- name: required, max:255
- slug: required, max:255, alpha_dash, unique:organizations,slug

---

### Membership

**Purpose**: Association between User and Organization with role

| Field | Type | Constraints | Notes |
|-------|------|-------------|-------|
| id | bigint | PK, auto-increment | |
| user_id | bigint | FK → users.id | Member user |
| organization_id | bigint | FK → organizations.id | Tenant boundary |
| role | enum | required | Owner, Admin, Manager, Staff, Accountant, Cleaning |
| created_at | timestamp | required | |
| updated_at | timestamp | required | |

**Constraints**:
- Unique combination: user_id + organization_id (one membership per user per org)
- organization_id required (Constitution Principle I: tenant isolation)

**Relationships**:
- belongsTo User
- belongsTo Organization

**Validation Rules**:
- user_id: required, exists:users,id
- organization_id: required, exists:organizations,id
- role: required, in:Owner,Admin,Manager,Staff,Accountant,Cleaning

---

### Invitation

**Purpose**: Pending invitation to join an organization

| Field | Type | Constraints | Notes |
|-------|------|-------------|-------|
| id | bigint | PK, auto-increment | |
| email | varchar(255) | required | Invitee email |
| role | enum | required | Role to assign on acceptance |
| token | varchar(255) | required, unique | Hashed invitation token |
| organization_id | bigint | FK → organizations.id | Target organization |
| invited_by | bigint | FK → users.id | Who sent the invitation |
| expires_at | timestamp | required | Default: 7 days from creation |
| accepted_at | timestamp | nullable | Null = pending |
| created_at | timestamp | required | |
| updated_at | timestamp | required | |

**Constraints**:
- token must be unique (hashed, not plain text)
- expires_at must be in the future at creation
- One pending invitation per email per organization

**Relationships**:
- belongsTo Organization
- belongsTo User (invited_by)

**Validation Rules**:
- email: required, email, max:255
- role: required, in:Owner,Admin,Manager,Staff,Accountant,Cleaning
- organization_id: required, exists:organizations,id

---

## Entity Relationship Diagram

```
User ──┬── Membership ──── Organization
       │       │
       │       └── role (enum)
       │
       ├── Invitation ──── Organization
       │       │
       │       ├── email, role, token
       │       └── expires_at, accepted_at
       │
       └── (Platform Admin - separate guard, not in this model)
```

## Migration Order

Per implementation plan database migration order:

1. `users` (existing Laravel default)
2. `organizations`
3. `organization_user` (membership pivot table)
4. `invitations`

## Indexes

- `users`: email (unique)
- `organizations`: slug (unique), owner_id
- `organization_user`: user_id + organization_id (unique composite), organization_id
- `invitations`: token (unique), email + organization_id (unique composite for pending check)
