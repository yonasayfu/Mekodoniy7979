<?php

namespace App\Http\Controllers;

use App\Jobs\RefreshCountersJob;
use App\Models\Elder;
use App\Models\ElderStatusEvent;
use App\Models\User;
use App\Notifications\ElderStatusChangedStaffNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class ElderLifecycleController extends Controller
{
    public function updateStatus(Request $request, Elder $elder)
    {
        $data = $request->validate([
            'to_status' => ['required', 'string', 'max:50'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        $fromStatus = $elder->current_status;
        $toStatus = $data['to_status'];
        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : now();

        DB::transaction(function () use ($elder, $fromStatus, $toStatus, $occurredAt, $data) {
            ElderStatusEvent::create([
                'elder_id' => $elder->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'reason' => $data['reason'] ?? null,
                'occurred_at' => $occurredAt,
                'created_by' => auth()->id(),
            ]);

            $updates = [
                'current_status' => $toStatus,
            ];

            if ($toStatus === 'admitted' && $elder->admitted_at === null) {
                $updates['admitted_at'] = $occurredAt;
            }

            if ($toStatus === 'deceased') {
                $updates['deceased_at'] = $occurredAt;
            }

            $elder->update($updates);
        });

        RefreshCountersJob::dispatch();

        $recipients = User::role('Super Admin')->get();
        if ($elder->branch_id) {
            $branchRecipients = User::where('branch_id', $elder->branch_id)
                ->role(['Branch Admin', 'Admin'])
                ->get();
            $recipients = $recipients->merge($branchRecipients)->unique('id');
        }

        Notification::send($recipients, new ElderStatusChangedStaffNotification($elder, $fromStatus, $toStatus));

        return back()->with('success', 'Elder status updated successfully.');
    }
}
