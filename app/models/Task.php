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

    // Creates a task plus its assignees and dependencies in one transaction.
    // Locked if any prerequisite isn't approved yet, otherwise not_started.
    public static function createFull(array $data, array $assigneeIds, array $depIds): int {
        $db = DB::getInstance();
        $db->beginTransaction();
        try {
            $locked = false;
            foreach ($depIds as $depId) {
                $r = $db->query('SELECT status FROM Task WHERE task_id = ?', [$depId]);
                if (!$r || $r[0]['status'] !== 'approved') { $locked = true; }
            }
            $taskId = $db->insert('Task', [
                'stage_id'    => $data['stage_id'],
                'name'        => $data['name'],
                'description' => $data['description'],
                'created_by'  => $data['created_by'],
                'status'      => $locked ? 'locked' : 'not_started',
                'task_type'   => $data['task_type'],
                'deadline'    => $data['deadline'],
            ]);
            foreach ($assigneeIds as $pmId) {
                $db->insert('TaskAssignment', ['task_id' => $taskId, 'user_id' => $pmId, 'assigned_by' => $data['created_by']]);
            }
            foreach ($depIds as $depId) {
                $db->insert('TaskDependency', ['task_id' => $taskId, 'depends_on_task_id' => $depId, 'set_by' => $data['created_by']]);
            }
            $db->commit();
            return $taskId;
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }

    // Updates a task and replaces its assignees and dependencies. Returns an error string or null.
    public static function updateFull(int $taskId, array $data, array $assigneeIds, array $depIds, int $by): ?string {
        $db = DB::getInstance();

        // Reject circular dependencies: a prerequisite must not (transitively) depend on this task.
        foreach ($depIds as $depId) {
            if ($depId === $taskId) { return 'A task cannot depend on itself.'; }
            $seen = [$depId]; $queue = [$depId];
            while ($queue) {
                $cur = array_shift($queue);
                foreach ($db->query('SELECT depends_on_task_id AS d FROM TaskDependency WHERE task_id = ?', [$cur]) as $r) {
                    $n = (int) $r['d'];
                    if ($n === $taskId) { return 'That would create a circular dependency.'; }
                    if (!in_array($n, $seen, true)) { $seen[] = $n; $queue[] = $n; }
                }
            }
        }

        $db->beginTransaction();
        try {
            self::update($taskId, $data);
            $db->execute('DELETE FROM TaskAssignment WHERE task_id = ?', [$taskId]);
            $db->execute('DELETE FROM TaskDependency WHERE task_id = ?', [$taskId]);
            foreach ($assigneeIds as $pmId) {
                $db->insert('TaskAssignment', ['task_id' => $taskId, 'user_id' => $pmId, 'assigned_by' => $by]);
            }
            $unmet = false;
            foreach ($depIds as $depId) {
                $db->insert('TaskDependency', ['task_id' => $taskId, 'depends_on_task_id' => $depId, 'set_by' => $by]);
                $r = $db->query('SELECT status FROM Task WHERE task_id = ?', [$depId]);
                if (!$r || $r[0]['status'] !== 'approved') { $unmet = true; }
            }
            // Only re-evaluate lock state for tasks that haven't started.
            $db->execute("UPDATE Task SET status = ? WHERE task_id = ? AND status IN ('locked','not_started')",
                [$unmet ? 'locked' : 'not_started', $taskId]);
            $db->commit();
            return null;
        } catch (Throwable $e) {
            $db->rollback();
            return 'Could not update the task. Please try again.';
        }
    }

    public static function assigneeIds(int $taskId): array {
        return array_map('intval', array_column(DB::getInstance()->query('SELECT user_id FROM TaskAssignment WHERE task_id = ?', [$taskId]), 'user_id'));
    }

    public static function dependencyIds(int $taskId): array {
        return array_map('intval', array_column(DB::getInstance()->query('SELECT depends_on_task_id AS d FROM TaskDependency WHERE task_id = ?', [$taskId]), 'd'));
    }

    // Distinct task types already used in a project
    public static function typesForProject(int $projectId): array {
        $rows = DB::getInstance()->query(
            'SELECT DISTINCT t.task_type FROM Task t JOIN Stage s ON s.stage_id = t.stage_id
             WHERE s.project_id = ? AND t.task_type IS NOT NULL AND t.task_type <> \'\' ORDER BY t.task_type',
            [$projectId]
        );
        return array_column($rows, 'task_type');
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