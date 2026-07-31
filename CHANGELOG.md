# BeeG Events — Project Progress / Status Tracker

> Updated: 2026-07-31 (after working session)
> Stack: Laravel 13.23.0 · PHP 8.5.5 · MySQL (beeg_db) · Stripe

---

## 0. Working Session — 2026-07-31 (audit fixes)

Full code audit completed — all 13 findings fixed:

- [x] **#1/#2 Double-booking + blocked-date protection** — `BookingController::store()` now checks `booked` / `blocked_offline` / active `held` slots inside a `DB::transaction` with `lockForUpdate()`; friendly 409 error returned on conflict
- [x] **#3 Commission** — new `config/commission.php` (default 10%, per-type override via env); `commission_amount` computed at booking creation; admin dashboard shows total commission
- [x] **#4 Vendor payouts** — new `payouts` table + `Payout` model + `PayoutService` (earnings = item prices − proportional commission share); admin Payouts page (record / mark processed); vendor dashboard earnings card
- [x] **#5 Reviews** — ownership + `completed` status + "vendor in booking" + duplicate-review checks
- [x] **#6 Disputes** — ownership + allowed-statuses + duplicate-open-dispute checks
- [x] **#7/#9 Vendor response** — ownership check on `BookingResponseController::respond()`; all-accepted → `discussing`, all-declined → cancelled + slots released; customer notified by email on every response
- [x] **#8 Package booking** — `bookPackage()` in `BookingController` (`booking_type=package`), route `POST customer/packages/{package}/book`, "Book This Package" form on packages page
- [x] **#10 Auto-complete** — new `app:auto-complete-bookings` command (confirmed bookings with past event date → completed), scheduled daily 02:00
- [x] **#11 Emails on new booking** — customer + all vendors notified via `BookingStatusMail` when a booking is created
- [x] **#12 Manual payment proof** — `proof_path` on payments; file upload in manual payment forms; admin proof link + Verify button (`POST admin/bookings/payments/{payment}/verify`)
- [x] **#13 Dead `inquiry` code** — removed from `BrowseController` date lists; held/blocked/expired-hold slots now counted correctly in availability
- [x] **#14 Payment intent ownership** — `confirmStripePayment()` returns metadata; controller verifies `booking_id` and exact amount before recording
- [x] **#15 `held_until`** — set to `now + 24h` when holding a slot; `ReleaseExpiredHolds` query simplified
- [x] Added `date`/`held_until` casts to `AvailabilitySlot`; `Payout` model casts
- [x] 2 new migrations ran (`proof_path`, `payouts`); all blade views compile; scheduler commands verified

---

- [x] Fixed `php artisan db:show` on MariaDB — custom grammar `app/Database/MySqlGrammar.php` + enabled `intl` PHP extension
- [x] Set `APP_DEBUG=false` in `.env`
- [x] Deleted stray `nul` file
- [x] Confirmed git repo + remote already initialized (commit `Project Start`)
- [x] Replaced default README with real project documentation
- [x] Created Stripe webhook endpoint (`POST /webhook/stripe`, `WebhookController`) + 2 tests
- [x] Verified custom booking form: free-text `notes` exists; budget → admin manual-quoting flow missing (open item)
- [x] Seeded 5 blog posts (`BlogPostSeeder`)
- [x] Automated tests written (booking, payment, webhook) then **removed on request** — `tests/` folder deleted, `phpunit.xml` cleaned
- [x] Documented scheduler cron + queue worker in README

---

## 1. Done (Ho Chuka)

### Setup
- [x] Laravel app installed + Composer dependencies installed
- [x] `.env` configured, `APP_KEY` generated, DB connection to `beeg_db` working
- [x] All **29 migrations** ran successfully (all tables created)
- [x] Storage link created (`public/storage` linked)
- [x] Fixed PHP warning: `Module "mysqli" is already loaded` (duplicate `extension=mysqli` in `C:\xampp\php\php.ini` commented out)

