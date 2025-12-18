# Mekodonia Home Connect - Remaining & Verified Tasks

This document outlines the current status of the Mekodonia Home Connect project, detailing tables, columns, data types, constraints, and relationships.

---

## Core Entities

### 1. `users` Table

*   **Purpose:** Stores user authentication and profile information, including roles and branch assignments.
*   **Relationships:**
    *   `approved_by` -> `users.id` (self-referencing for admin approval)
    *   `branch_id` -> `branches.id` (Many-to-One: A user belongs to one branch)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `name`                  | `VARCHAR(255)`        | `NOT NULL`                                   | User's full name                         |
| `email`                 | `VARCHAR(255)`        | `NOT NULL`, `UNIQUE`                         | User's email address                     |
| `email_verified_at`     | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `password`              | `VARCHAR(255)`        | `NOT NULL`                                   | Hashed password                          |
| `remember_token`        | `VARCHAR(100)`        | `NULLABLE`                                   |                                          |
| `account_status`        | `ENUM`                | `NOT NULL`, `DEFAULT 'pending'`              | 'pending', 'active', 'suspended'         |
| `account_type`          | `ENUM`                | `NOT NULL`, `DEFAULT 'internal'`             | 'internal' (staff), 'external' (donor)   |
| `approved_at`           | `TIMESTAMP`           | `NULLABLE`                                   | When the account was approved            |
| `approved_by`           | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | User who approved this account           |
| `two_factor_secret`     | `TEXT`                | `NULLABLE`                                   | For 2FA                                  |
| `two_factor_recovery_codes` | `TEXT`            | `NULLABLE`                                   | For 2FA recovery                         |
| `two_factor_confirmed_at` | `TIMESTAMP`         | `NULLABLE`                                   | When 2FA was confirmed                   |
| `branch_id`             | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | Assigned branch for multi-tenancy        |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `deleted_at`            | `TIMESTAMP`           | `NULLABLE`                                   | Soft deletes                             |

### 2. `branches` Table

*   **Purpose:** Stores information about the various branches of the Mekodonia organization.
*   **Relationships:** None directly from this table, but `users.branch_id` references this.

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `name`                  | `VARCHAR(255)`        | `NOT NULL`, `UNIQUE`                         | Name of the branch                       |
| `location`              | `VARCHAR(255)`        | `NOT NULL`                                   | Physical location/city                   |
| `contact_person`        | `VARCHAR(255)`        | `NULLABLE`                                   | Main contact for the branch              |
| `contact_phone`         | `VARCHAR(20)`         | `NULLABLE`                                   | Phone number of the branch               |
| `contact_email`         | `VARCHAR(255)`        | `NULLABLE`                                   | Email address of the branch              |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 3. `elders` Table

*   **Purpose:** Stores detailed profiles of elders supported by Mekodonia.
*   **Relationships:**
    *   `branch_id` -> `branches.id` (Many-to-One: An elder belongs to one branch)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `branch_id`             | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Branch the elder belongs to              |
| `first_name`            | `VARCHAR(255)`        | `NOT NULL`                                   | Elder's first name                       |
| `last_name`             | `VARCHAR(255)`        | `NOT NULL`                                   | Elder's last name                        |
| `gender`                | `ENUM`                | `NOT NULL`                                   | 'Male', 'Female', 'Other'                |
| `date_of_birth`         | `DATE`                | `NULLABLE`                                   | Elder's date of birth                    |
| `biography`             | `TEXT`                | `NULLABLE`                                   | Elder's life story                       |
| `profile_picture_path`  | `VARCHAR(255)`        | `NULLABLE`                                   | Path to profile image                    |
| `video_url`             | `VARCHAR(255)`        | `NULLABLE`                                   | URL to a video                           |
| `priority_level`        | `ENUM`                | `NOT NULL`, `DEFAULT 'low'`                  | 'low', 'medium', 'high'                  |
| `health_status`         | `TEXT`                | `NULLABLE`                                   | General health condition                 |
| `special_needs`         | `TEXT`                | `NULLABLE`                                   | Any specific care requirements           |
| `monthly_expenses`      | `DECIMAL(10, 2)`      | `NOT NULL`, `DEFAULT 0.00`                   | Total estimated monthly expenses         |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `deleted_at`            | `TIMESTAMP`           | `NULLABLE`                                   | Soft deletes                             |

### 4. `donations` Table

*   **Purpose:** Records all guest and registered user donations.
*   **Relationships:**
    *   `user_id` -> `users.id` (Many-to-One: A donation can be made by a registered user, NULL for guest)
    *   `elder_id` -> `elders.id` (Many-to-One: A donation can be for a specific elder)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `user_id`               | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | Registered user who made the donation    |
