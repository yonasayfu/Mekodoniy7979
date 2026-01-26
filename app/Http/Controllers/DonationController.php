<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\DonorProfile;
use App\Models\Elder;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Notifications\GuestDonationLoggedNotification;
use App\Notifications\GuestDonationReceiptNotification;
use App\Services\PreSponsorshipService;
use App\Support\Services\DonationReceiptService;
use App\Support\Services\KycService;
use App\Support\Services\TelebirrService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Store a newly created guest donation in storage.
     *
     * @param StoreDonationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeGuest(StoreDonationRequest $request, DonationReceiptService $receiptService, KycService $kycService, PreSponsorshipService $preSponsorshipService)
    {
        $validatedData = $request->validated();

        [$donorUser, $memberPassword] = $this->ensureGuestMember($validatedData);

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $uploadedReceiptPath = null;
        if ($request->hasFile('receipt')) {
            $uploadedReceiptPath = $request->file('receipt')->store('receipts/manual', 'public');
        }

        $mandateStoragePath = null;
        if ($request->hasFile('mandate')) {
            $mandateStoragePath = $request->file('mandate')->store('receipts/mandates', 'public');
        }

        $kycRequired = $kycService->shouldRequire($validatedData['amount'], 'ETB');
        $donationMode = $validatedData['donation_mode'] ?? 'one_time';
        $paymentGateway = $validatedData['payment_gateway'] ?? 'manual';

        $paymentStatus = 'pending';
        if ($uploadedReceiptPath || $mandateStoragePath) {
            $paymentStatus = 'awaiting_receipt';
        }

        $existingDonation = null;
        if (! empty($validatedData['existing_donation_id']) && $donorUser) {
            $existingDonation = Donation::where('id', $validatedData['existing_donation_id'])
                ->where('user_id', $donorUser->id)
                ->first();
        }

        if ($existingDonation) {
            $previousAmount = $existingDonation->amount;

            $existingDonation->update([
                'elder_id' => $elder?->id,
                'branch_id' => $elder?->branch_id,
                'amount' => $validatedData['amount'],
                'guest_name' => $validatedData['name'] ?? null,
                'guest_email' => $validatedData['email'] ?? null,
                'guest_phone' => $validatedData['phone'] ?? null,
                'payment_gateway' => $paymentGateway,
                'payment_reference' => $validatedData['payment_reference'] ?? $existingDonation->payment_reference,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'currency' => 'ETB',
                'donation_type' => $donationMode === 'sponsorship' ? 'guest_sponsorship' : 'guest_meal',
                'campaign_id' => $validatedData['campaign_id'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'cadence' => $validatedData['cadence'] ?? null,
                'recurrence_duration' => $validatedData['recurrence_duration'] ?? null,
                'deduction_schedule' => $validatedData['deduction_schedule'] ?? null,
                'kyc_required' => $kycRequired,
                'kyc_status' => $kycRequired ? 'pending' : 'not_required',
                'receipt_path' => $uploadedReceiptPath ?? $existingDonation->receipt_path,
                'mandate_path' => $mandateStoragePath ?? $existingDonation->mandate_path,
            ]);

            $donation = $existingDonation;

            $delta = $donation->amount - $previousAmount;
            $this->adjustFunding($elder, $donation->donation_type, $delta);
        } else {
            $donation = Donation::create([
                'user_id' => $donorUser?->id,
                'elder_id' => $elder?->id,
                'branch_id' => $elder?->branch_id,
                'amount' => $validatedData['amount'],
                'guest_name' => $validatedData['name'] ?? null,
                'guest_email' => $validatedData['email'] ?? null,
                'guest_phone' => $validatedData['phone'] ?? null,
                'receipt_path' => $uploadedReceiptPath,
                'mandate_path' => $mandateStoragePath,
                'payment_gateway' => $paymentGateway,
                'payment_reference' => $validatedData['payment_reference'] ?? null,
                'payment_id' => null,
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'currency' => 'ETB', // Default currency
                'donation_type' => $donationMode === 'sponsorship' ? 'guest_sponsorship' : 'guest_meal',
                'campaign_id' => $validatedData['campaign_id'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'cadence' => $validatedData['cadence'] ?? null,
                'recurrence_duration' => $validatedData['recurrence_duration'] ?? null,
                'deduction_schedule' => $validatedData['deduction_schedule'] ?? null,
                'kyc_required' => $kycRequired,
                'kyc_status' => $kycRequired ? 'pending' : 'not_required',
            ]);

            if ($elder && $donation->donation_type === 'guest_sponsorship') {
                $elder->increment('funding_received', (int) $donation->amount);
                $elder->refresh();
            }
        }

        if ($donorUser) {
            $donation->user_id = $donorUser->id;
            $donation->save();
        }

        $donation->setRelation('elder', $elder);

        $receiptStoragePath = $receiptService->ensureReceipt($donation);
        $receiptUrl = $receiptStoragePath ? url(route('receipts.show', $donation->receipt_uuid, false)) : null;

        if ($donation->guest_email && $receiptStoragePath) {
            Notification::route('mail', $donation->guest_email)
                ->notify(new GuestDonationReceiptNotification(
                    $donation,
                    $receiptStoragePath,
                    $memberPassword,
                    $donorUser?->phone_number,
                ));
        }

        $this->notifyBranchTeam($donation, $receiptUrl);

        if ($donationMode === 'sponsorship') {
            $preSponsorshipService->syncFromDonation(
                $donation,
                $validatedData['relationship'] ?? 'sponsorship',
            );
        }

        return redirect()->route('thank-you')->with('donation_summary', [
            'relationship' => $validatedData['relationship'] ?? 'sponsorship',
            'amount' => $donation->amount,
            'elder_name' => optional($elder)->first_name ? ($elder->first_name . ' ' . $elder->last_name) : null,
            'mode' => $donationMode,
            'cadence' => $validatedData['cadence'] ?? null,
            'payment_gateway' => $paymentGateway,
            'payment_status' => $paymentStatus,
            'deduction_schedule' => $validatedData['deduction_schedule'] ?? null,
            'receipt_url' => $receiptUrl,
            'mandate_url' => $mandateStoragePath
                ? Storage::disk('public')->url($mandateStoragePath)
                : null,
            'donor_name' => $validatedData['name'] ?? null,
            'donor_phone' => $validatedData['phone'] ?? null,
            'member_login_phone' => $donorUser?->phone_number,
            'member_password' => $memberPassword,
            'payment_reference' => $validatedData['payment_reference'] ?? null,
        ])->with('success', 'Thank you! Your donation is pending confirmation.');
    }

    public function prefillGuest(Request $request)
    {
        $reference = $request->query('payment_reference');

        if (! $reference) {
            return response()->json([
                'message' => 'Missing payment reference.',
            ], 422);
        }

        $donation = Donation::where('payment_reference', $reference)
            ->where('user_id', $request->user()?->id)
            ->first();

        if (! $donation) {
            return response()->json([
                'message' => 'Donation not found.',
            ], 404);
        }

        return response()->json([
            'donation' => [
                'id' => $donation->id,
                'amount' => $donation->amount,
                'notes' => $donation->notes,
                'relationship' => $donation->relationship,
                'elder_id' => $donation->elder_id,
                'campaign_id' => $donation->campaign_id,
                'payment_gateway' => $donation->payment_gateway,
                'payment_reference' => $donation->payment_reference,
            'donation_mode' => $donation->donation_mode,
            'cadence' => $donation->cadence,
            'recurrence_duration' => $donation->recurrence_duration,
            'deduction_schedule' => $donation->deduction_schedule,
            'donation_type' => $donation->donation_type,
        ],
    ]);
    }

    public function confirmGuestPayment(Request $request, Donation $donation, DonationReceiptService $receiptService)
    {
        abort_unless($request->user()?->can('donations.manage'), 403);

        $paymentStatus = 'confirmed';
        $status = $donation->donation_type === 'guest_sponsorship' ? 'confirmed' : 'completed';

        $donation->update([
            'payment_status' => $paymentStatus,
            'status' => $status,
        ]);

        $receiptStoragePath = $receiptService->ensureReceipt($donation);
        $receiptUrl = $receiptStoragePath
            ? url(route('receipts.show', $donation->receipt_uuid, false))
            : null;

        if ($request->boolean('send_receipt') && $donation->guest_email && $receiptStoragePath) {
            Notification::route('mail', $donation->guest_email)
                ->notify(new GuestDonationReceiptNotification($donation, $receiptStoragePath));
        }

        return response()->json([
            'message' => 'Payment confirmed',
            'receipt_url' => $receiptUrl,
            'payment_status' => $paymentStatus,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * This method can be used for authenticated users and integrated payment gateways.
     */
    public function store(StoreDonationRequest $request, TelebirrService $telebirrService, KycService $kycService)
    {
        $validatedData = $request->validated();

        $elderId = $validatedData['elder_id'] ?? null;
        $elder = $elderId ? Elder::find($elderId) : null;

        $gatewayReference = (string) Str::uuid();

        $kycRequired = $kycService->shouldRequire($validatedData['amount'], 'ETB');
        $donation = Donation::create([
            'user_id' => Auth::id(),
            'elder_id' => $elder?->id,
            'branch_id' => $elder?->branch_id,
            'amount' => $validatedData['amount'],
            'guest_name' => $validatedData['name'] ?? null,
            'guest_email' => $validatedData['email'] ?? null,
            'guest_phone' => $validatedData['phone'] ?? null,
            'payment_gateway' => 'telebirr',
            'payment_id' => $gatewayReference,
            'status' => 'pending',
            'currency' => 'ETB',
            'donation_type' => 'guest_one_time',
            'campaign_id' => $validatedData['campaign_id'] ?? null,
            'kyc_required' => $kycRequired,
            'kyc_status' => $kycRequired ? 'pending' : 'not_required',
        ]);

        PaymentTransaction::create([
            'donation_id' => $donation->id,
            'branch_id' => $donation->branch_id,
            'gateway' => 'telebirr',
            'gateway_reference' => $gatewayReference,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
            'status' => 'pending',
        ]);

        $paymentResponse = $telebirrService->initiatePayment(
            $gatewayReference,
            (float) $donation->amount,
            (string) Str::uuid(),
            'Donation'
        );

        if (($paymentResponse['status'] ?? null) !== 'success' || empty($paymentResponse['redirect_url'])) {
            return redirect()->back()->with('error', 'Payment failed: ' . ($paymentResponse['message'] ?? 'Unable to initiate payment'));
        }

        return redirect()->away($paymentResponse['redirect_url']);
    }

    protected function notifyBranchTeam(Donation $donation, ?string $receiptUrl = null): void
    {
        $recipients = User::role('Branch Admin')
            ->when($donation->branch_id, fn($query) => $query->where('branch_id', $donation->branch_id))
            ->get();

        if ($recipients->isEmpty()) {
            $recipients = User::role('Admin')->get();
        }

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new GuestDonationLoggedNotification($donation, $receiptUrl)
        );
    }

    private function ensureGuestMember(array $data): array
    {
        $phone = trim($data['phone'] ?? '');
        $email = trim($data['email'] ?? '');

        if ($email === '') {
            $phoneSlug = preg_replace('/\D+/', '', $phone) ?: Str::random(6);
            $email = sprintf('guest+%s@mekodonia.local', $phoneSlug);
        }

        if ($phone === '') {
            return [null, null];
        }

        $password = null;
        $user = User::where('phone_number', $phone)->first();
        $isNew = false;

        if (! $user) {
            $password = Str::random(10);
            $user = User::create([
                'name' => $data['name'] ?? 'Guest donor',
                'email' => $email,
                'phone_number' => $phone,
                'account_status' => User::STATUS_ACTIVE,
                'account_type' => User::TYPE_EXTERNAL,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'password' => $password,
            ]);
            $isNew = true;
        } else {
            $user->forceFill([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ])->save();
        }

        $user->syncRoles(['External', 'Donor']);

        $channels = [];
        if (! empty($data['phone'])) {
            $channels[] = 'sms';
        }
        if (! empty($data['email'])) {
            $channels[] = 'email';
        }
        if (empty($channels)) {
            $channels[] = 'sms';
        }

        DonorProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'relationship_goal' => $data['relationship'] ?? ($user->donorProfile?->relationship_goal ?? 'father'),
                'monthly_budget' => $data['amount'] ?? $user->donorProfile?->monthly_budget,
                'frequency' => $data['cadence'] ?? $user->donorProfile?->frequency,
                'preferred_contact_method' => ! empty($data['phone']) ? 'phone' : 'email',
                'contact_channels' => array_values(array_unique($channels)),
                'payment_preference' => $data['payment_gateway'] ?? $user->donorProfile?->payment_preference ?? 'manual',
                'notes' => $data['notes'] ?? $user->donorProfile?->notes,
                'onboarding_step' => 'payment',
                'is_completed' => true,
                'completed_at' => now(),
            ],
        );

        if ($isNew && $password) {
            session()->flash('member_password', $password);
        }

        return [$user, $isNew ? $password : null];
    }

    private function adjustFunding(?Elder $elder, string $donationType, int $delta): void
    {
        if (! $elder || $donationType !== 'guest_sponsorship' || $delta === 0) {
            return;
        }

        if ($delta > 0) {
            $elder->increment('funding_received', $delta);
        } else {
            $elder->decrement('funding_received', abs($delta));
            if ($elder->funding_received < 0) {
                $elder->funding_received = 0;
                $elder->save();
            }
        }

        $elder->refresh();
    }
}
