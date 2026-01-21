<?php

return [
    /**
     * Number of hours branch staff have to approve a visit before it is treated
     * as an SLA breach.
     */
    'approval_sla_hours' => env('VISIT_APPROVAL_SLA_HOURS', 48),

    /**
     * Number of hours before the SLA deadline that we should nudge branch staff.
     */
    'reminder_threshold_hours' => env('VISIT_APPROVAL_REMINDER_HOURS', 12),
];
