<?php
// Expects: $currentUser, $stats, $reviewQueue, $projects, $clientProjects
$reviewQueue = $reviewQueue ?? [];
?>
<div class="dash-page">

    <div class="dash-header">
        <h1>Welcome back<?= !empty($currentUser['name']) ? ', ' . htmlspecialchars(explode(' ', $currentUser['name'])[0]) : '' ?></h1>
        <p>Here's the state of the teams and reviews you own.</p>
    </div>

    <!-- Stat cards: Active Projects, Pending Reviews, Overdue, Blocked -->
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

    <!-- Needs Your Review: tasks contributors have submitted for approval -->
    <div class="card client-review-panel">
        <div class="client-review-panel__header">
            <h2>
                <?= renderIcon('review') ?> Needs Your Review
                <?php if (!empty($reviewQueue)): ?>
                    <span class="client-review-panel__count"><?= count($reviewQueue) ?></span>
                <?php endif; ?>
            </h2>
        </div>

        <?php if (empty($reviewQueue)): ?>
            <div class="dash-panel__empty">
                <?= renderIcon('circle-check') ?>
                <span>Nothing waiting on you right now</span>
            </div>
        <?php else: ?>
            <ul class="client-review-list">
                <?php foreach ($reviewQueue as $item): ?>
                    <li class="client-review-item">
                        <div class="client-review-item__body">
                            <div class="client-review-item__title"><?= htmlspecialchars($item['title']) ?></div>
                            <div class="client-review-item__meta"><?= htmlspecialchars($item['meta']) ?></div>
                        </div>
                        <a class="btn-sm btn-sm--primary" href="<?= url($item['href']) ?>">Review</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- every project user lead or contribute  -->
    <?php include __DIR__ . '/../partials/project-rollup-grid.php'; ?>

    <?php include __DIR__ . '/../partials/client-projects-widget.php'; ?>

</div>