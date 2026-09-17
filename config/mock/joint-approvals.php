<?php
return [
    1 => [ // Project Beta
        [
            'id'      => 1,
            'type'    => 'stage',
            'eyebrow' => 'Stage Completion Gate',
            'title'   => 'Stage completion — Development',
            'context' => 'All 8 tasks in this stage are approved.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'User Two',
                'decision'   => 'approved',
                'decidedAgo' => '2 days ago',
            ],
            'teamLead' => [
                'roleKey'    => 'team_lead',
                'name'       => 'User One',
                'decision'   => 'pending',
                'decidedAgo' => null,
            ],
        ],
    ],

    3 => [ // Project Alpha
        [
            'id'      => 2,
            'type'    => 'project',
            'eyebrow' => 'Project Handoff Gate',
            'title'   => 'Final project delivery',
            'context' => 'This is the last internal approval gate before the project can be marked ready for client delivery.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'User One',
                'decision'   => 'pending',
                'decidedAgo' => null,
            ],
            'teamLead' => [
                'roleKey'    => 'team_lead',
                'name'       => 'User Thirteen',
                'decision'   => 'pending',
                'decidedAgo' => null,
            ],
        ],
    ],

    4 => [ // Marketing Site Redesign
        [
            'id'      => 3,
            'type'    => 'stage',
            'eyebrow' => 'Stage Completion Gate',
            'title'   => 'Stage completion — Design',
            'context' => 'All 5 tasks in this stage are approved.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'User One',
                'decision'   => 'pending',
                'decidedAgo' => null,
            ],
            'teamLead' => [
                'roleKey'    => 'team_lead',
                'name'       => 'User Fifteen',
                'decision'   => 'approved',
                'decidedAgo' => '6 hours ago',
            ],
        ],
    ],
];