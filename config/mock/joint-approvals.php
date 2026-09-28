<?php
return [
    1 => [ // Project Beta
        [
            'id'=>10,'type'=>'stage','eyebrow'=>'Stage Completion Gate','title'=>'Stage completion - Design','context'=>'All 6 tasks in this stage are approved.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo User','decision'=>'pending','decidedAgo'=>null],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'Demo Team Lead','decision'=>'approved','decidedAgo'=>'3 hours ago'],
        ],
        [
            'id'=>11,'type'=>'stage','eyebrow'=>'Stage Completion Gate','title'=>'Stage completion - Requirement Gathering','context'=>'All 2 tasks in this stage are approved.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo User','decision'=>'approved','decidedAgo'=>'1 day ago'],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'Demo Team Lead','decision'=>'approved','decidedAgo'=>'1 day ago'],
        ],
        [
            'id'      => 1,
            'type'    => 'stage',
            'eyebrow' => 'Stage Completion Gate',
            'title'   => 'Stage completion - Development',
            'context' => 'All 8 tasks in this stage are approved.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'User Two',
                'decision'   => 'approved',
                'decidedAgo' => '2 days ago',
            ],
            'teamLead' => [
                'roleKey'    => 'team_lead',
                'name'       => 'Demo User',
                'decision'   => 'pending',
                'decidedAgo' => null,
            ],
        ],
    ],

    3 => [ // Project Alpha
        [
            'id'=>12,'type'=>'stage','eyebrow'=>'Stage Completion Gate','title'=>'Stage completion - Testing','context'=>'All 4 tasks in this stage are approved.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo User','decision'=>'pending','decidedAgo'=>null],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'Demo Team Lead','decision'=>'pending','decidedAgo'=>null],
        ],
        [
            'id'      => 2,
            'type'    => 'project',
            'eyebrow' => 'Project Handoff Gate',
            'title'   => 'Final project delivery',
            'context' => 'This is the last internal approval gate before the project can be marked ready for client delivery.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'Demo User',
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
            'title'   => 'Stage completion - Design',
            'context' => 'All 5 tasks in this stage are approved.',
            'manager' => [
                'roleKey'    => 'manager',
                'name'       => 'Demo User',
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

    2 => [ // Project Gamma
        [
            'id'=>20,'type'=>'stage','eyebrow'=>'Stage Completion Gate','title'=>'Stage completion - Testing','context'=>'All 3 tasks in this stage are approved.',
            'manager'=>['roleKey'=>'manager','name'=>'User Ten','decision'=>'pending','decidedAgo'=>null],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'Demo Team Lead','decision'=>'pending','decidedAgo'=>null],
        ],
    ],

    5 => [ // Project Delta (archived)
        [
            'id'=>30,'type'=>'project','eyebrow'=>'Project Handoff Gate','title'=>'Final project delivery','context'=>'Both internal approvals were recorded before the project was archived.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo User','decision'=>'approved','decidedAgo'=>'5 months ago'],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'User Forty-Nine','decision'=>'approved','decidedAgo'=>'5 months ago'],
        ],
    ],

    6 => [ // Project Epsilon (closed)
        [
            'id'=>31,'type'=>'project','eyebrow'=>'Project Handoff Gate','title'=>'Final project delivery','context'=>'Delivered and closed after client approval.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo User','decision'=>'approved','decidedAgo'=>'4 months ago'],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'User Fifty','decision'=>'approved','decidedAgo'=>'4 months ago'],
        ],
    ],

    7 => [ // Project Beta
        [
            'id'=>32,'type'=>'stage','eyebrow'=>'Stage Completion Gate','title'=>'Stage completion - Design','context'=>'All 4 tasks in this stage are approved.',
            'manager'=>['roleKey'=>'manager','name'=>'Demo Manager','decision'=>'approved','decidedAgo'=>'1 day ago'],
            'teamLead'=>['roleKey'=>'team_lead','name'=>'Demo Team Lead','decision'=>'pending','decidedAgo'=>null],
        ],
    ],
];
