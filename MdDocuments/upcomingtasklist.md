# Mekodonia Home Connect – Upcoming Task List

> Ordered backlog extracted from the finalization blueprint. Tasks are grouped by phase; items in the **Blockers** section must ship before any new features.

## Blockers (Stability + correctness)
- [x] **Fix timeline event helper mismatch**  
  - Updated `SponsorshipController` to call `TimelineEventService::createEvent()` with explicit metadata and loaded relations.  
  - Added coverage in `tests/Feature/GuestFlowsTest.php` to assert timeline entries are created for sponsorship create/update actions.
- [x] **Report controller imports + impact book template**  
  - Imported the missing facades, shipped `resources/views/reports/impact_book.blade.php`, and added an admin dashboard export view + Excel export class.  
  - `ReportController::exportReport()` now streams real DomPDF/Excel responses instead of JSON placeholders.
- [x] **Branch isolation + policies**  
  - Applied `BranchScope` to `CaseNote`, auto-inferred branch IDs from elders, and tightened policy checks with correct role names + branch matching for admins vs. branch staff.
- [x] **Guest donation acknowledgements**  
  - Guest donations now generate a branded PDF receipt, e-mail it to the donor, and notify the branch/admin team via database notifications.

## Phase 0 – Guest Journey & RBAC Hardening (Week 1)
- [x] **Welcome + CTA cleanup**: Revisit `resources/js/pages/Welcome.vue` so Father/Mother/Brother/Sister tiles stay horizontal, CTA buttons open the pre-sponsorship modal or guest donation form instead of redirecting to `/dashboard`, and hero slides use relative URLs (respecting whatever host/port is serving the SPA). Capture the 503-on-`/register` regression by checking Ziggy/`APP_URL`, and add an obvious “Back to Welcome” action on every guest-only page.
- [x] **Pre-sponsorship → donation hand-off**: Pass `pre_sponsorship_id`, `relationship_type`, and optional `elder_id` from `PreSponsorshipForm.vue` to `GuestDonation.vue`, surface those hints in the UI, and provide a cancel/back button so guests can navigate without using the browser chrome.
- [x] **Elder gallery & public show UX**: Ensure each elder card (and `"Sponsor this elder"` CTA under `Elders/PublicShow.vue`) stays in guest context, uses the local fallback photo (`public/images/monk-mekodoniya.jpg`), and offers one-time versus sponsorship buttons that never leak to admin dashboard routes.
- [x] **Admin dashboard + reports polish**: Sweep `Dashboard.vue`, `DonorDashboard.vue`, and `Reports/AdminDashboard.vue` for spacing, button placement, and action links so report exports, filters, and cards are aligned with the refreshed design brief.
- [x] **Sidebar, navigation & routing guards**: Reconcile `AppSidebar.vue`, `NavMain.vue`, and `routes/web.php` with the RBAC matrix—guest routes stay public, donor dashboards use `External|Donor`, branch staff respect `branch_id`, and no sidebar entry links to a missing or unauthorized view.
- [x] **RBAC + seeder refresh**: Rebuild `RolePermissionSeeder`, `UserSeeder`, `ElderFactory`, `HeroSlideSeeder`, and demo data so permissions/roles match `MdDocuments/rbac.md`, every elder has proper branch + consent placeholders, and seed output (including hero assets) references the local logo/photo files instead of remote placeholders.
- [x] **Static asset cleanup**: Replace any `.png` logo references with the shipped SVG, duplicate/upload `public/images/monk-mekodoniya.jpg` as needed, prune `/storage` files pointing at `https://via.placeholder.com`, and verify the asset accessor + hero resolver always fall back to local images.
- [x] **Add smoke tests** covering guest donation happy path, Telebirr webhook stub, sponsorship CRUD, and visit approvals so we can freeze the regression once these flows are fixed. *(Telebirr webhook still pending real integration test)*
- [x] **Extend staff/visit/sponsorship exports** to include branch, elder priority metadata, and sticky filters; confirm generated CSV/PDF output mirrors what admins need during audits.
- [x] **Implement audit log download (CSV)** using the `HandlesDataExport` concern for compliance reviewers.

