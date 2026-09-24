<?php

class PaymentMilestone {

    public static function findById(int $id): ?array {
        return DB::getInstance()->select('PaymentMilestone', ['payment_milestone_id' => $id]);
    }

    // All milestones for a project (with linked stage name and latest payment record), by due date.
    public static function listByProject(int $projectId): array {
        return DB::getInstance()->query(
            'SELECT m.*, s.name AS stage_name,
                    p.gateway_reference, p.amount_paid, p.status AS payment_status, p.paid_at
             FROM PaymentMilestone m
             LEFT JOIN Stage s ON s.stage_id = m.stage_id
             LEFT JOIN Payment p ON p.payment_milestone_id = m.payment_milestone_id
             WHERE m.project_id = ?
             ORDER BY m.due_date ASC, m.payment_milestone_id ASC',
            [$projectId]
        );
    }

    public static function create(int $projectId, ?int $stageId, int $createdBy, string $description, float $amount, string $dueDate): int {
        return DB::getInstance()->insert('PaymentMilestone', [
            'project_id'  => $projectId,
            'stage_id'    => $stageId,
            'created_by'  => $createdBy,
            'description' => $description,
            'amount'      => $amount,
            'due_date'    => $dueDate,
            'status'      => 'pending',
        ]);
    }

    // Only pending milestones may be edited.
    public static function update(int $id, ?int $stageId, string $description, float $amount, string $dueDate): bool {
        return DB::getInstance()->execute(
            "UPDATE PaymentMilestone SET stage_id = ?, description = ?, amount = ?, due_date = ?
             WHERE payment_milestone_id = ? AND status = 'pending'",
            [$stageId, $description, $amount, $dueDate, $id]
        ) > 0;
    }

    // Only pending milestones may be deleted.
    public static function delete(int $id): bool {
        return DB::getInstance()->execute(
            "DELETE FROM PaymentMilestone WHERE payment_milestone_id = ? AND status = 'pending'",
            [$id]
        ) > 0;
    }

    public static function markRequested(int $id): bool {
        return DB::getInstance()->execute(
            "UPDATE PaymentMilestone SET status = 'requested' WHERE payment_milestone_id = ? AND status = 'pending'",
            [$id]
        ) > 0;
    }
}