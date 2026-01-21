<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Catalog
    |--------------------------------------------------------------------------
    | Standalone permissions grouped by domain so we can iterate in seeders
    | or UI permission matrices. Each key represents a module or feature
    | and contains an array of discrete permissions.
    */
    'permissions' => [
        'users' => ['users.view', 'users.create', 'users.update', 'users.delete', 'users.manage', 'users.impersonate'],
        'staff' => ['staff.view', 'staff.create', 'staff.update', 'staff.delete', 'staff.manage'],
        'roles' => ['roles.view', 'roles.create', 'roles.update', 'roles.delete', 'roles.manage', 'permissions.view', 'permissions.manage'],
        'branches' => ['branches.view', 'branches.create', 'branches.update', 'branches.delete', 'branches.manage'],
        'elders' => ['elders.view', 'elders.create', 'elders.update', 'elders.delete', 'elders.manage', 'case_notes.view', 'case_notes.manage', 'case_notes.delete'],
        'sponsorships' => ['sponsorships.view', 'sponsorships.create', 'sponsorships.update', 'sponsorships.delete', 'sponsorships.manage'],
        'donations' => ['donations.view', 'donations.create', 'donations.update', 'donations.delete', 'donations.manage', 'donations.approve'],
        'visits' => ['visits.view', 'visits.create', 'visits.update', 'visits.delete', 'visits.manage', 'visits.approve'],
        'reports' => ['reports.view', 'reports.generate', 'reports.export', 'reports.view_all', 'reports.financial', 'reports.operational', 'reports.impact_book'],
        'campaigns' => ['campaigns.view', 'campaigns.create', 'campaigns.update', 'campaigns.delete', 'campaigns.manage'],
        'mailbox' => ['mailbox.view', 'mailbox.send', 'mailbox.process', 'mailbox.manage'],
        'notifications' => ['notifications.view', 'notifications.manage'],
        'reconciliation' => ['reconciliation.view', 'reconciliation.manage', 'reconciliation.match', 'reconciliation.ignore'],
        'activity_logs' => ['activity_logs.view', 'activity_logs.manage'],
        'data_exports' => ['data_exports.view', 'data_exports.create', 'data_exports.manage'],
        'system' => ['system.settings', 'system.backup', 'system.maintenance', 'system.logs'],
        'timeline' => ['timeline.view', 'timeline.create', 'timeline.update', 'timeline.delete', 'timeline.manage'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Wildcard Permissions
    |--------------------------------------------------------------------------
    | For roles that need broad authority we expose wildcard scopes. These
    | should map to the patterns Spatie uses to grant bulk access.
    */
        'wildcards' => [
            'users.*',
            'staff.*',
            'roles.*',
            'permissions.*',
            'branches.*',
            'elders.*',
            'sponsorships.*',
            'donations.*',
            'visits.*',
            'reports.*',
            'campaigns.*',
            'mailbox.*',
            'activity_logs.*',
            'data_exports.*',
            'system.*',
            'timeline.*',
            'case_notes.*',
            'notifications.*',
            'reconciliation.*',
        ],

    /*
    |--------------------------------------------------------------------------
    | Role Matrix
    |--------------------------------------------------------------------------
    | Each role declares a friendly label, description, and the list of
    | permissions it should receive. This makes the RBAC story easier to
    | expose to admins or documentation.
    */
    'roles' => [
        'Super Admin' => [
            'label' => 'System Owner',
            'description' => 'Full control across all branches, donors, and system settings.',
            'permissions' => [
                '*', // will be expanded in the seeder
                'notifications.view',
            ],
        ],
        'Admin' => [
            'label' => 'Operational Admin',
            'description' => 'Manages most operational areas but cannot change other admins.',
            'permissions' => [
                'users.*',
                'staff.*',
                'branches.*',
                'elders.*',
                'sponsorships.*',
                'donations.*',
                'visits.*',
                'reports.*',
                'campaigns.*',
                'mailbox.*',
                'activity_logs.*',
                'data_exports.*',
                'system.*',
                'timeline.*',
                'case_notes.*',
                'roles.view',
                'roles.update',
                'permissions.view',
                'notifications.view',
                'notifications.manage',
                'reconciliation.*',
            ],
        ],
        'Branch Admin' => [
            'label' => 'Branch Leader',
            'description' => 'Oversees a single branch: elders, staff, donations, and reports.',
            'permissions' => [
                'staff.view',
                'staff.create',
                'staff.update',
                'branches.view',
                'elders.*',
                'sponsorships.*',
                'donations.*',
                'visits.*',
                'campaigns.view',
                'reports.view',
                'reports.generate',
                'timeline.*',
                'case_notes.manage',
                'case_notes.delete',
            ],
        ],
        'Manager' => [
            'label' => 'Branch Manager',
            'description' => 'Supervises staff, elders, and reconciliation tasks.',
            'permissions' => [
                'users.view',
                'staff.*',
                'branches.view',
                'elders.*',
                'sponsorships.*',
                'donations.*',
                'visits.*',
                'reports.view',
                'reports.generate',
                'reports.export',
                'reports.operational',
                'mailbox.view',
                'mailbox.send',
                'activity_logs.view',
                'timeline.view',
                'timeline.create',
                'timeline.update',
                'case_notes.manage',
            ],
        ],
        'Branch Coordinator' => [
            'label' => 'Operations Lead',
            'description' => 'Handles elder matches, visits, and branch-level scheduling.',
            'permissions' => [
                'users.view',
                'staff.view',
                'staff.update',
                'branches.view',
                'elders.view',
                'elders.update',
                'donations.view',
                'donations.create',
                'visits.*',
                'reports.view',
                'reports.generate',
                'timeline.view',
                'timeline.create',
                'case_notes.view',
            ],
        ],
        'Field Officer' => [
            'label' => 'Field Team',
            'description' => 'Captures visits, case notes, and on-the-ground data.',
            'permissions' => [
                'users.view',
                'staff.view',
                'elders.view',
                'donations.view',
                'visits.view',
                'visits.create',
                'visits.update',
                'timeline.view',
                'timeline.create',
                'case_notes.view',
            ],
        ],
        'Finance Officer' => [
            'label' => 'Finance Analyst',
            'description' => 'Manages donations, approvals, and financial exports.',
            'permissions' => [
                'users.view',
                'donations.*',
                'reports.view',
                'reports.financial',
                'reports.generate',
                'reports.export',
                'data_exports.view',
                'data_exports.create',
                'reconciliation.*',
            ],
        ],
        'Auditor' => [
            'label' => 'Compliance Auditor',
            'description' => 'Read-only vantage to activity logs and reports.',
            'permissions' => [
                'users.view',
                'staff.view',
                'elders.view',
                'donations.view',
                'visits.view',
                'reports.view',
                'reports.generate',
                'activity_logs.view',
                'data_exports.view',
            ],
        ],
        'Reporting Analyst' => [
            'label' => 'Data Analyst',
            'description' => 'Produces and downloads reports.',
            'permissions' => [
                'users.view',
                'staff.view',
                'elders.view',
                'donations.view',
                'visits.view',
                'reports.view',
                'reports.generate',
                'reports.export',
                'data_exports.view',
            ],
        ],
        'Donor' => [
            'label' => 'Registered Donor',
            'description' => 'Authorizations for donors inside their own scope.',
            'permissions' => [
                'donations.view',
                'reports.view',
                'reports.generate',
            ],
        ],
        'External' => [
            'label' => 'Guest Donor',
            'description' => 'Public donors with minimal + read-only access.',
            'permissions' => [
                'users.view',
                'sponsorships.view',
                'donations.view',
                'reports.view',
            ],
        ],
    ],
];
