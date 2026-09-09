# Short-Term Rental SaaS — Implementation Plan

## 1. Product Vision

Build a multi-tenant SaaS for the Egyptian short-term rental market.

Core model:

- Property owners / managers create an account.
- Each account/company becomes a tenant.
- Tenants manage properties, units, calendars, availability, pricing, bookings, guests, payments, cleaning, expenses, and owner settlements.
- Optional marketplace functionality can be added later.
- The first release should prioritize the **management SaaS** rather than trying to build Airbnb + PMS simultaneously.

Target stack:

- Backend: Laravel
- Frontend: Vue 3 + Inertia
- Styling: Tailwind CSS
- Database: MySQL
- Queue/cache: Redis
- Storage: S3-compatible storage (MinIO locally, S3-compatible production)
- Authentication: Laravel authentication + email verification
- Authorization: Policies / roles / permissions
- API: Laravel API endpoints where integrations/mobile clients require them
- Testing: Pest/PHPUnit
- Deployment: Docker + Nginx + PHP-FPM + Supervisor

---

# 2. MVP Scope

## Phase 1 — SaaS Foundation

### Tenant / organization

Entities:

- users
- organizations
- organization_user
- roles
- permissions
- subscriptions
- plans

Requirements:

- User registration/login/logout.
- Email verification.
- Create organization during onboarding.
- Invite team members.
- Organization switcher if a user belongs to multiple organizations.
- Strict tenant isolation.
- Organization-level roles:
  - Owner
  - Admin
  - Manager
  - Staff
  - Accountant
  - Cleaning staff

Every business-owned record must be tenant-scoped.

Recommended approach:

- `organization_id` on all tenant-owned tables.
- Global tenant scope or explicit organization repository/service layer.
- Policies must verify organization ownership.
- Never trust organization IDs coming from the browser.

---

# 3. Subscription / SaaS Billing

Plans should initially be configurable from the database.

Example plans:

### Starter

- 1–5 units
- Basic bookings
- Basic calendar
- Basic reports

### Professional

- More units
- Team members
- Advanced reports
- Automated messages
- Expenses
- Owner settlements

### Business

- Unlimited / high unit limit
- Multiple users
- Advanced integrations
- API
- Channel management
- Automation

Tables:

- plans
- plan_features
- subscriptions
- subscription_items
- invoices
- payments

Implement feature limits through a centralized service:

`SubscriptionService`

Examples:

- max_properties
- max_units
- max_users
- max_bookings_per_month
- channel_integrations
- automated_messages

Do not hard-code plan limits inside controllers.

---

# 4. Property Management

## Property

A property is the physical real-estate container.

Examples:

- Villa
- Apartment building
- Chalet
- Compound unit
- Hotel apartment

Fields:

- id
- organization_id
- name
- property_type
- description
- address
- country
- governorate
- city
- area
- latitude
- longitude
- check_in_time
- check_out_time
- status
- notes

## Unit

A property can contain one or many rentable units.

Fields:

- id
- organization_id
- property_id
- name
- code
- unit_type
- bedrooms
- beds
- bathrooms
- max_guests
- floor
- size
- status
- description

Examples:

Property: "Marassi Building A"

Units:

- A101
- A102
- A103

For a standalone villa, the property can contain one unit.

---

# 5. Amenities

Tables:

- amenities
- amenity_unit
- amenity_property

Examples:

- Wi-Fi
- Air conditioning
- TV
- Washing machine
- Kitchen
- Parking
- Pool
- Sea view
- Balcony

Amenities should be database-driven rather than hard-coded.

---

# 6. Images / Media

Use Laravel filesystem abstraction.

Tables:

- media

Media should support:

- property images
- unit images
- ID/document attachments where appropriate
- invoices/receipts
- cleaning photos

Recommended structure:

`organizations/{organization_id}/properties/{property_id}/...`

Never store raw base64 images in database columns.

---

# 7. Pricing

Create a flexible pricing engine.

Tables:

- rate_plans
- rate_plan_prices
- seasonal_prices
- unit_price_overrides
- extra_charges
- discounts

Basic pricing model:

`unit + date range + price`

Support:

- nightly price
- weekly price
- monthly price
- weekend price
- seasonal price
- minimum stay
- maximum stay
- extra guest fee
- cleaning fee
- service fee
- taxes
- discounts

