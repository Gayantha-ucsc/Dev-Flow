<?php

return [
    1 => [ // Project Beta
        'stages' => [
            ['id' => 1, 'name' => 'Requirement Gathering'],
            ['id' => 2, 'name' => 'Design'],
            ['id' => 3, 'name' => 'Client Design Review'],
            ['id' => 4, 'name' => 'Development'],
            ['id' => 5, 'name' => 'Testing'],
            ['id' => 6, 'name' => 'Delivery and Handoff'],
        ],
        'tasks' => [
            [
                'id'          => 101,
                'title'       => 'API Authentication Module',
                'description' => 'Implement JWT-based authentication with refresh tokens and role-based access checks.',
                'stageId'     => 4,
                'stage'       => 'Development',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'dueDate'     => '2026-09-01',
                'assignees'   => [
                    ['user_id' => 8, 'name' => 'User Eight'],
                ],
                'dependencies' => [102],
                'progressNotes' => [
                    ['id' => 1, 'author' => 'User Eight', 'body' => 'OAuth endpoints scaffolded. Starting token validation middleware.', 'createdAt' => '2026-08-10 09:30'],
                    ['id' => 2, 'author' => 'User Seven', 'body' => 'Please add rate limiting before handoff to QA.', 'createdAt' => '2026-08-11 14:00'],
                ],
                'comments' => [
                    ['id' => 1, 'parentId' => null, 'author' => 'User Seven', 'body' => 'Can we align this with the existing session flow?', 'createdAt' => '2026-08-09 11:00'],
                    ['id' => 2, 'parentId' => 1, 'author' => 'User Eight', 'body' => 'Yes — I will keep backward compatibility for one sprint.', 'createdAt' => '2026-08-09 11:45'],
                ],
                'revisionRounds' => [
                    ['round' => 1, 'status' => 'approved', 'submittedAt' => '2026-08-05', 'reviewedAt' => '2026-08-06', 'reviewer' => 'User Seven', 'notes' => 'Initial scope approved.'],
                    ['round' => 2, 'status' => 'in_review', 'submittedAt' => '2026-08-12', 'reviewedAt' => null, 'reviewer' => 'User Seven', 'notes' => 'Middleware implementation pending review.'],
                ],
            ],
            [
                'id'          => 102,
                'title'       => 'Database Schema Migration',
                'description' => 'Create migration scripts for users, roles, and project membership tables.',
                'stageId'     => 4,
                'stage'       => 'Development',
                'status'      => 'completed',
                'priority'    => 'high',
                'dueDate'     => '2026-08-20',
                'assignees'   => [
                    ['user_id' => 8, 'name' => 'User Eight'],
                ],
                'dependencies' => [],
                'progressNotes' => [
                    ['id' => 1, 'author' => 'User Eight', 'body' => 'All migrations run cleanly on staging.', 'createdAt' => '2026-08-08 16:00'],
                ],
                'comments' => [],
                'revisionRounds' => [
                    ['round' => 1, 'status' => 'approved', 'submittedAt' => '2026-08-07', 'reviewedAt' => '2026-08-08', 'reviewer' => 'User Seven', 'notes' => 'Approved for production.'],
                ],
            ],
            [
                'id'          => 103,
                'title'       => 'Dashboard Widget Layout',
                'description' => 'Design responsive widget grid for the manager dashboard overview.',
                'stageId'     => 2,
                'stage'       => 'Design',
                'status'      => 'pending_review',
                'priority'    => 'medium',
                'dueDate'     => '2026-08-25',
                'assignees'   => [
                    ['user_id' => 9, 'name' => 'User Nine'],
                ],
                'dependencies' => [],
                'progressNotes' => [],
                'comments' => [],
                'revisionRounds' => [
                    ['round' => 1, 'status' => 'in_review', 'submittedAt' => '2026-08-12', 'reviewedAt' => null, 'reviewer' => 'User One', 'notes' => 'Awaiting client feedback on color palette.'],
                ],
            ],
        ],
    ],

    2 => [ // Project Gamma
        'stages' => [
            ['id' => 1, 'name' => 'Design'],
            ['id' => 2, 'name' => 'Development'],
            ['id' => 3, 'name' => 'Testing'],
            ['id' => 4, 'name' => 'Delivery and Handoff'],
        ],
        'tasks' => [
            [
                'id'          => 201,
                'title'       => 'Payment Gateway Integration',
                'description' => 'Integrate Stripe checkout for subscription billing.',
                'stageId'     => 2,
                'stage'       => 'Development',
                'status'      => 'blocked',
                'priority'    => 'high',
                'dueDate'     => '2026-08-28',
                'assignees'   => [
                    ['user_id' => 11, 'name' => 'User Eleven'],
                ],
                'dependencies' => [202],
                'progressNotes' => [
                    ['id' => 1, 'author' => 'User Eleven', 'body' => 'Blocked — waiting on API keys from finance team.', 'createdAt' => '2026-08-11 10:00'],
                ],
                'comments' => [
                    ['id' => 1, 'parentId' => null, 'author' => 'User Ten', 'body' => 'Escalated to finance — expect keys by Friday.', 'createdAt' => '2026-08-11 10:30'],
                ],
                'revisionRounds' => [],
            ],
            [
                'id'          => 202,
                'title'       => 'Webhook Handler Setup',
                'description' => 'Build webhook endpoint for payment confirmation events.',
                'stageId'     => 2,
                'stage'       => 'Development',
                'status'      => 'in_progress',
                'priority'    => 'medium',
                'dueDate'     => '2026-08-22',
                'assignees'   => [
                    ['user_id' => 11, 'name' => 'User Eleven'],
                    ['user_id' => 12, 'name' => 'User Twelve'],
                ],
                'dependencies' => [],
                'progressNotes' => [],
                'comments' => [],
                'revisionRounds' => [],
            ],
        ],
    ],

    3 => [ // Project Alpha
        'stages' => [
            ['id' => 1, 'name' => 'Discovery'],
            ['id' => 2, 'name' => 'Design'],
            ['id' => 3, 'name' => 'Development'],
            ['id' => 4, 'name' => 'Testing'],
            ['id' => 5, 'name' => 'Delivery and Handoff'],
        ],
        'tasks' => [
            [
                'id'          => 301,
                'title'       => 'Final Deployment Checklist',
                'description' => 'Run pre-launch checklist including DNS, SSL, and backup verification.',
                'stageId'     => 5,
                'stage'       => 'Delivery and Handoff',
                'status'      => 'in_progress',
                'priority'    => 'high',
                'dueDate'     => '2026-10-20',
                'assignees'   => [
                    ['user_id' => 13, 'name' => 'User Thirteen'],
                    ['user_id' => 14, 'name' => 'User Fourteen'],
                ],
                'dependencies' => [302],
                'progressNotes' => [
                    ['id' => 1, 'author' => 'User Thirteen', 'body' => 'SSL certificate renewed. DNS propagation in progress.', 'createdAt' => '2026-08-13 08:00'],
                ],
                'comments' => [],
                'revisionRounds' => [
                    ['round' => 1, 'status' => 'approved', 'submittedAt' => '2026-08-10', 'reviewedAt' => '2026-08-11', 'reviewer' => 'User One', 'notes' => 'Checklist template approved.'],
                ],
            ],
            [
                'id'          => 302,
                'title'       => 'Client Training Session',
                'description' => 'Prepare and deliver admin panel walkthrough for client team.',
                'stageId'     => 5,
                'stage'       => 'Delivery and Handoff',
                'status'      => 'not_started',
                'priority'    => 'medium',
                'dueDate'     => '2026-10-22',
                'assignees'   => [
                    ['user_id' => 15, 'name' => 'User Fifteen'],
                ],
                'dependencies' => [],
                'progressNotes' => [],
                'comments' => [],
                'revisionRounds' => [],
            ],
        ],
    ],
];
