# Database Schema for Project Mekodonia

This document outlines the database schema for the Project Mekodonia web application.

## 1. `users`

This table will store information about all users of the application, including donors, staff, and admins. The `spatie/laravel-permission` package will be used for role and permission management.

| Column          | Type                | Description                                                                 |
|-----------------|---------------------|-----------------------------------------------------------------------------|
| `id`            | `bigIncrements`     | Primary key.                                                                |
| `name`          | `string`            | Full name of the user.                                                      |
| `email`         | `string`, `unique`  | Email address of the user.                                                  |
| `password`      | `string`            | Hashed password of the user.                                                |
| `remember_token`| `rememberToken`     | For "remember me" functionality.                                            |
| `phone_number`  | `string`, `nullable`| Phone number of the user.                                                   |
| `address`       | `string`, `nullable`| Physical address of the user.                                               |
| `created_at`    | `timestamp`         |                                                                             |
| `updated_at`    | `timestamp`         |                                                                             |

## 2. `elders`

This table will store information about the elders in the organization.

| Column           | Type                | Description                                                                 |
|------------------|---------------------|-----------------------------------------------------------------------------|
| `id`             | `bigIncrements`     | Primary key.                                                                |
| `first_name`     | `string`            | First name of the elder.                                                    |
| `last_name`      | `string`            | Last name of the elder.                                                     |
| `bio`            | `text`, `nullable`  | A short biography of the elder.                                             |
| `date_of_birth`  | `date`, `nullable`  | Date of birth of the elder.                                                 |
| `gender`         | `string`, `nullable`| Gender of the elder ('male', 'female', 'other').                            |
| `monthly_needs`  | `decimal`, `nullable` | Estimated monthly expense for the elder.                                    |
| `status`         | `string`            | Status of the elder ('available', 'sponsored', 'archived').                 |
| `branch_id`      | `foreignId`         | Foreign key to the `branches` table.                                        |
| `created_by`     | `foreignId`         | Foreign key to the `users` table (staff who created the record).            |
| `created_at`     | `timestamp`         |                                                                             |
| `updated_at`     | `timestamp`         |                                                                             |

## 3. `branches`

This table will store information about the different branches of the organization.

| Column          | Type                | Description                                                                 |
|-----------------|---------------------|-----------------------------------------------------------------------------|
| `id`            | `bigIncrements`     | Primary key.                                                                |
| `name`          | `string`            | Name of the branch.                                                         |
| `address`       | `string`, `nullable`| Address of the branch.                                                      |
| `created_at`    | `timestamp`         |                                                                             |
| `updated_at`    | `timestamp`         |                                                                             |

## 4. `sponsorships` (renamed from `pledges`)

This table will link donors (`users`) with elders (`elders`) and store the details of the sponsorship.

| Column          | Type                | Description                                                                 |
|-----------------|---------------------|-----------------------------------------------------------------------------|
| `id`            | `bigIncrements`     | Primary key.                                                                |
| `donor_id`      | `foreignId`         | Foreign key to the `users` table.                                           |
| `elder_id`      | `foreignId`         | Foreign key to the `elders` table.                                          |
| `amount`        | `decimal`           | The amount of the sponsorship.                                              |
| `frequency`     | `string`            | The frequency of the sponsorship ('monthly', 'yearly', 'one-time').         |
| `start_date`    | `date`              | The start date of the sponsorship.                                          |
| `end_date`      | `date`, `nullable`  | The end date of the sponsorship (if applicable).                            |
| `status`        | `string`            | The status of the sponsorship ('active', 'completed', 'cancelled').         |
| `created_at`    | `timestamp`         |                                                                             |
| `updated_at`    | `timestamp`         |                                                                             |

## 5. `donations`

This table will store information about all donations, both from registered donors and guests.

| Column            | Type                | Description                                                                 |
|-------------------|---------------------|-----------------------------------------------------------------------------|
| `id`              | `bigIncrements`     | Primary key.                                                                |
| `donor_id`        | `foreignId`, `nullable`| Foreign key to the `users` table (for registered donors).                    |
| `guest_name`      | `string`, `nullable`| Name of the guest donor.                                                    |
| `guest_email`     | `string`, `nullable`| Email of the guest donor.                                                   |
| `amount`          | `decimal`           | The amount of the donation.                                                 |
| `sponsorship_id`  | `foreignId`, `nullable`| Foreign key to the `sponsorships` table (if the donation is for a sponsorship).|
| `transaction_id`  | `string`, `nullable`| The transaction ID from the payment gateway.                                |
| `payment_method`  | `string`            | The payment method used (e.g., 'telebirr', 'cbe_birr', 'bank_transfer').    |
| `status`          | `string`            | The status of the donation ('pending', 'completed', 'failed').              |
| `created_at`      | `timestamp`         |                                                                             |
| `updated_at`      | `timestamp`         |                                                                             |

## 6. `media`

This table will store information about media files (photos, videos) related to elders. We can use the `spatie/laravel-medialibrary` package to handle media.

| Column          | Type                | Description                                                                 |
|-----------------|---------------------|-----------------------------------------------------------------------------|
| `id`            | `bigIncrements`     | Primary key.                                                                |
| `model_type`    | `string`            | The class name of the model that the media is associated with (e.g., `App\Models\Elder`). |
| `model_id`      | `unsignedBigInteger`| The ID of the model that the media is associated with.                         |
| `collection_name`| `string`           | The name of the media collection (e.g., 'profile_pictures', 'videos').       |
| `name`          | `string`            | The name of the media file.                                                 |
| `file_name`     | `string`            | The name of the media file on disk.                                         |
| `mime_type`     | `string`, `nullable`| The mime type of the media file.                                            |
| `disk`          | `string`            | The disk where the media file is stored.                                    |
| `size`          | `unsignedBigInteger`| The size of the media file in bytes.                                        |
| `manipulations` | `json`              | Information about the manipulations performed on the media file.            |
| `custom_properties`| `json`           | Custom properties of the media file.                                        |
| `responsive_images`| `json`           | Information about the responsive images generated for the media file.       |
| `order_column`  | `integer`, `nullable`| The order of the media file in a collection.                                |
| `created_at`    | `timestamp`         |                                                                             |
| `updated_at`    | `timestamp`         |                                                                             |

## 7. `notifications`

This table will store notifications for users. Laravel's built-in notification system will be used.

| Column          | Type                | Description                                                                 |
|-----------------|---------------------|-----------------------------------------------------------------------------|
| `id`            | `uuid`, `primary`   | Primary key.                                                                |
| `type`          | `string`            | The class name of the notification.                                         |
| `notifiable_type`| `string`           | The class name of the notifiable model.                                     |
| `notifiable_id` | `unsignedBigInteger`| The ID of the notifiable model.                                             |
| `data`          | `text`              | The notification data.                                                      |
| `read_at`       | `timestamp`, `nullable`| The timestamp when the notification was read.                               |
| `created_at`    | `timestamp`         |                                                                             |
| `updated_at`    | `timestamp`         |                                                                             |

I will use the existing `pledges` table and rename it to `sponsorships` to better reflect its purpose. I will also add the new tables to the database.
I will also use the existing `users` table and add the new columns.
I will use the existing `branches` table.
I will use the existing `donations` table.
I will create the `elders` table.
I will install the `spatie/laravel-medialibrary` and `spatie/laravel-permission` packages.