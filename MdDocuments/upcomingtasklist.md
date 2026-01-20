# Mekodonia Home Connect – Upcoming Task List

> Ordered backlog extracted from the finalization blueprint. Tasks are grouped by phase; items in the **Blockers** section must ship before any new features.

## Blockers (Stability + correctness)
1. **Fix timeline event helper mismatch**  
   - Update `App\Support\Services\TimelineEventService` to expose a `create()` alias or update `SponsorshipController` (lines 67‑175) to call `createEvent()`.  
   - Add feature test that creating/updating a sponsorship enqueues a timeline row.
2. **Report controller imports + impact book template**  
   - Import `Auth`, `User`, and `Barryvdh\DomPDF\Facade\Pdf` inside `app/Http/Controllers/ReportController.php` and add `resources/views/reports/impact_book.blade.php` with branding.  
   - Replace the JSON stubs in `exportReport()` (lines 195‑210) with DomPDF / Laravel Excel exports.
3. **Branch isolation + policies**  
   - Extend `BranchScope` to include staff-facing roles (e.g., Branch Coordinator/Staff) and review models missing the scope (Visits, Sponsorships).  
   - Update policies to validate `branch_id` and fix lowercase `'admin'` comparisons in `App\Policies\CaseNotePolicy`.
4. **Guest donation acknowledgements**  
   - Send an email + database notification when `DonationController::storeGuest()` (lines 21‑49) runs; include a downloadable receipt placeholder using DomPDF.

## Phase 0 – Guest Journey & RBAC Hardening (Week 1)
- [ ] **Welcome + CTA cleanup**: Revisit `resources/js/pages/Welcome.vue` so Father/Mother/Brother/Sister tiles stay horizontal, CTA buttons open the pre-sponsorship modal or guest donation form instead of redirecting to `/dashboard`, and hero slides use relative URLs (respecting whatever host/port is serving the SPA). Capture the 503-on-`/register` regression by checking Ziggy/`APP_URL`, and add an obvious “Back to Welcome” action on every guest-only page.
- [ ] **Pre-sponsorship → donation hand-off**: Pass `pre_sponsorship_id`, `relationship_type`, and optional `elder_id` from `PreSponsorshipForm.vue` to `GuestDonation.vue`, surface those hints in the UI, and provide a cancel/back button so guests can navigate without using the browser chrome.
- [ ] **Elder gallery & public show UX**: Ensure each elder card (and `"Sponsor this elder"` CTA under `Elders/PublicShow.vue`) stays in guest context, uses the local fallback photo (`public/images/monk-mekodoniya.jpg`), and offers one-time versus sponsorship buttons that never leak to admin dashboard routes.
- [ ] **Admin dashboard + reports polish**: Sweep `Dashboard.vue`, `DonorDashboard.vue`, and `Reports/AdminDashboard.vue` for spacing, button placement, and action links so report exports, filters, and cards are aligned with the refreshed design brief.
- [ ] **Sidebar, navigation & routing guards**: Reconcile `AppSidebar.vue`, `NavMain.vue`, and `routes/web.php` with the RBAC matrix—guest routes stay public, donor dashboards use `External|Donor`, branch staff respect `branch_id`, and no sidebar entry links to a missing or unauthorized view.
- [ ] **RBAC + seeder refresh**: Rebuild `RolePermissionSeeder`, `UserSeeder`, `ElderFactory`, `HeroSlideSeeder`, and demo data so permissions/roles match `MdDocuments/rbac.md`, every elder has proper branch + consent placeholders, and seed output (including hero assets) references the local logo/photo files instead of remote placeholders.
- [ ] **Static asset cleanup**: Replace any `.png` logo references with the shipped SVG, duplicate/upload `public/images/monk-mekodoniya.jpg` as needed, prune `/storage` files pointing at `https://via.placeholder.com`, and verify the asset accessor + hero resolver always fall back to local images.
- [ ] **Add smoke tests** covering guest donation happy path, Telebirr webhook stub, sponsorship CRUD, and visit approvals so we can freeze the regression once these flows are fixed.
- [ ] **Extend staff/visit/sponsorship exports** to include branch, elder priority metadata, and sticky filters; confirm generated CSV/PDF output mirrors what admins need during audits.
- [ ] **Implement audit log download (CSV)** using the `HandlesDataExport` concern for compliance reviewers.

## Phase 1 – Payments & Donor Journey (Weeks 2‑4)
- [ ] **Telebirr real integration**: signing helper, RSA keys, payment initiation request, redirect handler, callback verification, and mismatch retry workflow. Add config entries + secrets UI.
- [ ] **Recurring auto-debit**: finish `RecurringPaymentService` (lines 10‑200) to call Telebirr/CBE APIs, persist subscription metadata, and create donations upon webhook/cron callbacks.
- [ ] **Donor onboarding wizard**: multi-step Vue flow (relationship → pledge → contacts → payment preference); store onboarding progress + preferences in DB.
- [ ] **Receipts + statements**: generate PDF + SMS for every donation, expose `/receipts/{uuid}` route, and surface an annual summary download under donor dashboard.

## Phase 2 – Branch Ops & Case Management (Weeks 4‑6)
- [ ] **Elder lifecycle UI**: render status timeline, health assessments, meds, and consent files on `Elders/Show.vue`; add create/edit modals.
- [ ] **Match / unmatch workflow**: staff propose matches, donors accept via notification/email, unresolved proposals auto-expire after 72h, and elders return to pool automatically.
- [ ] **Reconciliation screen**: upload Telebirr/CBE CSV exports, auto-match by reference, queue exceptions, and show branch-level outstanding balances.
- [ ] **Case notes overhaul**: add attachments, version history, visibility toggles, and permission checks aligned with branch + role.

## Phase 3 – Communications, Offline, Campaigns (Weeks 6‑8)
- [ ] **Outbound messaging service**: implement provider adapters (email, SMS, WhatsApp), per-channel throttling, retry policies, and admin UI for queued/sent/failure states. Respect `user_notification_preferences`.
- [ ] **Offline queue**: build IndexedDB storage + Background Sync worker per `MdDocuments/pwa_offline_sync_guidance.md` so elder updates, visit logs, and guest pledges sync once online.
- [ ] **Campaign microsites**: create CMS-like CRUD for campaign landing pages with goal tracking, shareable cards, and guest checkout shortcuts (select any urgent elder or general pool).
- [ ] **Visitor scheduling UX**: extend visit scheduler with calendar view, translator / transport toggles, and branch approval SLA notifications.

## Phase 4 – Reporting, Localization, Compliance (Weeks 8‑10)
- [ ] Finish admin report exports (PDF + Excel) with charts, promise stats, and branch filters; cache expensive aggregates via Redis.
- [ ] Build donor “Impact Book” template with branded sections (timeline, photos) and send automatically yearly via cron + outbound service.
- [ ] Internationalization: introduce Amharic (& future Afaan Oromo) translations, ETB currency helper/filter, Ethiopian calendar display (`andegna`), and RTL-safe typography.
- [ ] Compliance & backups: add elder consent upload requirements, KYC workflow for > USD 500, Redact/Export endpoints for donors, automated encrypted S3 backups, and retention policies.

## Always-on tasks
- Keep CI (lint/tests) green; extend Pest coverage whenever touching a module.
- Document major decisions in `/docs/` (ADR for payments, offline sync, notification providers).
- Update `MdDocuments/finializeProject.md` and this task list after every phase retro so everyone stays aligned.