Do not calculate booking totals only in Vue.

The server must be the source of truth.

Create:

`PricingService`

Responsibilities:

- Calculate nightly rates.
- Apply seasonal overrides.
- Apply discounts.
- Add fees.
- Add taxes.
- Calculate final booking amount.
- Return a detailed price breakdown.

---

# 8. Availability / Calendar

This is one of the most important modules.

Calendar must display:

- available
- reserved
- blocked
- cleaning
- maintenance
- check-in
- check-out

Tables:

- availability_blocks
- bookings

Availability blocks:

- unit_id
- start_date
- end_date
- type
- reason

Types:

- blocked
- maintenance
- owner_use
- other

Never allow overlapping confirmed bookings for the same unit.

Use database transactions and locking when creating a booking.

---

# 9. Booking Engine

Booking states:

- inquiry
- pending
- confirmed
- checked_in
- checked_out
- cancelled
- no_show

Booking fields:

- id
- organization_id
- booking_number
- source
- guest_id
- unit_id
- check_in
- check_out
- guests_count
- adults
- children
- nights
- subtotal
- discounts
- fees
- taxes
- total
- paid_amount
- due_amount
- currency
- status
- notes

Booking sources:

- direct
- Airbnb
- Booking.com
- WhatsApp
- phone
- walk_in
- website
- other

Booking items:

- booking_nights
- booking_fees
- booking_discounts
- booking_payments

Keep financial calculations auditable.

---

# 10. Guest Management

Tables:

- guests
- guest_contacts
- guest_notes
- guest_documents

Guest profile:

- name
- phone
- email
- country
- address
- notes

Do not collect sensitive identification information unless legally necessary.

If identity documents are required later, treat them as a separate security-sensitive module with strict permissions, encrypted storage where appropriate, and retention rules.

Guest history should show:

- previous bookings
- total stays
- total spending
- cancellations
- notes

---

# 11. Payments

Payment methods:

- cash
- bank transfer
- card
- online payment
- other

Tables:

- payments
- payment_methods
- refunds

Every payment must reference:

- organization
- booking
- amount
- currency
- method
- status
- transaction/reference number
- paid_at
- created_by

Payment states:

- pending
- completed
- failed
- refunded
- partially_refunded

Never edit historical payment amounts directly.

Use refunds / adjustments.

---

# 12. Expenses

Expenses are important for property managers.

Tables:

- expense_categories
- expenses

Examples:

- electricity
- water
- internet
- maintenance
- cleaning
- supplies
- commission
- property management
- other

Expense fields:

- property_id
- unit_id nullable
- category_id
- amount
- date
- description
- attachment
- created_by

---

# 13. Owner Management

A manager may manage properties owned by different owners.

Tables:

- owners
- owner_properties
- owner_statements
- owner_settlements

Owner dashboard:

- properties
- bookings
- revenue
- expenses
- commissions
- net amount
- paid amount
- outstanding amount

Example:

Booking revenue: 10,000 EGP

Platform/manager commission: 1,500 EGP

Expenses: 500 EGP

Owner net: 8,000 EGP

Do not mix owner accounting with SaaS subscription billing.

These are two separate financial domains.

---

# 14. Cleaning / Operations

Tables:

- cleaning_tasks
- maintenance_tasks
- staff_assignments

Cleaning task states:

- pending
- assigned
- in_progress
- completed
- inspected

Automatically generate cleaning tasks after checkout.

Example:

Checkout: 12 Sep 2026

Cleaning:

12 Sep 2026

Unit becomes ready only after cleaning is completed.

Later add:

- cleaning checklist
- before/after photos
- inventory checklist
- damage reports

---

# 15. Dashboard

Tenant dashboard should show:

- today's check-ins
- today's check-outs
- current guests
- occupancy
- revenue
- outstanding payments
- upcoming bookings
- cleaning tasks
- maintenance tasks

Charts:

- revenue by month
- bookings by month
- occupancy
- revenue by property
- booking source
- cancellation rate

Dashboard queries must be optimized.

Do not load all bookings and calculate statistics in Vue.

---

# 16. Calendar UI

Use a calendar component compatible with Vue.

Views:

- month
- week
- timeline
- unit-by-unit

Example:

