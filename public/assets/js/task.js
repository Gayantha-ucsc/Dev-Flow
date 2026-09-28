document.addEventListener('DOMContentLoaded', function () {

    /* ---- Task board filters ---- */
    var taskList = document.getElementById('taskList');
    if (taskList) {
        var taskSearch = document.getElementById('taskSearch');
        var statusFilters = document.getElementById('taskStatusFilters');
        var stageFilter = document.getElementById('stageFilter');
        var taskEmpty = document.getElementById('taskListEmpty');
        var rows = Array.prototype.slice.call(taskList.querySelectorAll('.task-row'));
        var activeStatus = 'all';

        function applyTaskFilters() {
            var q = (taskSearch ? taskSearch.value : '').trim().toLowerCase();
            var stage = stageFilter ? stageFilter.value : 'all';
            var visible = 0;
            rows.forEach(function (row) {
                var matchQ = !q || row.dataset.title.indexOf(q) !== -1;
                var matchS = activeStatus === 'all' || row.dataset.status === activeStatus;
                var matchStage = stage === 'all' || row.dataset.stage === stage;
                var show = matchQ && matchS && matchStage;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (taskEmpty) taskEmpty.hidden = visible !== 0;
        }

        if (taskSearch) taskSearch.addEventListener('input', applyTaskFilters);
        if (stageFilter) stageFilter.addEventListener('change', applyTaskFilters);
        if (statusFilters) {
            statusFilters.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-filter]');
                if (!btn) return;
                statusFilters.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
                btn.classList.add('is-active');
                activeStatus = btn.dataset.filter;
                applyTaskFilters();
            });
        }
    }

    /* ---- Task form ---- */
    var taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var isEdit = document.getElementById('deleteTaskBtn');
            if (window.showToast) {
                window.showToast('success', isEdit ? 'Task updated successfully.' : 'Task created successfully.');
            }
        });
    }

    var deleteBtn = document.getElementById('deleteTaskBtn');
    var deleteModal = document.getElementById('deleteTaskModal');
    if (deleteBtn && deleteModal) {
        deleteBtn.addEventListener('click', function () {
            deleteModal.removeAttribute('hidden');
            requestAnimationFrame(function () { deleteModal.classList.add('is-open'); });
        });
        deleteModal.querySelectorAll('[data-modal-close]').forEach(function (el) {
            el.addEventListener('click', function () {
                deleteModal.classList.remove('is-open');
                setTimeout(function () { deleteModal.setAttribute('hidden', ''); }, 200);
            });
        });
        var confirmDelete = document.getElementById('confirmDeleteTask');
        if (confirmDelete) {
            confirmDelete.addEventListener('click', function () {
                if (window.showToast) window.showToast('success', 'Task deleted.');
            });
        }
    }

    /* ---- Task board: delete-task confirmation modal ---- */
    var taskDeleteModal = document.getElementById('deleteTaskModal');
    if (taskDeleteModal) {
        var taskDeleteForm = document.getElementById('deleteTaskForm');
        var taskDeleteName = document.getElementById('deleteTaskName');

        function openTaskDeleteModal() {
            taskDeleteModal.removeAttribute('hidden');
            requestAnimationFrame(function () { taskDeleteModal.classList.add('is-open'); });
        }
        function closeTaskDeleteModal() {
            taskDeleteModal.classList.remove('is-open');
            setTimeout(function () { taskDeleteModal.setAttribute('hidden', ''); }, 200);
        }

        document.querySelectorAll('.js-delete-task').forEach(function (btn) {
            btn.addEventListener('click', function () {
                taskDeleteName.textContent = btn.dataset.taskName || 'this task';
                taskDeleteForm.action = btn.dataset.deleteUrl;
                openTaskDeleteModal();
            });
        });
        taskDeleteModal.querySelectorAll('[data-modal-close]').forEach(function (el) {
            el.addEventListener('click', closeTaskDeleteModal);
        });
    }

    /* ---- Task detail: side tabs ---- */
    var taskTabs = document.getElementById('taskTabs');
    if (taskTabs) {
        var panels = document.querySelectorAll('.task-tab-panel');
        taskTabs.addEventListener('click', function (e) {
            var tab = e.target.closest('.task-side-tab');
            if (!tab) return;
            var target = tab.dataset.tab;
            taskTabs.querySelectorAll('.task-side-tab').forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            panels.forEach(function (p) {
                var isTarget = p.dataset.panel === target;
                p.hidden = !isTarget;
                p.classList.toggle('is-active', isTarget);
            });
        });
    }

    /* ---- Status controls ---- */
    var statusControls = document.getElementById('statusControls');
    if (statusControls) {
        statusControls.addEventListener('click', function (e) {
            var btn = e.target.closest('.status-pill');
            if (!btn) return;
            statusControls.querySelectorAll('.status-pill').forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');
            if (window.showToast) window.showToast('success', 'Status updated to ' + btn.textContent.trim() + '.');
        });
    }

    /* ---- Assign panel ---- */
    bindInlinePanel('openAssignPanel', 'assignPanel', 'cancelAssign', 'saveAssign', 'Assignees updated.');

    /* ---- Dependency panel ---- */
    bindInlinePanel('openDepPanel', 'depPanel', 'cancelDep', 'saveDep', 'Dependencies updated.');

    function bindInlinePanel(openId, panelId, cancelId, saveId, toastMsg) {
        var openBtn = document.getElementById(openId);
        var panel = document.getElementById(panelId);
        var cancelBtn = document.getElementById(cancelId);
        var saveBtn = document.getElementById(saveId);
        if (!openBtn || !panel) return;
        openBtn.addEventListener('click', function () { panel.hidden = false; });
        if (cancelBtn) cancelBtn.addEventListener('click', function () { panel.hidden = true; });
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                panel.hidden = true;
                if (window.showToast) window.showToast('success', toastMsg);
            });
        }
    }

    /* ---- Progress notes ---- */
    var noteForm = document.getElementById('noteForm');
    if (noteForm) {
        noteForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var textarea = noteForm.querySelector('textarea');
            var list = document.getElementById('noteList');
            if (!textarea || !textarea.value.trim() || !list) return;
            var empty = list.querySelector('.note-feed__empty');
            if (empty) empty.remove();
            var item = document.createElement('li');
            item.className = 'note-feed__item';
            item.innerHTML = '<span class="note-feed__dot"></span><div class="note-feed__time">Just now</div><div class="note-feed__author">You</div><div class="note-feed__text"></div>';
            item.querySelector('.note-feed__text').textContent = textarea.value.trim();
            list.insertBefore(item, list.firstChild);
            textarea.value = '';
            if (window.showToast) window.showToast('success', 'Progress note added.');
        });
    }

    /* ---- Comments ---- */
    var commentForm = document.getElementById('commentForm');
    var commentParentId = document.getElementById('commentParentId');
    var cancelReply = document.getElementById('cancelReply');
    var commentList = document.getElementById('commentList');
    if (commentList) {
        commentList.addEventListener('click', function (e) {
            var replyBtn = e.target.closest('.comment__reply');
            if (!replyBtn) return;
            if (commentParentId) commentParentId.value = replyBtn.dataset.parentId;
            if (cancelReply) cancelReply.hidden = false;
            if (commentForm) commentForm.querySelector('textarea').focus();
        });
    }
    if (cancelReply) {
        cancelReply.addEventListener('click', function () {
            if (commentParentId) commentParentId.value = '';
            cancelReply.hidden = true;
        });
    }
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var textarea = commentForm.querySelector('textarea');
            if (!textarea || !textarea.value.trim()) return;
            if (window.showToast) window.showToast('success', 'Comment posted.');
            textarea.value = '';
            if (commentParentId) commentParentId.value = '';
            if (cancelReply) cancelReply.hidden = true;
        });
    }
});

