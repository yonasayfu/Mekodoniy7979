# Sponsorship Upgrade Plan

## Goal
Ensure every guest pledge (landing-page form) becomes a traceable lead for admins, then can be promoted into the formal `sponsorships` table so a single canonical dataset drives reporting, RBAC access, and the admin dashboard.

## Key Touchpoints
1. `resources/js/pages/Welcome.vue` & `resources/js/pages/GuestDonation.vue` – keep the guest experience identical while adding explicit signals (`elder_id`, `relationship`, `donation_mode`, etc.) that an admin can act upon.
2. `app/Http/Controllers/DonationController.php` – after persisting a guest pledge, create or update the associated `PreSponsorship` record and emit enough metadata to show up on admin screens.
3. `app/Http/Controllers/PreSponsorshipController.php` – reuse this controller/workflow for capturing pledges when someone uses the pop-up modal; ensure every guest pledge has a `pre_sponsorship` row.
4. `resources/js/pages/Sponsorships/Index.vue` (and supporting components) – add a filter or tab to show pending guest pledges and expose actions to “Confirm Sponsorship” that promotes the pledge.
5. `app/Http/Controllers/SponsorshipController.php` (or a dedicated service) – handle the transition from guest pledge + donation into a true `Sponsorship` (creating the row, linking the donation, updating statuses).
6. Reports/notifications – ensure the new sponsorship creation emits the same events/notifications as existing sponsorship flows so reporting sees this data.

## Task List
1. **Track guest pledges:** after `storeGuest` runs, call a service that creates/refreshes a `pre_sponsorship` record (with `donation_id`, `elder_id`, `relationship_type`, `donor info`, etc.).
2. **Expose pending pledges:** extend the sponsorship list page or build a new admin list view that queries `donations` where `donation_type = guest_sponsorship` and shows `pre_sponsorship` context, so admins can manage leads without extra tables.
3. **Confirm button/service:** add an action that promotes the pledge/donation into a `Sponsorship` (mindful of branches/amount/frequency) and updates all linked records (donation status, timeline events).
4. **Real-time cues:** emit events/notifications when a guest pledge is created so the admin dashboard cards reflect the lead instantly (WebSockets or polling).
5. **Document the flow:** update `MdDocuments/sponserUpgrad.md` with findings/status per step so we keep the idea straight.

## Next Update
Once the service for `donations` → `pre_sponsorship` is wired, note the file (`app/Services/`). If we refactor further, record the outcome here so the plan survives the work.
