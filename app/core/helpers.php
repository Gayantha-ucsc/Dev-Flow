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

function mockPageContext(string $currentRoute, string $pageTitle, array $extra = []): array {
    $user           = require __DIR__ . '/../../config/mock/users.php';
    $projectContext = currentProjectContext();
    $notifications  = require __DIR__ . '/../../config/mock/notifications.php';

    return array_merge(
        [
            'pageTitle'     => $pageTitle,
            'currentUser'   => $user,
            'currentRoute'  => $currentRoute,
            'unreadCount'   => $notifications['unreadCount'],
            'notifications' => $notifications['items'],
        ],
        $projectContext,
        $extra
    );
}

function currentProjectContext(): array {
    $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';

    if (empty($projectsList)) {
        return [
            'currentProjectId'   => null,
            'currentProjectName' => null,
            'userProjects'       => [],
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
        'activeRole' => $current['role'],
    ];
}

function setCurrentProjectId(int $id): void {
    Session::set('current_project_id', $id);
}

function userHasRoleAnywhere(string $role): bool {
    $projectsList = require __DIR__ . '/../../config/mock/projects-list.php';
    foreach ($projectsList as $row) {
        if ($row['role'] === $role) {
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

function sidebarHref(string $href, ?int $projectId): string {
    return url(str_replace('{id}', (string) $projectId, $href));
}

function avatarColorClass(string $seed): string {
    $palette = ['primary', 'pink', 'success', 'warning', 'danger', 'neutral'];
    return $palette[crc32($seed) % count($palette)];
}

function initials(string $name): string {
    $name = trim($name);
    if ($name === '') {
        return '';
    }

    $first = substr($name, 0, 1);
    $lastWord = strrchr($name, ' ');
    $last = $lastWord !== false ? substr($lastWord, 1, 1) : '';

    return strtoupper($first . $last);
}

// @param array $tasks One stage's task list 
function computeTaskColumns(array $tasks): array {
    $namesInStage = [];
    foreach ($tasks as $task) {
        $namesInStage[$task['name']] = true;
    }

    $columnOf = [];
    foreach ($tasks as $task) {
        $localDeps = array_filter($task['dependsOn'] ?? [], fn($name) => isset($namesInStage[$name]));

        if (empty($localDeps)) {
            $columnOf[$task['name']] = 0;
            continue;
        }

        $maxDepColumn = -1;
        foreach ($localDeps as $depName) {
            $maxDepColumn = max($maxDepColumn, $columnOf[$depName] ?? 0);
        }
        $columnOf[$task['name']] = $maxDepColumn + 1;
    }

    $columns = [];
    foreach ($tasks as $task) {
        $columns[$columnOf[$task['name']]][] = $task;
    }
    ksort($columns);

    return array_values($columns);
}

function taskStatusMeta(string $status): array {
    return match ($status) {
        'locked'         => ['tone' => 'slate',   'icon' => 'lock',           'label' => 'Locked'],
        'not_started'    => ['tone' => 'neutral', 'icon' => 'circle-dashed',  'label' => 'Not Started'],
        'in_progress'    => ['tone' => 'primary', 'icon' => 'circle-dot',     'label' => 'In Progress'],
        'pending_review' => ['tone' => 'info',    'icon' => 'eye',            'label' => 'Ready for Review'],
        'approved'       => ['tone' => 'success', 'icon' => 'circle-check',   'label' => 'Approved'],
        'rejected'       => ['tone' => 'wine',    'icon' => 'circle-x',       'label' => 'Rejected'],
        'blocked'        => ['tone' => 'pink',    'icon' => 'ban',            'label' => 'Blocked'],
        'overdue'        => ['tone' => 'danger',  'icon' => 'circle-alert',   'label' => 'Overdue'],
        default          => ['tone' => 'neutral', 'icon' => null,             'label' => ucfirst($status)],
    };
}