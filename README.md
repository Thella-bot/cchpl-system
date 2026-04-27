# CCHPL System

Membership management application for the Council for Culinary and Hospitality Professionals Lesotho (CCHPL), built with Laravel 10, PHP 8.1, and Livewire 3.

## Overview

This application supports the core membership lifecycle for CCHPL:

- member authentication and dashboard access
- membership application submission through Livewire
- payment initiation and proof submission
- member document downloads
- member resignation submission
- admin review of memberships, payments, documents, and resignations
- reporting and export workflows
- administrative role and audit management for super admins

The codebase includes both member-facing flows and role-restricted admin areas under `/admin`.

## Technology Stack

- PHP 8.1
- Laravel 10
- Laravel UI/Auth routes via `Auth::routes()`
- Livewire 3
- Laravel Sanctum
- Barryvdh DomPDF for PDF/document generation
- Guzzle HTTP client
- PHPUnit 10 for tests
- Laravel Pint for code style

## Current Application Areas

### Member-facing areas

Authenticated members can access:

- `/member/dashboard` - member dashboard
- `/member/profile` - profile editing
- `/membership/apply` - membership application form
- `/payment/initiate` - payment initiation and proof submission
- `/documents/certificate/{membership}` - certificate download
- `/documents/receipt/{payment}` - receipt download
- `/documents/welcome-pack/{membership}` - welcome pack download
- `/member/resign` - resignation submission

Notes from the current routes:

- the membership application and payment initiation flows are implemented as Livewire components
- some member routes are protected with email verification and throttling
- document downloads are handled by controllers and are expected to enforce ownership checks

### Admin areas

All admin routes are grouped under `/admin` and require authenticated admin access.

Current admin sections include:

- super admin management
- membership administration
- payment administration
- reports
- document review and document composition
- resignations administration
- membership category management

## Admin Roles Reflected in Routes

The route definitions currently reference these admin roles/middleware groupings:

- `super_admin`
- `membership_admin`
- `finance_admin`
- `payment_admin`
- `reports_admin`

In practice, the following admin responsibilities are visible from the route structure:

### Super Admin

Available under `/admin` with additional `super-admin` middleware:

- dashboard
- admin user listing and detail views
- admin creation
- admin role updates
- admin deactivation
- audit log access
- role management

Key routes:

- `/admin/dashboard`
- `/admin/admins`
- `/admin/audit-log`
- `/admin/roles`

### Membership Admin

Handles membership review workflows and member listing:

- pending memberships
- membership detail review
- approve/reject actions
- bulk actions
- export
- document review actions attached to memberships
- member and rejected-member listing
- resignations review/acknowledgement

Key route areas:

- `/admin/memberships/*`
- `/admin/resignations/*`

### Finance Admin

The current route file assigns membership category maintenance to finance admin or super admin:

- view categories
- edit category
- update category

Key routes:

- `/admin/memberships/categories`
- `/admin/memberships/categories/{category}/edit`

### Payment Admin

Handles payment verification workflows:

- pending payments
- verified and rejected payment lists
- payment detail view
- verify/reject actions
- receipt access

Key routes:

- `/admin/payments/*`

### Reports Admin

Handles reporting and export endpoints:

- reports dashboard/index
- membership reports
- payment reports
- member export
- payment export

Key routes:

- `/admin/reports/*`

## Document and Communication Features

The current route structure and view files show support for document-related workflows beyond basic uploads:

### Member document downloads

Members can download generated or stored documents for:

- certificates
- receipts
- welcome packs

### Admin document review queue

There is a dedicated document review area at:

- `/admin/documents`

This area includes routes for:

- queue listing
- draft preview
- per-review detail
- review update
- preview
- approval
- sending
- cancellation

### AGM notices and EC minutes composition

Within the admin document area, the system currently exposes composition routes for:

- AGM notices
- EC minutes

Key routes:

- `/admin/documents/compose/agm-notice`
- `/admin/documents/compose/ec-minutes`

This suggests a review-and-send workflow for official organization documents.

## Reporting Features

Based on the current routes and existing admin views, the reporting module includes:

- reports index/dashboard
- membership reporting
- payment reporting
- member export
- payment export

Relevant views exist under `resources/views/admin/reports`.

## Resignations Features

Members can submit resignations from:

- `/member/resign`

Admins with membership or super admin access can:

- view resignation listings
- inspect individual resignations
- acknowledge resignations

Relevant admin views exist under `resources/views/admin/resignations`.

## Main Code Areas

A few important locations in the current repository:

- `routes/web.php` - member and admin route definitions
- `app/Livewire/Membership/ApplicationForm.php` - membership application flow
- `app/Livewire/Payment/InitiatePayment.php` - payment initiation flow
- `app/Http/Controllers/Admin/` - admin controllers for memberships, payments, reports, resignations, documents, and super admin operations
- `app/Services/PaymentService.php` - payment-related business logic
- `resources/views/admin/` - Blade views for admin modules
- `tests/` - automated tests

## Setup

### Prerequisites

- PHP 8.1 or newer
- Composer
- a supported database for Laravel
- Node.js and npm if you need to build frontend assets

### Install dependencies

```bash
composer install
```

If frontend assets are used in your environment, install JavaScript dependencies as needed for the project setup already in use.

### Configure environment

Create your application environment file if it does not already exist:

```bash
cp .env.example .env
```

Update the database, mail, and application settings in `.env`.

The project also expects payment-related environment values for payment instructions:

```env
MPESA_SHORTCODE=your_shortcode
ECOCASH_MERCHANT=your_merchant_number
```

### Generate application key

```bash
php artisan key:generate
```

### Run migrations

```bash
php artisan migrate
```

If your environment uses seeders for baseline data such as membership categories or roles, run the relevant seeders available in the repository.

### Storage link

To make public storage files accessible:

```bash
php artisan storage:link
```

### Start the application

```bash
php artisan serve
```

Then visit the application in your browser.

## Authentication and Access

The application root currently redirects to the login page:

- `/` -> `/login`

Authentication routes are enabled with email verification support:

- login
- registration
- password reset
- email verification

Some member-facing routes explicitly require verified email addresses, especially around application and payment flows.

## Route Summary

### Public entry

- `/` redirects to `/login`

### Authenticated member routes

- `/member/dashboard`
- `/member/profile`
- `/membership/apply`
- `/payment/initiate`
- `/documents/*`
- `/member/resign`

### Authenticated admin routes

All grouped under:

- `/admin/*`

Including:

- `/admin/dashboard`
- `/admin/admins`
- `/admin/memberships/*`
- `/admin/payments/*`
- `/admin/reports/*`
- `/admin/documents/*`
- `/admin/resignations/*`

## Development Notes

- This repository uses standard Laravel structure and conventions.
- Livewire is used for interactive member workflows rather than placing all behavior in traditional controllers.
- DomPDF is installed, which aligns with the document and receipt generation features present in the routes and views.
- PHPUnit and Laravel Pint are available for testing and formatting.

## Useful Commands

```bash
php artisan migrate
php artisan storage:link
php artisan test
./vendor/bin/pint
```

## Repository Hygiene

Common generated files, caches, logs, and environment files should stay out of version control. The `.gitignore` file has been expanded to cover Laravel runtime artifacts such as framework cache, compiled views, sessions, logs, bootstrap cache, vendor, and local environment files.

## License

MIT