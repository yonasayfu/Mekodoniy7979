# Change Log – Phase 0 Kickoff Prep

## 1. Documentation Alignment
- Updated `MdDocuments/finializeProject.md` to highlight the urgent guest experience gaps (broken CTAs, placeholder assets, RBAC drift) and rewrote **Phase 0** to focus on welcome-flow fixes, RBAC/seed refresh, and stability work before deeper features.
- Rebuilt `MdDocuments/upcomingtasklist.md` so Phase 0 tasks mirror the refreshed blueprint (welcome CTA cleanup, pre-sponsorship → donation hand-off, elder gallery UX, sidebar/routing audit, RBAC + seeder refresh, asset cleanup, smoke tests, and audit-ready exports).

## 2. Welcome Page Enhancements
- Prevented hero title/description overlap by wrapping the slide copy in a centered translucent card, increasing line-height, and stacking CTAs responsively to keep the text legible on short viewports (`resources/js/pages/Welcome.vue`).
- Added a looping muted background video (`public/images/6096-188704568_small.mp4`) behind the hero content with stronger overlays so copy stays readable while showcasing Mekodonia visuals.

## 3. Guest Donation Flow Improvements
- Added a Ziggy-relative register CTA plus richer context on the guest donation page so unauthenticated users stay inside the guest journey (`resources/js/pages/Welcome.vue`, `GuestDonation.vue`).
- Connected the Pre-Sponsorship form to the donation experience by passing `mode=sponsorship`, surfacing the lead/relationship context, providing a “Back to Welcome” escape, and capturing optional notes dedicated to the elder (`app/Http/Controllers/PreSponsorshipController.php`, `resources/js/pages/GuestDonation.vue`).
- Allowed guests to submit contextual notes/dedications that persist on the backend by extending `StoreDonationRequest` validation and storing the value in `DonationController::storeGuest`.

## 4. RBAC & Branch Isolation Reinforcements
- Generalized `App\Scopes\BranchScope` so every branch-scoped staff role (Branch Admin, Branch Coordinator, Field teams, etc.) automatically filters Eloquent queries, while Super/Admin/Auditor roles remain unscopeable. Visits and Sponsorships now include the scope and auto-populate their `branch_id` from the linked elder to stop cross-branch data leakage (`app/Scopes/BranchScope.php`, `app/Models/Visit.php`, `app/Models/Sponsorship.php`).
- Added a dedicated `branch_id` column for sponsorships plus factory/seeder logic to populate it, ensuring seed data and future records reference local media/branch info (`database/migrations/2026_01_28_000001_add_branch_id_to_sponsorships_table.php`, `database/factories/SponsorshipFactory.php`, `database/seeders/SponsorshipSeeder.php`).
- Rebuilt RBAC seeds to include an explicit **Branch Admin** role with scoped permissions and created demo users for both Branch Admin and Branch Coordinator personas so dashboards/RBAC behavior match the documentation (`database/seeders/RolePermissionSeeder.php`, `database/seeders/UserSeeder.php`).

## 5. Phase 0 UI Polish
- Delivered a refreshed public Elder detail page with clear priority chips, branch/location context, and differentiated CTA buttons so guests understand how to help without touching admin dashboards (`resources/js/pages/Elders/PublicShow.vue`).
- Added a branch-health hero card plus better maintenance insights to the admin dashboard, giving staff instant visibility into high-priority/overdue tasks alongside existing quick links (`resources/js/pages/Dashboard.vue`).
- Reworked the reports admin dashboard with filter controls that behave well on mobile, summary metric cards, and a modern featured-matches carousel so stakeholders get print-ready snapshots (`resources/js/pages/Reports/AdminDashboard.vue`).
- Tightened navigation by resolving dashboard links and active states directly inside `AppSidebar.vue`, preventing donors from landing on admin routes while highlighting the current section.
- Hardened elder media fallbacks so any stale `https://via.placeholder.com` references or malformed stored paths now resolve to the local Mekodonia photo instead of 404-ing (`app/Models/Elder.php`).

