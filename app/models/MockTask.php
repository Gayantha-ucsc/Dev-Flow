<?php

class MockTask {

    private const EDITS_KEY   = 'mock_task_edits';
    private const DELETED_KEY = 'mock_task_deleted';

    public static function isMockId($id): bool {
        return is_string($id) && preg_match('/^m\d+-\d+-\d+$/', $id) === 1;
    }

    public static function forProject(int $projectId, array $stages): array {
        $source  = (require __DIR__ . '/../../config/mock/project-tasks.php')[$projectId] ?? [];
        $deleted = Session::get(self::DELETED_KEY, []);
        $edits   = Session::get(self::EDITS_KEY, []);
        $stages  = array_values($stages);

        $tasks = [];
        $idByName = [];
        foreach ($stages as $si => $stage) {
            foreach ($source[$si] ?? [] as $ti => $m) {
                $id = "m{$projectId}-{$si}-{$ti}";
                $idByName[$m['name']] = $id;
                $tasks[$id] = [
                    'task_id'        => $id,
                    'is_mock'        => true,
                    'project_id'     => $projectId,
                    'stage_id'       => (int) $stage['stage_id'],
                    'stage_name'     => $stage['name'],
                    'name'           => $m['name'],
                    'description'    => self::describe($m),
                    'task_type'      => $m['type'] ?? null,
                    'status'         => $m['status'],
                    'deadline'       => $m['deadline'] ?? null,
                    'blocked_reason' => $m['status'] === 'blocked' ? self::blockedReason($m) : null,
                    'acknowledged'   => !in_array($m['status'], ['locked', 'not_started'], true),
                    'created_at'     => date('Y-m-d 09:00:00', strtotime(($m['deadline'] ?? 'now') . ' -14 days')),
                    'updated_at'     => date('Y-m-d 16:30:00', strtotime(($m['deadline'] ?? 'now') . ' -2 days')),
                    'assignee_names' => $m['assignees'] ?? [],
                    'dep_ids'        => [],
                    '_dep_names'     => $m['dependsOn'] ?? [],
                ];
            }
        }
        foreach ($tasks as $id => &$t) {
            foreach ($t['_dep_names'] as $n) {
                if (isset($idByName[$n])) { $t['dep_ids'][] = $idByName[$n]; }
            }
            unset($t['_dep_names']);
        }
        unset($t);

        // Pass 2: session edits, then deletions.
        foreach ($edits as $id => $e) {
            if (isset($tasks[$id])) { $tasks[$id] = array_merge($tasks[$id], $e); }
        }
        foreach ($deleted as $id) { unset($tasks[$id]); }
        foreach ($tasks as $id => &$t) {
            $t['dep_ids'] = array_values(array_filter($t['dep_ids'], fn($d) => isset($tasks[$d])));
        }
        unset($t);

        return array_values($tasks);
    }

    // One mock task by id, or null (unknown or deleted).
    public static function find(string $id, array $stages): ?array {
        if (!self::isMockId($id)) { return null; }
        $projectId = (int) substr($id, 1, strpos($id, '-') - 1);
        foreach (self::forProject($projectId, $stages) as $t) {
            if ($t['task_id'] === $id) { return $t; }
        }
        return null;
    }

    public static function projectIdOf(string $id): int {
        return self::isMockId($id) ? (int) substr($id, 1, strpos($id, '-') - 1) : 0;
    }

