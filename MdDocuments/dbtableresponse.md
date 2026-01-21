# Database Table Reference

This document captures every major table created/extended by the migration stack you just ran (`php artisan migrate:fresh --seed`). Each entry lists: the migration that introduced or mutated the table, the data it holds, and the relationships it participates in. Use this as a single stop for the schema narrative before building queries, seeders, or docs.

## Table Catalog

| Table | Migration(s) | Purpose / Key Columns | Primary Relationships |
| --- | --- | --- | --- |
| `branches` | `0001_01_01_000003_create_branches_table`, `2025_12_17_081735_add_is_active_to_branches_table` | Stores branch metadata (name, address, active flag). `branch_leader_id`, SLA/contact info live on branch version. | `hasMany` elders, staff, donations, visits. |
| `users` | `0001_01_01_000004_create_users_table`, multiple adds (`2025_10_23_*`, `2025_12_16_*`, `2026_02_27_000014` etc.) | Houses staff/admin/donor accounts, recovery fields, kicks/bans/mutes, redaction flags. | `belongsTo` branch, `hasMany` donations/visits/logs, `hasOne` donor profile. |
| `staff` | `2025_01_01_200000_create_staff_table`, `2025_01_01_230000_add_avatar_path_to_staff_table` | Staff profile facts (role, branch, avatar, contact). | `belongsTo` branch, `hasMany` activity logs/visits. |
| `activity_logs` | `2025_01_01_210000_create_activity_logs_table` | Audit trail for models. | Polymorphic `subject` to elders/donations/users; used by compliance exports. |
| `data_exports` | `2025_01_01_220000_create_data_exports_table` | Records generated exports (audit/financial). | Linked to user who triggered export. |
| `cache`, `jobs` | `0001_01_01_000001_create_cache_table`, `0001_01_01_000002_create_jobs_table` | Laravel infrastructure tables for caching/queue. | N/A. |
| `notifications`, `personal_access_tokens`, `mailbox_*`, `two_factor_*` | various `2025_01_01_*` and `2025_10_*` migrations | Support notifications, mailbox, 2FA, and API tokens. | Tie to `users` via `notifiable_id`/`tokenable_id`. |
| `donations` | `2025_12_15_133659_create_donations_table`, then schema tweaks (`2025_12_17_083012`, `2025_12_17_102256`, `2025_12_22_200000`, `2025_12_22_200200`, `2026_02_15_000001`, `2026_02_16_000003`, `2026_02_27_000013`) | Tracks gifts (amount, currency, status, payment gateway, receipt fields, KYC flags). Added branch/campaign/sponsorship/receipt metadata plus `kyc_*`. | `belongsTo` user (donor), elder, sponsorship, branch, campaign; `hasMany` payment transactions. |
| `elders` | `2025_12_15_140219_create_elders_table`, multiple enrichments (`2025_12_17_101915` relationships + status, `2025_12_22_190000` lifecycle, `2026_02_20_000004` documents, `2026_02_27_000012` consent) | Elder profile (name, priority, branch, health, consent files, documents). | `belongsTo` branch; `hasMany` donations, visits, case notes, sponsorships, documents, timeline events. |
| `visits` | `2025_12_15_150146_create_visits_table`, `2025_12_17_082700_add_approved_by_to_visits_table`, `2026_02_25_000011_add_logistics_fields_to_visits_table` | Logs branch visits (date, translator, transport, approvals, SLA). | `belongsTo` branch, elder, staff (approved_by). |
| `timeline_events` | `2025_12_15_152351_create_timeline_events_table` | Donor timeline (event type, description, elder). | `belongsTo` user, elder; powers Impact Book + dashboard timeline. |
| `reports` & `annual_reports` | `2025_12_16_000000_create_report_module_tables`, `2025_12_17_102238_create_annual_reports_table` | Aggregated dashboards plus stored Impact Books with `pdf_path`. | `annual_reports` belongs to `users`, referenced by `ReportController`. |
| `warnings` | `2025_12_16_083638_create_warnings_table` | Non-blocking alerts for staff/users. | `belongsTo` user. |
| `hero_slides` | `2026_01_20_172235_create_hero_slides_table` | Hero slider content. | `belongsTo` branch/campaign and used by landing page seeds. |
| `pre_sponsorships` | `2026_01_20_181222_create_pre_sponsorships_table`, `2026_01_21_000001_add_relationship_type_to_pre_sponsorships_table` | Captures donor intents before full sponsorship (relationship type, notes). | Linked to `user`, `elder`, branch. |
| `sponsorships` | Base table from earlier migrations (merged with pledges in `2025_12_20_000000`), `2026_01_28_000001_add_branch_id_to_sponsorships_table`, `2026_02_15_000001_add_subscription_gateway_columns_to_sponsorships_table` | Represents active donor-elder relationships, subscription metadata, branch assignments. | `belongsTo` user, elder, branch; `hasMany` donations, proposals, documents. |
| `donor_profiles` | `2026_02_15_000002_create_donor_profiles_table` | Onboarding wizard data (relationship intent, contact prefs). | `belongsTo` user; influences dashboards. |
| `elder_documents` | `2026_02_20_000004_create_elder_documents_table` | Stores uploaded consent/ID attachments per elder. | `belongsTo` elder, user (uploader). |
| `sponsorship_proposals` | `2026_02_21_000005_create_sponsorship_proposals_table` | Pending invitations, statuses/expiry. | `belongsTo` elder, proposer user, donor. |
| `payment_reconciliation_imports`, `payment_reconciliation_items` | `2026_02_22_000006`, `2026_02_22_000007` | Uploads for Telebirr/CBE reconciliation with matched donations. | Imports `hasMany` items; items `belongsTo` donation/payment. |
| `case_notes`, `case_note_versions`, `case_note_attachments` | `2025_12_23_010000_create_case_notes_table` plus `2026_02_22_000008/9/10` | Case management with versioning and attachments. | Case notes `belongsTo` elder/user; versions/attachments `belongsTo` case note. |
| `campaigns` | `2025_12_22_200100_create_campaigns_table`, `2025_12_22_200200_add_campaign_id_to_donations_table`, `2026_02_22_000011_add_microsite_fields_to_campaigns_table` | Campaign landing metadata, microsite assets. | `hasMany` donations, hero slides; tracks CTA attribution. |
| `payment_transactions` | `2025_12_22_230000_create_payment_transactions_table` | Tracks gateway attempts per donation (Telebirr reference, status). | `belongsTo` donation, branch. |
| `outbound_messages` | `2025_12_23_000000_create_outbound_messages_table` | Queue of SMS/email blasts (status, channel). | `belongsTo` user/campaign; linked from queue command feedback. |
| `hero slides`, `timeline_events`, `activity_logs` etc | Additional support tables created earlier/repeated. | Referenced above. |
| `consent`, `kyc`, `redaction` columns | `2026_02_27_000012`, `_000013`, `_000014` | Extended `elders`, `donations`, `users` for consent/KYC/compliance workflows. | Consent fields added to `elders`, KYC columns on `donations`, redaction timestamps on `users`. |

## Relationship Highlights

- **Branches ↔ Elders / Donations / Visits** – Each branch scopes the data via `branch_id` columns and the global `BranchScope`. Branch admins/staff only see their branch’s elders/donations/visits.
- **Users ↔ Donations / Sponsorships / Annual Reports** – Donors log donations and sponsorships, and receive annual Impact Books stored in `annual_reports`.
- **Elders ↔ Documents / Case Notes / Timeline Events / Sponsorships** – The elder profile centralizes attachments, case notes, timeline entries, lifecycle health metadata, and sponsorship proposals.
- **Donations ↔ Payment Transactions / Campaigns / Reconciliation Imports** – Every donation ties to payment transactions and optionally a campaign/branch, while reconciliation imports match raw CSV rows back to donations.
- **Compliance expansions** – Consent fields per elder, KYC flags/notes on donations, and redaction timestamps on users make the workflow auditable. The `ComplianceController` exports/encrypts donor data based on these tables.
- **RBAC seeds** – `RolePermissionSeeder` populates Spatie tables (`roles`, `permissions`, `model_has_roles`, `role_has_permissions`) with the matrix described in `config/rbac.php`.

Use this reference when you sketch new queries, add seed data, or explain the model relationships to stakeholders.
