# Mekodonia Donor-Elder Web App: Detailed Task List

This document breaks down the project roadmap into a detailed, phased task list.

## Phase 1: Walking Skeleton (Guest Donation)

**Goal**: Implement a single, end-to-end feature to validate the core architecture and technology stack.

*   [x] **Project Setup**:
    *   [x] Initialize a new Git repository (Already done by user in `BasicBoilerPlateForLaravel`).
    *   [x] Configure `.env` for local development (database, mail, etc.).
    *   [x] Run `composer install` and `npm install`.
    *   [x] Run initial migrations and seeders (`php artisan migrate:fresh --seed`).

*   [x] **Landing Page**:
    *   [x] Create a simple landing page (`resources/js/pages/Welcome.vue`).
    *   [x] Add a "Donate a Meal" button.

*   [x] **Guest Donation Form**:
    *   [x] Create a new page for guest donations (`resources/js/pages/GuestDonation.vue`).
    *   [x] Add a form with fields for donation amount, and optional fields for name, email, and phone.

*   [x] **Backend for Guest Donations**:
    *   [x] Create a `Donation` model and migration.
    *   [x] Create a `DonationController` with a `store` method to handle the guest donation.
    *   [x] Implement validation for the donation form.

*   [x] **Payment Gateway Integration**:
    *   [x] Investigate the Telebirr sandbox environment and API.
    *   [x] Implement a `TelebirrService` to handle payment logic.
    *   [x] Integrate the `TelebirrService` with the `DonationController` to process payments.
    *   [x] Add a payment status to the `Donation` model.

*   [x] **CI/CD**:
    *   [x] Set up a GitHub Actions workflow to run tests and linters on each push.
    *   [x] Configure a deployment script for DigitalOcean.

## Phase 2: Core Platform (Admin & Branch Management)

**Goal**: Build the core administrative features for managing branches and elders.

*   [x] **Branch Management**:
    *   [x] Create a `Branch` model and migration.
    *   [x] Implement a CRUD interface for Super Admins to manage branches.

*   [x] **Elder Management**:
    *   [x] Create an `Elder` model and migration with all the fields from the roadmap (profile, priority, expenses, media).
    *   [x] Implement a CRUD interface for Branch Admins to manage elders within their branch.
    *   [x] Implement image and video uploads for elder profiles.

*   [x] **Multi-tenancy**:
    *   [x] Implement branch-level data isolation so that Branch Admins can only see data for their branch. This can be done using global scopes on the models.

## Phase 3: Donor Experience

**Goal**: Build the core features for registered donors.

*   [x] **Donor Registration**:
    *   [x] Implement a registration form for donors.
    *   [x] Update the `User` model to include donor-specific fields.

*   [x] **Donor Dashboard**:
    *   [x] Create a dashboard for registered donors.
    *   [x] Implement the "My Elders" section with the traffic-light status.
    *   [x] Implement the "Quick actions" (Pay now, Schedule visit, etc.).

*   [x] **Pledging (Adopting an Elder)**:
    *   [x] Implement the functionality for donors to "adopt" an elder.
    *   [x] Create a `Pledge` model to store the relationship between a donor and an elder.
    *   [x] Implement the "Match/Unmatch" functionality for Branch Admins.

*   [x] **Auto-Payment**:
    *   [x] Investigate Telebirr and CBE auto-debit options.
    *   [x] Implement the auto-payment setup for donors.

## Phase 4: Advanced Features

**Goal**: Implement the remaining features from the roadmap.

*   [x] **Visit Scheduler**:
    *   [x] Implement the visit scheduling feature for donors.
    *   [x] Create a `Visit` model and migration.

*   [x] **Impact Timeline**:
    *   [x] Implement the "Impact Timeline" for donors.

*   [x] **Offline Support (PWA)**:
    *   [x] Configure the application as a Progressive Web App (PWA) using `VitePWA`.
    *   [x] Implement service workers for caching.
    *   [x] Implement a "queue and sync" mechanism for offline actions.

*   [x] **Reporting**:
    *   [x] Implement the global reports for Super Admins.
    *   [x] Implement the "Impact Book" generation for donors.

*   [x] **Automation**:
    *   [x] Implement the cron jobs for sending reminders, generating reports, and updating statuses.
