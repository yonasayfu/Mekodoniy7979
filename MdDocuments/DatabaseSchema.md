# Mekodonia Home Connect - Detailed Database Schema

This document provides a detailed breakdown of the database schema based on the project outline.

## 1. `branches`

Stores the different Mekodonia branches.

| Column      | Type        | Constraints & Notes                               |
| :---------- | :---------- | :------------------------------------------------ |
| `id`        | `bigint`    | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`       |
| `name`      | `varchar`   | `255`, `NOT NULL`, `UNIQUE`                       |
| `location`  | `varchar`   | `255`, `NULLABLE` (e.g., "Addis Ababa, Ethiopia") |
| `is_active` | `boolean`   | `DEFAULT true`                                    |
| `created_at`| `timestamp` | `NULLABLE`                                        |
| `updated_at`| `timestamp` | `NULLABLE`                                        |

---

## 2. `users`

Stores donors, staff, and administrators.

| Column          | Type                | Constraints & Notes                                     |
| :-------------- | :------------------ | :------------------------------------------------------ |
| `id`            | `bigint`            | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`             |
| `branch_id`     | `bigint`            | `UNSIGNED`, `NULLABLE`, `FOREIGN KEY` -> `branches.id`  |
| `name`          | `varchar`           | `255`, `NOT NULL`                                       |
| `email`         | `varchar`           | `255`, `NOT NULL`, `UNIQUE`                             |
| `password`      | `varchar`           | `255`, `NOT NULL`                                       |
| `phone_number`  | `varchar`           | `255`, `NULLABLE`                                       |
| `address`       | `varchar`           | `255`, `NULLABLE`                                       |
| `city`          | `varchar`           | `255`, `NULLABLE`                                       |
| `country`       | `varchar`           | `255`, `NULLABLE`                                       |
| `account_status`| `varchar`           | `255`, `DEFAULT 'pending'` (pending, active, suspended) |
| `account_type`  | `varchar`           | `255`, `DEFAULT 'donor'` (donor, staff, admin)          |
| `approved_at`   | `timestamp`         | `NULLABLE`                                              |
| `approved_by`   | `bigint`            | `UNSIGNED`, `NULLABLE`, `FOREIGN KEY` -> `users.id`     |
| `created_at`    | `timestamp`         | `NULLABLE`                                              |
| `updated_at`    | `timestamp`         | `NULLABLE`                                              |

---

## 3. `elders`

Stores profiles of the elders who need support.

| Column                 | Type          | Constraints & Notes                                              |
| :--------------------- | :------------ | :--------------------------------------------------------------- |
| `id`                   | `bigint`      | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`                      |
| `branch_id`            | `bigint`      | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `branches.id`           |
| `name`                 | `varchar`     | `255`, `NOT NULL`                                                |
| `bio`                  | `text`        | `NULLABLE`, Elder's life story and background.                   |
| `profile_picture_path` | `varchar`     | `255`, `NULLABLE`                                                |
| `video_url`            | `varchar`     | `255`, `NULLABLE`                                                |
| `monthly_expense_amount`| `decimal`    | `8, 2`, `NOT NULL`, The estimated monthly support needed.        |
| `priority`             | `varchar`     | `255`, `DEFAULT 'medium'` (high, medium, low)                    |
| `status`               | `varchar`     | `255`, `DEFAULT 'available'` (available, matched, supported)     |
| `created_at`           | `timestamp`   | `NULLABLE`                                                       |
| `updated_at`           | `timestamp`   | `NULLABLE`                                                       |

---

## 4. `pledges`

Represents a donor's commitment to support an elder.

| Column            | Type        | Constraints & Notes                                              |
| :---------------- | :---------- | :--------------------------------------------------------------- |
| `id`              | `bigint`    | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`                      |
| `user_id`         | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `users.id` (The Donor)  |
| `elder_id`        | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `elders.id`             |
| `amount`          | `decimal`   | `8, 2`, `NOT NULL`, The pledged monthly amount.                  |
| `status`          | `varchar`   | `255`, `DEFAULT 'active'` (active, paused, ended)                |
| `next_billing_date`| `date`      | `NULLABLE`                                                       |
| `subscription_id` | `varchar`   | `255`, `NULLABLE`, ID from the payment gateway for recurring payments. |
| `created_at`      | `timestamp` | `NULLABLE`                                                       |
| `updated_at`      | `timestamp` | `NULLABLE`                                                       |

---

## 5. `donations`

Records every financial transaction, whether from a pledge or a one-time guest donation.

| Column          | Type        | Constraints & Notes                                                     |
| :-------------- | :---------- | :---------------------------------------------------------------------- |
| `id`            | `bigint`    | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`                             |
| `user_id`       | `bigint`    | `UNSIGNED`, `NULLABLE`, `FOREIGN KEY` -> `users.id` (Null for guests) |
| `pledge_id`     | `bigint`    | `UNSIGNED`, `NULLABLE`, `FOREIGN KEY` -> `pledges.id` (If part of a pledge) |
| `amount`        | `decimal`   | `10, 2`, `NOT NULL`                                                     |
| `payment_method`| `varchar`   | `255`, `NULLABLE` (e.g., 'Telebirr', 'Credit Card')                  |
| `transaction_id`| `varchar`   | `255`, `NULLABLE`, The ID from the payment gateway.                     |
| `status`        | `varchar`   | `255`, `DEFAULT 'pending'` (pending, successful, failed)          |
| `created_at`    | `timestamp` | `NULLABLE`                                                              |
| `updated_at`    | `timestamp` | `NULLABLE`                                                              |

---

## 6. `visits`

Schedules visits between donors and their supported elders.

| Column        | Type        | Constraints & Notes                                            |
| :------------ | :---------- | :------------------------------------------------------------- |
| `id`          | `bigint`    | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`                    |
| `user_id`     | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `users.id` (Donor)  |
| `elder_id`    | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `elders.id`           |
| `scheduled_at`| `datetime`  | `NOT NULL`                                                     |
| `status`      | `varchar`   | `255`, `DEFAULT 'pending'` (pending, approved, completed)      |
| `notes`       | `text`      | `NULLABLE`, Any notes from the donor or admin about the visit. |
| `created_at`  | `timestamp` | `NULLABLE`                                                     |
| `updated_at`  | `timestamp` | `NULLABLE`                                                     |

---

## 7. `timeline_events`

A log of significant events in the relationship between a donor and an elder.

| Column        | Type        | Constraints & Notes                                                        |
| :------------ | :---------- | :------------------------------------------------------------------------- |
| `id`          | `bigint`    | `UNSIGNED`, `PRIMARY KEY`, `AUTO_INCREMENT`                                |
| `user_id`     | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `users.id`                        |
| `elder_id`    | `bigint`    | `UNSIGNED`, `NOT NULL`, `FOREIGN KEY` -> `elders.id`                       |
| `event_type`  | `varchar`   | `255`, `NOT NULL` (e.g., "Pledge Started", "Donation Received", "Visit Scheduled") |
| `description` | `text`      | `NULLABLE`                                                                 |
| `occurred_at` | `datetime`  | `NOT NULL`                                                                 |
| `created_at`  | `timestamp` | `NULLABLE`                                                                 |
| `updated_at`  | `timestamp` | `NULLABLE`                                                                 |

This schema provides a solid foundation for the application. The next step is to create the Laravel migration files for each of these tables.
