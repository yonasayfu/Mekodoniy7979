# RBAC Summary (Role-Based Access Control)

This document distills the role and permission blueprint for Mekodonia Home Connect. The canonical definition lives in `config/rbac.php`, which drives the `RolePermissionSeeder` so the live database matches the documented matrix.

## Users & Credentials

All default passwords are `password` (development only). Seeded accounts include:

| Email | Role | Name | Description |
| :--- | :--- | :--- | :--- |
| `admin@example.com` | Super Admin | System Administrator | Full scope across branches, donors, reports, and infrastructure. |
| `auditor@example.com` | Auditor | Avery Auditor | Read-only view of data + audit trails. |
| `readonly@example.com` | Reporting Analyst | Riley Readonly | Generates and exports reports. |

Additional demo users come from `User::factory()` and tie into the `config/rbac.php` matrix as needed.

## Roles & Permissions

`config/rbac.php` now exposes:

1. A catalog of discrete permissions (users, staff, elders, donations, reports, etc.).
2. A whitelist of wildcard scopes (`users.*`, `donations.*`, etc.) that encapsulate full access to a domain.
3. A role matrix with friendly labels, descriptions, and curated permission sets so the UI or docs can render it alongside the seeder.

| Role | Label | Responsibilities |
| --- | --- | --- |
| **Super Admin** | System Owner | Owns every branch, elder, donation, report, and system setting. Seeded with `*` plus notification visibility. |
| **Admin** | Operational Admin | Manages users, staff, branches, elders, donations, visits, mailbox, campaigns, reports, and system logs without altering fellow admins. |
| **Branch Admin** | Branch Leader | Leads a single branch: hires staff, matches donors, approves donations, runs branch reports, and maintains case notes. |
| **Manager** | Branch Manager | Oversees operational health, reconciliation reports, and case-note governance inside their branch. |
| **Branch Coordinator** | Operations Lead | Coordinates visits, updates elder profiles, and accelerates match proposals while writing timelines. |
| **Field Officer** | Field Team | Captures visit data, writes timelines, and logs case notes without handling higher-level system settings. |
| **Finance Officer** | Finance Analyst | Approves donations, enforces KYC thresholds, and exports financial statements. |
| **Auditor** | Compliance Auditor | Read-only access to activity logs, donations, visits, and exports to validate controls. |
| **Reporting Analyst** | Data Analyst | Runs and downloads reports, dashboards, and exports without editing donors or system settings. |
| **Donor** | Registered Donor | A logged-in donor who sees personal donations, sponsorships, and impact books within the SPA. |
| **External** | Guest Donor | Browses elders, guest donations, and public reports with minimal access to private data. |

These definitions are referenced directly by `RolePermissionSeeder` so any change to `config/rbac.php` automatically flows into the seeded roles.
