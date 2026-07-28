<?php
// Mock data for the Manager's cross-project dashboard.
// Shape mirrors what DashboardController will eventually assemble from
// Project / Stage / Task / JointApprovalRequest / MemberApprovalRequest /
// StageChangeProposal / ActivityLog once those models exist - see
// DevFlow_Project_Reference.md section 23.6 for the layout this backs.

return [

    'stats' => [
        [
            'key'   => 'active_projects',
            'label' => 'Active Projects',
            'value' => 6,
            'icon'  => 'folder',
            'tone'  => 'neutral',
            'meta'  => '+1 since last week',
        ],
        [
            'key'   => 'pending_approvals',
            'label' => 'Pending Approvals',
            'value' => 5,
            'icon'  => 'review',
            'tone'  => 'primary',
            'meta'  => 'Require your sign-off',
        ],
        [
            'key'   => 'overdue_tasks',
            'label' => 'Overdue Tasks',
            'value' => 4,
            'icon'  => 'circle-alert',
            'tone'  => 'danger',
            'meta'  => 'Across 2 projects',
        ],
        [
            'key'   => 'blocked_tasks',
            'label' => 'Blocked Tasks',
            'value' => 3,
            'icon'  => 'ban',
            'tone'  => 'pink',
            'meta'  => 'Awaiting dependencies',
        ],
    ],

    // Cross-project queue of everything only a Manager can act on:
    // stage-completion joint approvals (FR-2.5), member-addition
    // confirmations (FR-2.6.3), and stage-change proposals (FR-2.4.2).
    'needsAttention' => [
        [
            'type'    => 'stage_completion',
            'icon'    => 'flag',
            'title'   => 'Stage Completion: Client Design Review',
            'project' => 'Project Beta',
            'meta'    => 'Team Lead has approved — awaiting your sign-off',
            'actions' => [
                ['label' => 'Review',  'variant' => 'ghost',   'href' => '/review'],
                ['label' => 'Approve', 'variant' => 'primary', 'href' => '#'],
            ],
        ],
        [
            'type'    => 'member_request',
            'icon'    => 'user-plus',
            'title'   => 'New Member: Ishara Perera — Developer',
            'project' => 'Project Gamma',
            'meta'    => 'Added by Team Lead — needs your confirmation',
            'actions' => [
                ['label' => 'Reject',  'variant' => 'text-danger', 'href' => '#'],
                ['label' => 'Approve', 'variant' => 'primary',     'href' => '#'],
            ],
        ],
        [
            'type'    => 'stage_change_proposal',
            'icon'    => 'pencil',
            'title'   => 'Stage Rename Proposed: "QA" → "Testing & QA"',
            'project' => 'Marketing Site Redesign',
            'meta'    => 'Proposed by Team Lead, with a reason attached',
            'actions' => [
                ['label' => 'Review', 'variant' => 'ghost', 'href' => '/review'],
            ],
        ],
        [
            'type'    => 'stage_completion',
            'icon'    => 'flag',
            'title'   => 'Final Delivery Ready for Joint Approval',
            'project' => 'Project Alpha',
            'meta'    => 'All stages approved — final handoff pending',
            'actions' => [
                ['label' => 'Review',  'variant' => 'ghost',   'href' => '/review'],
                ['label' => 'Approve', 'variant' => 'primary', 'href' => '#'],
            ],
        ],
    ],

    // Cross-project rollup, prioritized by urgency (at-risk projects first) -
    // not alphabetical, per 23.6. Deliberately excludes task-level detail.
    'projects' => [
        [
            'id'         => 2,
            'name'       => 'Project Gamma',
            'subtitle'   => 'Module Implementation',
            'health'     => 'at_risk',
            'percent'    => 40,
            'stages'     => ['completed', 'completed', 'in_progress', 'not_started'],
            'stageLabel' => 'Testing',
            'doneCount'  => 12,
            'activeCount'   => 5,
            'dangerCount'   => 3,
            'dangerLabel'   => 'Blocked',
        ],
        [
            'id'         => 1,
            'name'       => 'Project Beta',
            'subtitle'   => 'Core Architecture Revamp',
            'health'     => 'on_track',
            'percent'    => 65,
            'stages'     => ['completed', 'completed', 'completed', 'in_progress', 'not_started', 'not_started'],
            'stageLabel' => 'Development',
            'doneCount'  => 24,
            'activeCount'   => 8,
            'dangerCount'   => 0,
            'dangerLabel'   => 'Overdue',
        ],
        [
            'id'         => 3,
            'name'       => 'Project Alpha',
            'subtitle'   => 'Client Website Delivery',
            'health'     => 'on_track',
            'percent'    => 92,
            'stages'     => ['completed', 'completed', 'completed', 'completed', 'in_progress'],
            'stageLabel' => 'Delivery and Handoff',
            'doneCount'  => 31,
            'activeCount'   => 2,
            'dangerCount'   => 0,
            'dangerLabel'   => 'Overdue',
        ],
        [
            'id'         => 4,
            'name'       => 'Marketing Site Redesign',
            'subtitle'   => 'Landing Page + CMS',
            'health'     => 'at_risk',
            'percent'    => 28,
            'stages'     => ['completed', 'in_progress', 'not_started', 'not_started'],
            'stageLabel' => 'Design',
            'doneCount'  => 6,
            'activeCount'   => 4,
            'dangerCount'   => 1,
            'dangerLabel'   => 'Overdue',
        ],
    ],

    // Cross-project feed, each entry tagged with its project name per 23.6.
    'activity' => [
        [
            'time'    => '10 mins ago',
            'text'    => '<strong>Chamika (Team Lead)</strong> marked stage "Client Design Review" ready for client review.',
            'project' => 'Project Beta',
            'tone'    => 'default',
        ],
        [
            'time'    => '2 hours ago',
            'text'    => '<strong>Client</strong> approved task "Landing Page Wireframe".',
            'project' => 'Project Gamma',
            'tone'    => 'default',
        ],
        [
            'time'    => 'Yesterday, 4:30 PM',
            'text'    => '<strong>System</strong> escalated overdue task "API Integration" to you.',
            'project' => 'Marketing Site Redesign',
            'tone'    => 'danger',
        ],
        [
            'time'    => 'Yesterday, 1:15 PM',
            'text'    => '<strong>You</strong> approved the "Design Approval" stage completion.',
            'project' => 'Project Beta',
            'tone'    => 'default',
        ],
    ],
];