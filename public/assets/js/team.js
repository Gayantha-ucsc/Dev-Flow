document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('memberTable');
    if (!table) return; // toolbar hidden (one member, or empty state)

    var searchInput  = document.getElementById('memberSearch');
    var roleFilters   = document.getElementById('roleFilters');
    var statusFilter  = document.getElementById('statusFilter');
    var noResults     = document.getElementById('memberNoResults');
    if (!searchInput || !roleFilters || !statusFilter) return;

    var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
    var activeRole = 'all';

    function applyFilters() {
        var query = (searchInput.value || '').trim().toLowerCase();
        var status = statusFilter.value;
        var visibleCount = 0;

        rows.forEach(function (row) {
            var matchesSearch = !query || row.dataset.name.indexOf(query) !== -1;
            var matchesRole   = activeRole === 'all' || row.dataset.role === activeRole;
            var matchesStatus = status === 'all' || row.dataset.status === status;
            var visible = matchesSearch && matchesRole && matchesStatus;
            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (noResults) noResults.hidden = visibleCount !== 0;
    }

    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);

    roleFilters.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-filter]');
        if (!btn) return;
        roleFilters.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
        btn.classList.add('is-active');
        activeRole = btn.dataset.filter;
        applyFilters();
    });
});