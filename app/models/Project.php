<?php

class Project {

    // project by id. null if doesn't exist.
    public static function findById(int $projectId): ?array {
        return DB::getInstance()->select('Project', ['project_id' => $projectId]);
    }
}