| `elder_id`              | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | Elder receiving the donation             |
| `guest_name`            | `VARCHAR(255)`        | `NULLABLE`                                   | Name of guest donor                      |
| `guest_email`           | `VARCHAR(255)`        | `NULLABLE`                                   | Email of guest donor                     |
| `guest_phone`           | `VARCHAR(20)`         | `NULLABLE`                                   | Phone of guest donor                     |
| `amount`                | `DECIMAL(10, 2)`      | `NOT NULL`                                   | Donation amount                          |
| `currency`              | `VARCHAR(10)`         | `NOT NULL`, `DEFAULT 'ETB'`                  | Currency of donation                     |
| `payment_method`        | `VARCHAR(50)`         | `NULLABLE`                                   | e.g., 'Telebirr', 'CBE-Birr', 'Card'     |
| `transaction_id`        | `VARCHAR(255)`        | `NULLABLE`, `UNIQUE`                         | Transaction ID from payment gateway      |
| `status`                | `ENUM`                | `NOT NULL`, `DEFAULT 'pending'`              | 'pending', 'completed', 'failed', 'refunded' |
| `notes`                 | `TEXT`                | `NULLABLE`                                   | Any additional notes                     |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 5. `sponsorships` Table



*   **Purpose:** Records recurring sponsorships made by registered users to specific elders.

*   **Relationships:**

    *   `user_id` -> `users.id` (Many-to-One: A sponsorship is made by one registered user)

    *   `elder_id` -> `elders.id` (Many-to-One: A sponsorship is for one specific elder)



| Column Name             | Data Type             | Constraints                                  | Notes                                    |

| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |

| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |

| `user_id`               | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Registered user making the sponsorship        |

| `elder_id`              | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Elder receiving the sponsorship               |

| `amount`                | `DECIMAL(10, 2)`      | `NOT NULL`                                   | Sponsored amount                           |

| `currency`              | `VARCHAR(10)`         | `NOT NULL`, `DEFAULT 'ETB'`                  | Currency of sponsorship                       |

| `frequency`             | `ENUM`                | `NOT NULL`, `DEFAULT 'monthly'`              | 'once', 'monthly', 'quarterly', 'annually' |

| `start_date`            | `DATE`                | `NOT NULL`                                   | When the sponsorship starts                   |

| `end_date`            | `DATE`                | `NULLABLE`                                   | When the sponsorship ends (if applicable)     |

| `next_payment_date`     | `DATE`                | `NULLABLE`                                   | For recurring payments                   |

| `status`                | `ENUM`                | `NOT NULL`, `DEFAULT 'pending'`              | 'pending', 'active', 'completed', 'cancelled', 'overdue' |

| `subscription_id`       | `VARCHAR(255)`        | `NULLABLE`, `UNIQUE`                         | ID from payment gateway for recurring     |

| `notes`                 | `TEXT`                | `NULLABLE`                                   | Any additional notes                     |

| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

| `deleted_at`            | `TIMESTAMP`           | `NULLABLE`                                   | Soft deletes                             |



### 6. `visits` Table

*   **Purpose:** Records scheduled visits by registered users to elders.
*   **Relationships:**
    *   `user_id` -> `users.id` (Many-to-One: A visit is made by one registered user)
    *   `elder_id` -> `elders.id` (Many-to-One: A visit is for one specific elder)
    *   `branch_id` -> `branches.id` (Many-to-One: The branch where the visit takes place)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `user_id`               | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Registered user visiting                 |
