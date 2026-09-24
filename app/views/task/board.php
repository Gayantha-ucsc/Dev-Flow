<?php
// Expects: $tasks, $stages, $currentProjectName, $canEdit
$byStage = [];
foreach ($tasks as $t) { $byStage[$t['stage_id']][] = $t; }
?>
<div class="tasks-page">
    <div class="tasks-sticky">
        <div class="tasks-header">
            <div>
                <h1>Tasks</h1>
                <p>Tasks by stage for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
            </div>
            <div class="tasks-header__right">
                <?php if ($canEdit): ?>
                    <a href="<?= url('tasks/create') ?>" class="btn-add-member"><?= renderIcon('plus') ?> Create Task</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (empty($stages)): ?>
        <div class="card"><p class="form-hint">This project has no stages yet. Add a stage on the project page first.</p></div>
    <?php endif; ?>

    <?php foreach ($stages as $stage): $list = $byStage[$stage['stage_id']] ?? []; ?>
        <h2 class="card__title" style="margin:24px 0 8px"><?= renderIcon('flag') ?> <?= htmlspecialchars($stage['name']) ?>
            <span class="badge badge--neutral"><?= count($list) ?></span></h2>

        <?php if (empty($list)): ?>
            <div class="card"><p class="form-hint">No tasks in this stage.</p></div>
        <?php endif; ?>

        <div class="task-list">
        <?php foreach ($list as $task): $meta = taskStatusMeta($task['status']); ?>
            <div class="card task-row">
                <a href="<?= url('tasks/' . $task['task_id']) ?>" class="task-row__info" style="text-decoration:none;color:inherit">
                    <div class="task-row__title-line">
                        <h3 class="task-row__name"><?= htmlspecialchars($task['name']) ?></h3>
                        <span class="badge badge--<?= $meta['tone'] ?> badge--outline"><?= htmlspecialchars($meta['label']) ?></span>
                        <?php if (!empty($task['task_type'])): ?>
                            <span class="role-chip"><?= htmlspecialchars($task['task_type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="task-row__description"><?= htmlspecialchars(mb_strimwidth($task['description'] ?? '', 0, 100, '…')) ?></p>
                </a>
                <div class="task-row__meta">
                    <div class="task-row__meta-label">Deadline</div>
                    <div class="task-row__meta-value"><?= $task['deadline'] ? htmlspecialchars(date('M j, Y', strtotime($task['deadline']))) : '—' ?></div>
                </div>
                <?php if ($canEdit): ?>
                <div style="display:flex;gap:8px;align-items:center">
                    <a href="<?= url('tasks/' . $task['task_id'] . '/edit') ?>" class="btn-sm"><?= renderIcon('pencil') ?> Edit</a>
                    <form method="POST" action="<?= url('tasks/' . $task['task_id'] . '/delete') ?>"
                          onsubmit="return confirm('Delete this task? This cannot be undone.')">
                        <?= csrfField() ?>
                        <button type="submit" class="btn-sm btn-sm--danger-outline"><?= renderIcon('trash-2') ?> Delete</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>