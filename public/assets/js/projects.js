document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('projectList');
    if (!list) return; // no toolbar/list rendered (single project, or empty state)

    var searchInput = document.getElementById('projectSearch');
    var filterBar    = document.getElementById('projectFilters');
    var sortSelect   = document.getElementById('projectSort');
    if (!searchInput || !filterBar || !sortSelect) return; // toolbar hidden (one project) - nothing to wire up

    var rows         = Array.prototype.slice.call(list.querySelectorAll('.project-row'));

    var activeFilter = 'all';

    function applyFilters() {
        var query = (searchInput.value || '').trim().toLowerCase();
        rows.forEach(function (row) {
            var matchesSearch = !query || row.dataset.name.indexOf(query) !== -1;
            var matchesStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);

    filterBar.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-filter]');
        if (!btn) return;
        filterBar.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
        btn.classList.add('is-active');
        activeFilter = btn.dataset.filter;
        applyFilters();
    });

    sortSelect.addEventListener('change', function () {
        var key = sortSelect.value;
        var sorted = rows.slice().sort(function (a, b) {
            if (key === 'name') {
                return a.dataset.name.localeCompare(b.dataset.name);
            }
            if (key === 'deadline') {
                return new Date(a.dataset.deadline) - new Date(b.dataset.deadline);
            }
            return 0; // "Recently Updated" - keep server-given order (no updated_at in the mock yet)
        });
        sorted.forEach(function (row) { list.appendChild(row); });
    });
});