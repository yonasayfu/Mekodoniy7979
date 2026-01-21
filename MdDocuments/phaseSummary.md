# Phase Summary

## Phase 0 – Guest Journey & RBAC Hardening
- Welcome hero, CTA, and layout polish with the background video, horizontal CTA tiles, and “back to welcome” affordance resolved text/layout overlap (`MdDocuments/changeOne.md:7-33`).
- Guest donation hand-off now carries pre-sponsorship context/notes, retains a Ziggy-based register link, and stays within guest flows (`MdDocuments/changeOne.md:11-15`).
- RBAC/BranchScope, dashboard/report UI tweaks, sidebar/routing guard hardening, and local asset fallbacks were shipped along with normalized seeders/commands/data (`MdDocuments/changeOne.md:16-33`).

## Phase 1 – Payments & Donor Journey
- Telebirr integration with signing/verification helpers plus config toggles to run in simulate vs. live (`MdDocuments/changeOne.md:38-40`).
- Recurring payment service, subscription metadata, scheduled `recurring:charge-due`, and donor onboarding wizard (profile/model/controller/JS) now enforce onboarding before dashboard access (`MdDocuments/changeOne.md:51-60`).
- Donation receipt/statement generation, notifications, and public streaming endpoints were implemented (`MdDocuments/changeOne.md:62-65`).

## Phase 2 – Branch Ops & Case Management
- Elder lifecycle UI enhancements including consent/attachment module backed by new documents tables/controllers and timeline updates (`MdDocuments/changeOne.md:67-70`).
- Sponsorship proposal workflows, notifications, auto-expiry command, and unmatch UI now keep donors informed and return elders to the pool safely (`MdDocuments/changeOne.md:71-75`).
- Payment reconciliation import/matching workspace plus RBAC-scoped Reconciliation screens were shipped (`MdDocuments/changeOne.md:77-80`).
- Case notes versions/attachments with branch isolation, policy updates, and Vue filtering were introduced (`MdDocuments/changeOne.md:81-85`).

## Phase 3 – Communications, Offline, Campaigns
- Outbound messaging console (controller, Vue page, sidebar route) and shared notification channel are wired to queue infrastructure (`MdDocuments/changeOne.md:98-102`).
- Offline queue service worker, IndexedDB queue, and global HTTP helper ensure retries for elder/donation requests (`MdDocuments/changeOne.md:103-107`).
- Campaign microsite fields, landing page, and guest attribution through donation CTAs were implemented (`MdDocuments/changeOne.md:108-111`).
- Visit scheduling/logistics UX updates (translator, transport, SLA trackers) and data/logistics storage were added (`MdDocuments/changeOne.md:112-116`).

## Phase 4 – Reporting, Localization, Compliance (In Progress)
- Admin report exports, cached dashboard stats, and trend data for PDF/Excel exports are live, with Impact Book generator + annual report notifications in place (`MdDocuments/changeOne.md:117-123`).
- Built a branded Impact Book template with Ethiopian date callouts, persisted generated PDFs via `ImpactBookGenerator`, and now notify donors when new annual reports drop (`app/Http/Controllers/ReportController.php`, `resources/views/reports/impact_book.blade.php`, `app/Console/Commands/GenerateAnnualReports.php`).
- Introduced shared `ui` translations (`en`, `am`, `om`), a `useLocale` composable, RTL-safe styling, and Ethiopian calendar helpers to surface `andegna` dates on the donor-facing dashboards (`resources/js/composables/useLocale.ts`, `resources/lang/*/ui.php`, `resources/js/pages/Reports/DonorImpact.vue`, `app/Support/Calendars/EthiopianCalendar.php`).
- Implemented elder consent capture, KYC flags for large donations, compliance export/redaction endpoints, encrypted S3 backup snapshots, and new migrations/commands to support these workflows (`app/Http/Controllers/ComplianceController.php`, `app/Support/Services/KycService.php`, `database/migrations/2026_02_27_*`, `app/Console/Commands/EncryptedBackupCommand.php`).
