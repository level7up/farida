# Short-Term Rental SaaS — Modular Microservices Implementation Plan

## 1. Product Vision

Build a multi-tenant SaaS platform for short-term rental property managers and owners, with a modular architecture and independently deployable microservices.

### Core stack

- Backend: Laravel
- Frontend: Vue 3 + Inertia
- Styling: Tailwind CSS
- Database: MySQL
- Cache / queues: Redis
- Message broker: RabbitMQ
- Storage: S3-compatible storage / MinIO locally
- Runtime: Docker
- Reverse proxy: Nginx
- Process management: Supervisor where required
- Testing: Pest / PHPUnit

### Architectural principles

1. Each microservice owns its database.
2. Each microservice is internally modular.
3. Business logic belongs in domain/application layers, not controllers.
4. Services communicate through APIs for synchronous operations and RabbitMQ events for asynchronous workflows.
5. No service directly reads another service's database.
6. Tenant isolation is enforced at the service boundary.
7. Financial and booking operations must be auditable and idempotent.
8. Start with a small number of meaningful services; do not create a microservice for every feature.

---

# 2. High-Level Architecture

```text
                                    ┌──────────────────────┐
                                    │       Browser        │
                                    │   Vue + Inertia      │
                                    └──────────┬───────────┘
                                               │
                                               ▼
                                    ┌──────────────────────┐
                                    │   Web / BFF Layer    │
                                    │       Laravel        │
                                    └──────────┬───────────┘
                                               │
                                               ▼
                                    ┌──────────────────────┐
                                    │     API Gateway      │
                                    │ Auth / Routing / ACL │
                                    └──────────┬───────────┘
                                               │
              ┌────────────────────────────────┼────────────────────────────────┐
              │                                │                                │
              ▼                                ▼                                ▼
     ┌────────────────┐              ┌────────────────┐              ┌────────────────┐
     │ Identity       │              │ Property       │              │ Booking        │
     │ Service        │              │ Service        │              │ Service        │
     └───────┬────────┘              └───────┬────────┘              └───────┬────────┘
             │                               │                               │
             │                               │                               │
             └───────────────────────────────┼───────────────────────────────┘
                                             │
                                    ┌────────▼────────┐
                                    │    RabbitMQ      │
                                    │   Event Bus      │
                                    └────────┬────────┘
                                             │
                    ┌────────────────────────┼────────────────────────┐
                    │                        │                        │
                    ▼                        ▼                        ▼
           ┌────────────────┐      ┌────────────────┐      ┌────────────────┐
           │ Finance        │      │ Operations     │      │ Notification   │
           │ Service        │      │ Service        │      │ Service        │
           └────────────────┘      └────────────────┘      └────────────────┘

                         Future
                            │
                            ▼
                   ┌────────────────┐
                   │ Marketplace    │
                   │ Service        │
                   └────────────────┘
                            │
                            ▼
                   ┌────────────────┐
                   │ Channel        │
                   │ Manager        │
                   └────────────────┘
```

---

# 3. Microservice Boundaries

The initial system should contain these services:

| Service | Responsibility | Priority |
|---|---|---|
| Identity Service | Users, organizations, roles, permissions, SaaS accounts | P0 |
| Property Service | Properties, units, amenities, media | P0 |
| Booking Service | Availability, reservations, booking lifecycle | P0 |
| Finance Service | Payments, refunds, expenses, owner settlements | P0 |
| Operations Service | Cleaning, maintenance, staff tasks | P1 |
| Notification Service | Email, SMS, WhatsApp, push notifications | P1 |
| Marketplace Service | Public listings and marketplace | P2 |
| Channel Manager | Airbnb, Booking.com and other channels | P3 |

Do not split these into smaller services until there is a real scaling or ownership reason.

---

# 4. Internal Modularity Standard

Every Laravel microservice should use the same internal architecture.

