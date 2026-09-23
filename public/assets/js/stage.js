document.addEventListener('DOMContentLoaded', function () {
    var workflow = document.querySelector('[data-workflow-section]');
    if (!workflow) return;

    var stageList = workflow.querySelector('.stage-list');
    var icons = window.STAGE_ICONS || {};
    var draggedItem = null;

    /* Add stage (inline, like the project wizard) */
    var addBtn = workflow.querySelector('[data-add-stage]');
    if (addBtn && stageList) {
        addBtn.addEventListener('click', function () {
            var newItem = buildStageItem('New Stage');
            stageList.appendChild(newItem);
            renumberStages();
            newItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            startRename(newItem);
        });
    }

    /* Inline rename */
    workflow.addEventListener('click', function (e) {
        var editBtn = e.target.closest('.js-edit-stage');
        if (!editBtn) return;
        startRename(editBtn.closest('.stage-item'));
    });

    function startRename(item) {
        var display = item.querySelector('[data-name-display]');
        var input = item.querySelector('[data-name-input]');
        if (!display || !input) return;

        display.hidden = true;
        input.hidden = false;
        input.focus();
        input.select();

        function save() {
            var value = input.value.trim();
            if (value) {
                display.textContent = value;
                item.dataset.stageName = value;
                var deleteBtn = item.querySelector('.js-delete-stage');
                if (deleteBtn) deleteBtn.dataset.stageName = value;
                var addTaskCard = item.querySelector('.js-add-task');
                if (addTaskCard) addTaskCard.dataset.stageName = value;
            } else {
                input.value = display.textContent;
            }
            display.hidden = false;
            input.hidden = true;
        }

        input.addEventListener('blur', save, { once: true });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') input.blur();
        });
    }

    /* Drag-to-reorder (grip handle only) */
    stageList.querySelectorAll('.stage-item').forEach(attachDragHandlers);

    function attachDragHandlers(item) {
        var handle = item.querySelector('.stage-row__reorder');
        if (handle) {
            handle.addEventListener('mousedown', function () { item.draggable = true; });
            handle.addEventListener('touchstart', function () { item.draggable = true; });
        }

        item.addEventListener('dragend', function () {
            item.draggable = false;
            item.classList.remove('is-dragging');
            stageList.querySelectorAll('.stage-item').forEach(function (el) {
                el.classList.remove('is-drag-over');
            });
            draggedItem = null;
        });

        item.addEventListener('dragstart', function () {
            draggedItem = item;
            item.classList.add('is-dragging');
        });

        item.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (!draggedItem || draggedItem === item) return;
            item.classList.add('is-drag-over');
        });

        item.addEventListener('dragleave', function () {
            item.classList.remove('is-drag-over');
        });

        item.addEventListener('drop', function (e) {
            e.preventDefault();
            item.classList.remove('is-drag-over');
            if (!draggedItem || draggedItem === item) return;

            var items = Array.prototype.slice.call(stageList.querySelectorAll('.stage-item'));
            var fromIndex = items.indexOf(draggedItem);
            var toIndex = items.indexOf(item);

            if (toIndex > fromIndex) {
                item.after(draggedItem);
            } else {
                item.before(draggedItem);
            }
            renumberStages();
        });
    }

    /* Delete stage */
    var deleteModal    = document.querySelector('[data-modal="delete-stage-modal"]');
    var deleteNameEl   = deleteModal ? deleteModal.querySelector('[data-delete-stage-name]') : null;
    var deleteWarning  = deleteModal ? deleteModal.querySelector('[data-delete-stage-task-warning]') : null;
    var confirmDelete  = document.getElementById('confirmDeleteStage');
    var pendingStageItem = null;

    workflow.addEventListener('click', function (e) {
        var deleteBtn = e.target.closest('.js-delete-stage');
        if (!deleteBtn) return;

        pendingStageItem = deleteBtn.closest('.stage-item');
        var stageName = deleteBtn.dataset.stageName || '';
        var taskCount = parseInt(deleteBtn.dataset.taskCount, 10) || 0;

        if (deleteNameEl) deleteNameEl.textContent = stageName;
        if (deleteWarning) deleteWarning.hidden = taskCount === 0;

        openModal(deleteModal);
    });

    if (confirmDelete) {
        confirmDelete.addEventListener('click', function () {
            if (pendingStageItem) {
                var name = pendingStageItem.dataset.stageName || 'Stage';
                pendingStageItem.remove();
                renumberStages();
                if (window.showToast) showToast('success', '"' + name + '" was deleted.');
                pendingStageItem = null;
            }
            closeModal(deleteModal);
        });
    }

    function openModal(modal) {
        if (!modal) return;
        modal.removeAttribute('hidden');
        requestAnimationFrame(function () { modal.classList.add('is-open'); });
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        setTimeout(function () { modal.setAttribute('hidden', ''); }, 200);
    }

    function renumberStages() {
        stageList.querySelectorAll('.stage-item').forEach(function (item, i) {
            var numberEl = item.querySelector('.stage-row__number');
            if (numberEl) numberEl.textContent = i + 1;
        });
    }

    function buildStageItem(name) {
        var wrapper = document.createElement('div');
        wrapper.className = 'card stage-item';
        wrapper.dataset.stageName = name;
        wrapper.draggable = false;
        wrapper.innerHTML =
            '<div class="stage-row">' +
                '<button type="button" class="stage-row__reorder" aria-label="Reorder ' + escapeHtml(name) + '">' + icons.chevrons + '</button>' +
                '<span class="stage-row__number">0</span>' +
                '<div class="stage-row__body">' +
                    '<h3 class="stage-row__name" data-name-display>' + escapeHtml(name) + '</h3>' +
                    '<input type="text" class="stage-row__name-input" data-name-input value="' + escapeHtml(name) + '" draggable="false" hidden>' +
                    '<div class="stage-row__meta">' +
                        '<span class="stage-row__status stage-row__status--not-started">Not Started</span>' +
                    '</div>' +
                '</div>' +
                '<div class="stage-row__actions">' +
                    '<button type="button" class="icon-btn-sm js-edit-stage" draggable="false" aria-label="Edit ' + escapeHtml(name) + '">' + icons.pencil + '</button>' +
                    '<button type="button" class="icon-btn-sm icon-btn-sm--danger js-delete-stage" draggable="false" data-stage-name="' + escapeHtml(name) + '" data-task-count="0" aria-label="Delete ' + escapeHtml(name) + '">' + icons.trash + '</button>' +
                    '<button type="button" class="icon-btn-sm stage-row__expand-toggle" draggable="false" aria-expanded="false" aria-label="Expand ' + escapeHtml(name) + ' tasks">' + icons.chevronDown + '</button>' +
                '</div>' +
            '</div>' +
            '<div class="stage-expand" hidden>' +
                '<div class="task-strip" data-task-strip>' +
                    '<svg class="connector-layer" data-connector-layer></svg>' +
                    '<div class="task-card task-card--add js-add-task" data-stage-name="' + escapeHtml(name) + '">' + icons.plus + '<span>Add Task</span></div>' +
                '</div>' +
            '</div>';
        attachDragHandlers(wrapper);
        return wrapper;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
});