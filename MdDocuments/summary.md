# Project Summary

## Overview
**Mekodonia Home Connect** is a Laravel + Inertia (Vue 3) web application designed to connect donors with elders. It features role-based access control (RBAC), multi-tenancy (branches), and comprehensive management for elders, donations, and staff.

## Tech Stack
- **Backend**: Laravel 12, PostgreSQL (suggested), Spatie Permission, Fortify.
- **Frontend**: Inertia.js, Vue 3, Tailwind CSS, Shadcn UI (sidebar).
- **Environment**: PHP 8.2+, Node.js 20+.

## Key Features
- **Authentication**: RBAC with roles like Super Admin, Manager, Branch Coordinator, etc.
- **Modules**:
  - **Dashboard**: Overview of metrics.
  - **Management**: Branches, Elders, Sponsorships, Visits, Campaigns, Reports.
  - **Team**: Staff, Users, Roles.
- **Permissions**: Granular permissions (e.g., `users.manage`, `reports.view`) and wildcard support (e.g., `users.*`).

## Current Status
- **Date**: January 20, 2026.
- **Issue**: Admin sidebar items are hidden.
- **Root Cause**: The frontend permission checker (`AppSidebar.vue`) does not support wildcard permissions (e.g., `users.*` does not grant access to `users.manage`).

## Next Steps
1.  Fix the frontend `hasPermission` function in `AppSidebar.vue` to support wildcards.
2.  Verify the admin sidebar visibility.
