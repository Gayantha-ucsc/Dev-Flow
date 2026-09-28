<?php
// Expects: $task, $canEdit
$meta = taskStatusMeta($task['status']);
$assignees = $assignees ?? []; $dependsOn = $dependsOn ?? []; $blocks = $blocks ?? [];
$notes = $notes ?? []; $rounds = $rounds ?? []; $approvals = $approvals ?? [];
$fmt = fn($d) => $d ? htmlspecialchars(date('M j, Y H:i', strtotime($d))) : '-';
$taskLinks = function (array $rows) {
    if (!$rows) { return '<span class="td-muted">None</span>'; }
    $out = '';
    foreach ($rows as $r) {
        $m = taskStatusMeta($r['status']);
        $out .= '<a class="td-link" href="' . url('tasks/' . $r['task_id']) . '"><span>' . htmlspecialchars($r['name'])
              . '</span><span class="badge badge--' . $m['tone'] . '">' . htmlspecialchars($m['label']) . '</span></a>';
    }
    return $out;
};
$timeline = [];
foreach ($approvals as $a) { $timeline[] = ['kind' => 'decision', 'at' => $a['decided_at'], 'row' => $a]; }
?>
<div class="tasks-page"<?= !empty($task['is_mock']) ? ' data-mock-key="' . htmlspecialchars($task['mock_key']) . '"' : '' ?>>
    <div class="tasks-sticky">
        <a href="<?= url('tasks') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to tasks</a>
        <div class="tasks-header">
            <div>
                <h1 class="js-mock-name"><?= htmlspecialchars($task['name']) ?></h1>
                <p><span class="badge badge--<?= $meta['tone'] ?> badge--outline"><?= htmlspecialchars($meta['label']) ?></span>
                   <span class="role-chip"><?= renderIcon('flag') ?> <?= htmlspecialchars($task['stage_name']) ?></span>
                   <?php if (!empty($task['task_type'])): ?><span class="role-chip"><?= htmlspecialchars($task['task_type']) ?></span><?php endif; ?></p>
            </div>
            <?php if ($canEdit && !empty($task['is_mock'])): ?>
            <div class="tasks-header__right">
                <a href="<?= url('tasks/' . $task['task_id'] . '/edit') ?>" class="btn-sm"><?= renderIcon('pencil') ?> Edit</a>
                <button type="button" class="btn-sm btn-sm--danger-outline js-mock-delete-detail" data-task-name="<?= htmlspecialchars($task['name']) ?>"><?= renderIcon('trash-2') ?> Delete</button>
            </div>
            <?php elseif ($canEdit): ?>
            <div class="tasks-header__right">
                <a href="<?= url('tasks/' . $task['task_id'] . '/edit') ?>" class="btn-sm"><?= renderIcon('pencil') ?> Edit</a>
                <button type="button" class="btn-sm btn-sm--danger-outline js-delete-task" data-task-name="<?= htmlspecialchars($task['name']) ?>"
                        data-delete-url="<?= url('tasks/' . $task['task_id'] . '/delete') ?>"><?= renderIcon('trash-2') ?> Delete</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($task['blocked_reason'])): ?>
        <div class="td-alert"><?= renderIcon('ban') ?><div><strong>Blocked</strong><br><?= nl2br(htmlspecialchars($task['blocked_reason'])) ?></div></div>
    <?php endif; ?>

    <div class="td-layout">
        <div class="td-main">
            <div class="card">
                <div class="card__header"><h2 class="card__title"><?= renderIcon('file-text') ?> Description</h2></div>
                <div class="task-panel-body"><p class="td-text js-mock-desc"><?= nl2br(htmlspecialchars($task['description'] ?: 'No description.')) ?></p></div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title"><?= renderIcon('workflow') ?> Dependencies</h2></div>
                <div class="task-panel-body td-deps">
                    <div><h3 class="td-label">Depends on</h3><?= $taskLinks($dependsOn) ?></div>
                    <div><h3 class="td-label">Unlocks</h3><?= $taskLinks($blocks) ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title"><?= renderIcon('scroll-text') ?> Reviews &amp; Revisions</h2>
                    <span class="badge badge--neutral"><?= count($rounds) ?> round<?= count($rounds) === 1 ? '' : 's' ?></span></div>
                <div class="task-panel-body">
                    <?php if (!$approvals && !$rounds): ?><p class="td-muted">No review activity yet.</p><?php endif; ?>
                    <ul class="td-timeline">
                    <?php foreach ($rounds as $r): ?>
                        <li><span class="td-dot"></span><strong>Round <?= (int) $r['round_number'] ?></strong>
                            <span class="td-muted">started <?= $fmt($r['started_at']) ?><?= $r['closed_at'] ? ' · closed ' . $fmt($r['closed_at']) : ' · open' ?></span></li>
                    <?php endforeach; ?>
                    <?php foreach ($approvals as $a): $dm = approvalDecisionMeta($a['decision']); ?>
                        <li><span class="td-dot"></span>
                            <span class="badge badge--<?= $dm['tone'] ?>"><?= htmlspecialchars($dm['label']) ?></span>
                            <span>by <strong><?= htmlspecialchars($a['name']) ?></strong></span> <span class="td-muted"><?= $fmt($a['decided_at']) ?></span>
                            <?php if ($a['feedback']): ?><blockquote class="td-quote"><?= nl2br(htmlspecialchars($a['feedback'])) ?></blockquote><?php endif; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card__header"><h2 class="card__title"><?= renderIcon('chat') ?> Progress Notes</h2></div>
                <div class="task-panel-body">
                    <?php if (!$notes): ?><p class="td-muted">No progress notes yet.</p><?php endif; ?>
                    <ul class="td-timeline">
                    <?php foreach ($notes as $n): ?>
                        <li><span class="td-dot"></span><strong><?= htmlspecialchars($n['name']) ?></strong> <span class="td-muted"><?= $fmt($n['created_at']) ?></span>
                            <p class="td-text"><?= nl2br(htmlspecialchars($n['content'])) ?></p></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <aside class="td-side">
            <div class="card">
                <div class="card__header"><h2 class="card__title">Details</h2></div>
                <dl class="td-facts">
                    <dt>Status</dt><dd><span class="badge badge--<?= $meta['tone'] ?>"><?= htmlspecialchars($meta['label']) ?></span>
                        <small class="td-muted"><?= $task['acknowledged'] ? 'Acknowledged' : 'Not yet acknowledged' ?></small></dd>
                    <dt>Stage</dt><dd><?= htmlspecialchars($task['stage_name']) ?></dd>
                    <dt>Type</dt><dd><?= htmlspecialchars($task['task_type'] ?: '-') ?></dd>
                    <dt>Deadline</dt><dd><?= renderIcon('calendar') ?> <?= $task['deadline'] ? htmlspecialchars(date('M j, Y', strtotime($task['deadline']))) : '-' ?></dd>
                    <dt>Created by</dt><dd><?= htmlspecialchars($createdBy ?? '-') ?></dd>
                    <dt>Created</dt><dd><?= $fmt($task['created_at'] ?? null) ?></dd>
                    <dt>Updated</dt><dd><?= $fmt($task['updated_at'] ?? null) ?></dd>
                </dl>
            </div>
            <div class="card">
                <div class="card__header"><h2 class="card__title">Assignees</h2><span class="badge badge--neutral"><?= count($assignees) ?></span></div>
                <div class="task-panel-body">
                    <?php if (!$assignees): ?><p class="td-muted">Unassigned</p><?php endif; ?>
                    <?php foreach ($assignees as $a): ?>
                        <div class="td-person">
                            <span class="avatar avatar--<?= avatarColorClass($a['name']) ?>"><?= htmlspecialchars(initials($a['name'])) ?></span>
                            <div><strong><?= htmlspecialchars($a['name']) ?></strong><br><small class="td-muted"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $a['role']))) ?></small></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</div>

<div class="modal-overlay" data-modal="delete-task-modal" hidden id="deleteTaskModal">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form modal-box--confirm">
        <div class="modal-box__header">
            <h2>Delete Task</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">Permanently delete <strong id="deleteTaskName"></strong>? This cannot be undone, and any tasks depending on it may need their dependencies reviewed.</p>
            <form method="POST" id="deleteTaskForm">
                <?= csrfField() ?>
                <div class="modal-box__footer">
                    <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                    <button type="submit" class="btn-sm btn-sm--danger-outline">Delete task</button>
                </div>
            </form>
        </div>
    </div>
</div>
