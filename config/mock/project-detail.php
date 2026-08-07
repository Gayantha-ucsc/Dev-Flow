<?php

return [
    1 => [ // Project Beta
        'stages' => [
            ['name' => 'Requirement Gathering', 'status' => 'completed',   'tasksApproved' => 4, 'tasksTotal' => 4],
            ['name' => 'Design',                'status' => 'completed',   'tasksApproved' => 6, 'tasksTotal' => 6],
            ['name' => 'Client Design Review',  'status' => 'completed',   'tasksApproved' => 3, 'tasksTotal' => 3],
            ['name' => 'Development',           'status' => 'in_progress', 'tasksApproved' => 6, 'tasksTotal' => 10],
            ['name' => 'Testing',               'status' => 'not_started', 'tasksApproved' => 0, 'tasksTotal' => 0],
            ['name' => 'Delivery and Handoff',  'status' => 'not_started', 'tasksApproved' => 0, 'tasksTotal' => 0],
        ],
    ],

    2 => [ // Project Gamma
        'stages' => [
            ['name' => 'Design',               'status' => 'completed',   'tasksApproved' => 5, 'tasksTotal' => 5],
            ['name' => 'Development',          'status' => 'completed',   'tasksApproved' => 8, 'tasksTotal' => 8],
            ['name' => 'Testing',              'status' => 'in_progress', 'tasksApproved' => 3, 'tasksTotal' => 7],
            ['name' => 'Delivery and Handoff', 'status' => 'not_started', 'tasksApproved' => 0, 'tasksTotal' => 0],
        ],
    ],

    3 => [ // Project Alpha
        'stages' => [
            ['name' => 'Discovery',            'status' => 'completed',   'tasksApproved' => 4,  'tasksTotal' => 4],
            ['name' => 'Design',               'status' => 'completed',   'tasksApproved' => 6,  'tasksTotal' => 6],
            ['name' => 'Development',          'status' => 'completed',   'tasksApproved' => 12, 'tasksTotal' => 12],
            ['name' => 'Testing',              'status' => 'completed',   'tasksApproved' => 9,  'tasksTotal' => 9],
            ['name' => 'Delivery and Handoff', 'status' => 'in_progress', 'tasksApproved' => 2,  'tasksTotal' => 4],
        ],
    ],

    4 => [ // Marketing Site Redesign
        'stages' => [
            ['name' => 'Requirement Gathering', 'status' => 'completed',   'tasksApproved' => 3, 'tasksTotal' => 3],
            ['name' => 'Design',                'status' => 'in_progress', 'tasksApproved' => 2, 'tasksTotal' => 5],
            ['name' => 'Development',           'status' => 'not_started', 'tasksApproved' => 0, 'tasksTotal' => 0],
            ['name' => 'Delivery and Handoff',  'status' => 'not_started', 'tasksApproved' => 0, 'tasksTotal' => 0],
        ],
    ],

    5 => [ // Project Delta - archived, fully completed
        'stages' => [
            ['name' => 'Requirement Gathering', 'status' => 'completed', 'tasksApproved' => 5,  'tasksTotal' => 5],
            ['name' => 'Development',           'status' => 'completed', 'tasksApproved' => 10, 'tasksTotal' => 10],
            ['name' => 'Testing',               'status' => 'completed', 'tasksApproved' => 6,  'tasksTotal' => 6],
            ['name' => 'Delivery and Handoff',  'status' => 'completed', 'tasksApproved' => 3,  'tasksTotal' => 3],
        ],
    ],

    6 => [ // Project Epsilon - closed, delivered
        'stages' => [
            ['name' => 'Discovery',            'status' => 'completed', 'tasksApproved' => 3, 'tasksTotal' => 3],
            ['name' => 'Development',          'status' => 'completed', 'tasksApproved' => 7, 'tasksTotal' => 7],
            ['name' => 'Delivery and Handoff', 'status' => 'completed', 'tasksApproved' => 2, 'tasksTotal' => 2],
        ],
    ],
];