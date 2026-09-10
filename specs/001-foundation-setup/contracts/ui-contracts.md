# UI Contracts: Authentication & Organization

**Date**: 2026-09-08
**Feature**: 001-foundation-setup

## Page Contracts

### 1. Registration Page

**Route**: GET /register
**Component**: Auth/Register.vue

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | text | Yes | max:255 |
| email | email | Yes | email, max:255, unique:users |
| password | password | Yes | min:8, confirmed |
| password_confirmation | password | Yes | must match password |

**Submit Action**: POST /register
**Success Response**: Redirect to /verify-email (show verification notice)
**Error Response**: Flash error message, re-render form with old input

---

### 2. Login Page

**Route**: GET /login
**Component**: Auth/Login.vue

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| email | email | Yes | email |
| password | password | Yes | - |
| remember | checkbox | No | boolean |

**Submit Action**: POST /login
**Success Response**: Redirect to /dashboard (or /organizations if no org)
**Error Response**: Flash error message, re-render form

**Rate Limiting**: 5 attempts per minute per IP

---

### 3. Email Verification Page

**Route**: GET /verify-email
**Component**: Auth/VerifyEmail.vue

**Content**:
- Notice: "A new verification link has been sent to your email"
- Resend button (POST /email/verification-notification)
- Logout link

**Resend Action**: POST /email/verification-notification
**Success Response**: Flash success message
**Rate Limiting**: 1 per minute

---

### 4. Password Reset Request Page

**Route**: GET /forgot-password
**Component**: Auth/ForgotPassword.vue

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| email | email | Yes | email |

**Submit Action**: POST /forgot-password
**Success Response**: Flash success message (always, even if email not found)

---

### 5. Password Reset Page

**Route**: GET /reset-password/{token}
**Component**: Auth/ResetPassword.vue

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| email | email | Yes | email |
| password | password | Yes | min:8, confirmed |
| password_confirmation | password | Yes | must match password |

**Submit Action**: POST /reset-password
**Success Response**: Redirect to /login with success message

---

### 6. Dashboard Page

**Route**: GET /dashboard
**Component**: Dashboard/Index.vue

**Content**:
- Welcome message with user name
- Organization switcher (if user has multiple organizations)
- "Create Organization" button (if no organizations)
- Quick links to main features (placeholder for Phase 2+)

**Access**: Authenticated users only

---

### 7. Organization Creation Page

**Route**: GET /organizations/create
**Component**: Organizations/Create.vue

**Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | text | Yes | max:255 |
| slug | text | Yes | alpha_dash, max:255, unique:organizations |

**Submit Action**: POST /organizations
**Success Response**: Redirect to /dashboard with success message
**Error Response**: Flash error message, re-render form

---

### 8. Organization Switcher Component

**Component**: Organizations/Switcher.vue

**Props**:
| Prop | Type | Description |
|------|------|-------------|
| organizations | array | List of user's organizations |
| currentOrganization | object | Currently selected organization |

**Actions**:
- Select organization → POST /organizations/{id}/switch
- Create new → Navigate to /organizations/create

**Behavior**: Updates session context, refreshes page data

---

### 9. Team Invitation Page

**Route**: GET /organizations/{organization}/invitations
**Component**: Invitations/Index.vue

**Access**: Owner, Admin roles only

**Content**:
- List of pending invitations
- "Invite Member" button → opens modal/form

**Invite Form Fields**:
| Field | Type | Required | Validation |
|-------|------|----------|------------|
| email | email | Yes | email |
| role | select | Yes | in:Owner,Admin,Manager,Staff,Accountant,Cleaning |

**Submit Action**: POST /organizations/{organization}/invitations
**Success Response**: Flash success message, invitation appears in list

---

### 10. Invitation Accept Page

**Route**: GET /invitations/accept/{token}
**Component**: Invitations/Accept.vue

**Scenarios**:
1. **User not logged in**: Show invitation details, prompt to register/login
2. **User logged in, email matches**: Accept invitation, add membership, redirect to dashboard
3. **User logged in, email different**: Show error, suggest logging in with correct email

**Accept Action**: POST /invitations/{token}/accept
**Success Response**: Redirect to /dashboard with success message

---

## Component Contracts

### Toast Notification Component

**Component**: Components/Toast.vue

**Props**:
| Prop | Type | Description |
|------|------|-------------|
| message | string | Toast message text |
| type | string | success, error, warning, info |
| duration | number | Auto-dismiss time in ms (default: 5000) |

**Events**:
- dismiss: Emitted when toast is closed

### Flash Message Handler

**Behavior**: Reads flash session data (success, error, warning) and displays appropriate toast on page load.

---

## Layout Contracts

### Guest Layout

**Component**: Layouts/Guest.vue

**Slots**:
- default: Page content

**Features**:
- Centered card design
- Logo/branding
- Minimal navigation

### Authenticated Layout

**Component**: Layouts/Authenticated.vue

**Slots**:
- default: Page content

**Features**:
- Sidebar navigation
- Top bar with user menu
- Organization switcher
- Flash message area

### App Layout

**Component**: Layouts/App.vue

**Slots**:
- default: Page content

**Features**:
- Full-width layout
- Sidebar (collapsible)
- Top bar
- Breadcrumbs
