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

    <form class="task-form-layout" method="POST" action="<?= $action ?>"<?= !empty($task['is_mock']) ? ' data-mock-key="' . htmlspecialchars($task['mock_key']) . '"' : '' ?>>
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
                <?php $types = $types ?? []; $members = $members ?? []; $allTasks = $allTasks ?? []; $curAssignees = $curAssignees ?? []; $curDeps = $curDeps ?? []; ?>
                <div class="form-group">
                    <label>Type suggestions</label>
                    <div class="tag-suggestions__chips" id="typeChips">
                        <?php foreach (($types ?: ['frontend', 'backend', 'design', 'testing']) as $ty): ?>
                            <button type="button" class="tag-chip" data-tag-chip="<?= htmlspecialchars($ty) ?>"><?= htmlspecialchars($ty) ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group__label-row">
                        <label>Assignees</label>
                        <span class="form-group__hint" id="assigneeCount">0 assigned</span>
                    </div>
                    <div class="assignee-chips" id="assigneeChips"></div>
                    <div class="dropdown" data-dropdown>
                        <button type="button" class="assignee-add-btn" data-dropdown-trigger>
                            <?= renderIcon('user-plus') ?> Add assignee
                        </button>
                        <div class="dropdown__menu assignee-add-menu" data-dropdown-menu id="assigneeMenu">
                            <?php foreach ($members as $m): ?>
                                <button type="button" class="assignee-option" data-id="<?= (int) $m['project_member_id'] ?>" data-name="<?= htmlspecialchars($m['name']) ?>">
                                    <span class="avatar avatar--sm avatar--<?= avatarColorClass($m['name']) ?>"><?= htmlspecialchars(initials($m['name'])) ?></span>
                                    <?= htmlspecialchars($m['name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group__label-row"><label>Depends on (optional)</label></div>
                    <div class="dependency-chips" id="depChips"></div>
                    <div class="dropdown" data-dropdown>
                        <button type="button" class="dependency-add-btn" data-dropdown-trigger>
                            <?= renderIcon('user-plus') ?> Add dependency
                        </button>
                        <div class="dropdown__menu dependency-add-menu" data-dropdown-menu id="depMenu">
                            <?php foreach ($allTasks as $t): ?>
                                <button type="button" class="dependency-option" data-id="<?= (int) $t['task_id'] ?>" data-name="<?= htmlspecialchars($t['name']) ?>" data-stage="<?= (int) $t['stage_id'] ?>">
                                    <span class="dependency-option__name"><?= htmlspecialchars($t['name']) ?></span>
                                </button>
                            <?php endforeach; ?>
                            <p class="form-group__hint" id="depEmpty" style="padding:8px 12px" hidden>No tasks available in this stage.</p>
                        </div>
                    </div>
                    <p class="form-group__hint">Only tasks in the chosen stage. This task stays locked until all dependencies are approved.</p>
                </div>
                <div id="pickInputs"></div>
                <script>
                (function () {
                    var stage = document.getElementById('taskStage');
                    var initA = <?= json_encode(array_map('strval', $curAssignees)) ?>, initD = <?= json_encode(array_map('strval', $curDeps)) ?>;
                    var picked = { assignees: [], deps: [] };
                    document.querySelectorAll('#assigneeMenu .assignee-option').forEach(function (o) {
                        if (initA.indexOf(o.dataset.id) !== -1) picked.assignees.push({ id: o.dataset.id, name: o.dataset.name });
                    });
                    document.querySelectorAll('#depMenu .dependency-option').forEach(function (o) {
                        if (initD.indexOf(o.dataset.id) !== -1) picked.deps.push({ id: o.dataset.id, name: o.dataset.name });
                    });
                    var aMenu = document.getElementById('assigneeMenu'), dMenu = document.getElementById('depMenu');
                    var aChips = document.getElementById('assigneeChips'), dChips = document.getElementById('depChips');
                    var inputs = document.getElementById('pickInputs');
                    function esc(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

                    function render() {
                        aChips.innerHTML = ''; dChips.innerHTML = ''; inputs.innerHTML = '';
                        picked.assignees.forEach(function (p) {
                            aChips.insertAdjacentHTML('beforeend', '<span class="assignee-chip"><span>' + esc(p.name) + '</span><button type="button" class="assignee-chip__remove" data-rm="assignees" data-id="' + p.id + '">&times;</button></span>');
                            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="assignees[]" value="' + p.id + '">');
                        });
                        picked.deps.forEach(function (p) {
                            dChips.insertAdjacentHTML('beforeend', '<span class="dependency-chip"><span class="dependency-chip__name">' + esc(p.name) + '</span><button type="button" class="dependency-chip__remove" data-rm="deps" data-id="' + p.id + '">&times;</button></span>');
                            inputs.insertAdjacentHTML('beforeend', '<input type="hidden" name="dependencies[]" value="' + p.id + '">');
                        });
                        document.getElementById('assigneeCount').textContent = picked.assignees.length + ' assigned';
                        aMenu.querySelectorAll('.assignee-option').forEach(function (o) {
                            o.hidden = picked.assignees.some(function (p) { return p.id === o.dataset.id; });
                        });
                        var shown = 0;
                        dMenu.querySelectorAll('.dependency-option').forEach(function (o) {
                            var hide = o.dataset.stage !== stage.value || picked.deps.some(function (p) { return p.id === o.dataset.id; });
                            o.hidden = hide;
                            o.style.display = hide ? 'none' : '';
                            if (!hide) shown++;
                        });
                        document.getElementById('depEmpty').hidden = shown > 0;
                    }
                    function pick(menu, list, e, sel) {
                        var o = e.target.closest(sel);
                        if (!o) return;
                        list.push({ id: o.dataset.id, name: o.dataset.name });
                        var dd = menu.closest('[data-dropdown]');
                        if (dd) dd.classList.remove('is-open');
                        render();
                    }
                    aMenu.addEventListener('click', function (e) { pick(aMenu, picked.assignees, e, '.assignee-option'); });
                    dMenu.addEventListener('click', function (e) { pick(dMenu, picked.deps, e, '.dependency-option'); });
                    document.addEventListener('click', function (e) {
                        var b = e.target.closest('[data-rm]');
                        if (!b) return;
                        picked[b.dataset.rm] = picked[b.dataset.rm].filter(function (p) { return p.id !== b.dataset.id; });
                        render();
                    });
                    stage.addEventListener('change', function () { picked.deps = []; render(); });
                    document.getElementById('typeChips').addEventListener('click', function (e) {
                        var c = e.target.closest('.tag-chip');
                        if (!c) return;
                        document.getElementById('taskType').value = c.dataset.tagChip;
                        this.querySelectorAll('.tag-chip').forEach(function (x) { x.classList.toggle('is-selected', x === c); });
                    });
                    render();
                })();
                </script>
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