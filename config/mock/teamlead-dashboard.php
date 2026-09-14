<?php
return [

    'stats' => [
        [
            'key'   => 'active_projects',
            'label' => 'Active Projects',
            'value' => 2,
            'icon'  => 'folder',
            'tone'  => 'neutral',
            'meta'  => "You're leading these",
        ],
        [
            'key'   => 'pending_reviews',
            'label' => 'Pending Reviews',
            'value' => 3,
            'icon'  => 'review',
            'tone'  => 'pink',
            'meta'  => 'Awaiting your decision',
        ],
        [
            'key'   => 'overdue',
            'label' => 'Overdue Tasks',
            'value' => 1,
            'icon'  => 'circle-alert',
            'tone'  => 'danger',
            'meta'  => 'Past their deadline',
        ],
        [
            'key'   => 'blocked',
            'label' => 'Blocked Tasks',
            'value' => 2,
            'icon'  => 'ban',
            'tone'  => 'warning',
            'meta'  => 'Need your attention',
        ],
    ],

    // Tasks submitted by contributors and waiting on this Team Lead's approve / reject / request-changes decision
    'reviewQueue' => [
        [
            'title' => 'API Endpoint Security Audit',
            'meta'  => 'Project Beta • Submitted by Alex Chen • 2 hours ago',
            'href'  => 'review',
        ],
        [
            'title' => 'Mobile Responsive Layouts',
            'meta'  => 'Project Alpha • Submitted by Priya Nair • 5 hours ago',
            'href'  => 'review',
        ],
        [
            'title' => 'Webhook Handler Setup',
            'meta'  => 'Project Beta • Submitted by Jordan Lee • Yesterday',
            'href'  => 'review',
        ],
    ],
];