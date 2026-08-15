<?php

function renderIcon(string $name): string {
    $path = __DIR__ . "/../../public/assets/icons/{$name}.svg";

    if (!file_exists($path)) {
        return '';
    }

    return file_get_contents($path);
}

function baseUrl(): string {
    static $base = null;
    if ($base === null) {
        $app = require __DIR__ . '/../../config/app.php';
        $base = rtrim($app['base_url'], '/');
    }
    return $base;
}

function url(string $path = ''): string {
    return baseUrl() . '/' . ltrim($path, '/');
}

function iconJson(string $name): string {
    return json_encode(renderIcon($name));
}

function projectStatusTone(string $status): string {
    return match ($status) {
        'active'   => 'primary',
        'archived' => 'neutral',
        'closed'   => 'pink',
        default    => 'neutral',
    };
}

function currentProjectContext(): array {
    $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';

    if (empty($projectsList)) {
        return [
            'currentProjectId'   => null,
            'currentProjectName' => null,
            'userProjects'       => [],
            'userRoles'          => [],
            'activeRole'         => null,
        ];
    }

    $validIds = array_column($projectsList, 'id');
    $selected = Session::get('current_project_id');

    if ($selected === null || !in_array($selected, $validIds, true)) {
        $candidates = array_values(array_filter($projectsList, fn($row) => $row['status'] === 'active'));
        if (empty($candidates)) {
            $candidates = $projectsList;
        }
        usort($candidates, fn($a, $b) => strtotime($a['deadline']) <=> strtotime($b['deadline']));
        $selected = $candidates[0]['id'];
    }

    $current = null;
    foreach ($projectsList as $row) {
        if ($row['id'] === $selected) {
            $current = $row;
            break;
        }
    }

    return [
        'currentProjectId'   => $current['id'],
        'currentProjectName' => $current['name'],
        'userProjects'       => array_map(
            fn($row) => ['project_id' => $row['id'], 'name' => $row['name']],
            $projectsList
        ),
        'userRoles'  => $current['roles'],
        'activeRole' => selectActiveRole($current['roles']),
    ];
}

function selectActiveRole(array $roles): ?string {
    $precedence = ['manager', 'team_lead', 'developer', 'designer', 'client'];
    foreach ($precedence as $role) {
        if (in_array($role, $roles, true)) {
            return $role;
        }
    }
    return $roles[0] ?? null;
}

function setCurrentProjectId(int $id): void {
    Session::set('current_project_id', $id);
}

function userHasRoleAnywhere(string $role): bool {
    $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';
    foreach ($projectsList as $row) {
        if (in_array($role, $row['roles'], true)) {
            return true;
        }
    }
    return false;
}

function memberRoleTone(string $role): string {
    return match ($role) {
        'manager'   => 'pink',
        'team_lead' => 'primary',
        'developer' => 'success',
        'designer'  => 'warning',
        'client'    => 'neutral',
        default     => 'neutral',
    };
}

function memberRoleLabel(string $role): string {
    return match ($role) {
        'team_lead' => 'Team Lead',
        default     => ucfirst($role),
    };
}

function avatarColorClass(string $seed): string {
    $palette = ['primary', 'pink', 'success', 'warning', 'danger', 'neutral'];
    return $palette[crc32($seed) % count($palette)];
}

function projectHasTeamLead(array $members): bool {
    foreach ($members as $member) {
        if (in_array('team_lead', $member['roles'] ?? [], true)) {
            return true;
        }
    }
    return false;
}

function qualifyingApproverLabel(string $requestedByRole): string {
    return match ($requestedByRole) {
        'manager'   => 'Team Lead',
        'team_lead' => 'Manager',
        default     => 'Project Lead',
    };
}

function taskStatusTone(string $status): string {
    return match ($status) {
        'not_started'     => 'neutral',
        'in_progress'     => 'primary',
        'blocked'         => 'danger',
        'pending_review'  => 'warning',
        'completed'       => 'success',
        default           => 'neutral',
    };
}

