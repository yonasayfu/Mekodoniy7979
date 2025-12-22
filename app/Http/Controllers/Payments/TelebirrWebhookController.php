<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Jobs\RefreshCountersJob;
use App\Models\Donation;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Notifications\DonationCompletedStaffNotification;
use App\Support\Services\TimelineEventService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TelebirrWebhookController extends Controller
{
    public function __invoke(Request $request, TimelineEventService $timelineEventService)
    {
        $payload = $request->all();

        $gatewayReference = $request->input('outTradeNo')
            ?? $request->input('gateway_reference')
            ?? $request->input('reference')
            ?? null;

        if (! $gatewayReference) {
            return response()->json(['message' => 'Missing gateway reference.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $statusRaw = strtolower((string) ($request->input('status') ?? ''));
        $status = in_array($statusRaw, ['success', 'completed', 'paid'], true) ? 'completed' : 'failed';

        $gatewayTransactionId = $request->input('tradeNo')
            ?? $request->input('transaction_id')
            ?? null;

        $amountRaw = $request->input('totalAmount') ?? $request->input('amount');
        $amount = is_numeric($amountRaw) ? ((float) $amountRaw) : null;

        if ($request->has('totalAmount') && is_numeric($amountRaw)) {
            $amount = $amount / 100;
        }

        $currency = $request->input('currency') ?? 'ETB';

        DB::transaction(function () use ($gatewayReference, $gatewayTransactionId, $amount, $currency, $status, $payload, $timelineEventService) {
            $tx = PaymentTransaction::firstOrCreate(
                ['gateway_reference' => $gatewayReference],
                [
                    'gateway' => 'telebirr',
                    'status' => 'pending',
                ]
            );

            $donation = Donation::where('payment_gateway', 'telebirr')
                ->where('payment_id', $gatewayReference)
                ->latest('id')
                ->first();

            if ($donation && $tx->donation_id === null) {
                $tx->donation_id = $donation->id;
                $tx->branch_id = $donation->branch_id;
            }

            $alreadyFinal = in_array($tx->status, ['completed', 'failed'], true);
            $effectiveStatus = $alreadyFinal ? $tx->status : $status;

            $tx->fill([
                'gateway' => 'telebirr',
                'gateway_transaction_id' => $gatewayTransactionId ?: $tx->gateway_transaction_id,
                'amount' => $amount ?? $tx->amount,
                'currency' => $currency ?? $tx->currency,
                'raw_payload' => $payload,
            ]);

            if (! $alreadyFinal) {
                $tx->status = $status;
                $tx->processed_at = now();
            }

            $tx->save();

            if (! $donation) {
                return;
            }

            if ($effectiveStatus === 'completed' && $donation->status !== 'completed') {
                $donation->update([
                    'status' => 'completed',
                ]);

                RefreshCountersJob::dispatch($donation->user_id);

                $recipients = User::role('Super Admin')->get();
                if ($donation->branch_id) {
                    $branchRecipients = User::where('branch_id', $donation->branch_id)
                        ->role(['Branch Admin', 'Admin'])
                        ->get();
                    $recipients = $recipients->merge($branchRecipients)->unique('id');
                }

                Notification::send($recipients, new DonationCompletedStaffNotification($donation));

                $timelineEventService->createEvent(
                    'donation',
                    'Donation of ' . $donation->amount . ' ' . ($donation->currency ?? 'ETB') . ' received via Telebirr.',
                    Carbon::now(),
                    $donation->user,
                    $donation->elder,
                    $donation
                );
            }

            if ($effectiveStatus === 'failed' && $donation->status !== 'failed') {
                $donation->update([
                    'status' => 'failed',
                ]);
            }
        });

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
