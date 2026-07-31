# BeeG Events — Pending Work / To-Do

> Based on the project status as of 2026-07-31. This file lists only what is **not done yet**, grouped by priority, so it can be worked through in order (in a code editor / Claude Code).

---

## Priority 1 — Blockers Before Any Real Launch

- [ ] **Add real Stripe keys** — `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` are empty in `.env`; no real payment can be processed until these are set
- [ ] **Verify Stripe webhook route** — not yet confirmed working
- [ ] **Verify full payment flow end-to-end in browser** once keys are added (intent → confirm → manual payment paths)
- [ ] **Set `APP_DEBUG=false`** before any production deployment — currently `true`, which is a security risk
- [ ] **Configure a real mail driver (SMTP)** — currently set to `log`, so no real emails (booking confirmations, verification, notifications) are actually being sent
- [ ] **Verify email templates** (booking/inquiry notifications) actually send and render correctly once SMTP is live

## Priority 2 — Project Hygiene / Risk

- [ ] **Initialize git version control** — project has no repository yet, no history/backup
- [ ] **Delete stray `nul` file** (69 bytes) at project root
- [ ] **Fix `php artisan db:show` error** — `Table 'performance_schema.session_status' doesn't exist`; MySQL user needs `performance_schema` access, or the DB user config needs fixing
- [ ] **Replace default `README.md`** (still the stock Laravel readme) with real project documentation

## Priority 3 — Business Decisions Needed (blocks correct payment/commission logic)

- [ ] **Finalize commission % / amount** — flat rate vs. category-based (halls vs. smaller services)
- [ ] **Finalize vendor payout timing** — before or after the event date
- [ ] Confirm whether these need to be configurable per vendor/category in the admin panel, or fixed platform-wide

## Priority 4 — Content & Features Not Yet Complete

- [ ] **Add blog content** — module is built but has 0 posts; needed before the SEO content strategy can do anything
- [ ] **Confirm custom booking form exists** — not explicitly seen in the current build; verify it's implemented (free-text requirement + budget routed to admin for manual quoting, per the original plan)

## Priority 5 — Testing & Production Readiness

- [ ] **Write automated tests** — currently only the default Laravel `ExampleTest`; at minimum cover booking flow and payment flow given money is involved
- [ ] **Set up a queue worker / cron** for scheduled tasks (currently not configured)
- [ ] **Move off `APP_ENV=local` / `localhost`** — actual deployment/hosting not done yet

---

## Already Done (for reference — not pending)

Auth & roles, full customer flow (browse → cart → checkout → booking → payment page → messages → reviews → disputes → budget match), full vendor flow (onboarding → listings → calendar → bookings → messages), full admin flow (vendor verification → booking verification → disputes → categories → packages → corporate leads → blog CRUD → users), database schema (29/29 migrations, 21 models), and seeded demo data.
