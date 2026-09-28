<?php

class Project {

    // project by id. null if doesn't exist.
    public static function findById(int $projectId): ?array {
        return DB::getInstance()->select('Project', ['project_id' => $projectId]);
    }

    // update name / description / deadline for an existing project.
    public static function update(int $projectId, string $name, string $description, string $deadline): bool {
        return DB::getInstance()->update('Project', [
            'name'        => $name,
            'description' => $description,
            'deadline'    => $deadline,
        ], ['project_id' => $projectId]) > 0;
    }
}
