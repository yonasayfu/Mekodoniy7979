Perfect! Let’s create a full, revised blueprint for your Mekodonia Donor-Elder Web App, merging your ideas and the detailed “living blueprint” you found. This will serve as the complete reference before we design the database and start Laravel 12 + Vue 3 development.
Mekodonia Donor-Elder Web App – Full Revised Blueprint
1. Product Narrative (Elevator Pitch)
“Mekodonia Home Connect” is a donor-elder matchmaking and stewardship platform that turns elders into somebody’s “Father,” “Mother,” “Brother,” or “Sister” and ensures that the donor’s promise is fulfilled.
For donors: It feels like “online family connection” — browse elders, select one, pledge support, and track your relationship.
For branch staff: A lightweight CRM — manage elder profiles, track donations, approve pledges, and schedule visits.
For elders: A reminder that someone cares — personalized visits, messages, and support.
For Mekodonia: Centralized ledger of elders, donors, branches, and donations, with offline support for low-bandwidth areas.
Key principles: transparency, accountability, scalability across 60 branches, low-bandwidth optimization, Amharic support, and mobile-first design.
2. Users & Roles
Role	Permissions / Access	Notes
Super Admin	Global control, manage branches, donors, elders, roles, reports, notifications, audit logs	Multi-tenant setup (branch-level isolation)
Branch Admin / Editor	Manage elders in branch, approve donations, match/unmatch donors, upload media, handle offline sync	Limited to branch
Donor (Registered)	Browse elders, pledge support, auto-pay setup, schedule visits, track donations, receive notifications	Optional profile picture & privacy toggle
Guest Donor	One-time donations without registration	Quick checkout for meals or urgent donations
Accountant / Auditor	View donations, reconcile payments, generate reports	For internal compliance
3. Core Features / Modules
A. Public / Guest
Landing Page
Hero slider: “Become a Father Today”
Live counters: elders waiting, matched, visited this month
Buttons: “Long-term support” (donor profile) & “One-day support” (guest)
Wall of Love: auto-scroll of matched donors & elders (first name + photo)
FAQ, Trust badges (SSL, bank logos, charity certification)
Elder Gallery
Filters: Region, priority (Father > Mother > Brother > Sister), age, gender
Cards: photo, first name, short bio, monthly need, last visit info
Elder detail page: story, videos, photos, expense breakdown, “Adopt me” button
Guest Donation (One Meal)
Slider: ETB 70 (breakfast) – 150 (lunch) – 250 (dinner)
Choose elder or “any urgent elder”
Payment: Telebirr, CBE-Birr, Amole, M-Pesa, Visa
Capture minimal info: name, email (optional), phone
Immediate receipt: PDF + SMS notification
B. Donor Dashboard (Registered Donors)
Onboarding Wizard
Pick relationship type → show eligible elders
Set pledge amount, start date
Select preferred contact (SMS, email, WhatsApp, phone call)
Optional profile picture
My Elders
Cards with traffic-light status:
Green = paid this month
Amber = grace period
Red = overdue
Quick actions: Pay now, Schedule visit, Write letter, Download annual receipt
Auto-Payment Setup
Telebirr / CBE auto-debit (EthSwitch token)
Standing bank order (upload scanned form)
Manual reminder only
Visit Scheduler
Calendar with branch availability
Transport guidance & translator option
Branch confirms → SMS & calendar file sent
Impact Timeline
Visual feed: donations, visits, gifts, months supported
Shareable images for social media
C. Branch Staff / Editor
Elder Management
CRUD elder profiles: Amharic & English, photos (max 3, 500 kB each), videos, consent forms
Monthly expense breakdown: Food, Clothing, Medicine, Hygiene, Rent
Priority score (age, health, current support)
Match / Unmatch
Search donors by region, language, religion
One-click “Propose” → donor has 72h to accept
Auto-return unmatched elders to pool
Donation Reconciliation
Daily import from bank APIs or manual CSV upload
Auto-match donor-elder pledges, flag broken promises
Offline Support
Offline PWA: cache data, queue uploads, compress images/videos
D. Super Admin
Multi-tenant setup: branch-level data isolation
Role & permission matrix management
Global reports:
Matched vs waiting elders, donor churn, visit frequency, cash-flow forecasts
Communication hub: bulk SMS/email templates with placeholders
Scheduled “Thank-you Day” campaigns (auto PDF certificate generation)
Audit log: track edits, unmatched elders, payment status changes
E. Automation / Cron Jobs
Daily: check missing payments → send reminders (preferred channel)
Weekly: generate “next week visit plan” PDF for branches
Monthly: auto-issue receipts, update traffic-light flags
Yearly: compile “Impact Book” for donors
4. Non-Functional / Technical Checklist
Localization
Amharic primary, English, Afaan-Oromo (future)
Ethiopian calendar conversion (package: andegna/ecal)
ETB currency formatting
Low-Bandwidth & Devices
Lazy load images, WebP format, <100 kB each
Video thumbnails only
Offline PWA support, service worker caching
Simple, mobile-first UX
Payment & Compliance
Tokenized payments, PCI-DSS minimized
Foreign donations > USD 500: KYC scan
Ministry of Revenue-approved receipt numbers
Privacy & Security
Elder consent forms
Donor data protection (right to delete/export)
Laravel Sanctum SPA auth, optional Socialite
2FA for staff, rate limits, hashed passwords
Daily encrypted off-site backup
Performance & Maintainability
API <400 ms p95, first contentful paint <2s on 3G
Queue workers for SMS/email
Laravel conventions, feature tests, code quality tools (PHPStan, ESLint, Prettier)
5. Database & Data Considerations (High-Level)
Tables to include:
Users (donors, staff, admins)
Branches
Elders (profile, priority, expenses, media)
Donations / Pledges
Payments / Transactions
Visits (donor → elder)
Receipts (PDF storage & serial number)
Notifications (SMS, email, call)
Audit logs (edits, payment status changes, unmatches)
Relationships:
Elder ↔ Branch
Elder ↔ Donor (many-to-one, with history)
Donor ↔ Payment (one-to-many)
Branch ↔ Staff (one-to-many)
6. Next-Step Deliverables
Requirement Validation
30-min call with branch manager to confirm expense template fields
Sample anonymized CSV of 20 elders for testing import
Product Backlog (GitHub Issues)
Epics: Guest Meal Checkout, Elder CRUD, Match Engine, Payment Sync, Offline PWA, Reports
Database Model / Laravel Migrations
Users, Elders, Donors, Pledges, Payments, Visits, Receipts, Branches, Audit logs
Wireframes (Figma)
Landing page, elder gallery, donor dashboard, branch editor forms
Architecture Decision Record (ADR)
Laravel Octane vs vanilla, MySQL vs Postgres, Redis, storage (S3/Local), SMS gateway
Sprint 0 – Walking Skeleton
Goal: guest can donate one meal
Setup Laravel 12 + Vue3 + Inertia
Telebirr sandbox integration
Deploy to DigitalOcean, CI/CD via GitHub Actions
✅ With this full revised blueprint, we now have everything structured before starting coding.