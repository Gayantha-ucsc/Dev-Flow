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
        <div class="stage-group">
            <h2 class="stage-group__title"><?= renderIcon('flag') ?> <?= htmlspecialchars($stage['name']) ?>
                <span class="badge badge--neutral"><?= count($list) ?></span></h2>

            <?php if (empty($list)): ?>
                <div class="task-list__empty-state">
                    <?= renderIcon('tasks') ?>
                    <p>No tasks in this stage yet.</p>
                </div>
            <?php else: ?>
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
                    <div class="task-row__actions">
                        <a href="<?= url('tasks/' . $task['task_id'] . '/edit') ?>" class="btn-sm"><?= renderIcon('pencil') ?> Edit</a>
                        <button type="button" class="btn-sm btn-sm--danger-outline js-delete-task"
                                data-task-id="<?= (int) $task['task_id'] ?>"
                                data-task-name="<?= htmlspecialchars($task['name']) ?>"
                                data-delete-url="<?= url('tasks/' . $task['task_id'] . '/delete') ?>">
                            <?= renderIcon('trash-2') ?> Delete
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="modal-overlay" data-modal="delete-task-modal" hidden id="deleteTaskModal">
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form modal-box--confirm">
        <div class="modal-box__header">
            <h2>Delete Task</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">
                Permanently delete <strong id="deleteTaskName"></strong>? This cannot be undone, and any tasks
                depending on it may need their dependencies reviewed.
            </p>
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
