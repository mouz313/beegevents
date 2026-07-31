# BeeG Events — Pending Work / To-Do

> Based on the project status as of 2026-07-31. This file lists only what is **not done yet**, grouped by priority, so it can be worked through in order (in a code editor / Claude Code).
> Checkmarks `[x]` = completed during the 2026-07-31 working session.

---

## Priority 1 — Blockers Before Any Real Launch

- [ ] **Add real Stripe keys** — `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` are empty in `.env`; no real payment can be processed until these are set
- [x] **Verify Stripe webhook route** — route `POST /webhook/stripe` created (`WebhookController`), returns 503 until `STRIPE_WEBHOOK_SECRET` is set; tests added
- [ ] **Verify full payment flow end-to-end in browser** once keys are added (intent → confirm → manual payment paths)
- [x] **Set `APP_DEBUG=false`** — done in `.env`
- [ ] **Configure a real mail driver (SMTP)** — currently set to `log`, so no real emails (booking confirmations, verification, notifications) are actually being sent
- [ ] **Verify email templates** (booking/inquiry notifications) actually send and render correctly once SMTP is live

## Priority 2 — Project Hygiene / Risk

- [x] **Initialize git version control** — already existed: `main` branch + remote `origin` (github.com/mouz313/beegevents.git), initial commit `Project Start`
- [x] **Delete stray `nul` file** (69 bytes) at project root
- [x] **Fix `php artisan db:show` error** — `Table 'performance_schema.session_status' doesn't exist`; fixed with a MariaDB-aware custom grammar `app/Database/MySqlGrammar.php` (no vendor changes). Also enabled the `intl` PHP extension (required for `db:show` formatting)
- [x] **Replace default `README.md`** — replaced with real project documentation (setup, roles, payments, production checklist)

## Priority 3 — Business Decisions Needed (blocks correct payment/commission logic)

- [ ] **Finalize commission % / amount** — flat rate vs. category-based (halls vs. smaller services)
- [ ] **Finalize vendor payout timing** — before or after the event date
- [ ] Confirm whether these need to be configurable per vendor/category in the admin panel, or fixed platform-wide

## Priority 4 — Content & Features Not Yet Complete

- [x] **Add blog content** — 5 SEO-oriented posts seeded via `BlogPostSeeder` (wedding venue, budget, corporate checklist, marquee vs hall, caterer questions)
- [x] **Book Now modal** — Package / Custom / Budget cards in one modal; budget card builds an auto bundle (within-budget hall → slightly-above → services-only) and adds it to cart; old `customer/budget-match` page kept as-is
- [ ] **Budget → admin manual-quoting flow** — verified: free-text `notes` field exists in checkout ("Any special requirements..."), but the *budget → admin manual quote* flow is **not** implemented. Needs a design decision

## Priority 5 — Testing & Production Readiness

- [x] **Write automated tests** — was done (13 tests), but the `tests/` folder was deleted on request. Tests are not wanted in this project for now
- [x] **Scheduler** — `app:release-expired-holds` already scheduled every 5 minutes in `routes/console.php`; cron entry `* * * * * php /path/to/beegevents/artisan schedule:run` documented in README (still needs to be added on the server)
- [ ] **Queue worker** — app uses `database` queue driver; run `php artisan queue:work` on the server (no queued jobs used yet)
- [ ] **Move off `APP_ENV=local` / `localhost`** — actual deployment/hosting not done yet

---

## Already Done (for reference — not pending)

Auth & roles, full customer flow (browse → cart → checkout → booking → payment page → messages → reviews → disputes → budget match), full vendor flow (onboarding → listings → calendar → bookings → messages), full admin flow (vendor verification → booking verification → disputes → categories → packages → corporate leads → blog CRUD → users), database schema (29/29 migrations, 21 models), seeded demo data, Stripe webhook endpoint, README, git repo, blog seed data, automated tests.
