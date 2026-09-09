<?php
// Expects: $currentUser, $stats, $needsAttention, $projects, $activity

?>
<div class="dash-page">

    <div class="dash-header">
        <h1>Dashboard</h1>
        <p>Welcome back<?= !empty($currentUser['name']) ? ', ' . htmlspecialchars($currentUser['name']) : '' ?> - here's what needs your attention across your projects.</p>
    </div>

    <!-- Stat cards: Active Projects, Pending Approvals, Overdue Tasks, Blocked Tasks -->
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

    <div class="dash-columns">

        <!-- Needs Your Attention: joint approvals, member requests, stage-change proposals -->
        <div class="card dash-panel">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('bell') ?> Needs Your Attention</h2>
                <a class="card__link" href="<?= url('review') ?>">View All</a>
            </div>
            <?php if (empty($needsAttention)): ?>
                <div class="dash-panel__empty">
                    <?= renderIcon('circle-check') ?>
                    <span>You're all caught up</span>
                </div>
            <?php else: ?>
                <ul class="attention-list">
                    <?php foreach ($needsAttention as $item): ?>
                        <li class="attention-item">
                            <span class="attention-item__icon"><?= renderIcon($item['icon']) ?></span>
                            <div class="attention-item__body">
                                <div class="attention-item__title"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="attention-item__meta">
                                    <span class="attention-item__project"><?= htmlspecialchars($item['project']) ?></span>
                                    &nbsp;•&nbsp;<?= htmlspecialchars($item['meta']) ?>
                                </div>
                            </div>
                            <div class="attention-item__actions">
                                <?php foreach ($item['actions'] as $action): ?>
                                    <?php
                                        $btnClass = match ($action['variant']) {
                                            'primary'     => 'btn-sm btn-sm--primary',
                                            'text-danger' => 'btn-sm btn-sm--text-danger',
                                            default       => 'btn-sm',
                                        };
                                    ?>
                                    <a class="<?= $btnClass ?>" href="<?= htmlspecialchars($action['href']) ?>"><?= htmlspecialchars($action['label']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Recent Activity: cross-project feed, each entry project-tagged -->
        <div class="card dash-panel">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('clock') ?> Recent Activity</h2>
            </div>
            <?php if (empty($activity)): ?>
                <div class="dash-panel__empty">
                    <?= renderIcon('clock') ?>
                    <span>No recent activity yet</span>
                </div>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($activity as $entry): ?>
                        <li class="activity-item <?= $entry['tone'] === 'danger' ? 'activity-item--danger' : '' ?>">
                            <span class="activity-item__dot"></span>
                            <div class="activity-item__time"><?= htmlspecialchars($entry['time']) ?></div>
                            <div class="activity-item__text"><?= $entry['text'] ?></div>
                            <span class="activity-item__project"><?= htmlspecialchars($entry['project']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="activity-footer">
                    <a href="<?= url('notifications') ?>">Load More Activity</a>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Your Projects: compact rollup cards, prioritized by urgency -->
    <div class="dash-projects">
        <div class="dash-projects__header">
            <h2>Your Projects</h2>
            <a class="card__link" href="<?= url('projects') ?>">View All <?= renderIcon('arrow-up-right') ?></a>
        </div>
        <div class="dash-projects__grid">
            <?php foreach ($projects as $project): ?>
                <div class="card project-card project-card--<?= htmlspecialchars($project['health']) ?>">
                    <div class="project-card__top">
                        <div>
                            <h3 class="project-card__name"><?= htmlspecialchars($project['name']) ?></h3>
                            <p class="project-card__subtitle"><?= htmlspecialchars($project['subtitle']) ?></p>
                        </div>
                        <span class="badge <?= $project['health'] === 'at_risk' ? 'badge--danger' : 'badge--success' ?>">
                            <?= $project['health'] === 'at_risk' ? 'At Risk' : 'On Track' ?>
                        </span>
                    </div>

                    <div class="project-card__pipeline-row">
                        <?php $stages = $project['stages']; ?>
                        <?php include __DIR__ . '/../partials/stage-pipeline.php'; ?>
                        <span class="project-card__stage-label"><?= htmlspecialchars($project['stageLabel']) ?></span>
                    </div>

                    <div class="project-card__progress-row">
                        <span>Progress</span>
                        <strong><?= (int) $project['percent'] ?>%</strong>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar__fill" style="width: <?= (int) $project['percent'] ?>%;"></div>
                    </div>

                    <div class="project-card__footer">
                        <span class="legend-dot"><?= (int) $project['doneCount'] ?> Done</span>
                        <span class="legend-dot legend-dot--active"><?= (int) $project['activeCount'] ?> Active</span>
                        <?php if ($project['dangerCount'] > 0): ?>
                            <span class="legend-dot legend-dot--danger"><?= (int) $project['dangerCount'] ?> <?= htmlspecialchars($project['dangerLabel']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>