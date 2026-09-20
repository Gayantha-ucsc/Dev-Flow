<?php

return [
    'lastUpdatedBy' => 'User One',
    'lastUpdatedAt' => date('Y-m-d H:i:s', strtotime('-3 days')),

    'groups' => [
        [
            'label' => 'Task Management',
            'icon'  => 'tasks',
            'permissions' => [
                [
                    'key'   => 'task.create',
                    'label' => 'Create tasks',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'task.assign',
                    'label' => 'Assign tasks',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'task.review',
                    'label' => 'Review and approve tasks',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'task.set_dependencies',
                    'label' => 'Set task dependencies',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
            ],
        ],
        [
            'label' => 'Project & Stages',
            'icon'  => 'workflow',
            'permissions' => [
                [
                    'key'   => 'project.create',
                    'label' => 'Create projects',
                    'roles' => ['manager' => true, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'stage.manage',
                    'label' => 'Manage workflow stages',
                    'roles' => ['manager' => true, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'stage.propose_change',
                    'label' => 'Propose stage changes',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
            ],
        ],
        [
            'label' => 'Approvals',
            'icon'  => 'user-check',
            'permissions' => [
                [
                    'key'   => 'approval.stage_completion',
                    'label' => 'Joint approval - stage completion',
                    'roles' => ['manager' => true, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'approval.final_delivery',
                    'label' => 'Joint approval - final delivery',
                    'roles' => ['manager' => true, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => false],
                ],
            ],
        ],
        [
            'label' => 'Communication',
            'icon'  => 'chat',
            'permissions' => [
                [
                    'key'   => 'chat.client_room_access',
                    'label' => 'Access client chat room',
                    'roles' => ['manager' => true, 'team_lead' => true, 'designer' => false, 'developer' => false, 'client' => true],
                ],
                [
                    'key'   => 'comment.post',
                    'label' => 'Post comments on tasks',
                    'roles' => ['manager' => false, 'team_lead' => true, 'designer' => true, 'developer' => true, 'client' => false],
                ],
            ],
        ],
        [
            'label' => 'Payments',
            'icon'  => 'payment',
            'permissions' => [
                [
                    'key'   => 'payment.create_milestone',
                    'label' => 'Create payment milestones',
                    'roles' => ['manager' => true, 'team_lead' => false, 'designer' => false, 'developer' => false, 'client' => false],
                ],
                [
                    'key'   => 'payment.view_history',
                    'label' => 'View payment history',
                    'roles' => ['manager' => true, 'team_lead' => false, 'designer' => false, 'developer' => false, 'client' => true],
                ],
            ],
        ],
    ],
];