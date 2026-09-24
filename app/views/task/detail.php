<?php
// Expects: $task, $canEdit
$meta = taskStatusMeta($task['status']);
?>
<div class="tasks-page">
    <div class="tasks-sticky">
        <a href="<?= url('tasks') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to tasks</a>
        <div class="tasks-header">
            <div>
                <h1><?= htmlspecialchars($task['name']) ?></h1>
                <p><span class="badge badge--<?= $meta['tone'] ?> badge--outline"><?= htmlspecialchars($meta['label']) ?></span>
                   <span class="role-chip"><?= renderIcon('flag') ?> <?= htmlspecialchars($task['stage_name']) ?></span></p>
            </div>
            <?php if ($canEdit): ?>
            <div class="tasks-header__right">
                <a href="<?= url('tasks/' . $task['task_id'] . '/edit') ?>" class="btn-sm"><?= renderIcon('pencil') ?> Edit</a>
                <form method="POST" action="<?= url('tasks/' . $task['task_id'] . '/delete') ?>"
                      onsubmit="return confirm('Delete this task? This cannot be undone.')">
                    <?= csrfField() ?>
                    <button type="submit" class="btn-sm btn-sm--danger-outline"><?= renderIcon('trash-2') ?> Delete</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card task-section">
        <div class="task-panel-body">
            <p><strong>Description</strong><br><?= nl2br(htmlspecialchars($task['description'] ?? 'No description.')) ?></p>
            <p><strong>Type</strong><br><?= htmlspecialchars($task['task_type'] ?: '—') ?></p>
            <p><strong>Deadline</strong><br><?= $task['deadline'] ? htmlspecialchars(date('M j, Y', strtotime($task['deadline']))) : '—' ?></p>
            <p><strong>Created</strong><br><?= htmlspecialchars(date('M j, Y', strtotime($task['created_at']))) ?></p>
        </div>
    </div>
</div>