/* ---- Create Task side panel (project overview page) ---- */
document.addEventListener('DOMContentLoaded', function () {
    var panel = document.querySelector('[data-panel="create-task-panel"]');
    if (!panel) return;

    var stageBadge      = panel.querySelector('[data-task-stage-badge]');
    var nameInput        = document.getElementById('taskName');
    var descInput        = document.getElementById('taskDescription');
    var typeInput        = document.getElementById('taskType');
    var chipsWrap        = panel.querySelector('[data-tag-suggestions]');
    var deadlineHidden   = document.getElementById('taskDeadline');
    var deadlineTime     = document.getElementById('taskDeadlineTime');
    var assigneeChips    = panel.querySelector('[data-assignee-chips]');
    var assigneeCount    = panel.querySelector('[data-assignee-count]');
    var assigneeMenu     = panel.querySelector('[data-assignee-menu]');
    var dependencyChips  = panel.querySelector('[data-dependency-chips]');
    var dependencyMenu   = panel.querySelector('[data-dependency-menu]');
    var submitBtn        = document.getElementById('submitCreateTask');

    var activeStageName  = null;
    var activeStageId    = null; // set only for stages stored in the database
    var selectedAssignees = [];
    var selectedDependencies = [];

    document.addEventListener('click', function (e) {
        var addBtn = e.target.closest('.js-add-task');
        if (!addBtn) return;
        activeStageName = addBtn.dataset.stageName || '';
        var stageEl = addBtn.closest('.stage-item');
        activeStageId = stageEl && stageEl.dataset.stageId ? stageEl.dataset.stageId : null;
        if (stageBadge) stageBadge.textContent = activeStageName;
        resetPanel();
        openPanel(panel);
    });

    if (chipsWrap) {
        chipsWrap.addEventListener('click', function (e) {
            var chip = e.target.closest('.tag-chip');
            if (!chip) return;
            chipsWrap.querySelectorAll('.tag-chip').forEach(function (c) { c.classList.remove('is-selected'); });
            chip.classList.add('is-selected');
            if (typeInput) typeInput.value = chip.dataset.tagChip;
        });
    }

    if (assigneeMenu) {
        assigneeMenu.addEventListener('click', function (e) {
            var opt = e.target.closest('[data-assignee-option]');
            if (!opt || opt.classList.contains('is-added')) return;
            addAssignee(opt.dataset.assigneeName);
        });
    }

    if (dependencyMenu) {
        dependencyMenu.addEventListener('click', function (e) {
            var opt = e.target.closest('[data-dependency-option]');
            if (!opt || opt.classList.contains('is-added')) return;
            addDependency(opt.dataset.dependencyName, opt.dataset.dependencyStage);
            var dd = dependencyMenu.closest('[data-dropdown]');
            if (dd) dd.classList.remove('is-open'); // close after each pick
        });
    }

    function addAssignee(name) {
        if (selectedAssignees.indexOf(name) !== -1) return;
        selectedAssignees.push(name);
        renderAssignees();
    }

    function removeAssignee(name) {
        selectedAssignees = selectedAssignees.filter(function (n) { return n !== name; });
        renderAssignees();
    }

    function renderAssignees() {
        assigneeChips.innerHTML = '';
        selectedAssignees.forEach(function (name) {
            var chip = document.createElement('span');
            chip.className = 'assignee-chip';
            chip.innerHTML =
                '<span class="avatar avatar--sm avatar--' + colorClass(name) + '">' + initials(name) + '</span>' +
                '<span>' + escapeHtml(name) + '</span>' +
                '<button type="button" class="assignee-chip__remove" data-remove-assignee="' + escapeHtml(name) + '">' + (window.WIZARD_ICONS ? window.WIZARD_ICONS.x : '&times;') + '</button>';
            assigneeChips.appendChild(chip);
        });
        if (assigneeCount) assigneeCount.textContent = selectedAssignees.length + ' assigned';
        assigneeMenu.querySelectorAll('[data-assignee-option]').forEach(function (opt) {
            opt.classList.toggle('is-added', selectedAssignees.indexOf(opt.dataset.assigneeName) !== -1);
        });
    }

    if (assigneeChips) {
        assigneeChips.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-remove-assignee]');
            if (!btn) return;
            removeAssignee(btn.dataset.removeAssignee);
        });
    }

    function addDependency(name, stageName) {
        if (selectedDependencies.some(function (d) { return d.name === name; })) return;
        selectedDependencies.push({ name: name, stage: stageName });
        renderDependencies();
    }

    function removeDependency(name) {
        selectedDependencies = selectedDependencies.filter(function (d) { return d.name !== name; });
        renderDependencies();
    }

    function renderDependencies() {
        dependencyChips.innerHTML = '';
        selectedDependencies.forEach(function (dep) {
            var chip = document.createElement('div');
            chip.className = 'dependency-chip';
            chip.innerHTML =
                (window.TASK_ICONS ? window.TASK_ICONS.lock : '') +
                '<span class="dependency-chip__name">' + escapeHtml(dep.name) + '</span>' +
                '<button type="button" class="dependency-chip__remove" data-remove-dependency="' + escapeHtml(dep.name) + '">' + (window.WIZARD_ICONS ? window.WIZARD_ICONS.x : '&times;') + '</button>';
            dependencyChips.appendChild(chip);
        });
        dependencyMenu.querySelectorAll('[data-dependency-option]').forEach(function (opt) {
            var picked = selectedDependencies.some(function (d) { return d.name === opt.dataset.dependencyName; });
            // Only tasks from the current stage, and never ones already selected.
            opt.hidden = picked || opt.dataset.dependencyStage !== activeStageName;
        });
    }

    if (dependencyChips) {
        dependencyChips.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-remove-dependency]');
            if (!btn) return;
            removeDependency(btn.dataset.removeDependency);
        });
    }

    function resetPanel() {
        if (nameInput) nameInput.value = '';
        if (descInput) descInput.value = '';
        if (typeInput) typeInput.value = '';
        if (chipsWrap) chipsWrap.querySelectorAll('.tag-chip').forEach(function (c) { c.classList.remove('is-selected'); });
        if (deadlineHidden) deadlineHidden.value = '';
        if (deadlineTime) deadlineTime.value = '14:00';
        var dateField = panel.querySelector('[data-datepicker]');
        if (dateField && dateField.datepickerReset) dateField.datepickerReset();
        selectedAssignees = [];
        selectedDependencies = [];
        renderAssignees();
        renderDependencies();
        panel.querySelectorAll('.field-error').forEach(function (err) { err.textContent = ''; err.classList.remove('is-visible'); });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var name = nameInput ? nameInput.value.trim() : '';
            var nameErr = panel.querySelector('[data-error-for="taskName"]');
            var deadlineErr = panel.querySelector('[data-error-for="taskDeadline"]');
            var valid = true;

            if (!name) {
                if (nameErr) { nameErr.textContent = 'Task name is required.'; nameErr.classList.add('is-visible'); }
                valid = false;
            } else if (nameErr) {
                nameErr.textContent = ''; nameErr.classList.remove('is-visible');
            }

            if (!deadlineHidden || !deadlineHidden.value) {
                if (deadlineErr) { deadlineErr.textContent = 'Deadline is required.'; deadlineErr.classList.add('is-visible'); }
                valid = false;
            } else if (deadlineErr) {
                deadlineErr.textContent = ''; deadlineErr.classList.remove('is-visible');
            }

            if (!valid) return;

            // Real (database) stage: save through the server, then reload to show it.
            if (activeStageId) {
                var fd = new FormData();
                fd.append('ajax', '1');
                fd.append('csrf_token', window.STAGE_CSRF || '');
                fd.append('stage_id', activeStageId);
                fd.append('name', name);
                fd.append('description', (document.getElementById('taskDescription') || {}).value || '');
                fd.append('task_type', typeInput ? typeInput.value.trim() : '');
                fd.append('deadline', deadlineHidden.value);
                selectedAssignees.forEach(function (n) {
                    var o = assigneeMenu.querySelector('[data-assignee-option][data-assignee-name="' + n.replace(/"/g, '\\"') + '"]');
                    if (o && o.dataset.assigneeId && o.dataset.assigneeId !== '0') fd.append('assignees[]', o.dataset.assigneeId);
                });
                selectedDependencies.forEach(function (d) {
                    var opts = dependencyMenu.querySelectorAll('[data-dependency-option]');
                    for (var i = 0; i < opts.length; i++) {
                        var o = opts[i];
                        // mock tasks have no database id (0); they can't be saved as dependencies
                        if (o.dataset.dependencyName === d.name && o.dataset.dependencyStage === d.stage && o.dataset.dependencyId !== '0') {
                            fd.append('dependencies[]', o.dataset.dependencyId);
                        }
                    }
                });
                submitBtn.disabled = true;
                fetch(window.TASK_STORE_URL || '/tasks', { method: 'POST', body: fd, credentials: 'same-origin' })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                    .then(function (res) {
                        submitBtn.disabled = false;
                        if (res.ok && res.body.ok) { window.location.reload(); }
                        else if (window.showToast) { showToast('error', (res.body && res.body.error) || 'Could not create the task.'); }
                    })
                    .catch(function () {
                        submitBtn.disabled = false;
                        if (window.showToast) showToast('error', 'Could not create the task.');
                    });
                return;
            }

            appendTaskCard({
                name: name,
                deadline: deadlineHidden.value,
                assignees: selectedAssignees.slice(),
                dependencies: selectedDependencies.slice()
            });

            closePanel(panel);
            if (window.showToast) showToast('success', '"' + name + '" was added to ' + activeStageName + '.');
        });
    }

    function appendTaskCard(task) {
        var stageItem = Array.prototype.find.call(
            document.querySelectorAll('.stage-item'),
            function (item) { return item.dataset.stageName === activeStageName; }
        );
        if (!stageItem) return;

        var strip = stageItem.querySelector('[data-task-strip]');
        var addCard = strip.querySelector('.js-add-task');
        var existingNamesInStage = {};
        strip.querySelectorAll('.task-card__name').forEach(function (el) {
            existingNamesInStage[el.textContent.trim()] = el.closest('.task-card');
        });

        var localDeps = [];
        var crossStageDeps = [];
        task.dependencies.forEach(function (dep) {
            if (existingNamesInStage[dep.name]) {
                localDeps.push(existingNamesInStage[dep.name]);
            } else {
                crossStageDeps.push(dep.name);
            }
        });

        var isLocked = task.dependencies.length > 0;
        var meta = isLocked ? window.TASK_STATUS_META.locked : window.TASK_STATUS_META.not_started;
        var newId = 'task-new-' + Date.now();

        localDeps.forEach(function (depCard, i) {
            if (!depCard.id) depCard.id = 'task-dep-' + Date.now() + '-' + i;
        });
        var dependsIdsAttr = localDeps.map(function (d) { return d.id; }).join(',');

        var column = document.createElement('div');
        column.className = 'task-column';

        var card = document.createElement('div');
        card.className = 'task-card' + (isLocked ? ' task-card--locked' : '');
        card.id = newId;
        if (dependsIdsAttr) card.setAttribute('data-depends-ids', dependsIdsAttr);

        var assigneesHtml = '';
        if (task.assignees.length === 0) {
            assigneesHtml = '<span class="task-card__unassigned">Unassigned</span>';
        } else {
            task.assignees.slice(0, 2).forEach(function (name) {
                assigneesHtml += '<span class="avatar avatar--sm avatar--' + colorClass(name) + '" title="' + escapeHtml(name) + '">' + initials(name) + '</span>';
            });
            if (task.assignees.length > 2) {
                assigneesHtml += '<span class="task-card__more">+' + (task.assignees.length - 2) + '</span>';
            }
        }

        var deadlineDate = new Date(task.deadline + 'T00:00:00');
        var deadlineLabel = isNaN(deadlineDate.getTime()) ? task.deadline : deadlineDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

        card.innerHTML =
            '<span class="badge badge--' + meta.tone + '">' + meta.icon + meta.label + '</span>' +
            '<h4 class="task-card__name">' + escapeHtml(task.name) + '</h4>' +
            (crossStageDeps.length ? '<div class="task-card__depends">' + (window.TASK_ICONS ? window.TASK_ICONS.lock : '') + '<span class="task-card__depends-text">Depends on: ' + escapeHtml(crossStageDeps.join(', ')) + '</span></div>' : '') +
            '<div class="task-card__footer">' +
                '<div class="task-card__assignees">' + assigneesHtml + '</div>' +
                '<span class="task-card__deadline">' + escapeHtml(deadlineLabel) + '</span>' +
            '</div>';

        column.appendChild(card);
        strip.insertBefore(column, addCard);

        window.dispatchEvent(new Event('resize'));
    }

    function colorClass(seed) {
        var palette = ['primary', 'pink', 'success', 'warning', 'danger', 'neutral'];
        var hash = 0;
        for (var i = 0; i < seed.length; i++) hash = (hash * 31 + seed.charCodeAt(i)) >>> 0;
        return palette[hash % palette.length];
    }

    function initials(name) {
        name = name.trim();
        if (!name) return '';
        var parts = name.split(' ');
        var first = parts[0].charAt(0);
        var last = parts.length > 1 ? parts[parts.length - 1].charAt(0) : '';
        return (first + last).toUpperCase();
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});
/* ---- Demo tasks: edits/deletes kept temporarily in sessionStorage (no backend) ---- */
(function () {
    var K = 'mockTaskEdits', D = 'mockTaskDeleted';
    function load(k, d) { try { return JSON.parse(sessionStorage.getItem(k)) || d; } catch (e) { return d; } }
    var edits = load(K, {}), deleted = load(D, []);
    var root = document.querySelector('.tasks-page[data-mock-key]');

    // Apply saved edits / deletions on the board and detail page.
    document.querySelectorAll('.task-row[data-mock-key]').forEach(function (row) {
        var key = row.dataset.mockKey;
        if (deleted.indexOf(key) !== -1) { row.remove(); return; }
        var e = edits[key]; if (!e) return;
        var n = row.querySelector('.js-mock-name'), d = row.querySelector('.js-mock-desc');
        if (n && e.name) n.textContent = e.name;
        if (d && e.description) d.textContent = e.description.slice(0, 100);
        var dl = row.querySelector('.task-row__meta-value');
        if (dl && e.deadline) dl.textContent = new Date(e.deadline + 'T00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    });
    if (root && edits[root.dataset.mockKey]) {
        var e = edits[root.dataset.mockKey];
        var n = root.querySelector('.js-mock-name'), d = root.querySelector('.js-mock-desc');
        if (n && e.name) n.textContent = e.name;
        if (d && e.description) d.textContent = e.description;
    }

    // Save from the edit form.
    var form = document.querySelector('form[data-mock-key]');
    if (form) {
        form.addEventListener('submit', function (ev) {
            ev.preventDefault(); ev.stopImmediatePropagation();
            edits[form.dataset.mockKey] = {
                name: form.elements.name.value, description: form.elements.description.value,
                task_type: form.elements.task_type.value, deadline: form.elements.deadline.value
            };
            sessionStorage.setItem(K, JSON.stringify(edits));
            location.href = form.querySelector('.task-form-footer__actions a').href;
        }, true);
        var saved = edits[form.dataset.mockKey];
        if (saved) { ['name', 'description', 'task_type', 'deadline'].forEach(function (f) { if (saved[f] != null) form.elements[f].value = saved[f]; }); }
    }

    function markDeleted(key) { deleted.push(key); sessionStorage.setItem(D, JSON.stringify(deleted)); }

    // Demo deletes use the same custom modal as real tasks.
    var modal = document.getElementById('deleteTaskModal');
    var delForm = document.getElementById('deleteTaskForm');
    var pending = null;
    function closeModal() { modal.classList.remove('is-open'); setTimeout(function () { modal.setAttribute('hidden', ''); }, 200); }
    document.addEventListener('click', function (e) {
        var b = e.target.closest('.js-mock-delete, .js-mock-delete-detail');
        if (!b || !modal) return;
        var row = b.closest('.task-row');
        pending = { key: row ? row.dataset.mockKey : (root && root.dataset.mockKey), row: row };
        document.getElementById('deleteTaskName').textContent = b.dataset.taskName || 'this task';
        modal.removeAttribute('hidden');
        requestAnimationFrame(function () { modal.classList.add('is-open'); });
    });
    // Runs before the real-delete handlers: a pending demo delete never posts to the server.
    if (delForm) {
        delForm.addEventListener('submit', function (e) {
            if (!pending) return;
            e.preventDefault(); e.stopImmediatePropagation();
            markDeleted(pending.key);
            var wasRow = pending.row; pending = null;
            closeModal();
            if (wasRow) { wasRow.remove(); if (window.showToast) window.showToast('success', 'Task deleted.'); }
            else { location.href = document.querySelector('.back-link').href; }
        }, true);
        modal.querySelectorAll('[data-modal-close]').forEach(function (el) { el.addEventListener('click', function () { pending = null; }); });
        document.querySelectorAll('.js-delete-task').forEach(function (el) { el.addEventListener('click', function () { pending = null; }, true); });
    }
})();
