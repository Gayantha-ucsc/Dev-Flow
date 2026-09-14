<?php
// Expects: $currentUser, $stats, $needsAttention, $projects, $activity, $clientProjects

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

        <!-- joint approvals, member requests, stage-change proposals -->
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

        <!-- Recent Activity -->
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

    <!-- Your Projects; prioritized by urgency -->
    <?php include __DIR__ . '/../partials/project-rollup-grid.php'; ?>

    <?php include __DIR__ . '/../partials/client-projects-widget.php'; ?>

</div>