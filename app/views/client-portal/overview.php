<?php
// Expects: $currentUser, $project
$stages = $project['stages'];
$hero   = $project['hero'];
?>
<div class="client-overview">

    <div class="client-overview__top">
        <div class="client-overview__intro">
            <h1><?= htmlspecialchars($project['name']) ?></h1>
            <p>Welcome to your central hub for <?= htmlspecialchars($project['name']) ?>. Review the latest deliverables, track our collaborative progress, and manage upcoming milestones with ease.</p>

            <div class="client-overview__progress-row">
                <span>Overall Progress</span>
                <strong><?= (int) $project['percent'] ?>% Complete</strong>
            </div>
            <div class="progress-bar">
                <div class="progress-bar__fill" style="width: <?= (int) $project['percent'] ?>%;"></div>
            </div>
        </div>

        <?php if ($project['payment'] !== null): ?>
            <div class="card client-payment-card client-overview__payment">
                <?php if (($project['payment']['status'] ?? 'due') === 'paid'): ?>
                    <span class="client-payment-card__label"><?= renderIcon('circle-check') ?> Fully Paid</span>
                    <p class="client-payment-card__note">All scheduled payments for this project are complete.</p>
                <?php else: ?>
                    <span class="client-payment-card__label"><?= renderIcon('receipt') ?> Next Payment Due</span>
                    <div class="client-payment-card__amount">$<?= number_format((float) $project['payment']['amount'], 2) ?></div>
                    <p class="client-payment-card__note">
                        <?= renderIcon('calendar') ?> <?= htmlspecialchars(date('M j, Y', strtotime($project['payment']['dueDate']))) ?>
                    </p>
                <?php endif; ?>
                <a class="client-payment-card__link" href="<?= url('/payment') ?>">
                    View Payment Details <?= renderIcon('arrow-right') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($stages)): ?>
        <?php include __DIR__ . '/../partials/client-stage-timeline.php'; ?>
    <?php endif; ?>

    <?php if ($hero !== null): ?>
        <div class="card client-hero">
            <span class="client-hero__icon"><?= renderIcon('rocket') ?></span>
            <h2><?= htmlspecialchars($hero['heading']) ?></h2>
            <p><?= htmlspecialchars($hero['body']) ?></p>
            <div class="client-hero__actions">
                <a class="btn-sm btn-sm--primary client-hero__btn" href="<?= url('/client-portal/reviews') ?>">
                    <?= renderIcon('circle-check') ?> Approve Delivery
                </a>
                <a class="btn-outline client-hero__btn" href="<?= url('/client-portal/reviews') ?>">Request Changes</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="client-overview__reviews">
        <h2 class="client-overview__reviews-heading">
            <?= renderIcon('file-text') ?> Ready for Your Review
            <?php if (!empty($project['needsReview'])): ?>
                <span class="client-review-panel__count"><?= (int) count($project['needsReview']) ?></span>
            <?php endif; ?>
        </h2>

        <?php if (empty($project['needsReview'])): ?>
            <div class="card client-overview__reviews-empty">
                <?= renderIcon('circle-check') ?>
                <span>Nothing needs your review right now</span>
            </div>
        <?php else: ?>
            <?php foreach ($project['needsReview'] as $item): ?>
                <div class="card client-review-card">
                    <div class="client-review-card__body">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <p><?= htmlspecialchars($item['description'] ?? $item['meta']) ?></p>
                    </div>
                    <div class="client-review-card__actions">
                        <a class="btn-sm btn-sm--danger-outline" href="<?= url($item['href']) ?>">
                            <?= renderIcon('x') ?> Reject
                        </a>
                        <a class="btn-sm btn-sm--primary" href="<?= url($item['href']) ?>">
                            <?= renderIcon('check') ?> Approve
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>