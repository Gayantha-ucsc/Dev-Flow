document.addEventListener('DOMContentLoaded', function () {
    var page = document.querySelector('.notif-page');
    if (!page) return;

    var feed        = document.getElementById('notifFeed');
    var tabs         = page.querySelectorAll('.notif-tab');
    var markAllBtn   = document.getElementById('notifMarkAllRead');

    function updateTabCounts() {
        var rows = feed ? feed.querySelectorAll('[data-notif-row]') : [];
        var counts = { all: rows.length, tasks: 0, approvals: 0, chat: 0, payments: 0 };

        rows.forEach(function (row) {
            var cat = row.dataset.category;
            if (counts[cat] !== undefined) counts[cat]++;
        });

        tabs.forEach(function (tab) {
            var key = tab.dataset.notifTab;
            var countEl = tab.querySelector('[data-count-for="' + key + '"]');
            if (countEl) countEl.textContent = counts[key] !== undefined ? counts[key] : 0;
        });
    }

    function updateDayGroups() {
        if (!feed) return;
        feed.querySelectorAll('[data-day-group]').forEach(function (group) {
            var rows = group.querySelectorAll('[data-notif-row]');
            var visibleRows = Array.prototype.filter.call(rows, function (r) { return r.style.display !== 'none'; });
            group.style.display = visibleRows.length === 0 ? 'none' : '';

            var unreadLabel = group.querySelector('[data-unread-label]');
            if (!unreadLabel) return;
            var unreadCount = 0;
            rows.forEach(function (r) { if (r.dataset.read === '0') unreadCount++; });
            unreadLabel.textContent = unreadCount > 0 ? unreadCount + ' unread' : 'All caught up';
        });
    }

    function markAllAsReadCheck() {
        var anyUnread = feed && feed.querySelector('[data-notif-row][data-read="0"]');
        if (markAllBtn) markAllBtn.hidden = !anyUnread;
    }

    /* Tab filtering */
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); t.setAttribute('aria-selected', 'false'); });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            var target = tab.dataset.notifTab;
            if (!feed) return;
            feed.querySelectorAll('[data-notif-row]').forEach(function (row) {
                row.style.display = (target === 'all' || row.dataset.category === target) ? '' : 'none';
            });
            updateDayGroups();
        });
    });

    /* Row-level actions (Mark as read / Mute / Delete) via the ⋮ menu */
    document.addEventListener('click', function (e) {
        var actionBtn = e.target.closest('[data-notif-action]');
        if (!actionBtn) return;

        var row = actionBtn.closest('[data-notif-row]');
        if (!row) return;

        var action = actionBtn.dataset.notifAction;

        if (action === 'read') {
            row.classList.remove('is-unread');
            row.dataset.read = '1';
            var dot = row.querySelector('[data-unread-dot]');
            if (dot) dot.remove();
            updateDayGroups();
            markAllAsReadCheck();
            if (window.showToast) window.showToast('success', 'Marked as read.');
        }

        if (action === 'mute') {
            if (window.showToast) window.showToast('info', 'Thread muted. You won\'t be notified about further updates on it.');
        }

        if (action === 'delete') {
            row.classList.add('is-removing');
            setTimeout(function () {
                row.remove();
                updateDayGroups();
                updateTabCounts();
                markAllAsReadCheck();
            }, 200);
            if (window.showToast) window.showToast('success', 'Notification deleted.');
        }

        // Close the dropdown this button lives in.
        var dropdown = actionBtn.closest('[data-dropdown]');
        if (dropdown) dropdown.classList.remove('is-open');
    });

    /* Mark all as read */
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            if (!feed) return;
            feed.querySelectorAll('[data-notif-row][data-read="0"]').forEach(function (row) {
                row.classList.remove('is-unread');
                row.dataset.read = '1';
                var dot = row.querySelector('[data-unread-dot]');
                if (dot) dot.remove();
            });
            updateDayGroups();
            markAllBtn.hidden = true;
            if (window.showToast) window.showToast('success', 'All notifications marked as read.');
        });
    }
});