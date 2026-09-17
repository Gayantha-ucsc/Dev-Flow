<?php
// 'pending'  -> tasks currently sitting at status 'pending_review', awaiting an ApprovalRecord decision from the Team Lead.
// 'history'  -> a flattened, most-recent-first feed of past ApprovalRecord rows for this project, used on the "Reviewed history" tab.

return [
    1 => [ // Project Beta
        'pending' => [
            [
                'id'            => 1,
                'title'         => 'Database Schema V2',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eight', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'note'          => 'Resolved foreign key cascading constraints and indexed query parameters for tenant lookup.',
            ],
            [
                'id'            => 2,
                'title'         => 'Auth Edge Cases Documentation',
                'stage'         => 'Requirement Gathering',
                'revisionRound' => 2,
                'submitter'     => ['name' => 'User Nine', 'role' => 'Designer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-4 hours')),
                'note'          => 'Updated OAuth2 callback failure states and mobile session renewal sequence per team lead notes.',
            ],
            [
                'id'            => 3,
                'title'         => 'Billing Gateway Webhooks',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eight', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-1 day -3 hours')),
                'note'          => 'Implemented exponential backoff retry handler for Stripe webhook notifications.',
            ],
            [
                'id'            => 4,
                'title'         => 'Mobile Responsive Layouts',
                'stage'         => 'Client Design Review',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Nine', 'role' => 'Designer'],
                'submittedAt'   => '2026-08-18 10:00:00',
                'note'          => 'Verified drawer navigation and checkout flow breakpoints against Figma specifications.',
            ],
        ],
        'history' => [
            ['task' => 'Auth Flow Wireframes',      'round' => 1, 'decision' => 'approved',          'date' => '2026-08-14'],
            ['task' => 'Database Schema V2',        'round' => 1, 'decision' => 'rejected',          'date' => '2026-08-12'],
            ['task' => 'Billing Gateway Integration','round' => 1, 'decision' => 'changes_requested', 'date' => '2026-08-10'],
            ['task' => 'API Authentication Module',  'round' => 1, 'decision' => 'approved',          'date' => '2026-08-06'],
        ],
    ],

    2 => [ // Project Gamma
        'pending' => [
            [
                'id'            => 5,
                'title'         => 'Payment Gateway Integration',
                'stage'         => 'Development',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Eleven', 'role' => 'Developer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'note'          => 'Stripe checkout wired up for the subscription tier, pending review of failure handling.',
            ],
            [
                'id'            => 6,
                'title'         => 'Onboarding Illustration Set',
                'stage'         => 'Design',
                'revisionRound' => 1,
                'submitter'     => ['name' => 'User Twelve', 'role' => 'Designer'],
                'submittedAt'   => date('Y-m-d H:i:s', strtotime('-1 day')),
                'note'          => 'First pass on the three-step onboarding illustrations, ready for feedback.',
            ],
        ],
        'history' => [
            ['task' => 'Subscription Plan Schema', 'round' => 1, 'decision' => 'approved', 'date' => '2026-08-11'],
            ['task' => 'Checkout UI Draft',        'round' => 2, 'decision' => 'approved', 'date' => '2026-08-09'],
        ],
    ],
];