```text
service/
├── app/
│   ├── Modules/
│   │   ├── ModuleName/
│   │   │   ├── Domain/
│   │   │   │   ├── Entities/
│   │   │   │   ├── ValueObjects/
│   │   │   │   ├── Events/
│   │   │   │   ├── Exceptions/
│   │   │   │   └── Contracts/
│   │   │   ├── Application/
│   │   │   │   ├── Actions/
│   │   │   │   ├── DTOs/
│   │   │   │   └── Services/
│   │   │   ├── Infrastructure/
│   │   │   │   ├── Persistence/
│   │   │   │   ├── Integrations/
│   │   │   │   └── Messaging/
│   │   │   └── Presentation/
│   │   │       ├── Http/
│   │   │       ├── Requests/
│   │   │       └── Resources/
│   │   └── ...
│   └── Shared/
├── routes/
├── database/
├── tests/
└── docker/
```

### Rules

- Domain must not depend on HTTP.
- Domain must not depend on Vue/Inertia.
- Controllers call application actions/services.
- Infrastructure implements domain contracts.
- Events represent business facts.
- DTOs are used at service boundaries.
- Eloquent models stay inside the owning module.

---

# 5. Phase 0 — Architecture & Infrastructure

## Goal

Create the development and deployment foundation before implementing business features.

### Tasks

- Create Git repository structure.
- Define service naming conventions.
- Create Docker Compose for local development.
- Create MySQL containers/databases.
- Create Redis.
- Create RabbitMQ.
- Create Nginx gateway.
- Define internal Docker network.
- Define environment variable conventions.
- Configure centralized logging.
- Configure health checks.
- Configure CI pipeline.
- Define local and production configuration.

### Suggested repository

```text
short-rental-platform/
├── services/
│   ├── identity-service/
│   ├── property-service/
│   ├── booking-service/
│   ├── finance-service/
│   ├── operations-service/
│   └── notification-service/
├── frontend/
├── gateway/
├── infrastructure/
│   ├── docker/
│   ├── nginx/
│   └── rabbitmq/
├── docker-compose.yml
└── README.md
```

### Definition of Done

- All services start with Docker.
- Services can communicate over the internal network.
- RabbitMQ works.
- Redis works.
- Each service has its own database.
- CI runs tests successfully.

---

# 6. Phase 1 — Identity Service

## Modules

```text
Identity Service
├── Authentication
├── Users
├── Organizations
├── Memberships
├── Roles
├── Permissions
└── SaaS Account
```

## Responsibilities

- Registration
- Login/logout
- Email verification
- Password reset
- Organizations / tenants
- Organization membership
- Roles and permissions
- Current tenant context
- User invitations

## Core tables

```text
users
organizations
organization_user
roles
permissions
role_permissions
user_roles
invitations
```

## Tenant security

Every authenticated request must resolve a tenant context.

```text
Authenticated User
       ↓
Organization Membership
       ↓
TenantContext
       ↓
Authorized Request
```

Never trust `organization_id` from the browser as the source of authorization.

---

# 7. Phase 2 — Property Service

## Modules

```text
Property Service
├── Properties
├── Units
├── Amenities
├── Media
└── Property Settings
```

## Property

A physical real-estate container.

Examples:

- Villa
- Apartment building
- Chalet
- Compound unit
- Hotel apartment

## Unit

A rentable inventory item.

A property may contain one or many units.

## Core tables

```text
properties
units
amenities
amenity_property
amenity_unit
media
```

## API examples

```text
GET    /api/v1/properties
POST   /api/v1/properties
GET    /api/v1/properties/{property}
PUT    /api/v1/properties/{property}
DELETE /api/v1/properties/{property}

GET    /api/v1/units
POST   /api/v1/units
GET    /api/v1/units/{unit}
```

---

# 8. Phase 3 — Booking Service

This is one of the most critical services.

## Modules

```text
Booking Service
├── Availability
├── Bookings
├── Booking Pricing Snapshot
├── Booking Lifecycle
├── Booking Sources
└── Guest Reference
```

## Responsibilities

- Calendar
- Availability
- Reservations
- Date conflicts
- Booking lifecycle
- Booking source
- Booking totals snapshot
- Cancellation
- Check-in/check-out

