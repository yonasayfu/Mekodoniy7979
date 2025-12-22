# Upgrade Recommendations (Charity + Campaign Scale) + Upgraded Database Schema

## 1) Goals for the Next Upgrade

This app is **not just a donation form** — it becomes a **long-term relationship + case management system** for elders and donors, and it must survive **campaign spikes** (many visitors in short time).

### Primary goals

- **Trust + transparency** (important for charity)
- **Operational correctness** (elders lifecycle, branch workflows)
- **Campaign scalability** (spikes, high traffic, high payments)
- **Data quality** (audit trail, history, approvals)
- **Multi-channel communication** (email/SMS/WhatsApp/voice/manual calls)

---

## 2) Product / Feature Enhancements (Recommended)

### 2.1 Elder lifecycle tracking (core)

Right now you track an elder profile and some health text. For real operations you need:

- **Elder status lifecycle**
  - `available` (needs sponsor)
  - `sponsored` (has active sponsor)
  - `in_care` (in facility but not available for sponsorship)
  - `transferred` (moved branch)
  - `deceased`
  - `archived`

- **Health tracking as events (not only one text field)**
  - periodic health assessments
  - diagnoses list
  - medication list
  - hospitalization events

- **History timeline**
  - admission date
  - transfer date(s)
  - deceased date
  - supporting evidence documents

### 2.2 Sponsorship model upgrade (relationship model)

You mentioned Father/Mother/Brother/Sister type relationships. This should be first-class:

- **Relationship type** stored on the sponsorship
- **Sponsorship agreements**
  - promised amount/frequency
  - expected “visit cadence” (optional)
  - preferred payment channel
  - renewal rules
  - agreement PDF (optional)

- **Promise compliance**
  - monthly expected payment schedule
  - overdue logic
  - reminders
  - escalation to staff

### 2.3 Campaign features (high traffic)

During campaigns you’ll have spikes and marketing:

- **Campaign pages**
  - e.g. “Feed 500 elders this week”
  - shareable URLs
  - campaign progress bar

- **High-volume donation processing**
  - donation intents + webhook verification
  - dedupe / idempotency via `gateway_reference`

- **Anti-fraud / abuse protection**
  - rate limits
  - bot protection (Cloudflare Turnstile)
  - suspicious donation detection (optional)

### 2.4 Donor trust & transparency

- Public pages:
  - verified org profile, certificates, branch list
  - impact stats (aggregated, no sensitive elder info)
  - anonymized “wall of love” (opt-in)

- Donor receipts:
  - instant receipt email
  - downloadable PDF receipts
  - annual tax/thank-you report

### 2.5 Internal operations & data integrity

- **Approval workflows**
  - elder creation approval (optional)
  - sponsorship approval / unmatch approval
  - donation verification workflow when payment method is “bank transfer”

- **Audit trail**
  - who changed elder health status
  - who marked deceased
  - who edited monthly expense estimates

- **Imports/exports**
  - bulk import elders per branch from Excel
  - export donations for accounting

### 2.6 Notification engine

Instead of sending reminders directly, store “message jobs” so you can retry and audit:

- Email
- SMS (local providers)
- WhatsApp (optional)
- manual-call tasks assigned to staff

### 2.7 Performance & scaling recommendations

For campaign spikes:

- **Queue all notifications + report generation**
- **Cache landing page counters** (Redis)
- **Store media on object storage** (S3/Spaces), not server disk
- **CDN for assets**
- **DB indexes + read-optimized queries**

---

## 3) Upgraded Database Schema (Proposed)

This schema is designed to extend your current structure:
- Keep: `users`, `branches`, `elders`, `donations`, `sponsorships`, `visits`, `timeline_events`, `notifications`, `activity_logs`.
- Add: elder lifecycle tables, health history, campaign system, payment transaction tracking, communication outbox.

### 3.1 Core reference tables

#### 3.1.1 `branches`
Add (if missing):
- `code` (unique short code)
- `region` (nullable)
- `is_active` (bool)

Indexes:
- unique (`code`)

#### 3.1.2 `users`
Add (recommended):
- `phone` (unique nullable)
- `preferred_language` (e.g., `am`, `en`)
- `marketing_opt_in` (bool)

---

### 3.2 Elder domain tables

#### 3.2.1 `elders`
Recommended fields (in addition to existing):
- `code` (unique per org, human-friendly)
- `current_status` (enum)
- `admitted_at` (nullable)
- `deceased_at` (nullable)
- `public_profile_enabled` (bool, default false)
- `public_name` (nullable) (for privacy)

Indexes:
- unique (`code`)
- (`branch_id`, `current_status`)
- (`current_status`)

#### 3.2.2 `elder_status_events`
Tracks elder lifecycle changes.

- `id`
- `elder_id` FK
- `from_status` enum
- `to_status` enum
- `reason` text nullable
- `occurred_at` datetime
- `created_by` FK users

Indexes:
- (`elder_id`, `occurred_at`)

#### 3.2.3 `elder_health_assessments`
Structured health history.

- `id`
- `elder_id` FK
- `assessment_date` date
- `summary` text
- `mobility_level` enum nullable (e.g., `independent`, `assisted`, `bedridden`)
- `risk_level` enum nullable (`low`, `medium`, `high`)
- `created_by` FK users

Indexes:
- (`elder_id`, `assessment_date`)

#### 3.2.4 `elder_medical_conditions`
- `id`
- `elder_id` FK
- `condition_name` string
- `diagnosed_at` date nullable
- `status` enum (`active`, `resolved`) default active
- `notes` text nullable

Index:
- (`elder_id`, `status`)

