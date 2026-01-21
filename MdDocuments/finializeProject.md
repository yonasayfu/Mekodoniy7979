# Mekodonia Home Connect – Finalization Blueprint

## 1. Current Snapshot (January 2026)

- **Stack**: Laravel 12 + Inertia/Vue 3 SPA (`resources/js/app.ts`) with Tailwind CSS, Shadcn-inspired components, Sanctum + Fortify auth, Spatie Permissions, DomPDF, Medialibrary, and lab404 impersonation (see `composer.json`).
- **Modules already wired end‑to‑end**
    - **Public/GUEST**: Dynamic landing page with hero slider, counters, Wall of Love, and gallery filters (`app/Http/Controllers/WelcomeController.php`, `resources/js/pages/Welcome.vue`).
    - Guest meal donation flow posts to `DonationController::storeGuest` with a slider UI (`resources/js/pages/GuestDonation.vue`).
    - **Donor (External)**: Dashboard aggregates donation totals, supported elders, timeline feed (`app/Http/Controllers/DonorDashboardController.php`, `resources/js/pages/DonorDashboard.vue`). Sponsorship CRUD, visits, onboarding seeds, and hero slides exist with seed data.
    - **Branch/Admin**: Branch, Elder (with lifecycle/health/medication/case‑note submodules), Campaign, Visit, Staff, User, Role, Activity log, Notifications, Exports, and Report screens (see `routes/web.php`). Case management includes attachments and activity tracking.
    - **Ops/Automation**: Scheduled commands (`app/Console/Kernel.php`) for reminders, stats, promise checks, annual reports; Outbound message scaffolding, Telebirr webhook stub, payment transactions table, hero slides, campaign metrics, and queueable notifications.
    - **DX & Infra**: Vite PWA injectManifest setup (`vite.config.ts`), SSR hooks, GitHub Actions CI (lint + tests), Seeds for demo data, Docker compose example, DigitalOcean notes, and documentation under `MdDocuments/`.

## 2. Verified Foundation Highlights

- **Guest Engagement**: Landing analytics + gallery filters, CTA hand‑off to donation + pre-sponsorship forms.
- **Donation Pipeline**: Central `DonationController` records guest + authenticated gifts and spawns `PaymentTransaction` rows for simulated Telebirr flow.
- **Data Isolation**: Branch scope enforces branch admin visibility for scoped models, with branch-aware CRUD + exports and seed branches.
- **Operational UI**: All CRUD areas include AppLayout, breadcrumbs, filters, stats, exports, and activity timelines; notification bell + impersonation banners are wired via `HandleInertiaRequests`.
- **Automation Hooks**: Reminder command sends pledge/visit notices, Telebirr webhook updates donations + cache job warms widgets, and queues exist for outbound messaging.
- **Docs & Guidance**: Blueprint, schema, roadmap, upgrade recommendations, offline/PWA guidance, deployment script, and DigitalOcean steps live in `MdDocuments/`, ready for refinement.

## 3. Key Gaps & Risks (ordered by urgency)

1. **Guest funnel & CTA routing are broken** – Welcome hero, Father/Mother/Brother/Sister buttons, and “Sponsor this elder” CTAs often dump unauthenticated visitors onto `/dashboard` or `/register` (which resolves to `http://localhost` → 503). The new Pre‑Sponsorship modal saves leads but never pre-fills the Guest Donation form, elders detail pages do not always provide a safe back-to-home control, and there is no soft-landing modal when an action requires login. This leaves end users stuck before they can donate.
2. **Static assets & elder media still point to remote placeholders** – Browsers request `http://127.0.0.1:8000/images/mekodonia-logo.png` (404) and `/storage/https://via.placeholder.com/...` (403) because legacy seed data and hero slides reference absolute placeholder URLs. We need to ship local SVG/PNG assets (`public/images/mekodonia-logo.svg`, `public/images/monk-mekodoniya.jpg`), clean up storage, and guarantee the accessor logic always falls back to local files until media uploads are wired.
3. **RBAC/nav drift & seed refresh required** – `AppSidebar.vue`, `routes/web.php`, and `PermissionServiceProvider` are out of sync with the intended role matrix. Seeder data (roles, demo users, elders, hero slides) must be regenerated so dashboards, reports, and CTA links honor the updated RBAC plan and branch awareness.
4. **Payments still mocked** – `TelebirrService` only logs and returns a fake redirect while webhook trust uses a static header token (`app/Support/Services/TelebirrService.php`). No signing, callback verification, or real redirect/receipt handling exists; other gateways (CBE, Amole, card) are absent.
5. **Recurring/Auto-pay placeholders** – `RecurringPaymentService` only simulates subscription lifecycle (lines 10‑200) and never persists Donation entries or gateway metadata.
6. **Timeline event service mismatch** – `SponsorshipController` calls `$timelineEventService->create(...)` (lines 67‑175) but the service exposes `createEvent(...)`, so sponsorship create/update will throw.
7. **Report controller imports & exports** – `ReportController::generateImpactBook` references `Auth`, `User`, and `Pdf` without importing them and `exportReport()` still returns JSON placeholders (lines 195‑210). Impact book Blade/PDF templates are not provided.
8. **Branch isolation limited to Branch Admin** – `BranchScope` filters only for users with role `Branch Admin` (lines 19‑28). Staff/Auditor roles can still query cross‑branch data, and many models (Visits, Sponsorships) do not apply the scope at all.
9. **Guest receipts & donor comms** – Guest donations never send confirmations (SMS/email/PDF). Notification preferences exist, but outbound channels (SMS/WhatsApp) aren’t wired to actual providers.
10. **Offline/PWA queue not implemented** – Guidance exists in `MdDocuments/pwa_offline_sync_guidance.md`, but no IndexedDB queue, Background Sync registration, or Vue composables implement it.
11. **Localization & accessibility missing** – UI strings are hardcoded in English, Ethiopian calendar/currency helpers aren’t applied, and there’s no RTL or Amharic font handling.
12. **Data completeness & audits** – No elder consent upload, sponsorship agreement, payment reconciliation UI, or audit exports. Automation commands log but don’t guard against duplicates or operator overrides.
13. **Testing coverage** – Feature tests cover staff/users/dashboard but omit critical payment, sponsorship, visit, and report flows.

