# Payment Process Blueprint

## Vision
- Deliver an end-to-end guest payment path so donors can commit via Telebirr or bank transfer, agree to recurring deductions, attach signed authorization forms, and hand the context to the operations team without leaving the Mekodonia experience.
- Every CTA on the welcome page and elder spotlight should land on a form that: (1) pre-fills the relationship/elder/mode, (2) logs the pledge in the database, and (3) feeds the payment operations queue for reconciliation and execution.

## Current Progress
- Hero CTAs and elder spotlight buttons all open `/guest-donation` with pre-populated relationship/mode query params, keeping guests on the same landing experience.
- The welcome hero, filters, and “How Support Happens” story now read like a production landing page while still funneling visitors toward the guest donation form.
- The guest form already exposes QR/bank instructions, mandate downloads, cadence options, and file uploads, and the thank-you flow is wired to the backend receipt generator.
- Manual testing is the approved validation path for this payment flow—no browser automation scripts until we hand it over to finance for live data.

## Guest Payment Journey
1. **Awareness & intent** – Guests browse the hero cards or elder gallery, then open `/guest-donation?mode=<one_time|sponsorship>&elder_id=...&relationship=...`.
2. **Payment preference** – The form shows Telebirr instructions (QR code + transfer hints) and bank transfer details. Guests choose:
   - **Telebirr**: Scan the QR code, optionally specify a recurring cadence (monthly/quarterly/yearly) inside the notes, and upload a screenshot or receipt.
   - **Bank transfer**: Download the mandate PDF that spells out the deduction frequency, sign it, re-upload it to the form, and optionally confirm a recurring schedule or one-time event.
3. **Commitment capture** – The submission includes contact info, selected relationship, elder, donation_mode, cadence, and any uploaded proof/signature. The backend:
   - Validates media and cadence choices.
   - Creates a `Donation` record with `payment_gateway` set (`telebirr`, `bank`, or `manual`) plus `donation_type`.
   - Stores receipt/signature paths and notes about the cadence (“monthly pledge”, “12-week meal plan”, etc.).
4. **Ops handoff** – Branch admins get notified (email/SMS) with links to:
   - The donor’s uploaded mandate/receipt.
   - The elder profile/relationship.
   - The suggested transfer cadence so they can align with the finance team.

## Backend Fulfillment Requirements
- **Recurring cadence field**: Extend `StoreDonationRequest`/`DonationController` so the form can submit `cadence` (`monthly`, `weekly`, `annual`, or `custom`) and `recurrence_duration` (months/days/years).
- **Media handling**: Accept uploads (signature PDF/image, Telebirr slip) and store them on `storage/app/public/receipts/manual`. Save the paths on the donation record for auditing.
- **Signature download**: Provide a generated mandate (`resources/pdf/mandate-template.pdf`) guests can download, sign, and re-upload. The form should include a checklist/consent text confirming they authorize recurring deductions.
- **Operational state**: Add `payment_status` (`pending`, `awaiting_receipt`, `confirmed`, `failed`) and `deduction_schedule` fields so staff can track whether finance has confirmed the transfer.
- **Notifications**: After the form posts, trigger `GuestDonationLoggedNotification` with cadence and attachment links, and send a follow-up email/SMS once finance confirms (future work).
- **Audit trail**: Log every submission in the `donations` table, including `relationship`, `elder_id`, `notes`, `cadence`, `donation_mode`, `receipt_path`, `mandate_path`, and `created_by` (guest/anonymous). Use `ActivityLog` or a dedicated `PaymentIntent` model if more detail is needed.

## Staff Workflow
1. Finance/admin receives notification along with the uploaded files.
2. They verify the Telebirr transaction or bank deposit (use a future Telebirr webhook for auto-verification).
3. Once verified, they update `payment_status` to `confirmed`, attach any additional proof, and send the receipt PDF via `GuestDonationReceiptNotification`.
4. For recurring commitments, finance sets up the deduction schedule manually (Telebirr standing order, bank mandate). The system should store the agreed cadence and remind staff when renewals are due.

## Implementation Task List
1. [x] Add `cadence`, `recurrence_duration`, `mandate_path`, and `payment_status` fields to the guest donation form/back-end structures.
2. [x] Surface Telebirr QR and bank instructions on `GuestDonation.vue`, include upload widgets for receipts/signatures, and document the cadence options.
3. [x] Create or wire a downloadable mandate PDF/HTML that guests can sign, then re-upload via the form, storing the path and linking it in notifications.
4. [x] Extend `DonationController::storeGuest` to validate cadence inputs, handle file uploads, set `payment_status`, and fire `GuestDonationLoggedNotification` with the new metadata.
5. [x] Update the ops notification templates/details (`GuestDonationLoggedNotification`, mail, dashboard) so staff see cadence, elder, uploaded proof, and preferred gateway.
6. [x] Design UI for admins to download the uploaded files, mark the transfer as confirmed, and optionally trigger the receipt notification/email.
7. [x] Backstop with a `payment-flow` test that simulates a guest submitting Telebirr or bank data and validates the stored record plus attachments.
8. [ ] Surface a pending guest donation card on the admin dashboard that links to the uploaded receipt/mandate and lets staff confirm payments without leaving the overview screen.
9. [ ] Wire the Telebirr webhook to update guest donations using the stored payment reference/ID, set `payment_status`, and optionally resend the receipt when finance confirms the transfer.
10. [ ] Improve the thank-you/receipt page so it mirrors the guest’s chosen cadence, relationship, and provides direct links back to the mandate and generated receipt.

## Follow-up
- After these tasks, revisit the welcome page CTA labels so they mention “Scan Telebirr QR” or “Get Bank Mandate” as appropriate.
- Plan a Telebirr webhook integration that reads the `payment_id`/`gateway_reference`, auto-marks `payment_status`, and sends receipts without manual intervention.
- Manual testing remains the only approved QA step until the finance team signs off on real Telebirr data.
