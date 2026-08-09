document.addEventListener('DOMContentLoaded', function () {
    var stageList = document.querySelector('.stage-list');
    if (!stageList) return;

    stageList.addEventListener('click', function (e) {
        // Reorder/edit/delete are their own actions, not a toggle.
        if (e.target.closest('.stage-row__reorder')) return;
        if (e.target.closest('.stage-row__actions') && !e.target.closest('.stage-row__expand-toggle')) return;

        var row = e.target.closest('.stage-row');
        if (!row) return;

        var item = row.closest('.stage-item');
        var expand = item.querySelector('.stage-expand');
        var toggleBtn = row.querySelector('.stage-row__expand-toggle');
        var willOpen = expand.hasAttribute('hidden');

        expand.hidden = !willOpen;
        item.classList.toggle('stage-item--expanded', willOpen);
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', String(willOpen));
    });
});