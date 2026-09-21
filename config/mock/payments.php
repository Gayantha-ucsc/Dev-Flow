<?php

return [

    1 => [ // Project Beta
        [
            'id' => 101, 'description' => 'Kickoff deposit', 'stage' => 'Requirement Gathering',
            'amount' => 4000.00, 'dueDate' => '2026-02-20', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260216-4821', 'amountPaid' => 4000.00, 'status' => 'completed', 'paidAt' => '2026-02-16 10:42:00'],
        ],
        [
            'id' => 102, 'description' => 'Design approval', 'stage' => 'Client Design Review',
            'amount' => 6000.00, 'dueDate' => '2026-03-31', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260326-7713', 'amountPaid' => 6000.00, 'status' => 'completed', 'paidAt' => '2026-03-26 15:08:00'],
        ],
        [
            'id' => 103, 'description' => 'Development milestone', 'stage' => 'Development',
            'amount' => 8000.00, 'dueDate' => '2026-09-30', 'status' => 'requested',
            'payment' => ['reference' => 'GW-260914-2290', 'amountPaid' => 0.00, 'status' => 'failed', 'paidAt' => null],
        ],
        [
            'id' => 104, 'description' => 'Final delivery payment', 'stage' => 'Delivery and Handoff',
            'amount' => 5000.00, 'dueDate' => '2026-11-15', 'status' => 'pending',
            'payment' => null,
        ],
    ],

    3 => [ // Project Alpha
        [
            'id' => 301, 'description' => 'Discovery and architecture deposit', 'stage' => 'Discovery',
            'amount' => 5000.00, 'dueDate' => '2026-01-30', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260127-1054', 'amountPaid' => 5000.00, 'status' => 'completed', 'paidAt' => '2026-01-27 09:15:00'],
        ],
        [
            'id' => 302, 'description' => 'Design system sign-off', 'stage' => 'Design',
            'amount' => 6000.00, 'dueDate' => '2026-02-15', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260210-6382', 'amountPaid' => 6000.00, 'status' => 'completed', 'paidAt' => '2026-02-10 13:30:00'],
        ],
        [
            'id' => 303, 'description' => 'Final delivery payment', 'stage' => 'Delivery and Handoff',
            'amount' => 7000.00, 'dueDate' => '2026-10-15', 'status' => 'requested',
            'payment' => null,
        ],
    ],

    4 => [ // Marketing Site Redesign
        [
            'id' => 401, 'description' => 'Design sign-off', 'stage' => 'Design',
            'amount' => 2500.00, 'dueDate' => '2026-10-10', 'status' => 'pending',
            'payment' => null,
        ],
        [
            'id' => 402, 'description' => 'Launch payment', 'stage' => 'Delivery and Handoff',
            'amount' => 3500.00, 'dueDate' => '2026-12-01', 'status' => 'pending',
            'payment' => null,
        ],
    ],

    5 => [ // Project Delta (archived)
        [
            'id' => 501, 'description' => 'Decommission plan approval', 'stage' => 'Requirement Gathering',
            'amount' => 2000.00, 'dueDate' => '2025-11-15', 'status' => 'paid',
            'payment' => ['reference' => 'GW-251112-3340', 'amountPaid' => 2000.00, 'status' => 'completed', 'paidAt' => '2025-11-12 11:20:00'],
        ],
        [
            'id' => 502, 'description' => 'Migration and cutover', 'stage' => 'Development',
            'amount' => 4000.00, 'dueDate' => '2025-12-20', 'status' => 'paid',
            'payment' => ['reference' => 'GW-251218-8127', 'amountPaid' => 4000.00, 'status' => 'completed', 'paidAt' => '2025-12-18 16:45:00'],
        ],
        [
            'id' => 503, 'description' => 'Final handoff', 'stage' => 'Delivery and Handoff',
            'amount' => 3000.00, 'dueDate' => '2026-01-31', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260126-5569', 'amountPaid' => 3000.00, 'status' => 'completed', 'paidAt' => '2026-01-26 10:05:00'],
        ],
    ],

    6 => [ // Project Epsilon (closed)
        [
            'id' => 601, 'description' => 'Audit kickoff', 'stage' => 'Discovery',
            'amount' => 3000.00, 'dueDate' => '2025-07-15', 'status' => 'paid',
            'payment' => ['reference' => 'GW-250714-9012', 'amountPaid' => 3000.00, 'status' => 'completed', 'paidAt' => '2025-07-14 14:12:00'],
        ],
        [
            'id' => 602, 'description' => 'Final report acceptance', 'stage' => 'Delivery and Handoff',
            'amount' => 4500.00, 'dueDate' => '2025-10-10', 'status' => 'paid',
            'payment' => ['reference' => 'GW-251008-4476', 'amountPaid' => 4500.00, 'status' => 'completed', 'paidAt' => '2025-10-08 09:50:00'],
        ],
    ],

    7 => [ // Project Alpha (client-facing copy)
        [
            'id' => 701, 'description' => 'Discovery deposit', 'stage' => 'Discovery',
            'amount' => 1500.00, 'dueDate' => '2026-01-15', 'status' => 'paid',
            'payment' => ['reference' => 'GW-260112-3057', 'amountPaid' => 1500.00, 'status' => 'completed', 'paidAt' => '2026-01-12 09:40:00'],
        ],
        [
            'id' => 702, 'description' => 'Initial sprint delivery', 'stage' => 'Development',
            'amount' => 500.00, 'dueDate' => '2026-12-15', 'status' => 'requested',
            'payment' => null,
        ],
        [
            'id' => 703, 'description' => 'Final delivery payment', 'stage' => 'Final Delivery',
            'amount' => 3000.00, 'dueDate' => '2026-12-30', 'status' => 'pending',
            'payment' => null,
        ],
    ],

    8 => [ // Retainer Site Refresh
        [
            'id' => 801, 'description' => 'Design kickoff', 'stage' => 'Design',
            'amount' => 1200.00, 'dueDate' => '2027-01-05', 'status' => 'pending',
            'payment' => null,
        ],
        [
            'id' => 802, 'description' => 'Delivery payment', 'stage' => 'Delivery',
            'amount' => 1800.00, 'dueDate' => '2027-01-20', 'status' => 'pending',
            'payment' => null,
        ],
    ],

];