| Unit | 10 Sep | 11 Sep | 12 Sep | 13 Sep |
|---|---|---|---|---|
| A101 | Available | Booking | Booking | Available |
| A102 | Booking | Booking | Cleaning | Available |
| Villa 1 | Blocked | Blocked | Available | Booking |

Actions:

- create booking
- move booking
- block dates
- unblock dates
- open booking
- open guest
- create cleaning task

Drag-and-drop operations must be validated server-side.

---

# 17. Booking Creation Flow

Recommended flow:

1. Select property.
2. Select unit.
3. Select check-in/check-out.
4. Server checks availability.
5. Server calculates price.
6. Show price breakdown.
7. Select/create guest.
8. Select payment method.
9. Create booking inside transaction.
10. Create payment if applicable.
11. Create audit log.
12. Trigger notifications/events.

Use domain/service classes:

- `AvailabilityService`
- `PricingService`
- `BookingService`
- `PaymentService`

Controllers should remain thin.

---

# 18. Notifications

Start with:

- email
- database notifications

Later:

- WhatsApp
- SMS
- push notifications

Events:

- BookingCreated
- BookingConfirmed
- BookingCancelled
- GuestCheckedIn
- GuestCheckedOut
- PaymentReceived
- CleaningRequired

Listeners:

- send confirmation
- create cleaning task
- update analytics
- send owner notification

Use queues for external notifications.

---

# 19. Audit Logs

Every important operation should be auditable.

Tables:

- audit_logs

Track:

- user
- organization
- action
- entity type
- entity ID
- old values
- new values
- IP
- user agent
- timestamp

Important actions:

- booking created
- booking modified
- booking cancelled
- payment created
- refund created
- property changed
- price changed
- user invited
- permission changed

Never rely only on Laravel application logs for business auditing.

---

# 20. Marketplace — Phase 2

Do NOT build the public marketplace first.

After the management SaaS is stable, add:

Public property pages:

`/stays/{slug}`

Search:

- location
- dates
- guests
- price
- property type
- amenities

Booking:

- guest selects dates
- availability check
- price calculation
- checkout
- payment
- confirmation

Marketplace tables can extend the existing property/unit model.

Add:

- listing_status
- published_at
- public_slug
- public_description
- ranking metadata

---

# 21. Channel Manager — Phase 3

Later integrate:

- Airbnb
- Booking.com
- Expedia
- Vrbo
- direct booking website

Create abstraction:

`ChannelProviderInterface`

Example:

- `AirbnbChannelProvider`
- `BookingComChannelProvider`

Operations:

- import reservations
- export availability
- export prices
- sync reservation changes
- sync cancellations

Tables:

- channels
- channel_connections
- channel_listings
- channel_reservations
- channel_sync_logs

Never couple booking logic directly to Airbnb/Booking APIs.

---

# 22. Public Booking Website

Each organization may eventually have:

`organization-slug.yourdomain.com`

or:

`yourdomain.com/stays/organization-slug`

Features:

- property listing
- unit details
- gallery
- amenities
- availability
- pricing
- booking
- guest checkout
- payment
- confirmation

Later support custom domains.

---

# 23. Database Architecture

Core tables:

### SaaS

- users
- organizations
- organization_user
- plans
- plan_features
- subscriptions
- invoices
- subscription_payments

### Property

- properties
- units
- amenities
- amenity_property
- amenity_unit
- media

### Pricing

- rate_plans
- rate_plan_prices
- seasonal_prices
- unit_price_overrides
- extra_charges
- discounts

### Booking

- bookings
- booking_nights
- booking_fees
- booking_discounts
- booking_payments
- availability_blocks

### Guest

- guests
- guest_contacts
- guest_notes
- guest_documents

### Operations

- cleaning_tasks
- maintenance_tasks
- staff_assignments

### Finance

- payments
- refunds
- expense_categories
- expenses

### Owner

- owners
- owner_properties
- owner_statements
- owner_settlements

### System

- notifications
- audit_logs
- failed_jobs
- jobs
- cache

---

# 24. Laravel Structure

Recommended application structure:

`app/Domain/`

- SaaS/
- Organization/
- Property/
- Booking/
- Pricing/
- Guest/
- Payment/
- Expense/
- Owner/
- Operations/
- Notification/
- Integration/

Example:

`app/Domain/Booking/Services/BookingService.php`