### Roles & Authentication
- [x] 3 roles: **Admin**, **Vendor**, **Customer** (with `role` middleware)
- [x] Register, Login, Logout
- [x] Email verification flow
- [x] Password reset / forgot password

### Public / Guest Side
- [x] Homepage (welcome) with stats + featured halls/listings + testimonials
- [x] Browse halls, browse listings, category pages, search
- [x] Hall detail, listing detail pages
- [x] Packages page
- [x] Blog index + post pages (public)
- [x] Corporate inquiry form
- [x] Contact/inquiry form
- [x] `sitemap.xml`

### Customer Panel
- [x] Dashboard
- [x] Cart (add / remove / clear)
- [x] Checkout + create booking
- [x] My bookings (list / detail / cancel)
- [x] Payment page (Stripe intent / confirm / manual payment)
- [x] Messages with vendor/admin per booking
- [x] Submit reviews
- [x] Submit disputes
- [x] Budget match

### Vendor Panel
- [x] Onboarding (multi-step)
- [x] Dashboard
- [x] Profile (create / update, phone/address/logo/bank + cancellation policy fields)
- [x] Halls CRUD + image upload/delete
- [x] Floors CRUD
- [x] Hall units CRUD
- [x] Extra services per unit
- [x] Service listings CRUD
- [x] Bookings list + respond to booking items
- [x] Messages
- [x] Calendar + availability slots (hourly time slots, block/unblock)

### Admin Panel
- [x] Dashboard
- [x] Vendor verification (list, pending, show, verify, suspend, edit, delete)
- [x] Manage vendor halls/floors/units/extras as admin
- [x] Booking verification (verify, confirm, complete, cancel, record payment)
- [x] Messages
- [x] Disputes (view, resolve, reject)
- [x] Service categories CRUD
- [x] Packages CRUD
- [x] Corporate leads (list, show, status, delete)
- [x] Blog posts CRUD
- [x] Users CRUD

### Database / Models / Data
- [x] 21 Eloquent models created
- [x] Seeder data present: **23 users, 12 vendors, 7 categories, 15 halls, 57 units, 29 listings, 75 bookings, 38 payments, 18 reviews, 6 packages**

---

## 2. Not Done / Pending (Abhi Baqi / Masail)

### Bugs / Issues
- [ ] `php artisan db:show` fails:
  `Table 'performance_schema.session_status' doesn't exist`
  (MySQL user does not have access to `performance_schema` — grant permission or fix DB user)
- [ ] Blog has **0 posts** (module built, no content yet)
- [ ] `APP_DEBUG=true` in `.env` — **must be disabled in production**
- [ ] Stray file `nul` (69 bytes) at project root — needs deletion
- [ ] Not a git repository yet (no version control)

### Not Configured / Missing
- [ ] **Stripe keys empty** (`STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`) — real payments won't work until set
- [ ] Mail driver = `log` (no real emails sent; configure SMTP for production)
- [ ] Webhook route for Stripe not verified
- [ ] Queue worker / cron for scheduled tasks not set up

### Not Built / To Verify
- [ ] No real automated tests (only default Laravel `ExampleTest`)
- [ ] `README.md` still the default Laravel readme — should be replaced with project docs
- [ ] Live deployment not done (still `APP_ENV=local`, URL `localhost`)
- [ ] Verify payment flow end-to-end in browser after Stripe keys are added
- [ ] Verify email templates (booking/inquiry notification) send correctly

---

## 3. Quick Status Summary

| Area | Status |
|------|--------|
| Database schema | Done (29/29 migrations) |
| Auth + roles | Done |
| Customer flow | Done (cart → booking → payment) |
| Vendor flow | Done |
| Admin flow | Done |
| Blog | Partial (built, no content) |
| Payments (Stripe) | Code done, **keys missing** |
| Emails | Code done, **driver = log** |
| Tests | Missing |
| Production readiness | **Not ready** |
