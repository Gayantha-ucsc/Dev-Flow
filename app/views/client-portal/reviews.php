<?php
// Expects: $project, $needsReview, $hero, $historyItems
$reviewCount = count($needsReview);
$showFinalDelivery = ($hero['type'] ?? null) === 'final_delivery_ready';
$defaultTab = ($reviewCount > 0 || $showFinalDelivery) ? 'pending' : 'pending';
?>
<div class="review-page review-page--client">

    <div class="review-header">
        <div>
            <h1>Approvals</h1>
            <p>Review deliverables from the team and share your decision for <?= htmlspecialchars($project['name']) ?>.</p>
        </div>
    </div>

    <div class="review-tabs" role="tablist">
        <button type="button" class="review-tab is-active" data-tab="pending" role="tab">
            Needs your review <span class="review-tab__count"><?= $reviewCount + ($showFinalDelivery ? 1 : 0) ?></span>
        </button>
        <button type="button" class="review-tab" data-tab="history" role="tab">
            History
        </button>
    </div>

    <div class="review-panel" data-panel="pending">

        <?php if ($showFinalDelivery): ?>
            <div class="card review-item final-delivery-card" data-review-id="final-delivery">
                <div class="final-delivery-card__eyebrow"><?= renderIcon('circle-check') ?> Final delivery ready</div>
                <h2 class="review-item__title"><?= htmlspecialchars($hero['heading']) ?></h2>
                <p class="final-delivery-card__body"><?= htmlspecialchars($hero['body']) ?></p>

                <div class="review-item__actions">
                    <button type="button" class="btn-sm btn-sm--danger-outline js-reject">Reject delivery</button>
                    <button type="button" class="btn-sm btn-sm--primary js-approve">Approve delivery</button>
                </div>

                <div class="review-item__feedback" hidden>
                    <label class="review-item__feedback-label">Feedback (required)</label>
                    <p class="review-item__feedback-hint">Let the team know what needs to change before final delivery can be approved.</p>
                    <textarea class="review-item__feedback-input" rows="3" placeholder="Add your feedback..."></textarea>
                    <p class="review-item__feedback-error" hidden>Feedback is required to reject final delivery.</p>
                    <div class="review-item__feedback-actions">
                        <button type="button" class="btn-sm js-cancel-feedback">Cancel</button>
                        <button type="button" class="btn-sm btn-sm--danger-outline js-confirm-feedback">Confirm</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($needsReview)): ?>
            <?php if (!$showFinalDelivery): ?>
                <?php
                    $variant = 'card';
                    $icon    = 'circle-check';
                    $heading = "You're all caught up";
                    $subtext = 'Nothing needs your review right now. New deliverables from the team will show up here.';
                ?>
                <?php include __DIR__ . '/../partials/empty-state.php'; ?>
            <?php endif; ?>
        <?php else: ?>
            <div class="review-list" id="reviewList">
                <?php foreach ($needsReview as $index => $item): ?>
                    <div class="card review-item" data-review-id="<?= (int) $index ?>">
                        <div class="review-item__top">
                            <div class="review-item__title-line">
                                <h2 class="review-item__title"><?= htmlspecialchars($item['title']) ?></h2>
                                <span class="badge badge--neutral badge--outline"><?= htmlspecialchars(ucfirst($item['type'])) ?></span>
                            </div>
                            <p class="review-item__meta"><?= htmlspecialchars($item['meta']) ?></p>
                        </div>

                        <p class="review-item__note review-item__note--plain"><?= htmlspecialchars($item['description']) ?></p>

                        <div class="review-item__actions">
                            <button type="button" class="btn-sm btn-sm--danger-outline js-reject">Reject</button>
                            <button type="button" class="btn-sm btn-sm--primary js-approve">Approve</button>
                        </div>

                        <div class="review-item__feedback" hidden>
                            <label class="review-item__feedback-label">Feedback (required)</label>
                            <p class="review-item__feedback-hint">Let the team know what needs to change before resubmission.</p>
                            <textarea class="review-item__feedback-input" rows="3" placeholder="Add your feedback..."></textarea>
                            <p class="review-item__feedback-error" hidden>Feedback is required to reject.</p>
                            <div class="review-item__feedback-actions">
                                <button type="button" class="btn-sm js-cancel-feedback">Cancel</button>
                                <button type="button" class="btn-sm btn-sm--danger-outline js-confirm-feedback">Confirm</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="review-panel" data-panel="history" hidden>
        <div class="card review-history-card">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('scroll-text') ?> Your decisions</h2>
                <span class="review-history-card__subtitle">A permanent record of everything you've approved or rejected.</span>
            </div>

            <?php if (empty($historyItems)): ?>
                <?php
                    $variant = 'card';
                    $icon    = 'scroll-text';
                    $heading = 'No decisions yet';
                    $subtext = 'Approvals and rejections you make will be recorded here permanently.';
                ?>
                <?php include __DIR__ . '/../partials/empty-state.php'; ?>
            <?php else: ?>
                <table class="review-history-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Decision</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyItems as $row): ?>
                            <?php $meta = approvalDecisionMeta($row['decision']); ?>
                            <tr>
                                <td><?= htmlspecialchars($row['task']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><span class="badge badge--<?= $meta['tone'] ?>"><?= htmlspecialchars($meta['label']) ?></span></td>
                                <td><?= htmlspecialchars(date('M j, Y', strtotime($row['date']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>