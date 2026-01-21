<?php

return [
    'groups' => [
        [
            'label' => 'Platform',
            'items' => [
                [
                    'title' => 'Dashboard',
                    'href' => '/dashboard',
                    'icon' => 'LayoutGrid',
                    'permission' => null,
                ],
                [
                    'title' => 'Download Center',
                    'href' => '/exports',
                    'icon' => 'Download',
                    'permission' => null,
                ],
                [
                    'title' => 'Activity Logs',
                    'href' => '/activity-logs',
                    'icon' => 'ScrollText',
                    'permission' => 'activity-logs.view',
                ],
                [
                    'title' => 'Notifications',
                    'href' => '/notifications',
                    'icon' => 'MessageCircle',
                    'permission' => null,
                ],
                [
                    'title' => 'Mailbox',
                    'href' => '/mailbox',
                    'icon' => 'Settings',
                    'permission' => 'mailbox.view',
                ],
            ],
        ],
        [
            'label' => 'Management',
            'items' => [
                [
                    'title' => 'Branches',
                    'href' => '/branches',
                    'icon' => 'Globe2',
                    'permission' => 'branches.manage',
                ],
                [
                    'title' => 'Elders',
                    'href' => '/elders',
                    'icon' => 'Users',
                    'permission' => 'elders.manage',
                ],
                [
                    'title' => 'Sponsorships',
                    'href' => '/sponsorships',
                    'icon' => 'ClipboardList',
                    'permission' => 'sponsorships.manage',
                ],
                [
                    'title' => 'Reconciliation',
                    'href' => '/reconciliation',
                    'icon' => 'DollarSign',
                    'permission' => 'donations.manage',
                ],
                [
                    'title' => 'Visits',
                    'href' => '/visits',
                    'icon' => 'BookOpen',
                    'permission' => 'visits.manage',
                ],
                [
                    'title' => 'Campaigns',
                    'href' => '/campaigns',
                    'icon' => 'Folder',
                    'permission' => 'campaigns.manage',
                ],
                [
                    'title' => 'Reports',
                    'href' => '/reports',
                    'icon' => 'Folder',
                    'permission' => 'reports.view',
                ],
                [
                    'title' => 'Outbound Log',
                    'href' => '/outbound',
                    'icon' => 'ClipboardList',
                    'permission' => 'notifications.view',
                ],
            ],
        ],
        [
            'label' => 'Team',
            'items' => [
                [
                    'title' => 'Staff',
                    'href' => '/staff',
                    'icon' => 'Users',
                    'permission' => 'staff.view',
                ],
                [
                    'title' => 'Users',
                    'href' => '/users',
                    'icon' => 'UserCog',
                    'permission' => 'users.manage',
                ],
                [
                    'title' => 'Roles',
                    'href' => '/roles',
                    'icon' => 'Shield',
                    'permission' => 'roles.manage',
                ],
            ],
        ],
    ],
];
