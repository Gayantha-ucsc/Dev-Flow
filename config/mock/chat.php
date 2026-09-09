<?php

return [
    1 => [ // Project Beta
        'projectMessages' => [
            ['id' => 1, 'author' => 'User One', 'role' => 'manager', 'body' => 'Team — sprint review is Friday at 3 PM. Please update task statuses before then.', 'createdAt' => '2026-08-12 09:00'],
            ['id' => 2, 'author' => 'User Seven', 'role' => 'team_lead', 'body' => 'Noted. Dev tasks on track except auth module — needs one more day.', 'createdAt' => '2026-08-12 09:15'],
            ['id' => 3, 'author' => 'User Eight', 'role' => 'developer', 'body' => 'Auth middleware PR will be up by EOD.', 'createdAt' => '2026-08-12 09:30'],
        ],
        'stageMessages' => [
            'Development' => [
                ['id' => 1, 'author' => 'User Seven', 'role' => 'team_lead', 'body' => 'Focus on API auth and DB migration this week.', 'createdAt' => '2026-08-10 10:00'],
                ['id' => 2, 'author' => 'User Eight', 'role' => 'developer', 'body' => 'Migration done. Moving to auth module.', 'createdAt' => '2026-08-10 14:00'],
            ],
            'Design' => [
                ['id' => 1, 'author' => 'User Nine', 'role' => 'designer', 'body' => 'Dashboard mockups ready for review.', 'createdAt' => '2026-08-11 11:00'],
            ],
        ],
        'taskMessages' => [
            101 => [
                ['id' => 1, 'author' => 'User Seven', 'role' => 'team_lead', 'body' => 'Keep session compatibility in mind for auth.', 'createdAt' => '2026-08-09 11:00'],
                ['id' => 2, 'author' => 'User Eight', 'role' => 'developer', 'body' => 'Will do — adding adapter layer.', 'createdAt' => '2026-08-09 11:45'],
            ],
        ],
        'clientMessages' => [
            ['id' => 1, 'author' => 'User One', 'role' => 'manager', 'body' => 'Hi — weekly update: development is 65% complete. On track for September delivery.', 'createdAt' => '2026-08-11 16:00'],
        ],
        'clientAccess' => [
            ['user_id' => 1, 'name' => 'User One', 'role' => 'manager', 'access' => 'always'],
            ['user_id' => 7, 'name' => 'User Seven', 'role' => 'team_lead', 'access' => 'always'],
        ],
        'accessRequests' => [
            [
                'id'           => 1,
                'requester'    => 'User Eight',
                'requesterRole'=> 'developer',
                'taskTitle'    => 'API Authentication Module',
                'status'       => 'pending',
                'requestedAt'  => '2026-08-12 08:00',
                'approvers'    => ['User One (Manager)', 'User Seven (Team Lead)'],
            ],
        ],
    ],

    3 => [ // Project Alpha — client on team
        'projectMessages' => [
            ['id' => 1, 'author' => 'User One', 'role' => 'manager', 'body' => 'Final handoff prep starts next week.', 'createdAt' => '2026-08-13 10:00'],
        ],
        'stageMessages' => [
            'Delivery and Handoff' => [
                ['id' => 1, 'author' => 'User Thirteen', 'role' => 'developer', 'body' => 'Deployment checklist 80% done.', 'createdAt' => '2026-08-13 08:30'],
            ],
        ],
        'taskMessages' => [
            301 => [
                ['id' => 1, 'author' => 'User Thirteen', 'role' => 'developer', 'body' => 'SSL renewed — waiting on DNS.', 'createdAt' => '2026-08-13 08:00'],
            ],
        ],
        'clientMessages' => [
            ['id' => 1, 'author' => 'User Nineteen', 'role' => 'client', 'body' => 'When can we schedule the training session?', 'createdAt' => '2026-08-12 15:00'],
            ['id' => 2, 'author' => 'User One', 'role' => 'manager', 'body' => 'Proposing October 22 — does that work for your team?', 'createdAt' => '2026-08-12 15:30'],
            ['id' => 3, 'author' => 'User Nineteen', 'role' => 'client', 'body' => 'Yes, that works. Please send a calendar invite.', 'createdAt' => '2026-08-12 16:00'],
        ],
        'clientAccess' => [
            ['user_id' => 1, 'name' => 'User One', 'role' => 'manager', 'access' => 'always'],
            ['user_id' => 19, 'name' => 'User Nineteen', 'role' => 'client', 'access' => 'always'],
            ['user_id' => 31, 'name' => 'User Thirty-One', 'role' => 'client', 'access' => 'always'],
            ['user_id' => 13, 'name' => 'User Thirteen', 'role' => 'developer', 'access' => 'approved'],
        ],
        'accessRequests' => [
            [
                'id'           => 2,
                'requester'    => 'User Fifteen',
                'requesterRole'=> 'designer',
                'taskTitle'    => 'Client Training Session',
                'status'       => 'approved',
                'requestedAt'  => '2026-08-10 09:00',
                'approvers'    => ['User One (Manager)', 'User Nineteen (Client)', 'User Thirty-One (Client)'],
            ],
        ],
    ],
];