`app/Domain/Booking/Services/AvailabilityService.php`

`app/Domain/Pricing/Services/PricingService.php`

`app/Domain/Payment/Services/PaymentService.php`

Avoid putting business logic into:

- Controllers
- Vue components
- Eloquent models

Models should contain relationships, casts, scopes, and small domain behavior.

---

# 25. Inertia + Vue Structure

Suggested:

`resources/js/`

- Pages/
  - Auth/
  - Onboarding/
  - Dashboard/
  - Properties/
  - Units/
  - Calendar/
  - Bookings/
  - Guests/
  - Payments/
  - Expenses/
  - Owners/
  - Operations/
  - Settings/
- Components/
  - UI/
  - Forms/
  - Tables/
  - Calendar/
  - Booking/
- Layouts/
  - AppLayout.vue
  - AuthLayout.vue
  - PublicLayout.vue
- Composables/
  - usePermissions.ts
  - useBooking.ts
  - usePricing.ts
  - useTenant.ts
- Types/

Use TypeScript if possible.

---

# 26. Tailwind UI

Create a small internal design system.

Components:

- Button
- Input
- Select
- DatePicker
- Modal
- Drawer
- Dropdown
- Badge
- Table
- Pagination
- Card
- StatCard
- EmptyState
- Alert
- Toast
- ConfirmDialog

Do not repeatedly write large Tailwind class strings throughout pages.

Create reusable UI components.

---

# 27. Authorization

Permission examples:

- properties.view
- properties.create
- properties.update
- properties.delete

- bookings.view
- bookings.create
- bookings.update
- bookings.cancel

- payments.view
- payments.create
- payments.refund

- expenses.view
- expenses.create

- owners.view
- owners.manage

- team.view
- team.invite
- team.manage

Use policies plus permissions.

Frontend permissions are for UX only.

Backend authorization is mandatory.

---

# 28. Multi-Tenancy Security

This is critical.

Every request must have a tenant context.

Example:

`TenantContext`

Responsibilities:

- current organization
- current user membership
- current permissions

Never accept:

`organization_id`

from the client as authority.

For example, avoid:

`Booking::where('organization_id', $request->organization_id)`

Prefer resolving organization from authenticated tenant context.

All queries must be tenant-safe.

Add automated tests specifically for cross-tenant access.

---

# 29. Routes

Example:

`/app`

- `/app/dashboard`
- `/app/properties`
- `/app/units`
- `/app/calendar`
- `/app/bookings`
- `/app/guests`
- `/app/payments`
- `/app/expenses`
- `/app/owners`
- `/app/operations`
- `/app/reports`
- `/app/settings`

Admin SaaS:

`/admin`

- `/admin/organizations`
- `/admin/users`
- `/admin/plans`
- `/admin/subscriptions`
- `/admin/invoices`
- `/admin/system`

Public:

`/`
`/stays/{slug}`
`/booking/{bookingNumber}`

---

# 30. API

Version from day one:

`/api/v1`

Use API resources.

Potential endpoints:

- `GET /api/v1/properties`
- `POST /api/v1/properties`
- `GET /api/v1/units`
- `GET /api/v1/calendar`
- `POST /api/v1/bookings`
- `GET /api/v1/bookings/{booking}`
- `POST /api/v1/bookings/{booking}/cancel`
- `POST /api/v1/bookings/{booking}/payments`

Do not build every endpoint initially.

Build API endpoints only where integration/mobile/public booking needs them.

---

# 31. Queue / Jobs

Use Redis queues.

Jobs:

- SendBookingConfirmation
- SendCheckInReminder
- SendCheckoutReminder
- GenerateCleaningTask
- ProcessChannelSync
- GenerateOwnerStatement
- GenerateInvoice
- ProcessWebhook

Configure retries and idempotency.

External webhooks must be idempotent.

---

# 32. Scheduled Tasks

Examples:

Every minute:

- process scheduled notifications

Every 5 minutes:

- channel synchronization
- failed sync retry

Daily:

- upcoming check-in reminders
- upcoming checkout reminders
- owner statement generation

Monthly:

- subscription billing
- monthly reports

Use Laravel Scheduler.

---

# 33. Testing Strategy

Tests are mandatory for financial and availability logic.

### Unit tests

- price calculation
- seasonal pricing
- discounts
- fees
- taxes
- nights calculation
- commission calculation