## Booking states

```text
inquiry
pending
confirmed
checked_in
checked_out
cancelled
no_show
```

## Booking sources

```text
direct
airbnb
booking_com
whatsapp
phone
walk_in
website
other
```

## Critical rule

Never allow two confirmed bookings for the same unit and overlapping dates.

Use:

- Database transactions
- Row/application locks where appropriate
- Server-side availability validation
- Idempotency keys for external requests
- Concurrency tests

## Core tables

```text
bookings
booking_nights
booking_fees
booking_discounts
availability_blocks
```

The Booking Service owns booking state. Other services react to booking events instead of changing booking records directly.

---

# 9. Phase 4 — Finance Service

## Modules

```text
Finance Service
├── Payments
├── Refunds
├── Expenses
├── Owner Accounting
├── Commissions
└── Settlements
```

## Core tables

```text
payments
refunds
expense_categories
expenses
owners
owner_properties
owner_statements
owner_settlements
```

## Rules

- Never modify historical payment amounts directly.
- Use refunds and adjustments.
- Every transaction must be auditable.
- Finance owns financial state.
- Booking Service should not own accounting records.

Example:

```text
Booking Confirmed
       ↓
RabbitMQ Event
       ↓
Finance Service
       ↓
Create receivable / payment state
```

---

# 10. Phase 5 — Operations Service

## Modules

```text
Operations Service
├── Cleaning
├── Maintenance
├── Staff
├── Assignments
└── Checklists
```

## Cleaning states

```text
pending
assigned
in_progress
completed
inspected
```

## Event example

```text
BookingCheckedOut
        ↓
RabbitMQ
        ↓
Operations Service
        ↓
Create Cleaning Task
```

Later add:

- Cleaning checklists
- Before/after photos
- Inventory checks
- Damage reports
- Maintenance schedules

---

# 11. Phase 6 — Notification Service

## Modules

```text
Notification Service
├── Templates
├── Email
├── SMS
├── WhatsApp
├── Push
└── Preferences
```

Notification Service should consume domain events.

Example:

```text
BookingConfirmed
       ↓
RabbitMQ
       ↓
Notification Service
       ↓
Send Confirmation
```

Do not place email/SMS logic inside Booking or Property services.

---

# 12. Phase 7 — Dashboard & Reporting

The dashboard should aggregate data through APIs/events rather than directly querying service databases.

Initial dashboards:

- Revenue
- Occupancy
- Upcoming check-ins
- Upcoming check-outs
- Pending payments
- Cleaning tasks
- Expenses
- Booking sources

For heavy analytics later, introduce a reporting/read-model service instead of making transactional services handle large reporting queries.

---

# 13. Phase 8 — SaaS Billing

## Modules

```text
SaaS Billing
├── Plans
├── Features
├── Subscriptions
├── Invoices
└── Subscription Payments
```

Example limits:

```text
max_properties
max_units
max_users
max_bookings_per_month
channel_integrations
automated_messages
```

Feature limits must be centralized and configurable.

Do not hard-code subscription limits in controllers.

---

# 14. Phase 9 — Public Booking Website

Each organization can eventually have a public booking page.

Possible structure:

```text
organization.yourdomain.com
```

Features:

- Property listings
- Unit details
- Gallery
- Amenities
- Availability
- Pricing
- Guest checkout
- Online payment
- Booking confirmation

The public website should consume Booking, Property and Finance APIs rather than accessing their databases.

---

# 15. Phase 10 — Marketplace Service

Build only after the management SaaS is useful on its own.

## Modules

```text
Marketplace Service
├── Listings
├── Search
├── Reviews
├── Favorites
├── Public Profiles
└── Marketplace Booking
```

The marketplace should not become the source of truth for property inventory.

Property and Booking services remain authoritative.

---

# 16. Phase 11 — Channel Manager

Add external channels after the internal booking system is stable.

Examples:

- Airbnb
- Booking.com
- Other OTA providers

Use adapters:

