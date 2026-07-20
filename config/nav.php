<?php
// single source of info for sidebar navigation.
// Each item's 'roles' list controls who sees it

return [
    [
        'label' => 'Dashboard',
        'icon'  => 'dashboard',
        'href'  => '/dashboard',
        'roles' => ['team_lead', 'manager', 'developer', 'designer', 'client'],
    ],
    [
        'label' => 'Projects',
        'icon'  => 'folder',
        'href'  => '/projects',
        'roles' => ['team_lead', 'manager', 'developer', 'designer', 'client'],
    ],
    [
        'label' => 'Team & Roles',
        'icon'  => 'team',
        'href'  => '/team',
        'roles' => ['team_lead', 'manager'],
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
        'roles' => ['team_lead', 'manager'],
    ],
    [
        'label' => 'Approvals',
        'icon'  => 'review',
        'href'  => '/client-portal/reviews',
        'roles' => ['client'],
    ],
    [
        'label' => 'Chat',
        'icon'  => 'chat',
        'href'  => '/chat',
        'roles' => ['team_lead', 'manager', 'developer', 'designer', 'client'],
    ],
    [
        'label' => 'Reports & Monitoring',
        'icon'  => 'reports',
        'href'  => '/reports',
        'roles' => ['team_lead', 'manager'],
    ],
    [
        'label' => 'Payment',
        'icon'  => 'payment',
        'href'  => '/payment',
        'roles' => ['team_lead', 'manager'],
    ],
    [
        'label' => 'Payments',
        'icon'  => 'payment',
        'href'  => '/payment',
        'roles' => ['client'],
    ],
];