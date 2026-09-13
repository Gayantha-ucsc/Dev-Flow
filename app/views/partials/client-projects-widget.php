<?php
// Expects: $clientProjects.
if (empty($clientProjects)) {
    return;
}
?>
<div class="dash-projects client-projects-widget">
    <div class="dash-projects__header">
        <h2 class="card__title"><?= renderIcon('user') ?> Your Client Projects</h2>
        <a class="card__link" href="<?= url('/client-portal/reviews') ?>">View Approvals</a>
    </div>
    <p class="client-projects-widget__subtitle">
        Projects where you're the client contact, separate from the projects you manage above.
    </p>
    <div class="dash-projects__grid">
        <?php foreach ($clientProjects as $cp): ?>
            <div class="card client-projects-widget__card">
                <div class="client-projects-widget__top">
                    <h3 class="project-card__name"><?= htmlspecialchars($cp['name']) ?></h3>
                    <?php if (!empty($cp['needsReview'])): ?>
                        <span class="badge badge--primary"><?= (int) count($cp['needsReview']) ?> to review</span>
                    <?php else: ?>
                        <span class="badge badge--success">Up to date</span>
                    <?php endif; ?>
                </div>

                <div class="project-card__progress-row">
                    <span>Currently in: <strong><?= htmlspecialchars($cp['currentStage']) ?></strong></span>
                    <strong><?= (int) $cp['percent'] ?>%</strong>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar__fill" style="width: <?= (int) $cp['percent'] ?>%;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>