```php
interface ChannelProviderInterface
{
    public function publishListing(): void;
    public function syncAvailability(): void;
    public function syncPricing(): void;
    public function importReservations(): void;
}
```

Never couple core booking logic directly to an OTA API.

Suggested modules:

```text
Channel Manager
├── Providers
├── Listings
├── Availability Sync
├── Pricing Sync
├── Reservation Import
├── Webhooks
└── Sync Logs
```

---

# 17. Event-Driven Architecture

Use RabbitMQ for domain events.

Important events:

```text
OrganizationCreated
UserInvited
PropertyCreated
UnitCreated
BookingCreated
BookingConfirmed
BookingCancelled
BookingCheckedIn
BookingCheckedOut
PaymentCreated
PaymentCompleted
PaymentRefunded
CleaningTaskCreated
CleaningTaskCompleted
ExpenseCreated
OwnerStatementGenerated
SubscriptionCreated
SubscriptionRenewed
```

## Event rules

Every event should contain enough information for consumers to process it safely.

Example:

```json
{
  "event_id": "uuid",
  "event_type": "BookingConfirmed",
  "occurred_at": "2026-09-09T10:00:00Z",
  "organization_id": "uuid",
  "booking_id": "uuid",
  "unit_id": "uuid"
}
```

Consumers must be idempotent.

---

# 18. Service-to-Service Communication

## Synchronous HTTP

Use when the caller needs an immediate response.

```text
Booking Service
      ↓ HTTP
Property Service
      ↓
Unit information
```

## Asynchronous events

Use when the operation can happen independently.

```text
Booking Service
      ↓
BookingConfirmed
      ↓
RabbitMQ
      ├── Finance
      ├── Notification
      └── Operations
```

Do not create long synchronous chains between services.

Avoid:

```text
A → B → C → D → E
```

Prefer:

```text
A → Event Bus → B/C/D/E
```

---

# 19. Database Architecture

Each service owns its own database.

```text
identity_db
property_db
booking_db
finance_db
operations_db
notification_db
marketplace_db
```

## Never do this

```text
Booking Service
      ↓
property_db.units
```

## Do this instead

```text
Booking Service
      ↓ API/Event
Property Service
      ↓
Unit information
```

No cross-service foreign keys.

IDs should be treated as external references across service boundaries.

---

# 20. API Gateway / BFF

The browser should not need to know the internal topology of the platform.

```text
Vue + Inertia
      ↓
Laravel BFF
      ↓
API Gateway
      ↓
Microservices
```

Responsibilities:

- Authentication context
- Request routing
- Rate limiting
- Request correlation ID
- API versioning
- Response aggregation where needed
- Permission checks at the edge

Business authorization must still be enforced inside each service.

---

# 21. Frontend Architecture

Use one main SaaS frontend initially.

```text
frontend/
├── resources/js/
│   ├── Pages/
│   │   ├── Auth/
│   │   ├── Dashboard/
│   │   ├── Properties/
│   │   ├── Units/
│   │   ├── Calendar/
│   │   ├── Bookings/
│   │   ├── Guests/
│   │   ├── Finance/
│   │   ├── Operations/
│   │   └── Settings/
│   ├── Components/
│   │   ├── UI/
│   │   ├── Forms/
│   │   ├── Tables/
│   │   └── Calendar/
│   ├── Composables/
│   └── Types/
```

Tailwind should be wrapped in reusable UI components.

---

# 22. Authentication Between Services

User authentication should be centralized in Identity Service.

Recommended flow:

```text
Browser
  ↓
Identity Service
  ↓
Access Token / Session
  ↓
API Gateway
  ↓
Service
```

Internal service communication should use service credentials or signed service tokens rather than forwarding arbitrary browser credentials as authority.

---

# 23. Authorization

Example permissions:

```text
properties.view
properties.create
properties.update
properties.delete

bookings.view
bookings.create
bookings.update
bookings.cancel

payments.view
payments.create
payments.refund

expenses.view
expenses.create

owners.view
owners.manage

team.view
team.invite
team.manage
```

