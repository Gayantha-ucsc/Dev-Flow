<?php

class Task {

    // All tasks in a project, ordered by stage then id. Includes the stage name.
    public static function listByProject(int $projectId): array {
        return DB::getInstance()->query(
            'SELECT t.*, s.name AS stage_name, s.sequence_order
             FROM Task t
             JOIN Stage s ON s.stage_id = t.stage_id
             WHERE s.project_id = ?
             ORDER BY s.sequence_order ASC, t.task_id ASC',
            [$projectId]
        );
    }

    // Task by id, with its stage name and project_id. null if it doesn't exist.
    public static function findById(int $taskId): ?array {
        $rows = DB::getInstance()->query(
            'SELECT t.*, s.name AS stage_name, s.project_id
             FROM Task t
             JOIN Stage s ON s.stage_id = t.stage_id
             WHERE t.task_id = ? LIMIT 1',
            [$taskId]
        );
        return $rows[0] ?? null;
    }

    public static function create(array $data): int {
        return DB::getInstance()->insert('Task', [
            'stage_id'    => $data['stage_id'],
            'name'        => $data['name'],
            'description' => $data['description'],
            'created_by'  => $data['created_by'],
            'status'      => 'not_started',
            'task_type'   => $data['task_type'],
            'deadline'    => $data['deadline'],
        ]);
    }

    public static function update(int $taskId, array $data): void {
        DB::getInstance()->update('Task', [
            'stage_id'    => $data['stage_id'],
            'name'        => $data['name'],
            'description' => $data['description'],
            'task_type'   => $data['task_type'],
            'deadline'    => $data['deadline'],
        ], ['task_id' => $taskId]);
    }

    // Removes the task and its simple child rows. Throws if the task has review history (approvals, comments, chat).
    public static function delete(int $taskId): void {
        $db = DB::getInstance();
        $db->beginTransaction();
        try {
            $db->execute('DELETE FROM TaskAssignment WHERE task_id = ?', [$taskId]);
            $db->execute('DELETE FROM TaskDependency WHERE task_id = ? OR depends_on_task_id = ?', [$taskId, $taskId]);
            $db->execute('DELETE FROM ProgressNote WHERE task_id = ?', [$taskId]);
            $db->execute('DELETE FROM RevisionRound WHERE task_id = ?', [$taskId]);
            $db->execute('DELETE FROM Task WHERE task_id = ?', [$taskId]);
            $db->commit();
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }
}