#### 3.2.5 `elder_medications`
- `id`
- `elder_id` FK
- `medication_name` string
- `dosage` string nullable
- `frequency` string nullable
- `started_at` date nullable
- `ended_at` date nullable
- `notes` text nullable

Index:
- (`elder_id`, `ended_at`)

#### 3.2.6 `elder_documents`
For sensitive documents (medical reports, IDs), stored in private disk.

- `id`
- `elder_id` FK
- `type` enum (e.g., `medical_report`, `id_document`, `consent`, `other`)
- `file_media_id` (if using Spatie medialibrary) OR `path`
- `uploaded_by` FK users
- `uploaded_at`

---

### 3.3 Sponsorship / relationship tables

#### 3.3.1 `sponsorships`
Add/ensure:
- `relationship_type` enum (`father`, `mother`, `brother`, `sister`, `general_support`)
- `expected_amount` decimal
- `expected_currency` string default `ETB`
- `expected_frequency` enum (`monthly`, `quarterly`, `annually`)
- `started_at`, `ended_at`
- `status` enum (`pending`, `active`, `paused`, `ended`, `overdue`)

Indexes:
- (`elder_id`, `status`)
- (`user_id`, `status`)

#### 3.3.2 `sponsorship_payment_schedules`
This is important to track “promises”.

- `id`
- `sponsorship_id` FK
- `period_start` date
- `period_end` date
- `due_date` date
- `expected_amount` decimal
- `currency` string
- `status` enum (`due`, `paid`, `overdue`, `waived`)
- `paid_at` datetime nullable

Indexes:
- (`sponsorship_id`, `due_date`)
- (`status`, `due_date`)

---

### 3.4 Donations & payment tracking (campaign-safe)

#### 3.4.1 `donations`
Add/ensure:
- `branch_id` (denormalized for reporting)
- `campaign_id` nullable
- `donation_type` enum (`one_time`, `sponsorship_payment`, `general_fund`)
- `gateway` string nullable (`telebirr`, `cbe`, etc.)
- `gateway_reference` string nullable (unique)  
- `verified_at` datetime nullable

Indexes:
- unique (`gateway_reference`) when present
- (`campaign_id`, `status`)
- (`branch_id`, `created_at`)

#### 3.4.2 `payment_transactions`
Use this when integrating real gateways + webhooks.

- `id`
- `donation_id` FK nullable
- `sponsorship_id` FK nullable
- `provider` string
- `provider_reference` string
- `amount` decimal
- `currency` string
- `status` enum (`initiated`, `pending`, `success`, `failed`, `refunded`)
- `payload` json (raw webhook/request)
- `created_at`

Indexes:
- unique (`provider`, `provider_reference`)

---

### 3.5 Campaign system

#### 3.5.1 `campaigns`
- `id`
- `title` string
- `slug` string unique
- `description` text nullable
- `starts_at`, `ends_at`
- `goal_amount` decimal nullable
- `goal_currency` string default `ETB`
- `status` enum (`draft`, `active`, `ended`)
- `created_by` FK users

Indexes:
- unique (`slug`)
- (`status`, `starts_at`)

#### 3.5.2 `campaign_metrics_daily`
Optional but recommended for performance.

- `id`
- `campaign_id` FK
- `date`
- `total_amount`
- `donations_count`

Unique:
- (`campaign_id`, `date`)

---

### 3.6 Visits & case notes

#### 3.6.1 `visits`
Add/ensure:
- `requested_at`
- `approved_at`
- `rejected_reason` nullable

#### 3.6.2 `elder_case_notes`
Staff-only notes.

- `id`
- `elder_id` FK
- `note` text
- `visibility` enum (`internal`, `donor_visible`) default internal
- `created_by` FK users
- `created_at`

Indexes:
- (`elder_id`, `created_at`)

---

### 3.7 Communication / notification outbox

#### 3.7.1 `outbound_messages`
Central table to send + retry + audit.

- `id`
- `user_id` FK nullable (recipient)
- `channel` enum (`email`, `sms`, `whatsapp`, `call_task`, `in_app`)
- `to` string nullable (phone/email)
- `template` string nullable
- `subject` string nullable
- `body` text
- `status` enum (`queued`, `sent`, `failed`, `cancelled`)
- `provider` string nullable
- `provider_reference` string nullable
- `attempts` int default 0
- `last_error` text nullable
- `scheduled_at` datetime nullable
- `sent_at` datetime nullable

Indexes:
- (`status`, `scheduled_at`)
- (`user_id`, `created_at`)

#### 3.7.2 `user_notification_preferences`
You already have a migration for this; ensure it supports:
- per-channel toggles
- quiet hours
- language preference

---

## 4) Key Constraints / Rules (Important)

- **Elder privacy:** never expose medical details publicly.
- **Idempotent payments:** `gateway_reference` must be unique to avoid double-credit.
- **Branch isolation:** every elder/donation/sponsorship should be query-scoped by branch for staff.
- **Audit:** marking an elder `deceased` should create an `elder_status_events` row with `created_by`.

---

## 5) MVP Upgrade Roadmap (Suggested Order)

### Phase A (Most important)
- Elder lifecycle (`elder_status_events`)
- Health assessments + basic medical conditions
- Sponsorship relationship type + payment schedules

### Phase B (Campaign readiness)
- Campaigns + campaign donation attribution
- Payment transactions + webhook verification
- Caching counters + queue notifications

### Phase C (Operational maturity)
- Outbound message outbox
- Case notes
- Advanced reporting and exports

---

## 6) Notes About Your Current Docs

- Your `MdDocuments/database_schema.md` is already strong for the core tables.
- This upgrade document focuses on **history tracking**, **campaign scaling**, and **trust/accountability** for a real charity environment.