    // Saves an edit (name, description, stage, type, deadline, assignee names, dependency ids).
    // Returns an error string or null.
    public static function update(string $id, array $stages, array $data, array $assigneeNames, array $depIds): ?string {
        $all = self::forProject(self::projectIdOf($id), $stages);
        $byId = [];
        foreach ($all as $t) { $byId[$t['task_id']] = $t; }
        if (!isset($byId[$id])) { return 'That task no longer exists.'; }

        $depIds = array_values(array_unique(array_filter($depIds, fn($d) => isset($byId[$d]) && $d !== $id)));

        // Reject circular dependencies (a prerequisite must not lead back to this task).
        foreach ($depIds as $d) {
            $seen = [$d]; $queue = [$d];
            while ($queue) {
                $cur = array_shift($queue);
                foreach ($byId[$cur]['dep_ids'] ?? [] as $n) {
                    if ($n === $id) { return 'That would create a circular dependency.'; }
                    if (!in_array($n, $seen, true)) { $seen[] = $n; $queue[] = $n; }
                }
            }
        }

        $stageName = $byId[$id]['stage_name'];
        foreach ($stages as $s) {
            if ((int) $s['stage_id'] === (int) $data['stage_id']) { $stageName = $s['name']; }
        }

        // Re-evaluate lock state the same way the real Task model does for unstarted tasks.
        $status = $byId[$id]['status'];
        if (in_array($status, ['locked', 'not_started'], true)) {
            $unmet = false;
            foreach ($depIds as $d) { if ($byId[$d]['status'] !== 'approved') { $unmet = true; } }
            $status = $unmet ? 'locked' : 'not_started';
        }

        $edits = Session::get(self::EDITS_KEY, []);
        $edits[$id] = [
            'name'           => $data['name'],
            'description'    => $data['description'] ?? '',
            'stage_id'       => (int) $data['stage_id'],
            'stage_name'     => $stageName,
            'task_type'      => $data['task_type'],
            'deadline'       => $data['deadline'],
            'assignee_names' => array_values($assigneeNames),
            'dep_ids'        => $depIds,
            'status'         => $status,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];
        Session::set(self::EDITS_KEY, $edits);
        return null;
    }

    public static function delete(string $id): void {
        $deleted = Session::get(self::DELETED_KEY, []);
        if (!in_array($id, $deleted, true)) { $deleted[] = $id; }
        Session::set(self::DELETED_KEY, $deleted);
    }

    // Everything the detail page needs beyond the task row itself, in the same
    // shape TaskController::detail() builds from the database for real tasks.
    public static function detailExtras(array $task, array $stages): array {
        $all  = self::forProject((int) $task['project_id'], $stages);
        $byId = [];
        foreach ($all as $t) { $byId[$t['task_id']] = $t; }

        $people = self::projectPeople((int) $task['project_id']);
        $lead   = $people['team_lead'] ?? ($people['manager'] ?? 'Team Lead');

        $roleOf = function (string $name) use ($people): string {
            foreach ($people as $role => $n) { if ($n === $name) { return $role; } }
            return 'developer';
        };

        $assignees = [];
        foreach ($task['assignee_names'] as $n) { $assignees[] = ['name' => $n, 'role' => $roleOf($n)]; }
        usort($assignees, fn($a, $b) => strcmp($a['name'], $b['name']));

        $dependsOn = [];
        foreach ($task['dep_ids'] as $d) {
            $dependsOn[] = ['task_id' => $d, 'name' => $byId[$d]['name'], 'status' => $byId[$d]['status']];
        }
        $blocks = [];
        foreach ($all as $t) {
            if (in_array($task['task_id'], $t['dep_ids'], true)) {
                $blocks[] = ['task_id' => $t['task_id'], 'name' => $t['name'], 'status' => $t['status']];
            }
        }

        $worker = $task['assignee_names'][0] ?? 'Team member';
        [$notes, $rounds, $approvals] = self::history($task, $worker, $lead);

        return [
            'createdBy' => $lead,
            'assignees' => $assignees,
            'dependsOn' => $dependsOn,
            'blocks'    => $blocks,
            'notes'     => $notes,
            'rounds'    => $rounds,
            'approvals' => $approvals,
        ];
    }

    // ---- generated demo content -------------------------------------------------

    private static function projectPeople(int $projectId): array {
        $rows = DB::getInstance()->query(
            "SELECT pm.role, u.name FROM ProjectMember pm JOIN User u ON u.user_id = pm.user_id
             WHERE pm.project_id = ? AND pm.is_active = 1 ORDER BY pm.project_member_id",
            [$projectId]
        );
        $out = [];
        foreach ($rows as $r) { $out[$r['role']] ??= $r['name']; }
        return $out;
    }

    private static function describe(array $m): string {
        $type = $m['type'] ?? 'general';
        $lead = [
            'research' => 'Research and document findings for',
            'design'   => 'Design and prepare deliverables for',
            'review'   => 'Coordinate the client review of',
            'backend'  => 'Implement the server-side work for',
            'frontend' => 'Build the user-facing side of',
            'devops'   => 'Set up and verify infrastructure for',
            'qa'       => 'Plan and run quality checks for',
            'security' => 'Assess and report on',
            'delivery' => 'Prepare the handoff material for',
        ][$type] ?? 'Complete the work for';
        return $lead . ' "' . $m['name'] . '" and record the outcome so the next task can start.';
    }