## 6. Static Asset Cleanup
- Added an Artisan command `media:normalize` that rewrites any elder or hero slide images pointing to remote placeholder URLs (or empty/invalid paths) to the shipped local assets (`app/Console/Commands/NormalizeMediaAssets.php`, `app/Console/Kernel.php`). Run `php artisan media:normalize` (or `--dry-run`) to sanitize existing databases without touching new uploads.

## 7. Audit Log Exports & Richer CSV Data
- Built an audit-ready export for activity logs using the shared `HandlesDataExport` tooling, added a controller endpoint + route, and exposed a “Download CSV” button on the Activity Logs screen so compliance teams can capture a full change trail in one click (`app/Http/Controllers/ActivityLogController.php`, `routes/web.php`, `resources/js/pages/ActivityLogs/Index.vue`, `app/Support/Exports/ExportConfig.php`).
- Enriched staff, visit, and sponsorship exports with branch names and elder priority columns so CSV/PDF outputs mirror what regional admins need during reviews (`app/Support/Exports/ExportConfig.php`).

## 8. Smoke Tests for Critical Journeys
- Added `tests/Feature/GuestFlowsTest.php` covering guest donations, sponsorship CRUD, and visit approvals so the most fragile end-to-end flows have regression coverage while we iterate.

## 9. Telebirr Integration Hardening
- Rebuilt `TelebirrService` with RSA signing/verification helpers, configurable merchant keys, simulated vs. live modes, HTTP calls to Telebirr endpoints, and better failure logging. Callback handling now verifies Telebirr’s signature before updating transactions (`app/Support/Services/TelebirrService.php`, `app/Http/Controllers/Payments/TelebirrWebhookController.php`).
- Extended `config/services.php` with merchant IDs/keys + a `TELEBIRR_SIMULATE` flag so environments can switch between mocked behavior and real API traffic without code changes.

## 10. Blocker Round – Timeline, Reports & Branch Policies
- Resolved the sponsorship timeline bug by wiring `SponsorshipController` to `TimelineEventService::createEvent()` and augmented `tests/Feature/GuestFlowsTest.php` to assert events are created on store/update.
- Delivered export-ready reporting by importing the missing facades, adding `app/Exports/AdminDashboardExport.php`, and shipping `resources/views/reports/impact_book.blade.php` plus `resources/views/reports/admin_dashboard_export.blade.php` so PDF/Excel downloads now return branded files.
- Scoped case notes with `BranchScope`, auto-inferred `branch_id` from elders, and updated `App\Policies\CaseNotePolicy` to respect branch ownership while letting Admin/Super Admin override with the correct role casing.

## 11. Guest Donation Acknowledgements
- Extended `DonationController::storeGuest()` to generate a DomPDF receipt (`resources/views/donations/guest_receipt.blade.php`), store it under `storage/app/public/receipts/generated`, and keep any manual proof uploads separate.
- Added queued notifications so guests receive the receipt via email (`app/Notifications/GuestDonationReceiptNotification.php`) and branch/admin staff are alerted through database notifications (`app/Notifications/GuestDonationLoggedNotification.php`) with quick links back to the donations report.

## 12. Recurring Auto-Debit Foundations
- Added `subscription_gateway` + JSON metadata columns to sponsorships, made them castable, and refreshed the factory so seeds include autoplay-ready records (`database/migrations/2026_02_15_000001_add_subscription_gateway_columns_to_sponsorships_table.php`, `app/Models/Sponsorship.php`, `database/factories/SponsorshipFactory.php`).
- Wired `config/services.php` + `.env.example` with Telebirr/CBE recurring credentials so environments can flip between simulation and live modes without code edits.
- Rebuilt `App\Support\Services\RecurringPaymentService` to talk to Telebirr/CBE endpoints (or simulate), persist subscription IDs + metadata, and record `Donation`/`PaymentTransaction` rows whenever a scheduled auto-debit succeeds.
- Introduced the `recurring:charge-due` artisan command (scheduled daily via `app/Console/Kernel.php`) to sweep active subscriptions, optionally run in `--dry-run`, and delegate to the service for each due pledge.

