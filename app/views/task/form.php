<?php
// Expects: $stages, $task (null on create), $mode
$isEdit = ($mode ?? 'create') === 'edit';
$action = $isEdit ? url('tasks/' . $task['task_id'] . '/update') : url('tasks');
?>
<div class="tasks-page task-form-page">
    <div class="tasks-sticky">
        <a href="<?= $isEdit ? url('tasks/' . $task['task_id']) : url('tasks') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back</a>
        <div class="tasks-header"><div>
            <h1><?= $isEdit ? 'Edit Task' : 'Create Task' ?></h1>
            <p><?= $isEdit ? 'Update the task details.' : 'Add a new task to a stage.' ?></p>
        </div></div>
    </div>

    <form class="task-form-layout" method="POST" action="<?= $action ?>">
        <?= csrfField() ?>
        <div class="card task-section">
            <div class="card__header"><h2 class="card__title"><?= renderIcon('tasks') ?> Task Details</h2></div>
            <div class="task-panel-body">
                <div class="form-group">
                    <label for="taskName">Name</label>
                    <input type="text" id="taskName" name="name" required maxlength="150"
                           value="<?= htmlspecialchars($task['name'] ?? '') ?>" placeholder="Task name">
                </div>
                <div class="form-group">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" name="description" rows="4"
                              placeholder="Describe the work to be done..."><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
                </div>
                <div class="task-form-row">
                    <div class="form-group">
                        <label for="taskStage">Stage</label>
                        <select id="taskStage" name="stage_id" required>
                            <?php foreach ($stages as $s): ?>
                                <option value="<?= (int) $s['stage_id'] ?>" <?= ($task['stage_id'] ?? null) == $s['stage_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="taskType">Type</label>
                        <input type="text" id="taskType" name="task_type" maxlength="50"
                               value="<?= htmlspecialchars($task['task_type'] ?? '') ?>" placeholder="e.g. frontend, design">
                    </div>
                    <div class="form-group">
                        <label for="taskDeadline">Deadline</label>
                        <input type="date" id="taskDeadline" name="deadline" value="<?= htmlspecialchars($task['deadline'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="task-form-footer">
            <span></span>
            <div class="task-form-footer__actions">
                <a href="<?= $isEdit ? url('tasks/' . $task['task_id']) : url('tasks') ?>" class="btn-sm">Cancel</a>
                <button type="submit" class="btn-add-member"><?= $isEdit ? 'Save Changes' : 'Create Task' ?></button>
            </div>
        </div>
    </form>
</div>