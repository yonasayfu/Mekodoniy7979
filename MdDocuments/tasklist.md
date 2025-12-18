# Mekodonia Home Connect - Remaining & Verified Tasks

This document outlines the current status of the Mekodonia Home Connect project, detailing tables, columns, data types, constraints, and relationships.

## 1. Project Status (as of December 15, 2025)

### Implemented & Reviewed (Based on original MEKODONIYA_TASK_LIST.md)

These features have been implemented to a basic or foundational level.

#### Phase 1: Guest Donation
- [x] **Project Setup**:
    - [x] Composer and npm dependencies installed.
    - [x] Initial migrations and seeders run.
    - [x] `.env` configured.
- [x] **Landing Page**: Basic structure with "Donate a Meal" button and placeholders for other sections.
- [x] **Guest Donation Form**: Vue component and basic backend route/controller for form submission.
- [x] **Backend for Guest Donations**: `Donation` model, migration, and controller with validation.
- [x] **Payment Gateway Integration**: Mock `TelebirrService` created; integrated into `DonationController`.
- [x] **CI/CD**: Basic GitHub Actions workflow (`ci.yml`) for linting and testing.

#### Phase 2: Core Platform (Admin & Branch Management)
- [x] **Branch Management**: `Branch` model, migration, CRUD controller, and Vue components (`Index`, `Create`, `Show`, `Edit`).
- [x] **Elder Management**: `Elder` model, migration, CRUD controller (with image upload logic), and Vue components (`Index`, `Create`, `Show`, `Edit`).
- [x] **Multi-tenancy**: Global scope implemented for `User`, `Elder`, and `Donation` models to enforce branch-level data isolation. `branch_id` added to `users` table and `User` model.

#### Phase 3: Donor Experience
- [x] **Donor Registration**: Registration form updated with donor-specific fields; `User` model and `RegisteredUserController` updated.
- [x] **Donor Dashboard**: `DonorDashboard.vue` component created with mock data for metrics, supported elders, and timeline events; `DonorDashboardController` fetches and passes data.
- [x] **Pledging (Adopting an Elder)**: `Sponsorship` model, migration, CRUD controller, and Vue components (`Index`, `Create`, `Show`, `Edit`). Mock `RecurringPaymentService` created.
- [x] **Auto-Payment**: `subscription_id` and `next_billing_date` added to `pledges` table and `Sponsorship` model. Mock `RecurringPaymentService` for subscriptions.

#### Phase 4: Advanced Features
- [x] **Visit Scheduler**: `Visit` model, migration, CRUD controller, and Vue components (`Index`, `Create`, `Show`, `Edit`).
- [x] **Impact Timeline**: `TimelineEvent` model, migration, and `TimelineEventService` created. Integrated into `DonationController` and `SponsorshipController`. Displayed on `DonorDashboard.vue`.
- [x] **Offline Support (PWA)**: `VitePWA` configured in `vite.config.ts`; placeholder SVG icons added.
- [x] **Reporting**: `ReportController` with `index` method listing reports. `Reports/Index.vue` created.
- [x] **Automation**: `app/Console/Kernel.php` created; `SendRemindersCommand` created and scheduled.

---

### Database Schema and Models Implementation (Completed)

- [x] **Database Schema Outline**: Detailed `database_schema.md` created.
- [x] **Migrations Creation**:
    - [x] `create_branches_table` migration created and defined.
    - [x] `create_users_table` migration modified to include `branch_id`, `account_status`, `account_type`, `approved_at`, `approved_by`, `deleted_at`.
    - [x] `create_elders_table` migration created and defined.
    - [x] `create_donations_table` migration created and defined.
    - [x] `create_pledges_table` migration created and defined.
    - [x] `create_visits_table` migration created and defined.
    - [x] `create_timeline_events_table` migration created and defined.
    - [x] Existing `activity_logs` migration verified (no changes needed).
    - [x] Existing `permissions` and `roles` migrations verified (no changes needed).
- [x] **Model Updates**:
    - [x] `User` model updated for `branch_id` integration (fillable, relationship).
    - [x] `Branch` model updated with `users` and `elders` relationships.
    - [x] `Elder` model updated with `pledges`, `visits`, `timelineEvents` relationships.
    - [x] `Donation` model relationships (`user`, `elder`) and `$fillable` fields adjusted.
    - [x] `Sponsorship` model `$fillable` fields adjusted (added `currency`).
    - [x] `Visit` model `$fillable` and `approver` relationship added.
    - [x] `TimelineEvent` model `$fillable` adjusted (removed `donation_id`, changed `type` to `event_type`, removed `donation` relationship).
- [x] **Existing CRUD Integration**: `UserManagementController` and associated Vue components updated to handle `branch_id`.

---

### Partially Implemented & Needs Further Work / Review

These items represent areas where a basic framework is in place, but further development, refinement, or integration with external services is required based on the full project vision.

