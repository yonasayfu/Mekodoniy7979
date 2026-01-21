<?php

return [
    'disk' => env('BACKUP_DISK', 's3_backups'),
    'retention_days' => env('BACKUP_RETENTION_DAYS', 90),
];
