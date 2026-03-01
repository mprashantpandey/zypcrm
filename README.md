# ZypCRM Backend

Laravel 12 backend for a multi-tenant library/study-room SaaS platform.

## Overview

ZypCRM manages:
- Library tenants and subscription plans
- Student onboarding and multi-library memberships
- Seat allocation and unassignment
- Fee collection (online + manual) with reminders
- Attendance tracking and leave requests
- Admin reports, analytics, and audit logs

Tech stack:
- Laravel 12
- Livewire/Volt
- MySQL
- Sanctum (API auth)
- Razorpay + Stripe integrations

## Core Modules

- `Super Admin`
  - Tenant management
  - Platform settings
  - Subscription plan management
  - Global reports and dashboards
  - Cross-tenant student membership control

- `Library Owner`
  - Students, seats, plans, fees, attendance, leaves
  - Dashboard and operational reports

- `Student Panel`
  - Dashboard
  - Attendance view
  - Fee history
  - Leave request submission
  - Multi-library context support

## Authentication

Supported login methods (configurable in admin settings):
- Email/Password
- Phone OTP (Firebase)

API responses include `allowed_login_methods` so mobile apps can adapt UI dynamically.

## Billing and Fees

- Tenant subscription checkout via Stripe/Razorpay
- Fee payment links for students
- Payment verification and status updates
- Fee due reminders (scheduled)
- Subscription expiry reminders (scheduled)

## Audit Logging

Critical actions are logged (role-aware), including:
- Student update/delete
- Fee payment updates
- Seat unassign

## API Enhancements

Tenant APIs support:
- Pagination (`per_page`)
- Sorting (`sort_by`, `sort_dir`)

Applied on major resources like students, seats, and fees.

## Scheduler Jobs

Configured in `routes/console.php`:
- `library:notify-expiring-subscriptions` (daily)
- `library:send-fee-reminders` (daily)

Admin settings UI includes scheduler health (last run timestamp + command).

## Local Setup

1. Install dependencies:
```bash
composer install
npm install
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Configure database in `.env`, then migrate:
```bash
php artisan migrate
```

4. Build frontend assets:
```bash
npm run build
```

For development:
```bash
php artisan serve
npm run dev
php artisan queue:work
php artisan schedule:work
```

## Testing

```bash
php artisan test
```

## Important Notes

- Ensure payment keys are configured in Platform Settings before enabling gateways.
- Ensure Firebase settings are valid before enabling phone OTP login.
- Run scheduler in production (cron or `schedule:work`) so reminders are sent.

## License

This project is proprietary to ZypCRM unless stated otherwise by repository owner.
