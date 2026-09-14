<?php
// Expects: $projects (cards: id, name, subtitle, health, percent, stages, stageLabel, doneCount, activeCount, dangerCount, dangerLabel, optional role).
// Optional: $rollupTitle (defaults to "Your Projects"), $rollupViewAllHref.
if (empty($projects)) {
    return;
}
$rollupTitle       = $rollupTitle ?? 'Your Projects';
$rollupViewAllHref = $rollupViewAllHref ?? 'projects';
?>
<div class="dash-projects">
    <div class="dash-projects__header">
        <h2><?= htmlspecialchars($rollupTitle) ?></h2>
        <a class="card__link" href="<?= url($rollupViewAllHref) ?>">View All <?= renderIcon('arrow-up-right') ?></a>
    </div>
    <div class="dash-projects__grid">
        <?php foreach ($projects as $project): ?>
            <a href="<?= url('projects/' . $project['id']) ?>" class="card project-card project-card--<?= htmlspecialchars($project['health'] ?? 'on_track') ?>">
                <div class="project-card__top">
                    <div>
                        <h3 class="project-card__name"><?= htmlspecialchars($project['name']) ?></h3>
                        <p class="project-card__subtitle"><?= htmlspecialchars($project['subtitle']) ?></p>
                    </div>
                    <?php if (!empty($project['role'])): ?>
                        <span class="badge badge--<?= htmlspecialchars(memberRoleTone($project['role'])) ?>"><?= htmlspecialchars(memberRoleLabel($project['role'])) ?></span>
                    <?php else: ?>
                        <span class="badge <?= ($project['health'] ?? 'on_track') === 'at_risk' ? 'badge--danger' : 'badge--success' ?>">
                            <?= ($project['health'] ?? 'on_track') === 'at_risk' ? 'At Risk' : 'On Track' ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="project-card__pipeline-row">
                    <?php $stages = $project['stages']; ?>
                    <?php include __DIR__ . '/stage-pipeline.php'; ?>
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
                    <?php if (($project['dangerCount'] ?? 0) > 0): ?>
                        <span class="legend-dot legend-dot--danger"><?= (int) $project['dangerCount'] ?> <?= htmlspecialchars($project['dangerLabel']) ?></span>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>