<?php
// Expects: $currentUser, $clientProjects (array of bundles: id, name,
// percent, currentStage, needsReview, payment)
$multipleProjects = count($clientProjects) > 1;
?>
<div class="client-dash">

    <div class="client-dash__header">
        <h1>Welcome back<?= !empty($currentUser['name']) ? ', ' . htmlspecialchars($currentUser['name']) : '' ?></h1>
        <p>
            <?= $multipleProjects
                ? 'Here is a quick overview of your projects.'
                : 'Here is a quick overview of your project status.' ?>
        </p>
    </div>

    <?php foreach ($clientProjects as $cp): ?>

        <?php if ($multipleProjects): ?>
            <h2 class="client-dash__project-heading"><?= htmlspecialchars($cp['name']) ?></h2>
        <?php endif; ?>

        <div class="client-dash__columns">

            <!-- Needs Your Review: top priority -->
            <div class="card client-review-panel">
                <div class="client-review-panel__header">
                    <h2>
                        Needs Your Review
                        <?php if (!empty($cp['needsReview'])): ?>
                            <span class="client-review-panel__count"><?= (int) count($cp['needsReview']) ?></span>
                        <?php endif; ?>
                    </h2>
                </div>

                <?php if (empty($cp['needsReview'])): ?>
                    <div class="dash-panel__empty">
                        <?= renderIcon('circle-check') ?>
                        <span>Nothing needs your review right now</span>
                    </div>
                <?php else: ?>
                    <ul class="client-review-list">
                        <?php foreach ($cp['needsReview'] as $item): ?>
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

            <!-- Right column: this project's summary + payment summary -->
            <div class="client-dash__side">

                <!-- Single large project summary card -->
                <div class="card client-project-card">
                    <h2 class="client-project-card__name"><?= htmlspecialchars($cp['name']) ?></h2>

                    <div class="client-project-card__percent-row">
                        <span class="client-project-card__percent"><?= (int) $cp['percent'] ?>%</span>
                        <span class="client-project-card__percent-label">Complete</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar__fill" style="width: <?= (int) $cp['percent'] ?>%;"></div>
                    </div>

                    <p class="client-project-card__stage">
                        Currently in: <strong><?= htmlspecialchars($cp['currentStage']) ?></strong>
                    </p>

                    <a class="btn-outline btn-outline--block" href="<?= url('/client-portal/overview') ?>">
                        View Project Details
                    </a>
                </div>

                <!-- Payment summary card -->
                <?php if ($cp['payment'] !== null): ?>
                    <div class="card client-payment-card">
                        <?php if (($cp['payment']['status'] ?? 'due') === 'paid'): ?>
                            <span class="client-payment-card__label"><?= renderIcon('circle-check') ?> Fully Paid</span>
                            <p class="client-payment-card__note">All scheduled payments for this project are complete.</p>
                        <?php else: ?>
                            <span class="client-payment-card__label">Next Payment Due</span>
                            <div class="client-payment-card__amount">$<?= number_format((float) $cp['payment']['amount'], 2) ?></div>
                            <p class="client-payment-card__note">
                                Due: <?= htmlspecialchars(date('M j, Y', strtotime($cp['payment']['dueDate']))) ?>
                            </p>
                        <?php endif; ?>
                        <a class="client-payment-card__link" href="<?= url('/payment') ?>">View Payment Details</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    <?php endforeach; ?>

    <a class="client-dash__history-link" href="<?= url('/client-portal/history') ?>">
        View your full feedback and approval history <?= renderIcon('arrow-right') ?>
    </a>

</div>