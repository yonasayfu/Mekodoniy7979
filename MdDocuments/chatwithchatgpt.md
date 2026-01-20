Here in Ethiopia there are a well know big charity organisation called Mekodonia, for this organization I would like to develop a web app which has an idea of connecting the donor(person either company) with the elders where live there. The aim is to create a Father,Mother,Brother,Sister relationships and once the donor fill the form and promise to count/support the one he/she selected he will provide their monthly expenses also visit them at list when he/she have a time, in general the organization has around 60 branch across all over Ethiopia region and city and villages, so this app should serve us the connecter of the donor and the one who receive the donation from the organization, so it serves as a database too for the organization, the organization put by priority elders who needs attention and help or counts as a Father and Mother then brother and sister... and he put all data , their history, photo, video,therir monthly expenses, and so on, once they are hold by the donor the system put them on back for admin and has alert that the donor has keep his promise and he continue to support them, and will have a report, also give a notification for donor via different ways like sms, email,calll and others to put/insert the monthly support if he didn't not, the system on each year also provide the Thank you report for who support continuously and has many elders... for end user/donor the landing page should be simple and they select the one who like them the they put/insert a form which has their/donor detail like banks, how they transfer...address,at what range they support...and others, on the back end this ranged by super admin...also the end user will have a random help page or support incase they don't want to help consitencely, the page should have a one day (like lunch/dinner/breakfast for one elders) that has easily mobile transfer or bank transfer with out create any profile(I mean directly found on the landing page as a gust) on the landing page also any user/gust can see who is combine /match with who as a slide report for initiating) so if you understand my idea let develop this idea and first let see the outline and requirement development, once we have that then we will have a database scheme list then we will dive in to our larval project which I plan to.use.laravel 12(lates currently) with vue 3 and mysql database... so first let develop the idea and what another thing should I consider and should add on this project
from other AI agent I found this, is there anything that you can take from this and develop your idea....Below is a “living” blueprint you can copy-paste into your project repo and keep updating as you learn more.  
It is split into four parts:

1. Product Narrative – one-page story you can hand to anybody (board, donor, junior dev) and they immediately “get” the vision.
2. Functional Map – every screen, button, e-mail, SMS, cron-job, report.
3. Non-functional / Technical checklist – things that kill charity apps: offline branches, low bandwidth, Amharic input, GDPR-style consent, poor UX on $40 phones, etc.
4. Next-step deliverables – the exact artefacts we need before we open Laravel 12 + Vue 3 + MySQL.

Feel free to challenge or trim; the goal is to surface hidden complexity now, not after you have 60 branches uploading videos on 2G.

---

1. PRODUCT NARRATIVE (elevator pitch)

---

“Mekedonia Home Connect” is a match-making & stewardship platform that turns an orphan elder into somebody’s “Father”, “Mother”, “Brother” or “Sister” and then makes sure the promise really happens.

For the donor it feels like “online dating” – swipe, pick, promise, pay.  
For the branch staff it feels like “light-weight CRM” – upload elder story, mark need, track promise, confirm monthly arrival, print annual thank-you certificate.  
For the elder it feels like “someone remembers me” – a photo on the wall, a visit on Meskel, a text that says “Enkuan desta, I transferred this month”.

The organisation keeps the central ledger, but every branch can work offline and sync when 3G appears.

---

2. FUNCTIONAL MAP

---

A. PUBLIC / GUEST

1. Landing (Amh/Eng)
    - Hero slider → “Become a Father today”
    - Counters: 4 327 elders waiting, 1 841 matched, 312 visited this month
    - “I want to help NOW” → two big buttons  
      a) I want a long-term relation (create donor profile)  
      b) I want to buy one meal today (guest checkout)
    - Wall of Love – auto-scroll cards “Abebech got a brother → photo + first name only”
    - FAQ, Trust badges (SSL, bank logos, 501(c) equivalent)

2. Elder gallery
    - Filter: Region | Need level (Father > Mother > Brother > Sister) | Age | Gender
    - Cards: photo, first name only, 20-word bio, monthly need (ETB), flag “Visited last: 3 mo ago”
    - Click → detail page (story, video, 3 photos, expense break-down, “Adopt me” button)

3. One-meal checkout (guest)
    - Amount slider: ETB 70 (breakfast) – 150 (lunch) – 250 (dinner)
    - Choose elder or “any elder in urgent queue”
    - Pay: Telebirr, M-Pesa, Amole, CBE-Birr, Visa (if abroad)
    - Capture only: name, e-mail (optional), phone (for receipt)
    - Immediate PDF receipt + SMS “You bought dinner for Ayelech, God bless you.”

B. DONOR DASHBOARD (after registration / login)

1. On-boarding wizard
    - Pick relation type (Father/Mother/…) → system shows only those elders
    - Promise amount (can be > minimum), start date
    - Preferred contact channel (SMS, e-mail, WhatsApp, phone-call)
    - Upload profile picture (optional) – shown to elder (privacy toggle)

2. My Elders
    - Cards with traffic-light: Green = paid this month, Amber = grace days, Red = overdue
    - Quick actions: Pay now, Schedule visit, Write letter, Download annual tax receipt

3. Auto-payment setup
    - Telebirr / CBE auto-debit (uses EthSwitch token) OR
    - Standing bank order (upload scanned form) OR
    - Manual reminder only

4. Visit scheduler
    - Calendar with branch availability, transport tips, “I need translator” checkbox
    - Branch admin confirms → both parties get SMS + calendar file

5. Impact timeline
    - Like Facebook feed: “You covered 18 months, ETB 5 400, 3 visits, 1 Christmas gift”
    - Shareable image generator for social media (branding watermark)

C. BRANCH STAFF (role: branch_editor)

