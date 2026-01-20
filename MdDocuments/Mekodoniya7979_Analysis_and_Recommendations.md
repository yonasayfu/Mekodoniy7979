# Mekodonia Donor-Elder Web App: Analysis and Recommendations

This document outlines my understanding of the project based on the provided roadmap and offers some recommendations for a successful implementation.

## 1. Understanding of the Project

I understand that the goal is to build a web application called "Mekodonia Home Connect," which will serve as a matchmaking and stewardship platform between donors and elders in the care of the Mekodonia charity organization.

The application will have several key user roles with different levels of access and permissions:

- **Super Admin**: Global control over the entire platform.
- **Branch Admin/Editor**: Manages elders and donations for a specific branch.
- **Donor (Registered)**: Can pledge support, manage payments, schedule visits, and track their relationship with an elder.
- **Guest Donor**: Can make one-time donations without registration.
- **Accountant/Auditor**: Has read-only access to financial data for reporting and compliance.

The core features are well-defined and include:

- **Public-facing pages**: A landing page, an elder gallery with filtering, and a guest donation system.
- **Donor Dashboard**: Onboarding, managing "adopted" elders, payment setup, visit scheduling, and an "impact timeline".
- **Branch Staff Dashboard**: Elder profile management (CRUD), donor-elder matching, donation reconciliation, and offline support.
- **Super Admin Dashboard**: Multi-tenancy management, reporting, communication tools, and audit logs.

The technical requirements are also very clear, with a focus on:

- **Localization**: Supporting Amharic and other local languages.
- **Low-bandwidth optimization**: Ensuring the application is fast and usable on slow connections.
- **Offline support**: A PWA that can function without a stable internet connection.
- **Security and Compliance**: Protecting user data and adhering to financial regulations.

The database schema is well-structured, and the "Walking Skeleton" approach for Sprint 0 is an excellent strategy to validate the core architecture and technology stack.

## 2. Recommendations

Based on the roadmap, here are some of my recommendations for a successful project implementation:

### Technology Stack

- **Laravel 12 + Vue 3 + Inertia**: This is an excellent choice for this project. It provides a modern, reactive frontend with a powerful backend framework.
- **Offline Support (PWA)**: I recommend using the **`VitePWA`** plugin for Vue 3. It's a popular and well-maintained solution that will help us build a reliable Progressive Web App.
- **Payment Gateway Integration**: Integrating with multiple Ethiopian payment gateways (Telebirr, CBE-Birr, Amole, M-Pesa) will be a critical task. I will start by investigating their APIs. If official packages are not available, I will build custom integrations for each.
- **Image Optimization**: For low-bandwidth optimization, I recommend using the **`spatie/laravel-image-optimizer`** package to automatically compress images upon upload.
- **SMS Notifications**: For SMS notifications, I will investigate local SMS gateway providers in Ethiopia that have a simple API.

### Development Strategy

- **Sprint 0 (Walking Skeleton)**: I agree with the plan to focus on a single, end-to-end feature for the first sprint. The "guest can donate one meal" is a perfect choice. This will allow us to set up the core infrastructure, payment integration, and deployment pipeline early on.
- **Feature Prioritization**: After Sprint 0, I recommend we prioritize the following features to get a usable Minimum Viable Product (MVP) for registered donors and branch staff:
    1.  **Elder CRUD**: The ability for branch staff to create, read, update, and delete elder profiles.
    2.  **Donor Registration and Pledging**: Allowing donors to register and "adopt" an elder.
    3.  **Basic Donor Dashboard**: A simple dashboard for donors to see their pledged elder and payment status.
- **Offline Sync**: This is a complex feature. I suggest we start with a simple "queue and sync" mechanism. The PWA will store user actions (e.g., creating a new elder profile) in local storage (like IndexedDB). When the application comes back online, it will send the queued requests to the server.

### Potential Challenges

- **Payment Gateway APIs**: The quality and documentation of the payment gateway APIs might vary. This could be a potential bottleneck. I will prioritize the investigation of these APIs.
- **Data Migration**: If you have existing data for elders and donors, we will need to plan a data migration strategy.
- **Offline Sync Complexity**: A full-featured offline sync can be very complex. We should start with a simple version and iterate on it based on user feedback.

## 3. Upgrade: Robust RBAC & Multi-Tenancy System

To address the concern that the current RBAC "didn't work correctly" (likely due to data leakage between branches or insufficient permission granularity), I propose the following upgraded architecture.

### The Core Problem

A simple role check (e.g., `if user is admin`) is dangerous in a multi-branch organization. A **Branch Admin** in Addis Ababa must not be able to edit an Elder in Bahir Dar. We need **Role-Based Access Control (RBAC)** combined with **Scope-Based Access Control**.

### Proposed Architecture

#### 1. Database Schema Enhancements

We need to explicitly link users to branches.

- **`branches` table**: `id`, `name`, `region`, `settings` (JSON).
- **`users` table**: Add `branch_id` (nullable, foreign key to branches).
    - If `branch_id` is `NULL` → User is **Global/HQ** (Super Admin, Auditor).
    - If `branch_id` is `SET` → User is **Tenant-scoped** (Branch Admin, Staff).

#### 2. Role Definitions (using `spatie/laravel-permission`)

| Role             | Scope  | Permissions                    | Description                                              |
| :--------------- | :----- | :----------------------------- | :------------------------------------------------------- |
| **Super Admin**  | Global | `*` (All)                      | Can manage all branches, system config, and users.       |
| **Auditor**      | Global | `audit.view`, `finance.view`   | Read-only access to all branches for compliance.         |
| **Branch Admin** | Tenant | `elder.manage`, `staff.manage` | Full control **only within their assigned branch**.      |
| **Branch Staff** | Tenant | `elder.view`, `visit.log`      | Can view elders and log visits/expenses in their branch. |
| **Donor**        | Self   | `pledge.manage`, `self.view`   | Can only see their own profile and matched elders.       |

#### 3. Enforcing Isolation (The "Fix")

To prevent data leakage, we will use Laravel's **Global Scopes** and **Policies**.

**A. The Tenant Scope (Global Scope)**
Automatically filter queries based on the logged-in user's branch.

```php
// App/Scopes/TenantScope.php
public function apply(Builder $builder, Model $model) {
    if (auth()->check() && auth()->user()->branch_id) {
        $builder->where('branch_id', auth()->user()->branch_id);
    }
}
```

**B. The Policy Gate**
Even if a user bypasses the scope (e.g., via ID guessing), the Policy blocks them.

```php
// App/Policies/ElderPolicy.php
public function update(User $user, Elder $elder) {
    if ($user->hasRole('Super Admin')) return true;
    return $user->hasPermissionTo('elder.edit') && $user->branch_id === $elder->branch_id;
}
```

## 4. Next Steps

I am ready to start with the "Walking Skeleton" for Sprint 0. Before I do, I would like to confirm the following:

- **Project Location**: The project is created at `/Users/yonassayfu/VS_Code/Mekodoniy7979`. Is this correct?
- **Roadmap**: The provided roadmap is very detailed. I will use it as the main source of truth for the project.

Once you confirm the above, I will start by setting up the new Laravel project in the `Mekodoniy7979` directory.
