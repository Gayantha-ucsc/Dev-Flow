<?php

return [
    [
        'label' => 'Session & Security',
        'icon'  => 'lock',
        'description' => 'How long an idle session stays signed in before it must be re-authenticated (FR-1.4.2).',
        'settings' => [
            [
                'key'         => 'session_timeout_minutes',
                'label'       => 'Session inactivity timeout',
                'description' => 'How long a session stays signed in with no activity before it\'s invalidated.',
                'type'        => 'number',
                'value'       => 60,
                'unit'        => 'minutes',
                'updated_by'  => 'User One',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-30 days')),
            ],
        ],
    ],
    [
        'label' => 'Notifications',
        'icon'  => 'bell',
        'description' => 'Escalation timing and how often the client checks for new notifications.',
        'settings' => [
            [
                'key'         => 'overdue_escalation_days',
                'label'       => 'Overdue escalation threshold',
                'description' => 'Days a task stays overdue before the Manager is also notified, on top of the Team Lead.',
                'type'        => 'number',
                'value'       => 2,
                'unit'        => 'days',
                'updated_by'  => 'User One',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-14 days')),
            ],
            [
                'key'         => 'notification_polling_seconds',
                'label'       => 'Notification polling interval',
                'description' => 'How often the browser checks for new notifications in the background.',
                'type'        => 'number',
                'value'       => 30,
                'unit'        => 'seconds',
                'updated_by'  => 'User One',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-30 days')),
            ],
        ],
    ],
    [
        'label' => 'Chat',
        'icon'  => 'chat',
        'description' => 'Message refresh timing and the joint-approval timeout for client-room access.',
        'settings' => [
            [
                'key'         => 'chat_polling_seconds',
                'label'       => 'Chat polling interval',
                'description' => 'How often an open chat room re-fetches new messages.',
                'type'        => 'number',
                'value'       => 10,
                'unit'        => 'seconds',
                'updated_by'  => 'User One',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-30 days')),
            ],
            [
                'key'         => 'client_room_request_timeout_hours',
                'label'       => 'Client-room access request timeout',
                'description' => "Auto-expires a pending contributor addition request (FR-6.2.3) if Manager and Team Lead haven't both decided.",
                'type'        => 'number',
                'value'       => 48,
                'unit'        => 'hours',
                'updated_by'  => 'User Two',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-9 days')),
            ],
        ],
    ],
    [
        'label' => 'Workflow defaults',
        'icon'  => 'workflow',
        'description' => 'The template highlighted first when a Team Lead picks a workflow for a new project.',
        'settings' => [
            [
                'key'         => 'default_workflow_template',
                'label'       => 'Default workflow template',
                'description' => 'Pre-selected in the template picker at project creation (FR-2.2.3) — the Team Lead can still pick a different one or start from scratch.',
                'type'        => 'select',
                'value'       => 'Standard Web Development',
                'updated_by'  => 'User One',
                'updated_at'  => date('Y-m-d H:i:s', strtotime('-45 days')),
            ],
        ],
    ],
];