## 4. Finalization Plan

### Phase 0 – Guest Journey, RBAC & UI Stabilization (Week 1)

- **Welcome + CTA cleanup** – Rebuild the hero slider actions, Father/Mother/Brother/Sister cards, and “Sponsor this elder” buttons so they stay on guest routes (modal, new tab, or guest donation form) without redirecting to `/dashboard`. Handle register/login prompts via modal, ensure `route()` outputs relative URLs (respecting the current host/port), and add a consistent “Back to Welcome” affordance on public elder pages.
- **Guest donation hand-off** – Wire the Pre-Sponsorship modal to persist leads *and* pass context (`pre_sponsorship_id`, relationship, elder_id) into `GuestDonation.vue`, including UI hints for one-time vs sponsorship flows and a way back to the landing page.
- **Admin dashboard, report UI & sidebar audit** – Align `Dashboard.vue`, `Reports/AdminDashboard.vue`, `AppSidebar.vue`, and `NavMain` with the refreshed design system (spacing, button placement, responsive behavior) and verify every entry points at an existing route with the right permission guards.
- **Routes + RBAC sweep** – Review `routes/web.php` and the Spatie role bindings so guest routes stay public, donor dashboards use `External|Donor`, staff areas require the correct permissions, and branch scoping applies consistently. Recreate the RBAC seed data (roles, permissions, demo users) to match the matrix in `MdDocuments/rbac.md`.
- **Seeder + asset refresh** – Regenerate hero slides, elders, and demo donations so profile photos use `public/images/monk-mekodoniya.jpg` (or uploaded media), logo assets reference the SVG that exists in `public/images/`, and sample data is deterministic. Clean any stale storage files referencing placeholder domains.
- **Carry-over stability fixes** – Address the TimelineEvent service mismatch, import/impact-book gaps in `ReportController`, extend `BranchScope` + policies to staff roles, and send guest donation receipts/notifications. Top it off with high-value feature tests that cover guest donations, Telebirr webhook happy-path, sponsorship CRUD, and visit approvals.

### Phase 1 – Payments & Donor Journey (Weeks 2‑4)

- Implement real Telebirr integration: signing, redirect handling, callback verification, and failure management; add payment settings UI + secrets storage.
- Flesh out recurring payments for Telebirr/CBE (agreement flows, webhook ingestion, donation ledger updates).
- Build donor onboarding wizard (relationship selection, pledge slider, communication preferences, bank/Telebirr token capture).
- Generate receipts (PDF + SMS) and host them under `/receipts/{uuid}` for donors/auditors.

### Phase 2 – Branch Operations & Case Management (Weeks 4‑6)

- Expand elder lifecycle + health events UI, add consent/media uploads, and implement match/unmatch proposals with SLAs.
- Add donation reconciliation screen (import CSV/API, match vs. queue exceptions) and branch dashboards for promise tracking.
- Improve case notes with versioning, attachments, and permission fixes (`CaseNotePolicy` currently compares against lowercase `admin`).

### Phase 3 – Communication, Offline & Campaign Readiness (Weeks 6‑8)

- Finish outbound messaging service (provider adapters, retry policies, queue workers) and bind notification preferences to real channels.
- Implement offline queue (IndexedDB + Background Sync) for at least elder updates, visit logging, and guest pledges.
- Launch campaign landing builder (progress bars, shareable cards) and guest checkout shortcuts (anonymous meal purchase).

### Phase 4 – Reporting, Compliance & Performance (Weeks 8‑10)

- Ship admin report exports (PDF/Excel) and donor Impact Book with branded template + CDN assets, including the scheduled `reports:generate-annual` workflow and donor notifications (`app/Support/Services/ImpactBookGenerator.php`, `resources/views/reports/impact_book.blade.php`).
- Add localized strings, ETB currency helpers, Ethiopian calendar conversions, and Amharic fonts for RTL-safe layouts; share translations via `useLocale` and display Andegna dates on donor dashboards (`resources/lang/*/ui.php`, `resources/js/composables/useLocale.ts`, `resources/js/pages/Reports/DonorImpact.vue`).
- Introduce compliance guardrails (elder consent uploads, donation KYC flags, compliance export/redaction endpoints) and nightly encrypted backups with retention/purge logic backed by `backups:encrypted` (`database/migrations/2026_02_27_*`, `app/Http/Controllers/ComplianceController.php`, `app/Console/Commands/EncryptedBackupCommand.php`).

Each phase will produce demo-ready increments (deploy scripts + seeders updated accordingly). Once this blueprint is approved we can break it down into executable tickets (`upcomingtasklist.md`) and start implementing in priority order.
