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
1. **Track guest pledges:** after `storeGuest` runs, call a service that creates/refreshes a `pre_sponsorship` record (with `donation_id`, `elder_id`, `relationship_type`, `donor info`, etc.). _Done: added metadata columns, the `PreSponsorshipService`, and wire-up in `DonationController::storeGuest`._
2. **Expose pending pledges:** extend the sponsorship list page to surface invites with their `pre_sponsorship` context so admins can triage without adding a new table. _Done: `SponsorshipController@index` now returns a `preSponsorships` bucket and `resources/js/pages/Sponsorships/Index.vue` renders the pending cards._
3. **Confirm button/service:** add an action that promotes the pledge/donation into a `Sponsorship` (mindful of branches/amount/frequency updates). _Done: `PreSponsorshipController@promote` owns the route, `SponsorshipPromotionService` spins up the canonical sponsorship, updates the donation, and marks the lead confirmed._
4. **Real-time cues:** emit events/notifications when a guest pledge is created so dashboards and RBAC cards can react immediately. _Done: added `PreSponsorshipCreated` and `SponsorshipConfirmed` events plus broadcast channels; these fire from the service layer when pledges are created/promoted._
5. **Seed data for demos:** new `GuestPledgeSeeder` wires a pledge, a confirmed pledge, and their donations so the admin and donor dashboards can show the new flow immediately; it runs after the existing `PreSponsorshipSeeder` during seeding.
6. **Guest pendings UX:** the sponsorship index now gets donation metadata, displays friendly amounts, disables the confirm button when no elder is attached, and surfaces a warning; the promotion controller flashes so toasts show success/errors.
7. **Offline queue wiring:** queue entries now tag themselves with `x-mekodonia-queue` so the service worker can route POST sync requests via `NetworkOnly` and avoid the `cache.put` POST error.
8. **Pledge edit workflow:** added an edit page/route for pre-sponsorships so admins can pick an elder, update amounts/notes, reject a pledge, or confirm after making changes. Each pending card now links to the edit form alongside the quick confirm button; the edit options safely guard missing elder data to avoid JS errors when stale props arrive. 
9. **Realistic demo data:** replaced the single placeholder pledge with a multi-pledge seeder that uses Faker to generate guest names, emails, and amounts; it now promotes most pledges so the list shows real donor names instead of “donor/donr.”  
10. **Default sorting:** the sponsorship route respects `sort`/`direction` query parameters, and the pre-sponsorship cards link back to the list sorted by `created_at desc` so confirmed records land at the top.  
11. **Branch directory search:** `BranchController@index` now filters with case-insensitive `LIKE` across name/location/contact fields, supports `sort`/`direction`/`per_page`, and feeds those controls to the Branches table so CRUD/search/sort all work as expected.
11. **Offline POST handling:** the service worker now routes every POST through a `NetworkOnly` handler so Workbox never tries to cache them, which removes the `cache.put` POST error and lets the confirm action finish even when offline.
6. **Donation type enum update:** added `database/migrations/2026_03_11_000000_extend_donation_type_enum.php` so the Postgres check constraint accepts `guest_sponsorship` before any seeder writes that value.
5. **Document the flow:** update `MdDocuments/sponserUpgrad.md` with findings/status per step so we keep the idea straight.

## Next Update
Once the service for `donations` → `pre_sponsorship` is wired, note the file (`app/Services/`). If we refactor further, record the outcome here so the plan survives the work.