1. Elder profile CRUD
    - Amharic & English fields, voice-note story, 3 photos max 500 kB auto-compressed,
    - Consent form signed (photo upload), GDPR-style check-boxes
    - Expense template: Food, Clothing, Medicine, Hygiene, Rent – total = monthly need
    - Priority score (algorithm: age, health, visits, already matched?)

2. Match / Un-match
    - Search donors by region, language, religion (soft preference)
    - One-click “Propose” → donor gets SMS “We found you a Mother …”
    - Donor has 72 h to accept or decline (else elder returns to pool)

3. Money reconciliation
    - Daily import from bank API (CBE, Telebirr) or manual upload CSV
    - Auto-match to donor+elder, unmatched items → queue for staff
    - Flag “Broken promise” after 45 days → elder auto-returned to pool + admin alert

4. Offline mode (Progressive Web App)
    - React-query style cache; uploads queued; photo compression before store; sync when WiFi

D. SUPER-ADMIN

1. Multi-tenant settings (each branch = tenant, data isolated)
2. Role & permission matrix (Upgraded)
    - Super Admin: Global access, manages branches.
    - Branch Admin: Scoped to `branch_id`, manages specific branch elders/staff.
    - Branch Staff: Scoped to `branch_id`, read-only or limited write (visits).
    - Auditor: Global read-only.
    - Donor: Self-service only.
3. Global reports
    - Matched vs waiting elders by region
    - Donor churn, average delay, visit frequency
    - Forecast cash-flow next 6 months
4. Communication hub
    - Bulk SMS / e-mail with template variables {first_name}, {elder_name}, {last_visit}
    - Scheduled “Thank-you day” campaign (auto-generate certificates PDF)
5. Audit log (who changed elder need amount, who un-matched, exportable for board)

E. CRON / AUTOMATION

- Daily 08:00 – check missing payments → send reminder (channel chosen by donor)
- Weekly – generate “Next week visit plan” PDF for each branch
- Monthly – auto-issue receipt PDF, update green/amber/red flags
- Yearly – compile “Impact book” (print-ready) + e-mail to donor

---

3. NON-FUNCTIONAL / TECHNICAL CHECKLIST

---

1. Localization
    - i18n: Amharic (primary), English, Afaan-Oromo (later) – store in JSON, RTL-friendly fonts.
    - Date: Ethiopian calendar converter (package: andegna/ecal).
    - Number: ETB currency, comma separator.

2. Low-bandwidth & devices
    - Image lazy-load, WebP, <100 kB each, video thumbnail only, click to stream.
    - Vue3 build split in chunks; service-worker cache; offline page “You are offline – your data will sync when connected.”
    - USSD fallback for balance check: _804_<donor_id># (optional future)

3. Payment & Compliance
    - Telebirr, CBE-Birr, Amole, M-Pesa, Chapa (card) – use their official REST SDKs.
    - Save only last-4 digits, tokenise with provider; PCI-DSS scope minimised.
    - National Bank of Ethiopia directive 2022 – foreign donation KYC > USD 500 (scan passport).
    - Receipts must have serial number approved by Ministry of Revenue – auto-increment sequence table.

4. Privacy & Consent
    - Elder consent form checkbox: “I agree my photo and first name can be shown to potential donors.”
    - Donor data governed by Ethiopia Personal Data Protection Proclamation (draft 2022) – right to delete, export.
    - Children’s photo: if <18 guardian signature (rare but possible).

5. Security
    - Laravel Sanctum SPA auth + optional Socialite (Google).
    - 2-FA for staff (TOTP).
    - Rate-limit: 5 login attempts, 10 SMS/min, 50 API/min.
    - Hash password bcrypt, argon2id for new PHP.
    - Daily encrypted off-site backup (S3 or Wasabi).

6. Performance targets
    - First contentful paint <2 s on 3G.
    - API p95 <400 ms (Laravel Octane + Redis cache).
    - MySQL indices on elder_id, donor_id, payment_date, branch_id.
    - Queue workers for SMS (use local gateway e.g. Ethio-Telecom BulkSMS).

7. Maintainability
    - Follow Laravel 12 directory conventions.
    - Feature tests: at minimum “elder can be matched”, “payment marks green”, “reminder sent”.
    - PHPStan level 8, ESLint Airbnb, Prettier.

---

4. NEXT-STEP DELIVERABLES

---

Step 0 – Validate (this week)

- 30-min call with one branch manager: show Figma mock-ups, confirm “expense template” fields.
- Sample CSV of 20 real elders (anonymise) to test import.

Step 1 – Product backlog (GitHub issues)

- Create repo mekedonia-connect, label: epic / feature / bug.
- Epics: 1) Guest meal checkout, 2) Elder CRUD, 3) Match engine, 4) Payment sync, 5) Offline PWA, 6) Reports.

Step 2 – Data model v1 (next message)

- I will send you a Laravel-ready migration set (users, elders, donors, pledges, payments, visits, receipts, branches, audit_logs).
- We review together, then lock.

Step 3 – Wireframes (Figma)

- Landing, Elder gallery, Donor dashboard, Branch elder form.
- Share link to board for feedback.

Step 4 – Architecture decision record (ADR)

- Laravel Octane vs vanilla, MySQL vs Postgres, Redis cache yes/no, S3 bucket region, SMS gateway choice.

Step 5 – Sprint 0 – “Walking skeleton” (goal: guest can buy one meal)

- Laravel 12 + Vue3 inertia starter, Telebirr sandbox, deploy on DigitalOcean droplet Addis region, CI/CD with GitHub Actions.

Once you confirm the narrative and functional map above, I’ll paste the migration schema and we can generate the Laravel models.,
so let have the revised as a full