### Feature tests

- tenant isolation
- booking creation
- booking cancellation
- payment creation
- refund
- property CRUD
- user permissions

### Concurrency tests

Specifically test:

Two users attempting to book the same unit for the same dates.

Expected:

Only one booking succeeds.

---

# 34. Performance

Indexes:

- organization_id
- property_id
- unit_id
- guest_id
- booking status
- check_in
- check_out
- created_at

Composite indexes:

`organization_id + check_in`

`organization_id + check_out`

`unit_id + check_in + check_out`

Do not over-index before measuring.

Use eager loading.

Use pagination.

Use Redis caching for:

- plan features
- amenities
- dashboard aggregates
- frequently requested availability

---

# 35. Security

Implement:

- CSRF
- rate limiting
- email verification
- password reset
- 2FA later
- authorization policies
- tenant isolation
- secure file uploads
- MIME validation
- signed URLs for private files
- webhook signature validation
- audit logs
- secure session configuration

Never store:

- raw card numbers
- CVV
- payment credentials

Use payment providers for card handling.

---

# 36. Observability

Production should include:

- application logs
- queue monitoring
- failed jobs
- slow query monitoring
- exception tracking
- audit logs
- channel sync logs

Recommended later:

- Laravel Horizon
- Sentry
- uptime monitoring

---

# 37. Development Milestones

## Milestone 1 — Project Bootstrap

Deliver:

- Laravel project
- Vue + Inertia
- Tailwind
- MySQL
- Redis
- Docker
- authentication
- basic layout
- CI
- testing setup

Acceptance:

User can register/login and access the application.

---

## Milestone 2 — SaaS / Tenant

Deliver:

- organizations
- organization membership
- roles
- permissions
- tenant context
- onboarding
- organization switcher

Acceptance:

Two organizations cannot access each other's data.

---

## Milestone 3 — Properties

Deliver:

- properties CRUD
- units CRUD
- amenities
- media
- property/unit status

Acceptance:

Tenant can fully configure rentable inventory.

---

## Milestone 4 — Pricing

Deliver:

- rate plans
- seasonal pricing
- fees
- discounts
- pricing service

Acceptance:

Server returns correct price breakdown for arbitrary dates.

---

## Milestone 5 — Calendar

Deliver:

- availability
- blocks
- calendar UI
- unit timeline
- drag/drop

Acceptance:

User can visually manage inventory availability.

---

## Milestone 6 — Bookings

Deliver:

- booking CRUD
- availability validation
- guest selection
- price calculation
- booking statuses
- booking timeline

Acceptance:

Cannot create overlapping confirmed bookings.

---

## Milestone 7 — Payments

Deliver:

- payment recording
- payment history
- refunds
- outstanding balances

Acceptance:

Booking financial totals remain auditable.

---

## Milestone 8 — Guests

Deliver:

- guest profiles
- booking history
- notes
- contact details

---

## Milestone 9 — Operations

Deliver:

- cleaning tasks
- maintenance
- staff assignments
- checkout → cleaning workflow

---

## Milestone 10 — Owners

Deliver:

- owners
- owner/property relationship
- revenue calculation
- commission
- expenses
- settlements
- owner statements

---

## Milestone 11 — Dashboard / Reports

Deliver:

- occupancy
- revenue
- bookings
- outstanding balances
- expenses
- owner reports

---

## Milestone 12 — Notifications

Deliver:

- email
- database notifications
- automated booking/check-in/check-out messages

---

## Milestone 13 — SaaS Billing

Deliver:

- plans
- subscription management
- usage limits
- subscription invoices
- payment integration

---

## Milestone 14 — Public Booking

Deliver:

- public property pages
- search
- availability
- booking
- checkout
- payment

---

## Milestone 15 — Integrations

Deliver:

- channel abstraction
- webhooks
- sync engine
- Airbnb/Booking.com integrations as supported by available partner APIs

---

# 38. Recommended MVP Definition

The first production release should contain only:

1. Authentication
2. Organizations
3. Team members
4. Roles/permissions
5. Properties
6. Units
7. Amenities
8. Media
9. Pricing
10. Calendar
11. Bookings
12. Guests
13. Payments
14. Expenses
15. Cleaning tasks
16. Dashboard
17. Notifications
18. Audit logs
19. SaaS plans/subscriptions