Frontend permissions are for UX only.

Backend authorization is mandatory.

---

# 24. Tenant Isolation

The organization is the primary SaaS tenant.

Tenant context must be resolved from authenticated identity/membership.

```text
Request
  ↓
Authenticated User
  ↓
Organization Membership
  ↓
Tenant Context
  ↓
Service Authorization
```

Every tenant-owned record must contain an organization reference where appropriate.

Every service must enforce tenant ownership independently.

Add automated cross-tenant security tests.

---

# 25. Reliability Patterns

Implement these patterns from the beginning:

### Idempotency

Required for:

- Payment requests
- Booking creation from external channels
- Webhooks
- Event consumers

### Outbox pattern

For important business events:

```text
Database Transaction
      ↓
Business Record + Outbox Event
      ↓
Publisher
      ↓
RabbitMQ
```

This prevents a database transaction from succeeding while event publishing fails.

### Retry

External operations should have:

- Retry policy
- Exponential backoff
- Dead-letter handling
- Maximum retry count

### Correlation ID

Every request/event should carry a correlation ID for tracing.

---

# 26. Queues and Scheduled Jobs

Use Redis queues for local/service background jobs where RabbitMQ is not required as the integration boundary.

Use RabbitMQ for cross-service business events.

Examples:

```text
SendBookingConfirmation
SendCheckInReminder
SendCheckoutReminder
GenerateOwnerStatement
GenerateInvoice
ProcessWebhook
ProcessChannelSync
```

Laravel Scheduler:

```text
Every minute
- scheduled notifications

Every 5 minutes
- channel synchronization
- failed synchronization retry

Daily
- check-in reminders
- checkout reminders
- reports

Monthly
- subscription billing
```

---

# 27. Testing Strategy

Every service should have its own test suite.

## Unit tests

Test:

- Pricing
- Availability
- Discounts
- Fees
- Taxes
- Commission
- State transitions

## Feature tests

Test:

- Authentication
- Authorization
- Tenant isolation
- CRUD operations
- API contracts
- Event publishing

## Integration tests

Test:

- RabbitMQ consumers
- Service-to-service APIs
- Payment integrations
- Notification providers

## Contract tests

Services should verify that API/event contracts remain compatible.

## Concurrency tests

Especially test:

```text
Two users
   ↓
Same unit
   ↓
Same dates
   ↓
Only one confirmed booking
```

---

# 28. Observability

Implement from Phase 0.

Required:

- Centralized logs
- Request IDs
- Correlation IDs
- Health endpoints
- Queue monitoring
- Failed jobs monitoring
- RabbitMQ monitoring
- Database metrics
- Service latency metrics

Later:

- OpenTelemetry
- Distributed tracing
- Metrics dashboard
- Error tracking

---

# 29. Security

Requirements:

- HTTPS in production
- Secrets outside source control
- Service authentication
- Strict authorization
- Tenant isolation
- Rate limiting
- Input validation
- Signed webhooks
- Idempotent webhooks
- Secure file storage
- Audit logs
- Password hashing
- Token rotation where appropriate

Do not collect sensitive guest identification information unless legally necessary.

---

# 30. Audit Logs

Important actions should be auditable.

```text
user
organization
service
action
entity_type
entity_id
old_values
new_values
ip_address
user_agent
created_at
```

Audit examples:

- Booking cancelled
- Payment created
- Payment refunded
- Property modified
- User permission changed
- Subscription changed

---

# 31. Development Phases Summary

```text
Phase 0  Infrastructure
   ↓
Phase 1  Identity / SaaS
   ↓
Phase 2  Property
   ↓
Phase 3  Booking
   ↓
Phase 4  Finance
   ↓
Phase 5  Operations
   ↓
Phase 6  Notifications
   ↓
Phase 7  Dashboard / Reporting
   ↓
Phase 8  SaaS Billing
   ↓
Phase 9  Public Booking
   ↓
Phase 10 Marketplace
   ↓
Phase 11 Channel Manager
```

---

# 32. MVP Release