| `elder_id`              | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Elder being visited                      |
| `branch_id`             | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    | Branch where visit occurs                |
| `visit_date`            | `DATETIME`            | `NOT NULL`                                   | Scheduled date and time of visit         |
| `purpose`               | `VARCHAR(255)`        | `NULLABLE`                                   | Purpose of the visit (e.g., 'Check-up', 'Social') |
| `notes`                 | `TEXT`                | `NULLABLE`                                   | Any additional notes about the visit     |
| `status`                | `ENUM`                | `NOT NULL`, `DEFAULT 'pending'`              | 'pending', 'approved', 'rejected', 'completed' |
| `approved_by`           | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY` (`users.id`)       | User who approved the visit              |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `deleted_at`            | `TIMESTAMP`           | `NULLABLE`                                   | Soft deletes                             |

### 7. `timeline_events` Table

*   **Purpose:** Stores significant events related to elders and donors, forming the "Impact Timeline".
*   **Relationships:**
    *   `user_id` -> `users.id` (Many-to-One: The user associated with the event, e.g., donor)
    *   `elder_id` -> `elders.id` (Many-to-One: The elder associated with the event)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `user_id`               | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | User (donor/staff) involved in the event |
| `elder_id`              | `BIGINT UNSIGNED`     | `NULLABLE`, `FOREIGN KEY`                    | Elder involved in the event              |
| `event_type`            | `VARCHAR(255)`        | `NOT NULL`                                   | Type of event (e.g., 'Donation', 'Visit', 'Sponsorship', 'Milestone') |
| `description`           | `TEXT`                | `NOT NULL`                                   | Detailed description of the event        |
| `occurred_at`           | `DATETIME`            | `NOT NULL`                                   | When the event happened                  |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 8. `activity_logs` Table

*   **Purpose:** Records audit trail of actions performed within the application.
*   **Relationships:**
    *   `causer_id` -> `users.id` (Many-to-One: The user who performed the action)
    *   `subject_id` (polymorphic)
    *   `subject_type` (polymorphic)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `log_name`              | `VARCHAR(255)`        | `NULLABLE`                                   | Optional name for the log                |
| `description`           | `TEXT`                | `NOT NULL`                                   | Description of the activity              |
| `subject_type`          | `VARCHAR(255)`        | `NULLABLE`                                   | Polymorphic relation for the subject     |
| `subject_id`            | `BIGINT UNSIGNED`     | `NULLABLE`                                   | Polymorphic relation for the subject     |
| `causer_type`           | `VARCHAR(255)`        | `NULLABLE`                                   | Polymorphic relation for the causer      |
| `causer_id`             | `BIGINT UNSIGNED`     | `NULLABLE`                                   | User who caused the activity             |
| `properties`            | `JSON`                | `NULLABLE`                                   | Additional data about the activity       |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 9. `roles` Table

*   **Purpose:** Stores user roles (e.g., Super Admin, Branch Admin, Donor).
*   **Relationships:** None directly from this table, but linked via pivot tables.

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `name`                  | `VARCHAR(255)`        | `NOT NULL`, `UNIQUE`                         | Role name                                |
| `guard_name`            | `VARCHAR(255)`        | `NOT NULL`                                   | Guard name (e.g., 'web', 'api')          |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 10. `permissions` Table

*   **Purpose:** Stores individual permissions that can be assigned to roles or directly to users.
*   **Relationships:** None directly from this table, but linked via pivot tables.

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `id`                    | `BIGINT UNSIGNED`     | `PRIMARY KEY`, `AUTO_INCREMENT`              |                                          |
| `name`                  | `VARCHAR(255)`        | `NOT NULL`, `UNIQUE`                         | Permission name                          |
| `guard_name`            | `VARCHAR(255)`        | `NOT NULL`                                   | Guard name (e.g., 'web', 'api')          |
| `created_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |
| `updated_at`            | `TIMESTAMP`           | `NULLABLE`                                   |                                          |

### 11. `model_has_roles` Table (Pivot Table)

*   **Purpose:** Links users to roles.
*   **Relationships:**
    *   `role_id` -> `roles.id`
    *   `model_id` -> `users.id` (Polymorphic)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `role_id`               | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    |                                          |
| `model_type`            | `VARCHAR(255)`        | `NOT NULL`                                   | E.g., 'App\\Models\\User'                |
| `model_id`              | `BIGINT UNSIGNED`     | `NOT NULL`                                   | User ID                                  |
| `PRIMARY KEY`           | `(role_id, model_id, model_type)` | Composite Primary Key                        |                                          |

### 12. `model_has_permissions` Table (Pivot Table)

*   **Purpose:** Links users to direct permissions.
*   **Relationships:**
    *   `permission_id` -> `permissions.id`
    *   `model_id` -> `users.id` (Polymorphic)

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `permission_id`         | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    |                                          |
| `model_type`            | `VARCHAR(255)`        | `NOT NULL`                                   | E.g., 'App\\Models\\User'                |
| `model_id`              | `BIGINT UNSIGNED`     | `NOT NULL`                                   | User ID                                  |
| `PRIMARY KEY`           | `(permission_id, model_id, model_type)` | Composite Primary Key                        |                                          |

### 13. `role_has_permissions` Table (Pivot Table)

*   **Purpose:** Links roles to permissions.
*   **Relationships:**
    *   `permission_id` -> `permissions.id`
    *   `role_id` -> `roles.id`

| Column Name             | Data Type             | Constraints                                  | Notes                                    |
| :---------------------- | :-------------------- | :------------------------------------------- | :--------------------------------------- |
| `permission_id`         | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    |                                          |
| `role_id`               | `BIGINT UNSIGNED`     | `NOT NULL`, `FOREIGN KEY`                    |                                          |
| `PRIMARY KEY`           | `(permission_id, role_id)` | Composite Primary Key                        |                                          |
