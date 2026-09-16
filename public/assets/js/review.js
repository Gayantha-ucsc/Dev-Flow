document.addEventListener('DOMContentLoaded', function () {
    var page = document.querySelector('.review-page');
    if (!page) return;

    /* Tabs */
    var tabs = page.querySelectorAll('.review-tab');
    var panels = page.querySelectorAll('.review-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');

            var target = tab.dataset.tab;
            panels.forEach(function (panel) {
                panel.hidden = panel.dataset.panel !== target;
            });
        });
    });

    /* ---- Review item actions ---- */
    function closeFeedback(item) {
        var feedback = item.querySelector('.review-item__feedback');
        var actions  = item.querySelector('.review-item__actions');
        var input    = item.querySelector('.review-item__feedback-input');
        var error    = item.querySelector('.review-item__feedback-error');
        if (feedback) feedback.hidden = true;
        if (actions) actions.hidden = false;
        if (input) input.value = '';
        if (error) error.hidden = true;
    }

    function updateTabCount() {
        var remaining = document.querySelectorAll('.review-item').length;
        var countEl = page.querySelector('.review-tab__count');
        if (countEl) countEl.textContent = remaining;

        if (remaining === 0) {
            var list = document.getElementById('reviewList');
            if (list) {
                list.outerHTML =
                    '<div class="empty-state empty-state--card">' +
                    '<div class="empty-state__icon">' + (window.APP_ICONS ? window.APP_ICONS.circleCheck : '') + '</div>' +
                    '<h1 class="empty-state__heading">Nothing waiting on you</h1>' +
                    '<p class="empty-state__subtext">Every submitted task has been reviewed. New submissions from your team will show up here.</p>' +
                    '</div>';
            }
        }
    }

    function removeItem(item, toastType, message) {
        item.remove();
        updateTabCount();
        if (window.showToast) window.showToast(toastType, message);
    }

    document.querySelectorAll('.review-item').forEach(function (item) {
        var title = item.querySelector('.review-item__title');
        var taskName = title ? title.textContent.trim() : 'Task';

        var approveBtn = item.querySelector('.js-approve');
        var rejectBtn = item.querySelector('.js-reject');
        var changesBtn = item.querySelector('.js-request-changes');
        var cancelBtn = item.querySelector('.js-cancel-feedback');
        var confirmBtn = item.querySelector('.js-confirm-feedback');
        var actions = item.querySelector('.review-item__actions');
        var feedback = item.querySelector('.review-item__feedback');
        var input = item.querySelector('.review-item__feedback-input');
        var error = item.querySelector('.review-item__feedback-error');
        var pendingDecision = null;

        if (approveBtn) {
            approveBtn.addEventListener('click', function () {
                removeItem(item, 'success', '"' + taskName + '" approved.');
            });
        }

        function openFeedback(decision) {
            pendingDecision = decision;
            if (actions) actions.hidden = true;
            if (feedback) feedback.hidden = false;
            if (input) input.focus();
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () { openFeedback('rejected'); });
        }
        if (changesBtn) {
            changesBtn.addEventListener('click', function () { openFeedback('changes_requested'); });
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () { closeFeedback(item); });
        }
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                var value = input ? input.value.trim() : '';
                if (!value) {
                    if (error) error.hidden = false;
                    return;
                }
                var isReject = pendingDecision === 'rejected';
                removeItem(
                    item,
                    isReject ? 'info' : 'success',
                    isReject
                        ? '"' + taskName + '" rejected and reopened for the submitter.'
                        : 'Changes requested on "' + taskName + '".'
                );
            });
        }
    });
});