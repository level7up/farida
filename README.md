# Farida — Implementation Plan

## Architecture Decision

**Monolithic Laravel app** with domain modules under `app/Domain/`. Single database, single deployment. Domain boundaries are enforced via code organization, not separate services. This can be split into microservices later if scaling demands it.

---

## Tech Stack

| Layer         | Technology                    |
| ---------------| -------------------------------|
| Backend       | Laravel 13, PHP 8.3           |
| Frontend      | Vue 3 + Inertia + TypeScript  |
| Styling       | Tailwind CSS                  |
| Database      | MySQL                         |
| Cache/Queue   | Redis                         |
| Storage       | S3-compatible (MinIO locally) |
| Auth          | Laravel Breeze/Sanctum        |
| Authorization | spatie/laravel-permission     |
| Testing       | Pest                          |
| Build         | Vite                          |

---

## Domain Structure

```
app/Domain/
├── Auth/              # Registration, login, email verification
├── Organization/      # Tenants, memberships, invitations
├── Authorization/     # Roles, permissions, policies
├── Property/          # Properties, units, amenities, media
├── Pricing/           # Rate plans, seasonal pricing, fees, discounts
├── Booking/           # Availability, bookings, lifecycle, calendar
├── Guest/             # Guest profiles, history
├── Payment/           # Payments, refunds
├── Expense/           # Expenses, categories
├── Operations/        # Cleaning, maintenance, staff tasks
├── Owner/             # Owners, settlements, statements
├── Notification/      # Email, database notifications
└── Audit/             # Audit logs
```

Each domain follows this internal structure:

```
Domain/
├── Models/            # Eloquent models (relationships, scopes, casts)
├── Services/          # Business logic (thin controllers, fat services)
├── Events/            # Domain events
├── Exceptions/        # Domain exceptions
├── Policies/          # Authorization policies
└── DTOs/              # Data transfer objects (for complex inputs/outputs)
```

---

## Implementation Phases

### Phase 1 — Foundation (Days 1-3)

#### Day 1: Project Setup

- Install dependencies (Inertia, Vue, Tailwind, Breeze)
- Configure MySQL, Redis
- Set up Docker (if needed)
- Base layout, navigation, error handling

#### Day 2: Authentication

- Registration, login, logout
- Email verification
- Password reset
- Flash messages, toast system

#### Day 3: Organization & Tenancy

- Organizations migration/model
- Organization membership (`organization_user`)
- Tenant context middleware
- Organization switcher

---

### Phase 2 — Core Domain (Days 4-7)

#### Day 4: Authorization

- Roles & permissions (use `spatie/laravel-permission`)
- Policies for each domain
- Tenant isolation enforcement
- Organization roles: Owner, Admin, Manager, Staff, Accountant, Cleaning

#### Day 5: Onboarding & Settings

- Onboarding wizard (create org, first property)
- Organization settings
- Team invitation flow

#### Day 6: Properties

- Property CRUD (migration, model, policy, service, controller, Inertia page)
- Property types: Villa, Apartment, Chalet, Compound, Hotel
- Fields: name, type, address, governorate, city, check-in/out times, status

#### Day 7: Units

- Unit CRUD
- Fields: name, code, type, bedrooms, beds, bathrooms, max_guests, floor, status
- Property → Units relationship
- Unit status management

---

### Phase 3 — Pricing & Availability (Days 8-11)

#### Day 8: Amenities & Media

- Amenities (database-driven, not hard-coded)
- Media/uploads (S3/MinIO, organized by org/property/unit)
- Property & unit images

#### Day 9: Pricing Engine

- Rate plans
- Seasonal pricing
- Extra charges (cleaning, service, taxes)
- Discounts
- `PricingService` — server-side price calculation with breakdown

#### Day 10: Availability & Calendar

- Availability blocks (blocked, maintenance, owner_use)
- Calendar UI (month, week, unit-by-unit views)
- Overlap detection
- Drag-and-drop (validated server-side)

#### Day 11: Calendar Enhancement

- Unit timeline view
- Status indicators (available, booked, blocked, cleaning)
- Quick actions (block dates, create booking)

---

### Phase 4 — Bookings & Guests (Days 12-15)

#### Day 12: Booking Engine

- Booking CRUD with transactional creation
- Double-booking prevention (DB locks + validation)
- Booking states: inquiry → pending → confirmed → checked_in → checked_out → cancelled
- Booking sources: direct, airbnb, whatsapp, phone, walk_in

#### Day 13: Booking Flow

- 12-step booking creation flow
- `BookingService`, `AvailabilityService` coordination
- Price breakdown display
- Guest selection during booking

#### Day 14: Guests

- Guest profiles
- Booking history
- Contact details, notes
- Guest search