function taskStatusLabel(string $status): string {
    return match ($status) {
        'not_started'     => 'Not Started',
        'in_progress'     => 'In Progress',
        'blocked'         => 'Blocked',
        'pending_review'  => 'Pending Review',
        'completed'       => 'Completed',
        default           => ucfirst(str_replace('_', ' ', $status)),
    };
}

function taskPriorityTone(string $priority): string {
    return match ($priority) {
        'high'   => 'danger',
        'medium' => 'warning',
        'low'    => 'neutral',
        default  => 'neutral',
    };
}

/**
 * Task UI permissions — single source for backend role checks later.
 * Views wrap sections with data-task-perm keys matching these flags.
 */
function taskPermissions(?string $activeRole, array $currentUser = [], ?array $task = null): array {
    $isLead = in_array($activeRole, ['manager', 'team_lead'], true);
    $isContributor = in_array($activeRole, ['developer', 'designer'], true);

    $isAssignee = false;
    if ($task && !empty($task['assignees'])) {
        foreach ($task['assignees'] as $a) {
            if (($a['user_id'] ?? null) === ($currentUser['user_id'] ?? null)) {
                $isAssignee = true;
                break;
            }
        }
    }

    return [
        'canCreateTask'         => $isLead,
        'canEditTask'           => $isLead,
        'canDeleteTask'         => $isLead,
        'canAssign'             => $isLead,
        'canManageDependencies' => $isLead,
        'canSetAnyStatus'       => $isLead,
        'canUpdateStatus'       => $isLead || $isContributor,
        'canAddProgressNote'    => $isLead || $isContributor,
        'canComment'            => $isLead || $isContributor,
        'canViewRevisions'      => true,
        'canManageRevisions'    => $isLead,
        'canSubmitRevision'     => $isLead || $isContributor,
        'isAssignee'            => $isAssignee,
        'roleLabel'             => memberRoleLabel($activeRole ?? 'member'),
    ];
}

/** UI milestone: show all task sections while backend wiring is pending. */
function taskPermissionsUiDemo(array $perms): array {
    foreach (array_keys($perms) as $key) {
        if ($key === 'roleLabel' || $key === 'isAssignee') {
            continue;
        }
        if (is_bool($perms[$key])) {
            $perms[$key] = true;
        }
    }
    return $perms;
}

/** UI milestone: show all chat sections while backend wiring is pending. */
function chatPermissionsUiDemo(array $perms): array {
    foreach (array_keys($perms) as $key) {
        if ($key === 'roleLabel') {
            continue;
        }
        if (is_bool($perms[$key])) {
            $perms[$key] = true;
        }
    }
    return $perms;
}

/**
 * Chat UI permissions — wrap sections with data-chat-perm keys for backend.
 */
function chatPermissions(?string $activeRole, bool $hasApprovedClientAccess = false): array {
    $isLead = in_array($activeRole, ['manager', 'team_lead'], true);
    $isContributor = in_array($activeRole, ['developer', 'designer'], true);
    $isClient = $activeRole === 'client';

    return [
        'canViewProjectChat'      => $isLead || $isContributor || $isClient,
        'canPostProjectChat'      => $isLead || $isContributor,
        'canViewStageChat'        => $isLead || $isContributor,
        'canPostStageChat'        => $isLead || $isContributor,
        'canViewTaskChat'         => $isLead || $isContributor,
        'canPostTaskChat'         => $isLead || $isContributor,
        'canViewClientChat'       => $isLead || $isClient || $hasApprovedClientAccess,
        'canPostClientChat'       => $isLead || $isClient || $hasApprovedClientAccess,
        'canRequestClientAccess'  => $isContributor,
        'canApproveClientAccess'  => $isLead,
        'roleLabel'               => memberRoleLabel($activeRole ?? 'member'),
    ];
}