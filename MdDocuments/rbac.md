# RBAC Summary (Role-Based Access Control)

This document outlines the users, roles, and permissions configured in the Mekodonia Home Connect system.

## Users & Credentials

All default passwords are set to `password` (for development environments).

| Email | Role | Name | Description |
| :--- | :--- | :--- | :--- |
| **`admin@example.com`** | **Super Admin** | System Administrator | Full access to everything. |
| **`auditor@example.com`** | **Auditor** | Avery Auditor | View-only access to most data + logs. |
| **`readonly@example.com`** | **Reporting Analyst** | Riley Readonly | Reports and data export access. |

*(Note: Additional random users are created by seeders with the `User::factory()` but do not have fixed emails.)*

## Roles & Permissions

The system uses a hierarchical and functional role structure.

### 1. Super Admin
*   **Access:** Full System Access (Wildcard `*`)
*   **Capabilities:** Can manage all aspects of the system including other admins, system settings, and sensitive data.
*   **Permissions:** `users.*`, `staff.*`, `roles.*`, `permissions.*`, `branches.*`, `elders.*`, `sponsorships.*`, `donations.*`, `visits.*`, `reports.*`, `campaigns.*`, `mailbox.*`, `activity_logs.*`, `data_exports.*`, `system.*`, `timeline.*`

### 2. Admin
*   **Access:** High-level Operational Access
*   **Capabilities:** Similar to Super Admin but cannot manage other Admins/Super Admins (restricted `roles.manage`).
*   **Permissions:** `users.*`, `staff.*`, `branches.*`, `elders.*`, `sponsorships.*`, `donations.*`, `visits.*`, `reports.*`, `mailbox.*`, `activity_logs.*`, `data_exports.*`, `system.*`, `timeline.*` (plus `roles.view`, `roles.update`, `permissions.view`)

### 3. Manager
*   **Access:** Branch & Operational Oversight
*   **Capabilities:** Can manage staff, elders, sponsorships, and view reports. Cannot delete system-level data.
*   **Permissions:** `users.view`, `staff.*`, `branches.view`, `elders.*`, `sponsorships.*`, `donations.*`, `visits.*`, `reports.view`, `reports.generate`, `reports.export`, `reports.operational`, `mailbox.view`, `mailbox.send`, `activity_logs.view`, `timeline.view`, `timeline.create`, `timeline.update`

### 4. Branch Coordinator
*   **Access:** Branch Specific Operations
*   **Capabilities:** Manage elders and staff within their purview.
*   **Permissions:** `users.view`, `staff.view`, `staff.update`, `branches.view`, `elders.view`, `elders.update`, `donations.view`, `donations.create`, `visits.*`, `reports.view`, `reports.generate`, `timeline.view`, `timeline.create`

### 5. Field Officer
*   **Access:** On-the-ground Operations
*   **Capabilities:** View data and manage visits.
*   **Permissions:** `users.view`, `staff.view`, `elders.view`, `donations.view`, `visits.view`, `visits.create`, `visits.update`, `timeline.view`, `timeline.create`

### 6. Finance Officer
*   **Access:** Financial Data
*   **Capabilities:** Manage donations and generate financial reports.
*   **Permissions:** `users.view`, `donations.*`, `reports.view`, `reports.financial`, `reports.generate`, `reports.export`, `data_exports.view`, `data_exports.create`

### 7. Auditor
*   **Access:** Compliance & Oversight
*   **Capabilities:** Read-only access to operational data and activity logs.
*   **Permissions:** `users.view`, `staff.view`, `elders.view`, `donations.view`, `visits.view`, `reports.view`, `reports.generate`, `activity_logs.view`, `data_exports.view`

### 8. Reporting Analyst
*   **Access:** Data Analysis
*   **Capabilities:** Generate and export reports.
*   **Permissions:** `users.view`, `staff.view`, `elders.view`, `donations.view`, `visits.view`, `reports.view`, `reports.generate`, `reports.export`, `data_exports.view`

### 9. External (Donor)
*   **Access:** Public/Donor Facing
*   **Capabilities:** View own sponsorships and donations.
*   **Permissions:** `users.view`, `sponsorships.view`, `donations.view`, `reports.view`
