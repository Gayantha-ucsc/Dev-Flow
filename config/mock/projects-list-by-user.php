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
        'id' => 4, 'name' => 'Site Redesign', 'description' => 'Landing Page + CMS',
        'status' => 'active', 'health' => 'at_risk', 'role' => 'team_lead', 'percent' => 28,
        'stages' => ['completed', 'in_progress', 'not_started', 'not_started'], 'stageLabel' => 'Design',
        'deadline' => '2026-11-05', 'pendingCount' => 1, 'overdueCount' => 1, 'blockedCount' => 0,
        'milestonesPaid' => 0, 'milestonesTotal' => 2,
    ],
    [
        'id' => 1, 'name' => 'DevFlow Demo Project', 'description' => 'Core Architecture Revamp',
        'status' => 'active', 'health' => 'on_track', 'role' => 'manager', 'percent' => 65,
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
        'id' => 7, 'name' => 'Project Beta', 'description' => 'Client Website Delivery',
        'status' => 'active', 'health' => 'on_track', 'role' => 'client', 'percent' => 65,
        'stages' => ['completed', 'completed', 'in_progress', 'not_started', 'not_started'],
        'stageLabel' => 'Development', 'deadline' => '2026-12-20', 'pendingCount' => 2,
        'overdueCount' => 0, 'blockedCount' => 0, 'milestonesPaid' => 1, 'milestonesTotal' => 3,
    ],
    [
        'id' => 8, 'name' => 'Site Refresh', 'description' => 'Quarterly Content Refresh',
        'status' => 'active', 'health' => 'on_track', 'role' => 'client', 'percent' => 20,
        'stages' => ['completed', 'in_progress', 'not_started', 'not_started'], 'stageLabel' => 'Design',
        'deadline' => '2027-01-10', 'pendingCount' => 0, 'overdueCount' => 0, 'blockedCount' => 0,
        'milestonesPaid' => 0, 'milestonesTotal' => 2,
    ],
];

// ---- Helper accounts: same projects, seen from their own seeded role ----
// Rows are the demo rows above with only the role swapped, so each helper sees
// exactly the projects database/seed.sql gives them membership of.
$byId = [];
foreach ($PERSONA_MANAGER_MIX as $row) { $byId[$row['id']] = $row; }
$asRole = function (array $idToRole) use ($byId): array {
    $out = [];
    foreach ($idToRole as $id => $role) { $out[] = array_merge($byId[$id], ['role' => $role]); }
    return $out;
};

// ---- Map real user_id -> persona (matches database/seed.sql) ----
// 1 = admin: no projects on purpose (system administration only).
// 2 = demo: manager on 1, 3, 5, 6; developer on 2; team lead on 4; client on 7, 8.
return [
    2 => $PERSONA_MANAGER_MIX,
    3 => $asRole([2 => 'manager', 4 => 'manager', 7 => 'manager', 8 => 'manager']),
    4 => $asRole([1 => 'team_lead', 2 => 'team_lead', 3 => 'team_lead', 6 => 'team_lead', 7 => 'team_lead']),
    5 => $asRole([1 => 'developer', 3 => 'developer', 4 => 'developer', 5 => 'developer', 7 => 'developer', 8 => 'developer']),
    6 => $asRole([1 => 'client', 2 => 'client', 3 => 'client', 4 => 'client']),
    7 => $asRole([1 => 'designer']),
];
