<?php
// single source of info for sidebar navigation.
// Each item's 'roles' list controls who sees it

return [
    'project' => [
        [
            'label' => 'Team & Roles',
            'icon'  => 'team',
            'href'  => '/team',
            'roles' => ['manager', 'team_lead'],
        ],
        [
            'label' => 'Tasks',
            'icon'  => 'tasks',
            'href'  => '/tasks',
            'roles' => ['team_lead', 'developer', 'designer'],
        ],
        [
            'label' => 'Review & Approval',
            'icon'  => 'review',
            'href'  => '/review',
            'roles' => ['team_lead'],
        ],
        [
            // Client's equivalent of the internal review queue
            'label' => 'Approvals',
            'icon'  => 'user-check',
            'href'  => '/client-portal/reviews',
            'roles' => ['client'],
        ],
        [
            'label' => 'Chat',
            'icon'  => 'chat',
            'href'  => '/chat',
            'roles' => ['manager', 'team_lead', 'developer', 'designer', 'client'],
        ],
        [
            'label' => 'Reports & Monitoring',
            'icon'  => 'reports',
            'href'  => '/reports',
            'roles' => ['manager', 'team_lead'],
        ],
        [
            'label' => 'Payment',
            'icon'  => 'payment',
            'href'  => '/payment',
            'roles' => ['manager', 'team_lead'],
        ],
        [
            // Client's equivalent of Payment
            'label' => 'Payments',
            'icon'  => 'payment',
            'href'  => '/payment',
            'roles' => ['client'],
        ],
    ],

    'admin' => [
        ['label' => 'User Management',      'icon' => 'users',        'href' => '/admin/users'],
        ['label' => 'Roles & Permissions',  'icon' => 'shield-check', 'href' => '/admin/permissions'],
        ['label' => 'Workflow Templates',   'icon' => 'workflow',     'href' => '/admin/templates'],
        ['label' => 'System Settings',      'icon' => 'settings',     'href' => '/admin/settings'],
        ['label' => 'Audit Log',            'icon' => 'scroll-text',  'href' => '/admin/audit'],
    ],
];