    private static function blockedReason(array $m): string {
        $pool = [
            'Waiting on a decision from the client before this can continue.',
            'Depends on access credentials that have not been provided yet.',
            'Waiting for the upstream task to be reworked before this can proceed.',
        ];
        return $pool[crc32($m['name']) % count($pool)];
    }

    // Deterministic notes, revision rounds and review decisions derived from the task status.
    private static function history(array $task, string $worker, string $lead): array {
        $seed   = crc32($task['name']);
        $status = $task['status'];
        $base   = strtotime(($task['deadline'] ?? date('Y-m-d')) . ' 10:00:00');
        $at     = fn(int $days, int $hour = 10) => date('Y-m-d H:i:s', strtotime(date('Y-m-d', $base + $days * 86400) . " $hour:00:00"));

        $noteText = [
            'Started on this today. Outline is in place and the first pass is under way.',
            'Made good progress, the main structure is done. Polishing the remaining parts.',
            'Checked the approach against the earlier decisions. No changes needed so far.',
            'Clarified a couple of open questions with the team lead. Continuing.',
        ];
        $feedback = [
            'Good work. Everything matches the agreed scope.',
            'Looks complete and consistent with the earlier stages. Approved.',
            'Clear and well documented. Nothing further needed.',
        ];
        $changes = [
            'A few sections are unclear. Please tighten them up and resubmit.',
            'Some edge cases are missing. Please cover them before the next review.',
            'Please align this with the earlier decisions on naming and structure.',
        ];

        $notes = [];
        $rounds = [];
        $approvals = [];

        if (in_array($status, ['in_progress', 'pending_review', 'blocked', 'overdue', 'approved'], true)) {
            $notes[] = ['name' => $worker, 'created_at' => $at(-12), 'content' => $noteText[$seed % 4]];
            if ($status !== 'approved') {
                $notes[] = ['name' => $worker, 'created_at' => $at(-6), 'content' => $noteText[($seed + 1) % 4]];
            }
        }
        if ($status === 'blocked') {
            $notes[] = ['name' => $lead, 'created_at' => $at(-3), 'content' => 'Marked as blocked: ' . ($task['blocked_reason'] ?? 'waiting on an external dependency') ];
        }

        $reworked = ($seed % 3 === 0);

        if ($status === 'approved') {
            if ($reworked) {
                $rounds[]    = ['round_number' => 1, 'started_at' => $at(-9), 'closed_at' => $at(-8)];
                $approvals[] = ['decision' => 'changes_requested', 'feedback' => $changes[$seed % 3], 'decided_at' => $at(-8), 'name' => $lead];
                $rounds[]    = ['round_number' => 2, 'started_at' => $at(-6), 'closed_at' => $at(-5)];
                $approvals[] = ['decision' => 'approved', 'feedback' => $feedback[$seed % 3], 'decided_at' => $at(-5), 'name' => $lead];
            } else {
                $rounds[]    = ['round_number' => 1, 'started_at' => $at(-6), 'closed_at' => $at(-5)];
                $approvals[] = ['decision' => 'approved', 'feedback' => $feedback[$seed % 3], 'decided_at' => $at(-5), 'name' => $lead];
            }
        } elseif ($status === 'pending_review') {
            $n = 1;
            if ($reworked) {
                $rounds[]    = ['round_number' => 1, 'started_at' => $at(-8), 'closed_at' => $at(-7)];
                $approvals[] = ['decision' => 'changes_requested', 'feedback' => $changes[$seed % 3], 'decided_at' => $at(-7), 'name' => $lead];
                $n = 2;
            }
            $rounds[] = ['round_number' => $n, 'started_at' => $at(-2), 'closed_at' => null];
        } elseif ($status === 'in_progress' && $reworked) {
            $rounds[]    = ['round_number' => 1, 'started_at' => $at(-8), 'closed_at' => $at(-7)];
            $approvals[] = ['decision' => 'changes_requested', 'feedback' => $changes[$seed % 3], 'decided_at' => $at(-7), 'name' => $lead];
            $rounds[]    = ['round_number' => 2, 'started_at' => $at(-5), 'closed_at' => null];
        }

        return [$notes, $rounds, $approvals];
    }
}
