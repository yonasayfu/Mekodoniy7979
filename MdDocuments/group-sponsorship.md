# Group Sponsorship Tracker

## Vision
- Treat each elder in the welcome gallery as a micro-campaign with a funding goal and a visible progress indicator so multiple guests can chip in toward the same elder even if no single donor covers the full request.
- Keep the guest donation form unchanged (each pledge remains a discrete donation) but update the elder’s running total so the UI and admin dashboard can tell when the campaign is done.
- Surface the funding status everywhere donors and staff land (welcome gallery, elder public profile, admin notifications) so we can retire fully-funded elders and focus on the next need.

## Status
- [x] Data schema: added `funding_goal`/`funding_received` to `elders` with a migration that defaults to zero, updated `Elder::fillable`/casts, and seeded sample goals so local demos highlight the remaining need (`database/migrations/2026_03_14_000001_add_group_sponsorship_fields_to_elders_table.php`, `app/Models/Elder.php`, `database/seeders/ElderSeeder.php`).
- [x] Backend flow: `WelcomeController` now streams each elder’s goal, received total, progress ratio, and goal/met flag; `DonationController::storeGuest` increments `funding_received` whenever a guest sponsorship is recorded so the campaign progress updates immediately (`app/Http/Controllers/WelcomeController.php`, `app/Http/Controllers/DonationController.php`).
- [x] UI: welcome gallery cards (and any future elder detail views) display a progress bar, remaining amount, and disable the CTA once the target is reached while still funneling guests to the guest-donation form (`resources/js/pages/Welcome.vue`).

## Next steps
1. Decide whether the elder public profile should mirror the same progress indicator and disable the CTA there as well (use the `funding_goal`/`funding_received` fields added above).
2. Update the admin dashboard or notifications to highlight when an elder is fully funded so staff can retire them from the featured list and redirect donors to active campaigns.
3. Later, consider exposing a dedicated “group sponsorship” landing page or campaign view that aggregates all pledges per elder and links to the same guest donation form when the target is still open.

## Recent progress
- Added a toggle on the welcome filters so guests can keep fully funded elders out of the default view but still opt to uncover them if needed (`include_funded` flag and `Welcome.vue` button).
- Surface the same progress info on the elder public profile so every timeline/honour story includes the goal, remaining amount, and progress bar; sponsor action now visually reflects when a campaign closes (`resources/js/pages/Elders/PublicShow.vue`).
- Notifications and the admin dashboard now show the campaign goal/remaining amounts plus a progress bar so staff immediately understand whether a guest donation pushes a campaign over the finish line (`app/Notifications/GuestDonationLoggedNotification.php`, `app/Http/Controllers/DashboardController.php`, `resources/js/pages/Dashboard.vue`, `resources/js/pages/Notifications/Index.vue`).

> Rerun `php artisan migrate` & `php artisan db:seed --class=ElderSeeder` (plus any other necessary seeders) whenever you want to refresh the demo elders so the progress bars match the new goals.