- [x] **Deployment Script for DigitalOcean**: The `ci.yml` has a placeholder; a concrete deployment script needs to be written. (Placeholder script added to `MdDocuments/digitalocean_deploy_script.md` and `ci.yml` updated to call it).
- [x] **Comprehensive "Wall of Love"**: The current implementation is a placeholder; needs dynamic content from matched donors/elders. (Backend data fetching and frontend rendering with basic auto-scrolling implemented).
- [x] **Elder Gallery Dynamic Content**: The Elder Gallery on the landing page needs dynamic filters and data binding. (Backend data fetching with filters and frontend rendering with filtering UI implemented).
- [x] **Telebirr/CBE Auto-Payment Integration**: The payment services (`TelebirrService`, `RecurringPaymentService`) are currently mock implementations. Actual integration with Telebirr/CBE APIs is required. (Placeholder integration logic provided for both services).
- [x] **Notification System Integration**: `SendRemindersCommand` is a placeholder. Real email/SMS sending using Laravel's notification system (and potentially external services like Twilio) needs implementation. (Notifications created, command updated, and configuration guidance provided).
- [x] **Impact Book Generation**: `ReportController` has a placeholder for this. Full implementation involving fetching specific donor data, generating a PDF, and custom templates. (Backend method, Blade template, and frontend trigger implemented).
- [x] **User Role Management for Branch Admin**: While `BranchAdmin` behavior is scoped, explicit role assignment and a clear UI for managing this within the system are needed. (Conditional validation for `branch_id` in `UserStoreRequest` and `UserUpdateRequest` implemented).
- [x] **Elder Profile Media Uploads**: While the `profile_picture_path` and `video_url` fields exist, and the controller has basic upload logic, the actual UI for image/video upload (beyond `FileUploadField` for profile picture) and display on elder profiles needs to be built out. (Frontend upload components and display for both image and video implemented).
- [x] **Queue and Sync Mechanism for Offline PWA**: The PWA is configured, but the specific logic for queuing offline actions and syncing them when online needs implementation. (Conceptual outline and guidance provided in `MdDocuments/pwa_offline_sync_guidance.md`).

---

### Pending Implementation (New Features from Detailed Description)

These features are either completely new or represent significant expansions not yet started.

- [x] **Kick System**: For temporary user removal with automated re-invitation. (Migration, model updates, controller methods, and UI implemented).
- [x] **Ban System**: Permanent user removal with timed ban options. (Migration, model updates, controller methods, and UI implemented).
- [x] **Mute System**: Temporary restriction from typing in text channels. (Migration, model updates, controller methods, and UI implemented).
- [ ] **Unmute Command**: Manually lift all active mutes.
- [ ] **Warn List**: Display paginated list of warnings.
- [ ] **AI-Powered Moderation**: Use OpenAI GPT models to analyze chat for inappropriate content.
- [ ] **Authentication Setup**:
    - Optional profile picture and privacy toggle (donor).
- [ ] **Donor Onboarding Wizard**: Guided process for picking relationship type, setting pledge, contact preference.
- [ ] **Elder Gallery Filters**: Implement filters for region, priority, age, gender.
- [ ] **Elder Detail Page**: Full story, videos, photos, expense breakdown, "Adopt me" button logic.
- [ ] **Guest Donation Receipt**: PDF + SMS notification for immediate receipt.
- [ ] **User Authentication**:
    - [ ] **Report Module**: Implement `ReportService`, `AdminReportDashboard`, and `DonorImpactPage` based on the Blueprint.
    - 2FA for staff.
    - Laravel Sanctum SPA auth, optional Socialite.
- [ ] **Security**:
    - Tokenized payments, PCI-DSS minimized.
    - KYC scan for foreign donations > USD 500.
    - Ministry of Revenue-approved receipt numbers.
    - Elder consent forms.
    - Donor data protection (right to delete/export).
    - Daily encrypted off-site backup.
- [ ] **Performance & Maintainability**:
    - API <400 ms p95, first contentful paint <2s on 3G.
    - Queue workers for SMS/email.
    - Laravel conventions, feature tests, code quality tools (PHPStan, ESLint, Prettier).
- [ ] **Localization**: Amharic primary, English, Afaan-Oromo (future). Ethiopian calendar conversion. ETB currency formatting.
- [ ] **Low-Bandwidth & Devices**: Lazy load images, WebP format, <100 kB each. Video thumbnails only. Simple, mobile-first UX.
- [ ] **Global Reports for Super Admins**: (Beyond simple listing - actual data fetching and presentation).
- [ ] **Communication Hub**: Bulk SMS/email templates with placeholders.
- [ ] **Scheduled “Thank-you Day” Campaigns**: Auto PDF certificate generation.
- [ ] **Audit log**: track edits, unmatched elders, payment status changes.

---

## 2. Additional Considerations & Questions

These questions are repeated from my previous detailed response and are crucial for further development.

1.  **User Roles & Permissions**:
    *   Are there any other specific roles needed, e.g., for staff managing only elders without financial access?

2.  **Communication Channels**:
    *   For SMS/Email/Calls: Is there an existing service (e.g., Twilio) or method the organization prefers? What's the plan for programmable voice calls?

3.  **Media Storage**:
    *   What's the expected volume of media (elder photos/videos)? This impacts storage choices (local vs. cloud like AWS S3/DigitalOcean Spaces).

4.  **Reporting Details**:
    *   For "Impact Book" and global reports: what specific data points, formats, or templates are required? On-demand or scheduled?

5.  **Offline Functionality (PWA)**:
    *   Which specific actions need to be available offline (e.g., viewing elder profiles, making a pledge draft)?

6.  **Technology Stack Confirmation**:
    *   Are the confirmed technologies (Laravel 12, Vue 3, Inertia.js, Tailwind CSS, Shadcn-Vue, MySQL) finalized, or are you open to suggestions for specific parts?

Please review this `tasklist.md` and provide your feedback. Once we align on these points, we can proceed with the database schema design and further implementation.