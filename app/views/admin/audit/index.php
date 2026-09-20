<?php
// Expects: $entries, $actors, $totalCount, $allCount, $page, $totalPages,
//          $rangeStart, $rangeEnd, $filters
$q          = $filters['q'] ?? '';
$actor      = $filters['actor'] ?? 'all';
$actionType = $filters['action_type'] ?? 'all';
$targetType = $filters['target_type'] ?? 'all';
$dateFrom   = $filters['date_from'] ?? '';
$dateTo     = $filters['date_to'] ?? '';
?>
<div class="admin-audit-page">

    <div class="admin-page-header">
        <div>
            <h1>Audit log</h1>
            <p>A system-wide record of every significant action. Entries can't be edited or deleted.</p>
        </div>
    </div>

    <form class="card admin-audit-toolbar" method="get" action="<?= url('admin/audit') ?>" id="adminAuditFilterForm">
        <div class="projects-search admin-audit-toolbar__search">
            <?= renderIcon('search') ?>
            <input type="text" name="q" id="auditSearch" placeholder="Search by action or details" value="<?= htmlspecialchars($q) ?>">
        </div>

        <label class="admin-select">
            <span>Actor:</span>
            <select name="actor" id="auditActorFilter">
                <option value="all" <?= $actor === 'all' ? 'selected' : '' ?>>All users</option>
                <?php foreach ($actors as $actorName): ?>
                    <option value="<?= htmlspecialchars($actorName) ?>" <?= $actor === $actorName ? 'selected' : '' ?>>
                        <?= htmlspecialchars($actorName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="admin-select">
            <span>Action:</span>
            <select name="action_type" id="auditActionFilter">
                <option value="all" <?= $actionType === 'all' ? 'selected' : '' ?>>All</option>
                <option value="create"  <?= $actionType === 'create' ? 'selected' : '' ?>>Created</option>
                <option value="update"  <?= $actionType === 'update' ? 'selected' : '' ?>>Updated</option>
                <option value="approve" <?= $actionType === 'approve' ? 'selected' : '' ?>>Approved</option>
                <option value="reject"  <?= $actionType === 'reject' ? 'selected' : '' ?>>Rejected</option>
                <option value="delete"  <?= $actionType === 'delete' ? 'selected' : '' ?>>Deleted</option>
            </select>
        </label>

        <label class="admin-select">
            <span>Target:</span>
            <select name="target_type" id="auditTargetFilter">
                <option value="all" <?= $targetType === 'all' ? 'selected' : '' ?>>All</option>
                <option value="task"    <?= $targetType === 'task' ? 'selected' : '' ?>>Task</option>
                <option value="stage"   <?= $targetType === 'stage' ? 'selected' : '' ?>>Stage</option>
                <option value="project" <?= $targetType === 'project' ? 'selected' : '' ?>>Project</option>
                <option value="user"    <?= $targetType === 'user' ? 'selected' : '' ?>>User</option>
                <option value="none"    <?= $targetType === 'none' ? 'selected' : '' ?>>System (no target)</option>
            </select>
        </label>

        <label class="admin-select admin-audit-toolbar__dates">
            <span>From:</span>
            <input type="date" name="date_from" id="auditDateFrom" value="<?= htmlspecialchars($dateFrom) ?>">
            <span>To:</span>
            <input type="date" name="date_to" id="auditDateTo" value="<?= htmlspecialchars($dateTo) ?>">
        </label>

        <button type="button" class="icon-btn-sm" id="auditFiltersReset" aria-label="Reset filters" title="Reset filters">
            <?= renderIcon('loader-circle') ?>
        </button>
    </form>



    <div class="card admin-audit-table-card">

        <?php if (empty($entries)): ?>
            <?php
                $variant = 'card';
                $icon    = 'scroll-text';
                $heading = 'No matching entries';
                $subtext = 'Try a different search term, or widen the date range and filters.';
            ?>
            <?php include __DIR__ . '/../../partials/empty-state.php'; ?>
        <?php else: ?>

            <div class="admin-audit-table-scroll">
                <table class="admin-audit-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Actor</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <?php
                                $actionMeta = auditActionTypeMeta($entry['action_type']);
                                $targetMeta = auditTargetTypeMeta($entry['target_type']);
                            ?>
                            <tr>
                                <td class="admin-audit-table__timestamp">
                                    <span><?= htmlspecialchars(date('M j, Y', strtotime($entry['created_at']))) ?></span>
                                    <span class="admin-audit-table__time"><?= htmlspecialchars(date('H:i', strtotime($entry['created_at']))) ?></span>
                                </td>
                                <td>
                                    <div class="admin-user-cell">
                                        <?php if ($entry['is_system']): ?>
                                            <span class="avatar avatar--neutral"><?= renderIcon('workflow') ?></span>
                                        <?php else: ?>
                                            <span class="avatar avatar--<?= avatarColorClass($entry['actor']) ?>">
                                                <?= htmlspecialchars(initials($entry['actor'])) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="admin-user-cell__body">
                                            <span class="admin-user-cell__name"><?= htmlspecialchars($entry['actor']) ?></span>
                                            <?php if ($entry['is_system']): ?>
                                                <span class="admin-audit-table__system-tag">Automated</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge--<?= $actionMeta['tone'] ?>"><?= htmlspecialchars($entry['action']) ?></span>
                                </td>
                                <td>
                                    <?php if ($entry['target_type'] === 'none'): ?>
                                        <span class="admin-audit-table__muted">&mdash;</span>
                                    <?php else: ?>
                                        <div class="admin-audit-target">
                                            <span class="admin-audit-target__type"><?= renderIcon($targetMeta['icon']) ?><?= $targetMeta['label'] ?></span>
                                            <span class="admin-audit-target__name"><?= htmlspecialchars($entry['target']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="admin-audit-table__details"><?= htmlspecialchars($entry['details']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-users-footer">
                <p class="admin-users-footer__count">
                    Showing <strong><?= (int) $rangeStart ?>-<?= (int) $rangeEnd ?></strong> of <strong><?= (int) $totalCount ?></strong> entries
                </p>
                <?php
                    $baseUrl    = '/admin/audit';
                    $baseParams = ['q' => $q, 'actor' => $actor, 'action_type' => $actionType, 'target_type' => $targetType, 'date_from' => $dateFrom, 'date_to' => $dateTo];
                ?>
                <?php include __DIR__ . '/../../partials/pagination.php'; ?>
            </div>

        <?php endif; ?>
    </div>
</div>