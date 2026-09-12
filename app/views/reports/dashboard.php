<?php
// Expects: $currentUser, $currentProjectName, $projectName, $stats,
//          $stageProgress, $teamWorkload, $contribution, $approvalHistory
$decisionLabel = fn($d) => match ($d) {
    'approved'          => 'Approved',
    'rejected'          => 'Rejected',
    'changes_requested' => 'Changes Requested',
    default             => ucfirst($d),
};
$decisionTone = fn($d) => match ($d) {
    'approved'          => 'success',
    'rejected'          => 'danger',
    'changes_requested' => 'warning',
    default             => 'neutral',
};
?>
<div class="reports-page">

    <div class="reports-header">
        <div>
            <h1>Reports &amp; Monitoring</h1>
            <p>Track progress across <?= htmlspecialchars($projectName ?? 'this project') ?>.</p>
        </div>
    </div>

    <div class="dash-stats reports-stats">
        <div class="card stat-card">
            <div class="stat-card__top">
                <span class="stat-card__label">Overall Progress</span>
                <span class="stat-card__icon"><?= renderIcon('trending-up') ?></span>
            </div>
            <div class="stat-card__value"><?= (int) $stats['percent'] ?>%</div>
            <div class="progress-bar reports-stat-progress">
                <div class="progress-bar__fill" style="width: <?= (int) $stats['percent'] ?>%;"></div>
            </div>
        </div>

        <div class="card stat-card stat-card--pink">
            <div class="stat-card__top">
                <span class="stat-card__label">Blocked Tasks</span>
                <span class="stat-card__icon"><?= renderIcon('ban') ?></span>
            </div>
            <div class="stat-card__value"><?= (int) $stats['blocked'] ?></div>
            <div class="stat-card__meta"><?= $stats['blocked'] > 0 ? 'Awaiting dependencies' : 'None right now' ?></div>
        </div>

        <div class="card stat-card stat-card--danger">
            <div class="stat-card__top">
                <span class="stat-card__label">Overdue Tasks</span>
                <span class="stat-card__icon"><?= renderIcon('circle-alert') ?></span>
            </div>
            <div class="stat-card__value"><?= (int) $stats['overdue'] ?></div>
            <div class="stat-card__meta"><?= $stats['overdue'] > 0 ? 'Past their deadline' : 'Nothing overdue' ?></div>
        </div>

        <div class="card stat-card stat-card--primary">
            <div class="stat-card__top">
                <span class="stat-card__label">Joint Approvals Pending</span>
                <span class="stat-card__icon"><?= renderIcon('review') ?></span>
            </div>
            <div class="stat-card__value"><?= (int) $stats['pending'] ?></div>
            <div class="stat-card__meta"><?= $stats['pending'] > 0 ? 'Awaiting sign-off' : 'You\'re all caught up' ?></div>
        </div>
    </div>

    <!-- Team workload + Stage progress -->
    <div class="reports-columns">

        <div class="card dash-panel reports-panel">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('users') ?> Team Workload</h2>
                <span class="reports-panel__count"><?= count($teamWorkload) ?> contributor<?= count($teamWorkload) === 1 ? '' : 's' ?></span>
            </div>
            <?php if (empty($teamWorkload)): ?>
                <div class="dash-panel__empty">
                    <?= renderIcon('circle-check') ?>
                    <span>No tasks assigned yet</span>
                </div>
            <?php else: ?>
                <ul class="workload-list">
                    <?php foreach ($teamWorkload as $row): ?>
                        <li class="workload-row">
                            <span class="avatar avatar--sm avatar--<?= avatarColorClass($row['name']) ?>"><?= htmlspecialchars(initials($row['name'])) ?></span>
                            <div class="workload-row__body">
                                <div class="workload-row__top">
                                    <span class="workload-row__name"><?= htmlspecialchars($row['name']) ?></span>
                                    <span class="workload-row__count">
                                        <?= (int) $row['total'] ?> task<?= $row['total'] === 1 ? '' : 's' ?>
                                        <?php if ($row['open'] > 0): ?>
                                            <span class="badge badge--warning workload-row__badge"><?= (int) $row['open'] ?> open</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="progress-bar workload-row__bar">
                                    <div class="progress-bar__fill" style="width: <?= (int) $row['barPercent'] ?>%;"></div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card dash-panel reports-panel">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('workflow') ?> Stage Progress</h2>
                <span class="reports-panel__count"><?= count($stageProgress) ?> stage<?= count($stageProgress) === 1 ? '' : 's' ?></span>
            </div>
            <?php if (empty($stageProgress)): ?>
                <div class="dash-panel__empty">
                    <?= renderIcon('circle-check') ?>
                    <span>No stages set up yet</span>
                </div>
            <?php else: ?>
                <ul class="stage-progress-list">
                    <?php foreach ($stageProgress as $stage): ?>
                        <li class="stage-progress-row">
                            <div class="stage-progress-row__top">
                                <span class="stage-progress-row__name"><?= htmlspecialchars($stage['name']) ?></span>
                                <span class="stage-progress-row__percent"><?= (int) $stage['percent'] ?>%</span>
                            </div>
                            <div class="progress-bar stage-progress-row__bar">
                                <div class="progress-bar__fill" style="width: <?= (int) $stage['percent'] ?>%;"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

    <!-- Approval & Revision History -->
    <div class="card reports-table-card">
        <div class="card__header">
            <h2 class="card__title"><?= renderIcon('scroll-text') ?> Approval &amp; Revision History</h2>
            <span class="reports-panel__count">Audit log of every recorded decision</span>
        </div>

        <?php if (!empty($approvalHistory)): ?>
        <div class="reports-toolbar">
            <div class="projects-search">
                <?= renderIcon('search') ?>
                <input type="text" id="historySearch" placeholder="Search items or feedback...">
            </div>
            <div class="projects-filters" id="decisionFilters">
                <button type="button" class="filter-pill is-active" data-filter="all">All</button>
                <button type="button" class="filter-pill" data-filter="approved">Approved</button>
                <button type="button" class="filter-pill" data-filter="rejected">Rejected</button>
                <button type="button" class="filter-pill" data-filter="changes_requested">Changes Requested</button>
            </div>
        </div>
        <?php endif; ?>

        <div class="reports-table-scroll">
            <table class="reports-table" id="historyTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Stage</th>
                        <th>Decision</th>
                        <th>Decided By</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvalHistory as $entry): ?>
                        <tr data-search="<?= htmlspecialchars(strtolower($entry['item'] . ' ' . $entry['feedback'])) ?>"
                            data-decision="<?= htmlspecialchars($entry['decision']) ?>">
                            <td class="reports-table__muted"><?= htmlspecialchars(date('M j, Y', strtotime($entry['date']))) ?></td>
                            <td class="reports-table__item"><?= htmlspecialchars($entry['item']) ?></td>
                            <td class="reports-table__muted"><?= htmlspecialchars($entry['stage']) ?></td>
                            <td><span class="badge badge--<?= $decisionTone($entry['decision']) ?>"><?= htmlspecialchars($decisionLabel($entry['decision'])) ?></span></td>
                            <td><?= htmlspecialchars($entry['decidedBy']) ?></td>
                            <td class="reports-table__feedback"><?= htmlspecialchars($entry['feedback']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($approvalHistory)): ?>
                <p class="reports-table__no-results">No approval decisions recorded for this project yet.</p>
            <?php else: ?>
                <p class="reports-table__no-results" id="historyNoResults" hidden>No entries match your search.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Team Contribution Summary -->
    <div class="card reports-table-card">
        <div class="card__header">
            <h2 class="card__title"><?= renderIcon('user-check') ?> Team Contribution Summary</h2>
            <span class="reports-panel__count">Output and approval metrics for this project</span>
        </div>

        <?php if (empty($contribution)): ?>
            <div class="dash-panel__empty reports-table-card__empty">
                <?= renderIcon('circle-check') ?>
                <span>No contributor activity to summarize yet</span>
            </div>
        <?php else: ?>
        <div class="reports-table-scroll">
            <table class="reports-table" id="contributionTable">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Tasks Completed</th>
                        <th>Tasks In Progress</th>
                        <th>Revision Rounds (avg)</th>
                        <th>Approval Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contribution as $row): ?>
                        <tr>
                            <td>
                                <div class="member-cell">
                                    <span class="avatar avatar--<?= avatarColorClass($row['name']) ?>"><?= htmlspecialchars(initials($row['name'])) ?></span>
                                    <span>
                                        <span class="member-cell__name"><?= htmlspecialchars($row['name']) ?></span><br>
                                        <span class="reports-table__muted"><?= htmlspecialchars($row['role']) ?></span>
                                    </span>
                                </div>
                            </td>
                            <td><?= (int) $row['completed'] ?></td>
                            <td><?= (int) $row['inProgress'] ?></td>
                            <td><?= number_format($row['avgRevision'], 1) ?> round<?= $row['avgRevision'] == 1.0 ? '' : 's' ?></td>
                            <td>
                                <?php if ($row['approvalRate'] === null): ?>
                                    <span class="reports-table__muted">No decisions yet</span>
                                <?php else: ?>
                                    <div class="approval-rate">
                                        <span class="badge badge--success"><?= (int) $row['approvalRate'] ?>%</span>
                                        <div class="progress-bar approval-rate__bar">
                                            <div class="progress-bar__fill" style="width: <?= (int) $row['approvalRate'] ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="reports-actions">
            <button type="button" class="btn-outline" id="generateReportBtn"><?= renderIcon('file-text') ?> Generate Report</button>
            <button type="button" class="btn-add-member" id="exportCsvBtn"><?= renderIcon('download') ?> Export CSV</button>
        </div>
    </div>

</div>

<script>
    window.REPORTS_DATA = <?= json_encode([
        'projectName'   => $projectName,
        'stageProgress' => $stageProgress,
        'contribution'  => $contribution,
    ]) ?>;
</script>