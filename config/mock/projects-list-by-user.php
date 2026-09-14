<?php
$PERSONA_MANAGER_MIX = [
    [
        'id' => 2, 'name' => 'Project Gamma', 'description' => 'Module Implementation',
        'status' => 'active', 'health' => 'at_risk', 'role' => 'developer', 'percent' => 40,
        'stages' => ['completed', 'completed', 'in_progress', 'not_started'], 'stageLabel' => 'Testing',
        'deadline' => '2026-08-30', 'pendingCount' => 1, 'overdueCount' => 2, 'blockedCount' => 3,
        'milestonesPaid' => 1, 'milestonesTotal' => 3,
    ],
    [
        'id' => 4, 'name' => 'Marketing Site Redesign', 'description' => 'Landing Page + CMS',
        'status' => 'active', 'health' => 'at_risk', 'role' => 'manager', 'percent' => 28,
        'stages' => ['completed', 'in_progress', 'not_started', 'not_started'], 'stageLabel' => 'Design',
        'deadline' => '2026-11-05', 'pendingCount' => 1, 'overdueCount' => 1, 'blockedCount' => 0,
        'milestonesPaid' => 0, 'milestonesTotal' => 2,
    ],
    [
        'id' => 1, 'name' => 'Project Beta', 'description' => 'Core Architecture Revamp',
        'status' => 'active', 'health' => 'on_track', 'role' => 'team_lead', 'percent' => 65,
        'stages' => ['completed', 'completed', 'completed', 'in_progress', 'not_started', 'not_started'],
        'stageLabel' => 'Development', 'deadline' => '2026-09-15', 'pendingCount' => 0, 'overdueCount' => 0,
        'blockedCount' => 0, 'milestonesPaid' => 2, 'milestonesTotal' => 4,
    ],
    [
        'id' => 3, 'name' => 'Project Alpha', 'description' => 'Client Website Delivery',
        'status' => 'active', 'health' => 'on_track', 'role' => 'manager', 'percent' => 92,
        'stages' => ['completed', 'completed', 'completed', 'completed', 'in_progress'],
        'stageLabel' => 'Delivery and Handoff', 'deadline' => '2026-10-24', 'pendingCount' => 1,
        'overdueCount' => 0, 'blockedCount' => 0, 'milestonesPaid' => 2, 'milestonesTotal' => 3,
    ],
    [
        'id' => 5, 'name' => 'Project Delta', 'description' => 'Legacy Database Decommissioning',
        'status' => 'archived', 'health' => null, 'role' => 'manager', 'percent' => 100,
        'stages' => ['completed', 'completed', 'completed', 'completed'], 'stageLabel' => 'Completed',
        'deadline' => '2026-07-01', 'pendingCount' => 0, 'overdueCount' => 0, 'blockedCount' => 0,
        'milestonesPaid' => 3, 'milestonesTotal' => 3,
    ],
    [
        'id' => 6, 'name' => 'Project Epsilon', 'description' => 'Security Audit & Compliance Review Q2',
        'status' => 'closed', 'health' => null, 'role' => 'manager', 'percent' => 100,
        'stages' => ['completed', 'completed', 'completed'], 'stageLabel' => 'Delivered',
        'deadline' => '2026-06-15', 'pendingCount' => 0, 'overdueCount' => 0, 'blockedCount' => 0,
        'milestonesPaid' => 2, 'milestonesTotal' => 2,
    ],
    [
        'id' => 7, 'name' => 'Project Alpha', 'description' => 'Client Website Delivery',
        'status' => 'active', 'health' => 'on_track', 'role' => 'client', 'percent' => 65,
        'stages' => ['completed', 'completed', 'in_progress', 'not_started', 'not_started'],
        'stageLabel' => 'Development', 'deadline' => '2026-12-20', 'pendingCount' => 2,
        'overdueCount' => 0, 'blockedCount' => 0, 'milestonesPaid' => 1, 'milestonesTotal' => 3,
    ],
    [
        'id' => 8, 'name' => 'Retainer Site Refresh', 'description' => 'Quarterly Content Refresh',
        'status' => 'active', 'health' => 'on_track', 'role' => 'client', 'percent' => 20,
        'stages' => ['completed', 'in_progress', 'not_started', 'not_started'], 'stageLabel' => 'Design',
        'deadline' => '2027-01-10', 'pendingCount' => 0, 'overdueCount' => 0, 'blockedCount' => 0,
        'milestonesPaid' => 0, 'milestonesTotal' => 2,
    ],
];

// Pure contributor: developer on one project, designer on another
$PERSONA_CONTRIBUTOR_ONLY = [
    $PERSONA_MANAGER_MIX[0], // Project Gamma - developer
    [
        'id' => 1, 'name' => 'Project Beta', 'description' => 'Core Architecture Revamp',
        'status' => 'active', 'health' => 'on_track', 'role' => 'designer', 'percent' => 65,
        'stages' => ['completed', 'completed', 'completed', 'in_progress', 'not_started', 'not_started'],
        'stageLabel' => 'Development', 'deadline' => '2026-09-15', 'pendingCount' => 0, 'overdueCount' => 0,
        'blockedCount' => 0, 'milestonesPaid' => 2, 'milestonesTotal' => 4,
    ],
];

// Team lead only
$PERSONA_TEAM_LEAD_ONLY = [
    $PERSONA_MANAGER_MIX[2], // Project Beta - team_lead
];

// Contributor + client mix, no manager anywhere -> dashboard/contributor
$PERSONA_CONTRIBUTOR_AND_CLIENT = [
    $PERSONA_MANAGER_MIX[0], // Project Gamma - developer
    $PERSONA_MANAGER_MIX[6], // Project Alpha - client
];

// Client only -> dashboard/client
$PERSONA_CLIENT_ONLY = [
    $PERSONA_MANAGER_MIX[6],
    $PERSONA_MANAGER_MIX[7],
];

// Map real user_id -> persona 
return [
    1 => $PERSONA_TEAM_LEAD_ONLY,
    // 2 => $PERSONA_CONTRIBUTOR_ONLY,
    // 3 => $PERSONA_TEAM_LEAD_ONLY,
    // 4 => $PERSONA_CONTRIBUTOR_AND_CLIENT,
    // 5 => $PERSONA_CLIENT_ONLY,
    // 6 => $PERSONA_MANAGER_MIX,
];