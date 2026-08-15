<?php
// Expects: $tasks, $stages, $teamMembers, $taskPerms, $mode, $task (edit)
$isEdit = ($mode ?? 'create') === 'edit';
$formTask = $task ?? null;
$prereqOptions = array_filter($tasks ?? [], fn($t) => !$isEdit || $t['id'] !== ($formTask['id'] ?? 0));
?>
<div class="tasks-page task-form-page">

    <div class="tasks-sticky">
        <a href="<?= $isEdit ? url('tasks/' . $formTask['id']) : url('tasks') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back</a>
        <div class="tasks-header">
            <div>
                <h1><?= $isEdit ? 'Edit Task' : 'Create Task' ?></h1>
                <p><?= $isEdit ? 'Update task details, assignees, and dependencies.' : 'Define a new task for the current project stage.' ?></p>
            </div>
            <span class="role-chip"><?= renderIcon('user') ?> <?= htmlspecialchars($taskPerms['roleLabel']) ?></span>
        </div>
    </div>

    <form class="task-form-layout" id="taskForm">

        <!-- PERM: canCreateTask / canEditTask -->
        <div class="card task-section" data-task-perm="<?= $isEdit ? 'canEditTask' : 'canCreateTask' ?>">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('tasks') ?> Task Details</h2>
                <span class="task-perm-badge">Team Lead</span>
            </div>
            <div class="task-panel-body">
                <div class="form-group">
                    <label for="taskTitle">Title</label>
                    <input type="text" id="taskTitle" name="title" required
                           value="<?= htmlspecialchars($formTask['title'] ?? '') ?>"
                           placeholder="Task title">
                </div>
                <div class="form-group">
                    <label for="taskDescription">Description</label>
                    <textarea id="taskDescription" name="description" rows="4"
                              placeholder="Describe the work to be done..."><?= htmlspecialchars($formTask['description'] ?? '') ?></textarea>
                </div>
                <div class="task-form-row">
                    <div class="form-group">
                        <label for="taskStage">Stage</label>
                        <select id="taskStage" name="stageId" required>
                            <?php foreach ($stages as $stage): ?>
                                <option value="<?= (int) $stage['id'] ?>"
                                    <?= ($formTask['stageId'] ?? '') == $stage['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($stage['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="taskPriority">Priority</label>
                        <select id="taskPriority" name="priority">
                            <?php foreach (['low', 'medium', 'high'] as $p): ?>
                                <option value="<?= $p ?>" <?= ($formTask['priority'] ?? 'medium') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="taskDue">Due Date</label>
                        <input type="date" id="taskDue" name="dueDate"
                               value="<?= htmlspecialchars($formTask['dueDate'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- PERM: canAssign -->
        <div class="card task-section" data-task-perm="canAssign">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user') ?> Assignees</h2>
                <span class="task-perm-badge">Team Lead</span>
            </div>
            <div class="task-panel-body">
                <p class="form-hint">Select one or more team members for this task.</p>
                <div class="picker-list" id="assigneePicker">
                    <?php
                        $selectedIds = array_column($formTask['assignees'] ?? [], 'user_id');
                        foreach ($teamMembers ?? [] as $member):
                            if (in_array('client', $member['roles'], true)) continue;
                            $checked = in_array($member['user_id'], $selectedIds, true);
                    ?>
                        <label class="picker-item">
                            <input type="checkbox" name="assignees[]" value="<?= (int) $member['user_id'] ?>" <?= $checked ? 'checked' : '' ?>>
                            <span class="avatar avatar--<?= avatarColorClass($member['name']) ?>"><?= htmlspecialchars(strtoupper(substr($member['name'], 0, 1))) ?></span>
                            <span class="picker-item__label"><?= htmlspecialchars($member['name']) ?></span>
                            <span class="badge badge--<?= memberRoleTone($member['roles'][0]) ?>"><?= memberRoleLabel($member['roles'][0]) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- PERM: canManageDependencies -->
        <div class="card task-section" data-task-perm="canManageDependencies">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('grip-vertical') ?> Dependencies</h2>
                <span class="task-perm-badge">Team Lead</span>
            </div>
            <div class="task-panel-body">
                <p class="form-hint">Prerequisite tasks that must finish before this one starts.</p>
                <div class="picker-list" id="dependencyPicker">
                    <?php if (empty($prereqOptions)): ?>
                        <p class="form-hint">No other tasks available.</p>
                    <?php else: ?>
                        <?php foreach ($prereqOptions as $opt): ?>
                            <?php $depChecked = in_array($opt['id'], $formTask['dependencies'] ?? [], true); ?>
                            <label class="picker-item">
                                <input type="checkbox" name="dependencies[]" value="<?= (int) $opt['id'] ?>" <?= $depChecked ? 'checked' : '' ?>>
                                <span class="picker-item__label"><?= htmlspecialchars($opt['title']) ?></span>
                                <span class="badge badge--<?= taskStatusTone($opt['status']) ?>"><?= taskStatusLabel($opt['status']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="task-form-footer">
            <?php if ($isEdit && $taskPerms['canDeleteTask']): ?>
                <button type="button" class="btn-sm btn-sm--danger-outline" id="deleteTaskBtn"><?= renderIcon('trash-2') ?> Delete Task</button>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
            <div class="task-form-footer__actions">
                <a href="<?= $isEdit ? url('tasks/' . $formTask['id']) : url('tasks') ?>" class="btn-sm">Cancel</a>
                <button type="submit" class="btn-add-member"><?= $isEdit ? 'Save Changes' : 'Create Task' ?></button>
            </div>
        </div>
    </form>
</div>

<?php if ($isEdit): ?>
<div class="modal-overlay" id="deleteTaskModal" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Delete Task</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">Delete <strong><?= htmlspecialchars($formTask['title']) ?></strong>? This cannot be undone.</p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="button" class="btn-sm btn-sm--danger-outline" id="confirmDeleteTask">Delete Task</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