## 13. Donor Onboarding Wizard
- Created the `donor_profiles` table/model + factory so each donor can track relationship goals, pledge targets, contact channels, payment preference, and completion timestamps (`database/migrations/2026_02_15_000002_create_donor_profiles_table.php`, `app/Models/DonorProfile.php`, `database/factories/DonorProfileFactory.php`, `database/seeders/UserSeeder.php`).
- Added the `DonorOnboardingController`, request validation, Inertia wizard (`resources/js/pages/DonorOnboarding/Wizard.vue`), and new routes so donors step through relationship → pledge → contact → payment with autosave.
- Updated `DonorDashboardController` to enforce onboarding completion before surfacing insights and exposed a daily-usable wizard entrypoint inside the donor auth group. 

## 14. Receipts & Statements
- Extended the `donations` table with `receipt_uuid`/`receipt_issued_at`, added `DonationReceiptService`, and consolidated all PDFs under `resources/views/donations/receipt.blade.php` + annual statement view so every donation (guest or authenticated) gets a stored receipt.
- Wired guest + Telebirr flows to generate/store receipts immediately, fire guest/donor notifications (including `/receipts/{uuid}` links), and exposed `receipts.annual` downloads on the donor dashboard.
- Shipped `DonationReceiptController` routes for public receipt streaming + authenticated annual statements, plus new UI affordances on the donor dashboard for fast access to statements.

## 15. Elder Lifecycle & Consent Workspace
- Added the `elder_documents` table/model/controller + upload form request so staff can capture signed consent forms, IDs, and medical reports per elder (stored under `storage/app/public/elders/documents`).
- `Elders/Show.vue` now renders lifecycle changes via `ActivityTimeline`, keeps the existing health/medication CRUD in one surface, and introduces a consent/attachment module with upload/download/delete controls gated by `elders.manage`.
- Controller payloads were updated to hydrate document metadata (including download URLs and uploader info) so the new Vue UI remains reactive without extra API calls.

## 16. Match & Unmatch Workflow
- Introduced `SponsorshipProposal` notifications plus the `ElderMatchStateService` so every proposal creation/accept/decline/cancel/expiry updates the elder’s `current_status`, records a timeline event, and keeps donors/staff in the loop via email + in-app alerts. Pending invitations now auto-expire hourly through the updated `sponsorship-proposals:expire` command.
- Added donor-facing proposal management on the `DonorDashboard` (pending invitation list with accept/decline buttons) and wired the proposal controller to validate donor roles, log actions, and spin up sponsorships automatically upon acceptance.
- Delivered an explicit unmatch endpoint/UI on `Sponsorships/Show.vue` (with optional reason capture) so staff can retire relationships, notify donors, and return elders to the public gallery without manual DB edits.

## 17. Donation Reconciliation Workspace
- Created `payment_reconciliation_imports` + `payment_reconciliation_items` tables, Eloquent models, and a matching service that ingests Telebirr/CBE CSV exports, auto-links rows to donations/payment transactions, and tracks branch-aware status totals.
- Added the `PaymentReconciliationController` + Inertia screens (`Reconciliation/Index.vue`, `Reconciliation/Show.vue`) so finance officers can upload files, review import history, filter unmatched rows, manually link donations, or ignore noise entries—all scoped by RBAC/branches and reachable from the sidebar.
- Seeded navigation + routes behind `donations.manage`, and exposed manual matching/ignoring endpoints so branch finance teams can keep outstanding balances tidy without touching the database directly.

## 18. Case Notes Overhaul
- Added versioning + attachment support for case notes via new tables (`case_note_versions`, `case_note_attachments`) and controller logic so every edit captures history, and staff can upload supporting files per elder while keeping branch scope/cleanup automatic.
- Expanded visibility controls to include branch-only notes, wired `case_notes.*` permissions through seeds/routes, and refreshed the Vue component so users can filter, preview history, and remove attachments inline.
- API responses now include attachment metadata and recent versions, keeping the elder detail view in sync with the richer backend without extra round-trips.

## 19. Outbound Messaging Console & Channel
- Added `OutboundMessageController` + Vue dashboard (`resources/js/pages/Outbound/Index.vue`) so staff can filter/sort queued/sent messages, keeping an eye on failures without digging into the DB. Sidebar navigation now exposes the log for users with `notifications.view`.
- Introduced an `OutboundChannel` for Laravel notifications plus the associated permission seeds/routes so future notifications can rely on the shared queue infrastructure instead of bespoke mailers.

