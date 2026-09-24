<?php

class ProjectMember {

    // TODO: since the member role is changed for one role per project this will change
    public static function rolesForUser(int $projectId, int $userId): array {
        $rows = DB::getInstance()->query(
            'SELECT role FROM ProjectMember WHERE project_id = ? AND user_id = ? AND is_active = 1',
            [$projectId, $userId]
        );
        return array_column($rows, 'role');
    }

    // Only an active Manager or Team Lead may add, rename, reorder or delete stages
    public static function canManageStages(int $projectId, int $userId): bool {
        $roles = self::rolesForUser($projectId, $userId);
        return in_array('manager', $roles, true) || in_array('team_lead', $roles, true);
    }

    // Every active project this user belongs to, one row per project
    public static function projectsForUser(int $userId): array {
        $rows = DB::getInstance()->query(
            'SELECT p.project_id, p.name, p.status, pm.role
             FROM ProjectMember pm
             JOIN Project p ON p.project_id = pm.project_id
             WHERE pm.user_id = ? AND pm.is_active = 1
             ORDER BY p.name ASC',
            [$userId]
        );

        $rolePriority = ['manager' => 1, 'team_lead' => 2, 'designer' => 3, 'developer' => 3, 'client' => 4];

        $byProject = [];
        foreach ($rows as $row) {
            $projectId = (int) $row['project_id'];
            $currentBest = $byProject[$projectId]['role'] ?? null;

            if ($currentBest === null || ($rolePriority[$row['role']] ?? 9) < ($rolePriority[$currentBest] ?? 9)) {
                $byProject[$projectId] = $row;
            }
        }

        return array_values($byProject);
    }
}
