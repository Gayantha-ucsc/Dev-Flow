document.addEventListener('DOMContentLoaded', function () {

    /* Approval history: search + decision filter */
    var table = document.getElementById('historyTable');
    if (table) {
        var searchInput = document.getElementById('historySearch');
        var filters      = document.getElementById('decisionFilters');
        var noResults    = document.getElementById('historyNoResults');

        if (searchInput && filters) {
            var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            var activeDecision = 'all';

            function applyFilters() {
                var query = (searchInput.value || '').trim().toLowerCase();
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var matchesSearch   = !query || row.dataset.search.indexOf(query) !== -1;
                    var matchesDecision = activeDecision === 'all' || row.dataset.decision === activeDecision;
                    var visible = matchesSearch && matchesDecision;
                    row.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (noResults) noResults.hidden = visibleCount !== 0;
            }

            searchInput.addEventListener('input', applyFilters);
            filters.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-filter]');
                if (!btn) return;
                filters.querySelectorAll('.filter-pill').forEach(function (p) { p.classList.remove('is-active'); });
                btn.classList.add('is-active');
                activeDecision = btn.dataset.filter;
                applyFilters();
            });
        }
    }

    /* CSV export helpers */
    function downloadCsv(filename, rows) {
        var csv = rows.map(function (row) {
            return row.map(function (cell) {
                var value = String(cell === null || cell === undefined ? '' : cell);
                if (value.indexOf(',') !== -1 || value.indexOf('"') !== -1 || value.indexOf('\n') !== -1) {
                    value = '"' + value.replace(/"/g, '""') + '"';
                }
                return value;
            }).join(',');
        }).join('\r\n');

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var url  = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    var data = window.REPORTS_DATA || { projectName: 'project', stageProgress: [], contribution: [] };
    var safeName = (data.projectName || 'project').toLowerCase().replace(/[^a-z0-9]+/g, '-');

    var exportBtn = document.getElementById('exportCsvBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            var rows = [['Member', 'Role', 'Tasks Completed', 'Tasks In Progress', 'Revision Rounds (avg)', 'Approval Rate (%)']];
            data.contribution.forEach(function (row) {
                rows.push([row.name, row.role, row.completed, row.inProgress, row.avgRevision, row.approvalRate === null ? '' : row.approvalRate]);
            });
            downloadCsv(safeName + '-team-contribution.csv', rows);
            if (window.showToast) window.showToast('success', 'Team contribution summary exported.');
        });
    }

    var generateBtn = document.getElementById('generateReportBtn');
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            var rows = [['Stage', 'Completion (%)', 'Status']];
            data.stageProgress.forEach(function (stage) {
                rows.push([stage.name, stage.percent, stage.status]);
            });
            downloadCsv(safeName + '-progress-report.csv', rows);
            if (window.showToast) window.showToast('success', 'Project progress report generated.');
        });
    }
});