document.addEventListener('DOMContentLoaded', function () {

    var list = document.getElementById('myTasksList');
    if (!list) {
        return;
    }

    var search = document.getElementById('myTasksSearch');
    var tabs = document.getElementById('myTasksTabs');
    var empty = document.getElementById('myTasksEmpty');
    var rows = Array.prototype.slice.call(list.querySelectorAll('.mytask-row'));
    var activeStatus = 'all';

    function applyFilters() {
        var q = (search ? search.value : '').trim().toLowerCase();
        var visible = 0;

        rows.forEach(function (row) {
            var matchesQuery = !q ||
                row.dataset.title.indexOf(q) !== -1 ||
                row.dataset.project.indexOf(q) !== -1;
            var matchesStatus = activeStatus === 'all' || row.dataset.status === activeStatus;
            var show = matchesQuery && matchesStatus;
            row.style.display = show ? '' : 'none';
            if (show) {
                visible++;
            }
        });

        if (empty) {
            empty.hidden = visible !== 0;
        }
    }

    if (search) {
        search.addEventListener('input', applyFilters);
    }

    if (tabs) {
        tabs.addEventListener('click', function (e) {
            var pill = e.target.closest('.filter-pill');
            if (!pill) {
                return;
            }
            activeStatus = pill.dataset.filter;
            tabs.querySelectorAll('.filter-pill').forEach(function (p) {
                p.classList.remove('is-active');
            });
            pill.classList.add('is-active');
            applyFilters();
        });
    }
});