# BeeG Events

An event management platform built with Laravel. Connects customers with event venues (halls) and service vendors (catering, decor, photography, etc.) with bookings, payments, messaging, reviews, and disputes.

## Tech Stack

- **Framework:** Laravel 13 (PHP 8.3+)
- **Database:** MySQL / MariaDB
- **Payments:** Stripe (Payment Intents)
- **Queue / Cache / Session:** database driver
- **Mail:** log driver (SMTP pending)

## Roles

| Role | Access |
|------|--------|
| **Customer** | Browse venues/services, cart, checkout, bookings, payments, messages, reviews, disputes, budget match |
| **Vendor** | Onboarding, halls/floors/units, service listings, availability calendar, booking responses, inquiries, messages |
| **Admin** | Vendor verification, booking verification, payments, disputes, categories, packages, corporate leads, blog, users |

## Setup

```bash
composer install
copy .env.example .env        # Windows
php artisan key:generate
php artisan migrate
php artisan db:seed           # demo data (users, vendors, halls, listings, bookings)
php artisan storage:link
php artisan serve             # http://localhost:8000
```

Database config lives in `.env` (`DB_DATABASE`, `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`).

## Key Directories

```
app/Http/Controllers/    Auth, Browse, Customer/, Vendor/, Admin/
app/Models/              21 Eloquent models
app/Services/            PaymentService, CancellationService, BudgetMatchingService
app/Console/Commands/    ReleaseExpiredHolds
app/Mail/                BookingStatusMail, InquiryNotification
app/Database/            MySqlGrammar (MariaDB fix for artisan db:show)
database/migrations/     29 migrations (all applied)
database/seeders/        Admin, categories, demo data
routes/web.php           All routes
```

## Scheduled Tasks

`app:release-expired-holds` runs every 5 minutes (defined in `routes/console.php`).

On the server, the scheduler needs one cron entry:

```
* * * * * php /path/to/beegevents/artisan schedule:run >> /dev/null 2>&1
```

## Queue Worker

The app uses the `database` queue driver. For queued jobs to process, run:

```bash
php artisan queue:work
```

## Payments (Stripe)

Set these in `.env`:

```
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

The payment flow: customer creates a booking → server creates a Payment Intent → Stripe.js confirms in the browser → server retrieves intent and records the payment. Manual/bank-transfer payments are recorded and verified by admin.

## Production Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Configure SMTP (`MAIL_MAILER=smtp`, host/username/password)
- [ ] Add Stripe keys and webhook endpoint
- [ ] `php artisan config:cache`, `route:cache`, `view:cache`
- [ ] Set up the scheduler cron entry (above)
- [ ] Set up a queue worker

## Notes / Known Issues

- `php artisan db:show` on MariaDB is fixed via a custom grammar (`app/Database/MySqlGrammar.php`) — vendor files are untouched.
- Blog module is built but has no content yet.
- No automated tests yet.
- See `TODO.md` for the full pending-work list.