## 20. Offline Queue & Background Sync
- Wired a global axios wrapper (`resources/js/lib/http.ts`) with IndexedDB persistence (`resources/js/lib/offlineQueue.ts`) so any request that fails while offline is queued locally, registered with the service worker, and retried automatically once connectivity returns.
- Extended the custom service worker (`resources/js/sw.js`) to handle the `offline-submission-sync` tag, drain the queue, and retry each request in order; the SPA listens to online/offline events (`resources/js/app.ts`) to reflect status and trigger manual drains when the browser lacks Background Sync support.
- Swapped client modules (notifications, global search, 2FA, etc.) to use the shared HTTP helper so critical elder/donation actions benefit from the offline queue without further code changes.

## 21. Campaign Microsites
- Added microsite-focused fields (short blurb, CTA text/URL, accent color, hero image, video) plus upload handling to `CampaignController`, giving admins full control over the public share experience directly from Create/Edit forms.
- Shipped a guest-facing landing route (`campaign/{slug}` → `Campaigns/Landing.vue`) that showcases hero imagery, progress stats, urgent elders, and branded CTAs that auto-pass `campaign_id` into the guest donation form.
- Updated internal views so Create/Edit collect the new data, Show includes a quick “View microsite” shortcut, and guest donations now honor the `campaign_id` query parameter for seamless attribution.

## 22. Visitor Scheduling UX
- Added logistics metadata to the `visits` table (translator/transport flags, preference notes, SLA timestamps) plus a dedicated `config/visits.php` and `.env` knobs so branch SLA windows and reminder thresholds can be tuned per environment.
- `VisitController::index` now serves responsive calendar payloads, inline stats, and SLA alert cartridges while normalizing logistics payloads during `store`/`update` so every visit tracks translator needs, transport preferences, and deadlines.
- Visitor forms gained translator/transport toggles, language/preference pickers, and logistics notes so branch staff can capture grant-specific requirements before saving; the public show page now surfaces those details along with SLA/approval timestamps for quick reference.

## 23. Admin Report Exports
- Cached the enhanced dashboard payload (branch/date-range aware) and now include promise stats, monthly trend data, and branch-filtered guest donations before building the summary so exports stay fast even under load (`app/Services/ReportService.php`).
- `ReportController` feeds the Inertia view and export endpoints with the cached stats/trend payload, plus the PDF/Excel exports now render the monthly donations trend and branch metadata (`app/Http/Controllers/ReportController.php`, `resources/views/reports/admin_dashboard_export.blade.php`, `app/Exports/AdminDashboardExport.php`).
- The Vue dashboard surfaces fulfilment badges and a six-month donations trend chart before printing/exporting so stakeholders can tell the story visually (`resources/js/pages/Reports/AdminDashboard.vue`).

## 24. Donor Impact Book
- Introduced `ImpactBookGenerator` to render the branded Impact Book PDF, store it under `storage/app/public/impact_books/{year}`, and call it from `ReportService::generateAnnualReports` so every consistent donor gets a yearly summary without manual overhead (`app/Support/Services/ImpactBookGenerator.php`, `app/Services/ReportService.php`).
- Refined `generateAnnualReports` so it now looks for donors who actively sponsored elders and made approved payments during the target year (instead of relying solely on the `consecutive_months_kept` flag) so running `php artisan reports:generate-annual` produces PDFs even in a fresh dataset.
- Annual report generation now notifies donors via `AnnualImpactBookReadyNotification`, stores the `AnnualReport` record with the PDF path, and sends CSV/Excel exports for empowered supervisors; a download controller + Tao route ensure donors can fetch the stored file (`app/Notifications/AnnualImpactBookReadyNotification.php`, `app/Http/Controllers/AnnualReportController.php`, `routes/web.php`).
- The donor dashboard lists the available Impact Books and lets donors download the latest issue, while the impact book blade now shows timeline highlights, trend tables, and a gratitude note per year (`resources/js/pages/DonorDashboard.vue`, `resources/views/reports/impact_book.blade.php`).
