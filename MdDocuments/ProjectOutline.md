# Mekodonia Home Connect - Project Outline & Requirements

This document outlines the features, database schema, and next steps for the Mekodonia Home Connect project.

## 1. Core Features

### For Donors (End Users)

*   **Guest Experience:**
    *   Landing page with a "Wall of Love" showing recent donor-elder pairings.
    *   Guest donation option for one-time contributions (e.g., "buy a meal") with easy mobile/bank transfer, no registration required.
    *   Elder gallery with profiles of elders who need support.
*   **Donor Authentication & Profile:**
    *   Simple registration and login.
    *   Donor profile management (contact details, payment methods, etc.).
*   **Pledging (Adopting an Elder):**
    *   Donors can browse the elder gallery and select an elder to "adopt".
    *   A pledge form to commit to monthly support.
    *   The system tracks the pledge and sends reminders for payment.
*   **Donor Dashboard:**
    *   A personalized dashboard showing the donor's impact: total donations, supported elders, and a timeline of their contributions.
    *   A section to manage their active pledges.
*   **Visit Scheduling:**
    *   A feature to schedule visits with their supported elders.
*   **Impact Reporting:**
    *   An annual "Impact Book" PDF report summarizing their contributions.
    *   "Thank You" certificates for long-term supporters.

### For Mekodonia Admins

*   **Admin Dashboard:**
    *   A global overview of the organization's performance: total pledges, total collections, number of active donors, etc.
    *   Ability to filter stats by branch.
*   **Branch Management:**
    *   CRUD (Create, Read, Update, Delete) for branches.
*   **Elder Management:**
    *   CRUD for elders, including their bio, photos, videos, and monthly expense needs.
    *   Ability to set a priority for elders who need urgent support.
*   **Donor Management:**
    *   View all donors and their pledge history.
*   **Pledge Management:**
    *   Track the status of all pledges.
    *   Mark pledges as active, paused, or ended.
*   **Financial Management:**
    *   Track all donations and reconcile them with bank/mobile money statements.
*   **Communication Hub:**
    *   Send bulk SMS/email notifications to donors (e.g., payment reminders).
*   **User Management:**
    *   Manage admin users and their roles/permissions.

## 2. High-Level Database Schema

*   **`users`**: Stores both donors and admins.
    *   `id`, `name`, `email`, `password`, `role` (donor, admin, super-admin), `branch_id` (for admins), etc.
*   **`branches`**:
    *   `id`, `name`, `location`, etc.
*   **`elders`**:
    *   `id`, `name`, `bio`, `profile_picture_path`, `video_url`, `monthly_expense_amount`, `priority` (high, medium, low), `status` (available, supported), `branch_id`, etc.
*   **`pledges`**: The link between a donor and an elder.
    *   `id`, `user_id` (donor), `elder_id`, `amount`, `status` (active, paused, ended), `next_billing_date`, etc.
*   **`donations`**:
    *   `id`, `user_id` (nullable for guest donations), `amount`, `payment_method`, `status` (pending, successful, failed), `pledge_id` (if applicable), `transaction_id`, etc.
*   **`visits`**:
    *   `id`, `user_id` (donor), `elder_id`, `scheduled_at`, `status` (pending, approved, completed), etc.
*   **`timeline_events`**: A log of significant events.
    *   `id`, `user_id`, `elder_id`, `event_type` (e.g., "Pledge Started", "Donation Received"), `description`, `occurred_at`, etc.

## 3. Next Steps

1.  **Review & Refine:** Please review this outline and let me know if it aligns with your vision. We can add, remove, or change features as needed.
2.  **Detailed Schema Design:** Once the feature set is confirmed, we can create a detailed database schema with all columns, data types, and relationships.
3.  **Project Scaffolding:** Set up the Laravel project with the necessary dependencies, authentication, and basic structure.
4.  **Iterative Development:** We will build the features one by one, starting with the core functionality (admin dashboards, elder management) and then moving on to the donor-facing features.

I believe this outline provides a solid foundation for the Mekodonia Home Connect project. I'm ready to proceed with the next steps when you are.