The first production release should include:

```text
✓ Identity / SaaS
✓ Organizations / Tenants
✓ Roles / Permissions
✓ Properties
✓ Units
✓ Availability
✓ Calendar
✓ Bookings
✓ Guests
✓ Payments
✓ Expenses
✓ Cleaning
✓ Basic Dashboard
✓ Notifications
✓ Audit Logs
```

Do not wait for:

- Marketplace
- Airbnb integration
- Booking.com integration
- Mobile apps
- Advanced analytics

The PMS must provide value before the marketplace exists.

---

# 33. Recommended First Sprint

## Day 1 — Infrastructure

- Create repository
- Docker Compose
- MySQL
- Redis
- RabbitMQ
- Nginx
- CI

## Day 2 — Service Templates

Create Laravel template with:

- Module structure
- API versioning
- Health endpoint
- Logging
- Error handling
- Docker configuration
- Testing setup

## Day 3 — Identity

- Users
- Authentication
- Organizations
- Memberships

## Day 4 — Authorization

- Roles
- Permissions
- Policies
- Tenant context

## Day 5 — Property

- Properties
- Units
- Property API

## Day 6 — Messaging

- RabbitMQ connection
- Event base class
- Event publisher
- Consumer base class
- Idempotency handling

## Day 7 — End-to-End Flow

```text
Register
   ↓
Create Organization
   ↓
Create Property
   ↓
Create Unit
   ↓
Publish UnitCreated
   ↓
Booking Service receives event
```

At the end of Sprint 1, the infrastructure and service boundaries should be working before implementing the full booking engine.

---

# 34. Critical Architectural Rules

## Rule 1 — Database ownership

One database belongs to one service.

## Rule 2 — No shared Eloquent models

Do not import another service's models.

## Rule 3 — No cross-service database queries

All communication goes through APIs/events.

## Rule 4 — Business logic stays inside modules

```text
Controller
   ↓
Application Action
   ↓
Domain
   ↓
Infrastructure
```

## Rule 5 — Events represent facts

Prefer:

```text
BookingConfirmed
```

over:

```text
TellFinanceToCreatePayment
```

The event should describe what happened, not what another service must do.

## Rule 6 — Booking owns availability decisions

Other services can request availability but cannot directly modify booking state.

## Rule 7 — Finance owns money

Booking can contain a pricing snapshot, but Finance owns payment and accounting state.

## Rule 8 — Start modular, scale services when needed

Microservices are boundaries, not an excuse to create dozens of applications.

---

# 35. Final Target Architecture

```text
                           ┌───────────────────────┐
                           │       Frontend        │
                           │   Vue + Inertia       │
                           │       Tailwind        │
                           └───────────┬───────────┘
                                       │
                                       ▼
                           ┌───────────────────────┐
                           │      Laravel BFF      │
                           └───────────┬───────────┘
                                       │
                                       ▼
                           ┌───────────────────────┐
                           │      API Gateway      │
                           └───────────┬───────────┘
                                       │
             ┌─────────────────────────┼─────────────────────────┐
             │                         │                         │
             ▼                         ▼                         ▼
       Identity Service         Property Service          Booking Service
             │                         │                         │
       identity_db              property_db               booking_db
             │                         │                         │
             └─────────────────────────┼─────────────────────────┘
                                       │
                                       ▼
                              ┌─────────────────┐
                              │    RabbitMQ     │
                              │   Event Bus     │
                              └────────┬────────┘
                                       │
                    ┌──────────────────┼──────────────────┐
                    │                  │                  │
                    ▼                  ▼                  ▼
             Finance Service    Operations Service   Notification Service
                    │                  │                  │
               finance_db        operations_db       notification_db

                         Future services

                    ┌────────────────────────────┐
                    │ Marketplace / Channel Mgmt │
                    └────────────────────────────┘
```

## Product strategy

**Build the PMS first. Build the marketplace second. Build channel integrations third.**

The core SaaS should remain valuable even if the marketplace has zero listings and no external channel integrations are connected.