Do NOT initially build:

- Airbnb integration
- Booking.com integration
- Marketplace
- Mobile application
- Dynamic pricing AI
- Advanced accounting
- Custom domains

Those should come after the core SaaS proves the workflow.

---

# 39. First Database Migration Order

Recommended migration order:

1. users
2. organizations
3. organization_user
4. roles
5. permissions
6. role_user / organization_roles
7. plans
8. plan_features
9. subscriptions
10. properties
11. units
12. amenities
13. amenity_property
14. amenity_unit
15. media
16. rate_plans
17. rate_plan_prices
18. seasonal_prices
19. extra_charges
20. guests
21. availability_blocks
22. bookings
23. booking_nights
24. booking_fees
25. booking_discounts
26. payments
27. refunds
28. expense_categories
29. expenses
30. cleaning_tasks
31. maintenance_tasks
32. staff_assignments
33. owners
34. owner_properties
35. owner_statements
36. owner_settlements
37. notifications
38. audit_logs

---

# 40. First Sprint

Start with this exact order:

### Day 1

- Create Laravel project
- Configure MySQL
- Configure Redis
- Install Inertia
- Install Vue
- Install Tailwind
- Configure Vite
- Configure Docker

### Day 2

- Authentication
- Base application layout
- Navigation
- Flash messages
- Toast system
- Error handling

### Day 3

- Organizations
- Organization membership
- Tenant context

### Day 4

- Roles
- Permissions
- Policies

### Day 5

- Onboarding wizard
- Organization settings
- Team invitation

### Day 6

- Property migrations/models
- Property CRUD

### Day 7

- Unit migrations/models
- Unit CRUD

At the end of Sprint 1:

`Register → Create Organization → Invite User → Create Property → Create Unit`

should work end-to-end.

---

# 41. Critical Architectural Rules

1. Tenant isolation is mandatory.
2. Server is the source of truth for money and availability.
3. Financial records are append-only/auditable.
4. Booking creation must be transactional.
5. Prevent double booking at the database/service level.
6. Business logic belongs in domain services.
7. Vue is not responsible for financial calculations.
8. External integrations use adapters/interfaces.
9. Queue all slow external operations.
10. Every important business mutation gets an audit trail.
11. Use UUID/ULID for public identifiers where appropriate.
12. Never expose sequential internal IDs unnecessarily in public URLs.
13. Build marketplace and channel management after the core PMS is stable.
14. Keep SaaS billing separate from property-owner financial settlement.
15. Design the schema for multi-property and multi-unit management from day one.

---

# 42. Definition of Done for MVP

The MVP is production-ready when:

- A company can register.
- It can create its organization.
- It can invite employees.
- Permissions work.
- It can create properties and units.
- It can upload property images.
- It can configure prices.
- It can block dates.
- It can create bookings.
- Double bookings are prevented.
- Guests can be managed.
- Payments can be recorded.
- Expenses can be recorded.
- Cleaning tasks are generated.
- Dashboard metrics are correct.
- Notifications work.
- Audit logs work.
- Tenant isolation is tested.
- Automated tests cover booking/pricing/payment logic.
- SaaS subscription limits are enforced.
- Production deployment and backups are configured.

---

# 43. Suggested Build Strategy

Build the system vertically rather than creating all database tables first.

For each feature:

1. Migration
2. Model
3. Factory
4. Policy
5. Service
6. Request validation
7. Controller
8. Inertia page
9. Vue components
10. Tests
11. Audit/event handling

Example:

`Property`

→ migration

→ model

→ policy

→ PropertyService

→ requests

→ controller

→ Inertia page

→ Vue form/table

→ tests

Then move to `Unit`.

This keeps every milestone usable and reduces unfinished modules.

---

# 44. Product Positioning

The product should initially be positioned as:

**"Operating system for short-term rental managers in Egypt."**

Not merely:

**"Another property listing website."**

The core value is helping a manager operate 5, 20, 100+ units from one dashboard.

Marketplace can become the demand-generation layer later.

The long-term architecture should therefore support:

`SaaS PMS`
        ↓
`Property Management`
        ↓
`Booking Engine`
        ↓
`Operations`
        ↓
`Financial Management`
        ↓
`Channel Manager`
        ↓
`Public Marketplace`
