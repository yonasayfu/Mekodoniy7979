<?php

namespace App\Support\Exports;

use Illuminate\Support\Str;

class ExportConfig
{
    public static function brandHeader(string $documentTitle): array
    {
        $logoPath = public_path('images/logo.svg');

        return [
            'logo' => file_exists($logoPath) ? $logoPath : null,
            'organization_name' => config('app.name', 'Laravel'),
            'document_title' => $documentTitle,
        ];
    }

    public static function staff(): array
    {
        return [
            'label' => 'Staff Directory',
            'type' => 'staff',
            'filename_prefix' => 'staff-directory',

            'csv' => [
                'headers' => [
                    '#',
                    'Full Name',
                    'Email',
                    'Phone',
                    'Job Title',
                    'Branch',
                    'Status',
                    'Linked User',
                ],
                'fields' => [
                    'index',
                    'full_name',
                    'email',
                    'phone',
                    'job_title',
                    [
                        'field' => 'user.branch.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'status',
                        'transform' => fn ($value) => Str::of($value ?? '')->replace('_', ' ')->title(),
                        'default' => 'Inactive',
                    ],
                    [
                        'field' => 'user.name',
                        'default' => '—',
                    ],
                ],
                'with_relations' => ['user:id,name,branch_id', 'user.branch:id,name'],
                'filename_prefix' => 'staff-directory',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Directory - Full Export',
                'filename_prefix' => 'staff-directory',
                'orientation' => 'landscape',
                'with_relations' => ['user:id,name,branch_id', 'user.branch:id,name'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    [
                        'key' => 'user.branch.name',
                        'label' => 'Branch',
                        'transform' => fn ($model) => $model->user?->branch?->name ?? '—',
                    ],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Directory - Current View',
                'filename_prefix' => 'staff-current-view',
                'orientation' => 'landscape',
                'with_relations' => ['user:id,name,branch_id', 'user.branch:id,name'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    [
                        'key' => 'user.branch.name',
                        'label' => 'Branch',
                        'transform' => fn ($model) => $model->user?->branch?->name ?? '—',
                    ],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Profile Summary',
                'filename_prefix' => 'staff-profile',
                'with_relations' => ['user:id,name,email,branch_id', 'user.branch:id,name'],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    [
                        'key' => 'user.branch.name',
                        'label' => 'Branch',
                        'transform' => fn ($model) => $model->user?->branch?->name ?? '—',
                    ],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'hire_date', 'label' => 'Hire Date'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],
        ];
    }

    public static function users(): array
    {
        return [
            'label' => 'User Roster',
            'type' => 'users',
            'filename_prefix' => 'users',

            'csv' => [
                'headers' => [
                    '#',
                    'Name',
                    'Email',
                    'Roles',
                    'Direct Permissions',
                    'Has 2FA',
                    'Linked Staff',
                    'Staff Status',
                ],
                'fields' => [
                    'index',
                    'name',
                    'email',
                    [
                        'field' => 'roles',
                        'transform' => fn ($value) => $value instanceof \Illuminate\Support\Collection
                            ? $value->implode(', ')
                            : (is_array($value) ? implode(', ', $value) : $value),
                        'default' => '—',
                    ],
                    [
                        'field' => 'permissions',
                        'transform' => fn ($value) => $value instanceof \Illuminate\Support\Collection
                            ? $value->implode(', ')
                            : (is_array($value) ? implode(', ', $value) : $value),
                        'default' => 'Inherited',
                    ],
                    [
                        'field' => 'has_two_factor',
                        'transform' => fn ($value) => $value ? 'Yes' : 'No',
                    ],
                    ['field' => 'staff.full_name', 'default' => '—'],
                    [
                        'field' => 'staff.status',
                        'transform' => fn ($value) => $value ? Str::of($value)->headline() : '—',
                        'default' => '—',
                    ],
                ],
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'User Accounts - Full Export',
                'filename_prefix' => 'users-roster',
                'orientation' => 'landscape',
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    [
                        'key' => 'roles',
                        'label' => 'Roles',
                        'transform' => fn ($model) => $model->roles?->pluck('name')->join(', ') ?: '—',
                    ],
                    [
                        'key' => 'permissions',
                        'label' => 'Direct Permissions',
                        'transform' => fn ($model) => $model->permissions?->pluck('name')->join(', ') ?: 'Inherited',
                    ],
                    [
                        'key' => 'has_two_factor',
                        'label' => '2FA',
                        'transform' => fn ($model) => $model->two_factor_secret ? 'Yes' : 'No',
                    ],
                    [
                        'key' => 'staff.full_name',
                        'label' => 'Staff',
                        'transform' => fn ($model) => $model->staff?->full_name ?? '—',
                    ],
                    [
                        'key' => 'staff.status',
                        'label' => 'Staff Status',
                        'transform' => fn ($model) => $model->staff?->status ? Str::of($model->staff->status)->headline() : '—',
                    ],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'User Accounts - Current View',
                'filename_prefix' => 'users-current-view',
                'orientation' => 'landscape',
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    [
                        'key' => 'roles',
                        'label' => 'Roles',
                        'transform' => fn ($model) => $model->roles?->pluck('name')->join(', ') ?: '—',
                    ],
                    [
                        'key' => 'has_two_factor',
                        'label' => '2FA',
                        'transform' => fn ($model) => $model->two_factor_secret ? 'Yes' : 'No',
                    ],
                    [
                        'key' => 'staff.full_name',
                        'label' => 'Staff',
                        'transform' => fn ($model) => $model->staff?->full_name ?? '—',
                    ],
                ],
            ],
        ];
    }

    public static function branches(): array
    {
        return [
            'label' => 'Branch Directory',
            'type' => 'branches',
            'filename_prefix' => 'branches',

            'csv' => [
                'headers' => [
                    'ID',
                    'Name',
                    'Location',
                    'Contact Person',
                    'Contact Email',
                    'Contact Phone',
                ],
                'fields' => [
                    'id',
                    'name',
                    'location',
                    'contact_person',
                    'contact_email',
                    'contact_phone',
                ],
            ],
        ];
    }

    public static function elders(): array
    {
        return [
            'label' => 'Elders Directory',
            'type' => 'elders',
            'filename_prefix' => 'elders',

            'csv' => [
                'headers' => [
                    'ID',
                    'Name',
                    'Date of Birth',
                    'Gender',
                    'Health Conditions',
                    'Sponsorship Status',
                    'Branch Name',
                ],
                'fields' => [
                    'id',
                    'name',
                    'date_of_birth',
                    'gender',
                    'health_conditions',
                    'sponsorship_status',
                    [
                        'field' => 'branch.name',
                        'default' => '—',
                    ],
                ],
                'with_relations' => ['branch:id,name'],
            ],
        ];
    }

    public static function sponsorships(): array
    {
        return [
            'label' => 'Sponsorships Directory',
            'type' => 'sponsorships',
            'filename_prefix' => 'sponsorships',

            'csv' => [
                'headers' => [
                    'ID',
                    'Donor',
                    'Elder',
                    'Elder Priority',
                    'Branch',
                    'Amount',
                    'Frequency',
                    'Start Date',
                    'End Date',
                    'Status',
                ],
                'fields' => [
                    'id',
                    [
                        'field' => 'user.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'elder.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'elder.priority_level',
                        'transform' => fn ($value) => $value ? Str::headline($value) : '—',
                        'default' => '—',
                    ],
                    [
                        'field' => 'branch.name',
                        'default' => '—',
                    ],
                    'amount',
                    'frequency',
                    'start_date',
                    'end_date',
                    'status',
                ],
                'with_relations' => ['user:id,name', 'elder:id,first_name,last_name,priority_level', 'branch:id,name'],
            ],
        ];
    }

    public static function visits(): array
    {
        return [
            'label' => 'Visits Directory',
            'type' => 'visits',
            'filename_prefix' => 'visits',

            'csv' => [
                'headers' => [
                    'ID',
                    'Visitor',
                    'Elder',
                    'Branch',
                    'Elder Priority',
                    'Visit Date',
                    'Purpose',
                    'Notes',
                    'Status',
                ],
                'fields' => [
                    'id',
                    [
                        'field' => 'user.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'elder.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'branch.name',
                        'default' => '—',
                    ],
                    [
                        'field' => 'elder.priority_level',
                        'transform' => fn ($value) => $value ? Str::headline($value) : '—',
                        'default' => '—',
                    ],
                    'visit_date',
                    'purpose',
                    'notes',
                    'status',
                ],
                'with_relations' => ['user:id,name', 'elder:id,first_name,last_name,priority_level', 'branch:id,name'],
            ],
        ];
    }

    public static function activityLogs(): array
    {
        return [
            'label' => 'Activity Log Audit',
            'type' => 'activity_logs',
            'filename_prefix' => 'activity-logs',
            'csv' => [
                'headers' => [
                    '#',
                    'Timestamp',
                    'Actor',
                    'Action',
                    'Description',
                    'Subject',
                    'Subject ID',
                    'Changes',
                ],
                'fields' => [
                    'index',
                    [
                        'field' => 'created_at',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d H:i:s') ?? $value,
                    ],
                    [
                        'field' => 'causer.name',
                        'default' => 'System',
                    ],
                    'action',
                    [
                        'field' => 'description',
                        'default' => '—',
                    ],
                    [
                        'field' => 'subject_type',
                        'transform' => fn ($value) => $value ? class_basename($value) : '—',
                        'default' => '—',
                    ],
                    [
                        'field' => 'subject_id',
                        'default' => '—',
                    ],
                    [
                        'field' => 'changes',
                        'transform' => fn ($value) => $value ? json_encode($value) : '—',
                        'default' => '—',
                    ],
                ],
                'with_relations' => ['causer:id,name'],
            ],
        ];
    }
}
