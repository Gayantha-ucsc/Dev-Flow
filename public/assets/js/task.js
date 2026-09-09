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
