<?php

namespace App\Notifications;

use App\Models\Elder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ElderStatusChangedStaffNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Elder $elder,
        public ?string $fromStatus,
        public string $toStatus,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'elder.status_changed',
            'elder_id' => $this->elder->id,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'branch_id' => $this->elder->branch_id,
            'message' => 'Elder status changed.',
            'url' => route('elders.show', $this->elder),
        ];
    }
}
