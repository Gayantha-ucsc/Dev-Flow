<?php
// Expects: $currentUser, $stats, $myTasks, $recentFeedback, $projects, $clientProjects
$myTasks       = $myTasks ?? [];
$recentFeedback = $recentFeedback ?? [];

$statusFilters = [
    'all'            => 'All',
    'in_progress'    => 'In Progress',
    'pending_review' => 'Ready for Review',
    'blocked'        => 'Blocked',
    'locked'         => 'Locked',
];
$statusCounts = array_count_values(array_column($myTasks, 'status'));
?>
<div class="dash-page">

    <div class="dash-header">
        <h1>Welcome back<?= !empty($currentUser['name']) ? ', ' . htmlspecialchars(explode(' ', $currentUser['name'])[0]) : '' ?></h1>
        <p>Here's what's on your plate across your projects.</p>
    </div>

    <!-- Stat cards -->
    <div class="dash-stats">
        <?php foreach ($stats as $stat): ?>
            <div class="card stat-card stat-card--<?= htmlspecialchars($stat['tone']) ?>">
                <div class="stat-card__top">
                    <span class="stat-card__label"><?= htmlspecialchars($stat['label']) ?></span>
                    <span class="stat-card__icon"><?= renderIcon($stat['icon']) ?></span>
                </div>
                <div class="stat-card__value"><?= (int) $stat['value'] ?></div>
                <div class="stat-card__meta"><?= htmlspecialchars($stat['meta']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- My Tasks -->
    <div class="card mytasks-panel">
        <div class="mytasks-panel__header">
            <h2 class="card__title">
                <?= renderIcon('tasks') ?> My Tasks
                <?php if (!empty($myTasks)): ?>
                    <span class="client-review-panel__count"><?= count($myTasks) ?></span>
                <?php endif; ?>
            </h2>
            <a class="card__link" href="<?= url('tasks') ?>">Open Task Board</a>
        </div>

        <?php if (empty($myTasks)): ?>
            <div class="dash-panel__empty" style="height: 160px;">
                <?= renderIcon('circle-check') ?>
                <span>You have no assigned tasks right now</span>
            </div>
        <?php else: ?>

            <div class="mytasks-toolbar">
                <div class="projects-search mytasks-search">
                    <?= renderIcon('search') ?>
                    <input type="text" id="myTasksSearch" placeholder="Search my tasks...">
                </div>
                <div class="projects-filters" id="myTasksTabs">
                    <?php foreach ($statusFilters as $key => $label): ?>
                        <button type="button" class="filter-pill <?= $key === 'all' ? 'is-active' : '' ?>" data-filter="<?= htmlspecialchars($key) ?>">
                            <?= htmlspecialchars($label) ?><?php if ($key !== 'all' && !empty($statusCounts[$key])): ?> (<?= (int) $statusCounts[$key] ?>)<?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <ul class="mytasks-list" id="myTasksList">
                <?php foreach ($myTasks as $task): ?>
                    <?php
                        $meta     = taskStatusMeta($task['status']);
                        $target   = url('projects/' . $task['projectId']);
                        $btnClass = match ($task['action']['variant'] ?? 'ghost') {
                            'primary' => 'btn-sm btn-sm--primary',
                            default   => 'btn-sm',
                        };
                    ?>
                    <li class="mytask-row"
                        data-title="<?= htmlspecialchars(strtolower($task['title'])) ?>"
                        data-project="<?= htmlspecialchars(strtolower($task['projectName'])) ?>"
                        data-status="<?= htmlspecialchars($task['status']) ?>">

                        <div class="mytask-row__main">
                            <div class="mytask-row__title-line">
                                <span class="mytask-row__name"><?= htmlspecialchars($task['title']) ?></span>
                                <span class="badge badge--<?= htmlspecialchars($meta['tone']) ?>">
                                    <?php if ($meta['icon']): ?><?= renderIcon($meta['icon']) ?><?php endif; ?>
                                    <?= htmlspecialchars($meta['label']) ?>
                                </span>
                            </div>
                            <div class="mytask-row__tags">
                                <span class="role-chip"><?= renderIcon('folder') ?> <?= htmlspecialchars($task['projectName']) ?></span>
                                <span class="role-chip"><?= renderIcon('flag') ?> <?= htmlspecialchars($task['stage']) ?></span>
                            </div>
                            <?php if (!empty($task['note'])): ?>
                                <p class="mytask-row__note <?= $task['status'] === 'blocked' ? 'mytask-row__note--danger' : '' ?>">
                                    <?= renderIcon($task['status'] === 'blocked' ? 'circle-alert' : 'lock') ?>
                                    <?= htmlspecialchars($task['note']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="mytask-row__meta">
                            <div class="mytask-row__deadline <?= !empty($task['overdue']) ? 'mytask-row__deadline--overdue' : '' ?>">
                                <?= renderIcon('clock') ?>
                                <?= htmlspecialchars(date('M j, Y', strtotime($task['deadline']))) ?>
                                <?= !empty($task['overdue']) ? '(Overdue)' : '' ?>
                            </div>
                            <a class="<?= $btnClass ?>" href="<?= $target ?>"><?= htmlspecialchars($task['action']['label'] ?? 'View') ?></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="mytasks-list__empty" id="myTasksEmpty" hidden>No tasks match your filters.</p>

        <?php endif; ?>
    </div>

    <!-- Recent Feedback -->
    <div class="card dash-panel mytasks-feedback-panel">
        <div class="card__header">
            <h2 class="card__title"><?= renderIcon('review') ?> Recent Feedback</h2>
        </div>
        <?php if (empty($recentFeedback)): ?>
            <div class="dash-panel__empty">
                <?= renderIcon('circle-check') ?>
                <span>No feedback yet</span>
            </div>
        <?php else: ?>
            <ul class="feedback-list">
                <?php foreach ($recentFeedback as $item): ?>
                    <?php $decision = approvalDecisionMeta($item['decision']); ?>
                    <li class="feedback-item">
                        <div class="feedback-item__top">
                            <span class="feedback-item__task"><?= htmlspecialchars($item['task']) ?></span>
                            <span class="badge badge--<?= htmlspecialchars($decision['tone']) ?>"><?= htmlspecialchars($decision['label']) ?></span>
                            <span class="feedback-item__time"><?= htmlspecialchars($item['time']) ?></span>
                        </div>
                        <div class="feedback-item__meta">
                            Reviewed by <strong><?= htmlspecialchars($item['reviewer']) ?></strong> (<?= htmlspecialchars(memberRoleLabel($item['role'])) ?>)
                            &nbsp;•&nbsp;<?= htmlspecialchars($item['project']) ?>
                        </div>
                        <blockquote class="feedback-item__quote">&ldquo;<?= htmlspecialchars($item['comment']) ?>&rdquo;</blockquote>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- every project user contribute to -->
    <?php include __DIR__ . '/../partials/project-rollup-grid.php'; ?>

    <?php include __DIR__ . '/../partials/client-projects-widget.php'; ?>

</div>