## Phase 1 – Payments & Donor Journey (Weeks 2‑4)
- [x] **Telebirr real integration**: signing helper, RSA keys, payment initiation request, redirect handler, callback verification, and mismatch retry workflow. Add config entries + secrets UI.
- [x] **Recurring auto-debit**: Fully rewired `RecurringPaymentService`, added gateway-specific config/env hooks, stored subscription metadata on `sponsorships`, created a scheduled `recurring:charge-due` command, and now persist donations + payment transactions whenever Telebirr/CBE auto-debits run (simulation friendly with real HTTP paths ready).
- [x] **Donor onboarding wizard**: Added the `donor_profiles` table/model, multi-step Inertia wizard, onboarding routes/controllers, and dashboard gate so donors capture relationship intent, pledge, contact prefs, and payment mode before landing on their dashboard.
- [x] **Receipts + statements**: Added receipt UUID/issued-at columns, a reusable receipt generator service, donor/guest notifications, `/receipts/{uuid}` streaming route, and an annual statement download from the donor dashboard once donations settle.

## Phase 2 – Branch Ops & Case Management (Weeks 4‑6)
- [x] **Elder lifecycle UI**: Refreshed `Elders/Show.vue` with proper status timelines, richer health/med sections, and a new consent/attachment module backed by `elder_documents` + upload/download/delete flows.
- [x] **Match / unmatch workflow**: Donor dashboard now surfaces pending proposals with accept/decline actions, proposal events trigger mail/database notifications plus auto-expiry via `sponsorship-proposals:expire`, and staff can unmatch active sponsorships (returning elders to the pool) directly from `Sponsorships/Show`.
- [x] **Reconciliation screen**: Added CSV import storage (`payment_reconciliation_imports/items`), automated + manual matching workflows, sidebar navigation, and Inertia screens so finance staff can upload Telebirr/CBE exports, auto-match donations, and triage unmatched balances per branch.
- [x] **Case notes overhaul**: Introduced attachment uploads, version history, branch-only visibility, and refreshed permissions/UI so staff can collaborate securely while keeping donor-visible notes curated.

## Phase 3 – Communications, Offline, Campaigns (Weeks 6‑8)
- [x] **Outbound messaging service**: Implemented shared notification channel + UI log so staff can filter queued/sent/failed messages, seeded `notifications.view`, and kept it wired to the existing queue/commands for retries and cleanup.
- [x] **Offline queue**: Added IndexedDB-backed request queue, axios interceptor, and service worker sync hook so elder updates, visits, and donations captured offline are retried automatically once connectivity returns.
- [x] **Campaign microsites**: Added microsite fields + hero uploads to campaign CRUD, a public `campaign/{slug}` landing page with progress stats/urgent elders, and CTA links that pass `campaign_id` into the guest donation flow.
- [x] **Visitor scheduling UX**: delivered a calendar-based scheduling overview, translator/transport toggles plus notes in the visit forms, and branch SLA reminder cards + alerts so pending approvals surface before they breach.

## Phase 4 – Reporting, Localization, Compliance (Weeks 8‑10)
- [x] Finish admin report exports (PDF + Excel) with charts, promise stats, and branch filters; cache expensive aggregates via Redis.
  - Live dashboard now serves cached stats/trends plus fulfillment badges before exporting; the PDF + Excel views include the monthly donations trend table and branch metadata.
- [x] Build donor “Impact Book” template with branded sections (timeline, photos) and send automatically yearly via cron + outbound service.
  - ImpactBookGenerator now produces PDF copies stored under `storage/app/public/impact_books/{year}`, the donor dashboard lists the generated reports, and `reports:generate-annual` notifies donors via `AnnualImpactBookReadyNotification`.
- [x] Internationalization: introduce Amharic (& future Afaan Oromo) translations, ETB currency helper/filter, Ethiopian calendar display (`andegna`), and RTL-safe typography.
  - Added shared `ui` translation bundles for `en`, `am`, and `om`, provided `useLocale` helpers, and surfaced the Ethiopian calendar date inside the donor experience.
- [x] Compliance & backups: add elder consent upload requirements, KYC workflow for > USD 500, Redact/Export endpoints for donors, automated encrypted S3 backups, and retention policies.
  - Added consent/upload metadata, KYC flags on donations, compliance controller endpoints for exports/redaction, and a scheduled `backups:encrypted` command that stores encrypted JSON snapshots on the `s3_backups` disk.

## Always-on tasks
- Keep CI (lint/tests) green; extend Pest coverage whenever touching a module.
- Document major decisions in `/docs/` (ADR for payments, offline sync, notification providers).
- Update `MdDocuments/finializeProject.md` and this task list after every phase retro so everyone stays aligned.
