# Welcome Page Finalization

## Aim
- Make the landing experience obvious for donors and guests: every CTA should either scroll to a relevant section or land on a form that is already wired to the backend so the visitor can donate, pledge, or sponsor without guessing where to go.
- Track progress here so we can verify each refinement (hero, filters, gallery, wall of love, counters) is aligned with the project aim.

## Findings
1. `resources/js/pages/Welcome.vue:447-478` still uses `Link` components for in-page anchors (Learn More / Browse Elders). Inertia hijacks the click, so the browser performs a full visit to `/#how-it-works` instead of smoothly scrolling to the section.
2. The hero quick actions like **Find a Father** still filter `#elders-gallery` via `openRelationshipGallery` (see `resources/js/pages/Welcome.vue:191-218`) so visitors stay on the hero until they pick an elder. The hero CTA button and relationship cards now redirect to the guest donation form with `relationship`/`mode` properly seeded.
3. Elder cards link to `route('elders.public.show', elder.id, false)` (`resources/js/pages/Welcome.vue:744-799`) but there is no immediate “Sponsor this elder” CTA that pre-fills the guest donation form with `elder_id`/`relationship`. The backend is ready to accept those query params (`resources/js/pages/GuestDonation.vue:52-84` and `app/Http/Controllers/DonationController.php:23-85`), so the link path can be shortened.
4. Hero slides rely on seeded `HeroSlide` rows (`database/seeders/HeroSlideSeeder.php:8-38`) and `WelcomeController` asset resolution (`app/Http/Controllers/WelcomeController.php:33-92`), so any mismatch (inactive slides, missing `cta_link`) leaves the hero section empty or the button inert.

## Task List
- [x] Switch the Win section anchors (`Learn More`, hero quick action `Browse Elders`) to `button`/`<a>` elements that call `scrollToAnchor` so the indicator stays on the same page; drop the `Link` wrappers that trigger Inertia visits (`resources/js/pages/Welcome.vue:447-478`).
- [x] Extend the hero CTA + relationship cards to open the guest donation/pre-sponsorship form with the chosen relationship and donation mode encoded in the query string. Re-use the same `relationship` + `mode` keys that `resources/js/pages/GuestDonation.vue:52-84` already reads.
- [x] Add a “Sponsor this elder” CTA on each elder card so the donor can jump straight to `/guest-donation?elder_id={id}&relationship=...`. Populate `relationship` from the card’s highlight (or default to `'sponsorship'` for now) so the backend (`app/Http/Controllers/DonationController.php:23-85`) receives context without manual entry.
- [x] Harden hero data by validating `/HeroSlide` seed data and the `WelcomeController` response; ensure each active slide has a `cta_link` (modal, anchor, or route) and fallback assets so the UI never falls back to the pale banner in `resources/js/pages/Welcome.vue:506-516`.
- [x] Verify the elder filters (`api` and `router.get` calls around `resources/js/pages/Welcome.vue:137-205`) keep their state in the query string, and document the expected counters/filters in this file so future enhancements (e.g., region/branch filtering) can re-purpose the existing hooks without regressing the guest experience.

## Updates
- The hero area now exposes highlight cards (trust, transparency, responsiveness) below the CTA so the first impression reads more like a professional landing experience.
- Active filters show badges plus a “Clear” action underneath the hero so guests immediately see the relationship/priority/gender filters they applied.
- Reworded the “How Support Happens” and “Donor Journey” sections so they articulate two complementary narratives—discovery/matching/confirmation and curiosity-to-commitment—avoiding verbatim duplication while keeping dedicated CTAs (`resources/js/pages/Welcome.vue:691-793`).
- The hero section now has padding on top so it clears the fixed navbar, and the CTA emphasizes that donors browse the gallery first; “Find a Father/Mother” actions keep filtering rather than jumping straight to the form.
- The “Trust Signals” section highlights the verified elders, transparent support, and responsive team messages with distinct cards—each explains how we vet elders and track impact instead of acting as redundant buttons, and they live in their own dedicated block with descriptive copy (`resources/js/pages/Welcome.vue:534-710`).
- Updated the “Our Impact” counter section with a gradient background, branded heading (“Stories told through numbers”), and narrative copy so the stats feel like a storytelling moment rather than a plain metric list (`resources/js/pages/Welcome.vue:850-898`).
- Elder cards now surface the relationship, branch, and location metadata shared by `WelcomeController`, and the “Sponsor this elder” CTA seeds `/guest-donation` with that elder plus relationship context; filters stay in sync because the priority/gender refs now follow the query string via new watchers.
- The seeded experience is intentionally limited—branches, staff, elders, and guest donations each stop at three demo entries (`BranchSeeder`, `StaffSeeder`, `ElderSeeder`, and the new `GuestDonationSeeder`); rerun `php artisan db:seed` when you need those demo donations refreshed so the admin card and notifications always show a manageable list.
- The guest form now requires a name/phone, auto-creates a DONOR member record (phone as login) with a generated password, and the thank-you/receipt surfaces those credentials so donors can sign in, view their history, and update their pledge; the admin cards now show the entered name instead of “Unknown donor.”

> Hero quick actions continue to act as gallery filters, and the donation form only surfaces after a guest chooses an elder and hits “Sponsor this elder,” so the selection step is front-and-center.
