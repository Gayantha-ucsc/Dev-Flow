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