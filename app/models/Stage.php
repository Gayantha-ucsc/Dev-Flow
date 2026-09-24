<?php

class Stage {

    // stage by id. null if doesn't exist.
    public static function findById(int $stageId): ?array {
        return DB::getInstance()->select('Stage', ['stage_id' => $stageId]);
    }

    // All stages on a project, in display order .
    public static function listByProject(int $projectId): array {
        return DB::getInstance()->query(
            'SELECT * FROM Stage WHERE project_id = ? ORDER BY sequence_order ASC',
            [$projectId]
        );
    }

    public static function belongsToProject(int $stageId, int $projectId): bool {
        return DB::getInstance()->exists('Stage', ['stage_id' => $stageId, 'project_id' => $projectId]);
    }

    // How many tasks in this stage.
    public static function taskCount(int $stageId): int {
        return DB::getInstance()->count('Task', ['stage_id' => $stageId]);
    }

    private static function nextSequenceOrder(int $projectId): int {
        $rows = DB::getInstance()->query(
            'SELECT COALESCE(MAX(sequence_order), 0) AS max_order FROM Stage WHERE project_id = ?',
            [$projectId]
        );
        return ((int) ($rows[0]['max_order'] ?? 0)) + 1;
    }

    // add a stage to a project. 
    // New stages always land at the end of the order.
    public static function create(int $projectId, string $name): int {
        return DB::getInstance()->insert('Stage', [
            'project_id'     => $projectId,
            'name'           => $name,
            'sequence_order' => self::nextSequenceOrder($projectId),
            'status'         => 'not_started',
        ]);
    }

    // rename a stage.
    public static function rename(int $stageId, string $name): bool {
        return DB::getInstance()->update('Stage', ['name' => $name], ['stage_id' => $stageId]) > 0;
    }

    // delete a stage. 
    // should check taskCount() other wise will fail
    public static function delete(int $stageId): bool {
        return DB::getInstance()->delete('Stage', ['stage_id' => $stageId]) > 0;
    }

    // move a stage one position up or down
    public static function move(int $stageId, string $direction): bool {
        if (!in_array($direction, ['up', 'down'], true)) {
            return false;
        }

        $stage = self::findById($stageId);
        if (!$stage) {
            return false;
        }

        $projectId    = (int) $stage['project_id'];
        $currentOrder = (int) $stage['sequence_order'];

        if ($direction === 'up') {
            $comparator = '<';
            $orderBy    = 'DESC';
        } else {
            $comparator = '>';
            $orderBy    = 'ASC';
        }

        $db = DB::getInstance();

        $neighbours = $db->query(
            "SELECT stage_id, sequence_order FROM Stage
             WHERE project_id = ? AND sequence_order {$comparator} ?
             ORDER BY sequence_order {$orderBy} LIMIT 1",
            [$projectId, $currentOrder]
        );

        if (empty($neighbours)) {
            return false; // already first or last
        }
        $neighbour = $neighbours[0];

        $db->beginTransaction();
        try {
            $db->update('Stage', ['sequence_order' => -1], ['stage_id' => $stageId]);
            $db->update('Stage', ['sequence_order' => $currentOrder], ['stage_id' => $neighbour['stage_id']]);
            $db->update('Stage', ['sequence_order' => $neighbour['sequence_order']], ['stage_id' => $stageId]);
            $db->commit();
            return true;
        } catch (Throwable $e) {
            $db->rollback();
            return false;
        }
    }
}