#### Day 15: Booking Management

- Booking details page
- Check-in/check-out actions
- Booking modifications
- Cancellation flow

---

### Phase 5 — Finance & Operations (Days 16-19)

#### Day 16: Payments

- Payment recording (cash, bank transfer, card, online)
- Payment history per booking
- Refunds
- Outstanding balances
- Financial audit trail (append-only)

#### Day 17: Expenses

- Expense categories (electricity, water, internet, maintenance, cleaning, etc.)
- Expense CRUD
- Property/unit association
- Attachment support

#### Day 18: Operations

- Cleaning tasks (auto-generated after checkout)
- Maintenance tasks
- Staff assignments
- Cleaning states: pending → assigned → in_progress → completed → inspected

#### Day 19: Owners

- Owner profiles
- Owner-property relationships
- Revenue calculation, commissions
- Owner settlements & statements

---

### Phase 6 — Dashboard & Notifications (Days 20-22)

#### Day 20: Dashboard

- Today's check-ins/check-outs
- Occupancy rate
- Revenue metrics
- Upcoming bookings
- Cleaning task queue
- Charts (revenue by month, bookings, occupancy)

#### Day 21: Reports

- Revenue reports
- Occupancy reports
- Booking source analysis
- Expense reports
- Owner statements

#### Day 22: Notifications

- Email notifications (booking confirmation, check-in reminder, checkout reminder)
- Database notifications
- Automated messages
- Notification preferences

---

### Phase 7 — SaaS & Polish (Days 23-25)

#### Day 23: SaaS Billing

- Plans (Starter, Professional, Business)
- Plan features & limits
- Subscription management
- Feature enforcement via `SubscriptionService`

#### Day 24: Audit Logs

- Audit log model & migration
- Track all important mutations
- User, organization, action, entity, old/new values, IP, user agent
- Audit log viewer

#### Day 25: Testing & Polish

- Unit tests for pricing, availability, booking logic
- Feature tests for tenant isolation, CRUD, permissions
- Concurrency tests (double-booking prevention)
- Cross-tenant security tests

---

## Critical Rules

1. **Tenant isolation is mandatory** — `organization_id` on all tenant tables, never trust browser-provided org ID
2. **Server is source of truth** for money and availability — no Vue-side calculations
3. **Financial records are append-only** — use refunds, never edit payment amounts
4. **Booking creation is transactional** — DB locks to prevent double bookings
5. **Business logic in services, not controllers** — controllers stay thin
6. **Audit trail for important mutations** — every booking, payment, property change

---

## Database Migration Order

Follow this exact order:

1. users
2. organizations
3. organization_user
4. roles, permissions, role_user
5. plans, plan_features, subscriptions
6. properties, units
7. amenities, amenity_property, amenity_unit
8. media
9. rate_plans, rate_plan_prices, seasonal_prices, extra_charges
10. guests
11. availability_blocks
12. bookings, booking_nights, booking_fees, booking_discounts
13. payments, refunds
14. expense_categories, expenses
15. cleaning_tasks, maintenance_tasks, staff_assignments
16. owners, owner_properties, owner_statements, owner_settlements
17. notifications, audit_logs

---

## Packages to Install

```bash
# Auth & Permissions
composer require laravel/breeze
composer require spatie/laravel-permission

# Frontend
npm install @inertiajs/vue3 vue @vitejs/plugin-vue
npm install -D typescript @types/node

# Utilities
composer require league/flysystem-aws-s3-v3
composer require intervention/image

# Testing
composer require pestphp/pest
```

---

## Definition of Done (MVP)

The app is production-ready when:

- [ ] Company can register, create org, invite employees
- [ ] Permissions work (Owner/Admin/Manager/Staff/Accountant/Cleaning)
- [ ] Properties and units can be created and managed
- [ ] Property images can be uploaded
- [ ] Prices can be configured (nightly, weekly, monthly, seasonal)
- [ ] Calendar shows availability visually
- [ ] Bookings can be created with double-booking prevention
- [ ] Guests can be managed
- [ ] Payments can be recorded with audit trail
- [ ] Expenses can be recorded
- [ ] Cleaning tasks auto-generate after checkout
- [ ] Dashboard metrics are correct
- [ ] Notifications work
- [ ] Audit logs capture important actions
- [ ] Tenant isolation is tested
- [ ] SaaS subscription limits are enforced
- [ ] Automated tests cover booking/pricing/payment logic

---

## Build Strategy

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

---

## MVP Scope — What NOT to Build Initially

- Airbnb/Booking.com integrations
- Public marketplace
- Mobile application
- Dynamic pricing AI
- Advanced accounting
- Custom domains
- Channel manager

These come after the core PMS proves the workflow.
