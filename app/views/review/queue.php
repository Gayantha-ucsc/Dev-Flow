<?php
// Expects: $pendingItems, $historyItems, $currentProjectName
$pendingCount = count($pendingItems);
?>
<div class="review-page">

    <div class="review-header">
        <div>
            <h1>Review &amp; approval queue</h1>
            <p>Tasks submitted for your review.</p>
        </div>
    </div>

    <div class="review-tabs" role="tablist">
        <button type="button" class="review-tab is-active" data-tab="pending" role="tab">
            Pending review <span class="review-tab__count"><?= $pendingCount ?></span>
        </button>
        <button type="button" class="review-tab" data-tab="history" role="tab">
            Reviewed history
        </button>
    </div>

    <div class="review-panel" data-panel="pending">
        <?php if (empty($pendingItems)): ?>
            <?php
                $variant  = 'card';
                $icon     = 'circle-check';
                $heading  = 'Nothing waiting on you';
                $subtext  = 'Every submitted task has been reviewed. New submissions from your team will show up here.';
            ?>
            <?php include __DIR__ . '/../partials/empty-state.php'; ?>
        <?php else: ?>
            <div class="review-list" id="reviewList">
                <?php foreach ($pendingItems as $item): ?>
                    <div class="card review-item" data-review-id="<?= (int) $item['id'] ?>">
                        <div class="review-item__top">
                            <div class="review-item__title-line">
                                <h2 class="review-item__title"><?= htmlspecialchars($item['title']) ?></h2>
                                <span class="badge badge--neutral badge--outline">Stage: <?= htmlspecialchars($item['stage']) ?></span>
                                <?php if ($item['revisionRound'] > 1): ?>
                                    <span class="badge badge--warning">Revision round <?= (int) $item['revisionRound'] ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="review-item__submitter">
                                <span class="avatar avatar--<?= avatarColorClass($item['submitter']['name']) ?>">
                                    <?= htmlspecialchars(initials($item['submitter']['name'])) ?>
                                </span>
                                <span class="review-item__submitter-name"><?= htmlspecialchars($item['submitter']['name']) ?></span>
                                <span class="review-item__submitter-role">(<?= htmlspecialchars($item['submitter']['role']) ?>)</span>
                                <span class="review-item__dot">&bull;</span>
                                <span class="review-item__time">Submitted <?= htmlspecialchars(timeAgo($item['submittedAt'])) ?></span>
                            </div>
                        </div>

                        <blockquote class="review-item__note">&ldquo;<?= htmlspecialchars($item['note']) ?>&rdquo;</blockquote>

                        <div class="review-item__actions">
                            <button type="button" class="btn-sm js-request-changes">Request changes</button>
                            <button type="button" class="btn-sm btn-sm--danger-outline js-reject">Reject</button>
                            <button type="button" class="btn-sm btn-sm--primary js-approve">Approve</button>
                        </div>

                        <div class="review-item__feedback" hidden>
                            <label class="review-item__feedback-label">Feedback (required)</label>
                            <p class="review-item__feedback-hint">Explain what needs to change before resubmission.</p>
                            <textarea class="review-item__feedback-input" rows="3" placeholder="Add feedback for the submitter..."></textarea>
                            <p class="review-item__feedback-error" hidden>Feedback is required to reject or request changes.</p>
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
                <h2 class="card__title"><?= renderIcon('scroll-text') ?> Revision history</h2>
                <span class="review-history-card__subtitle">Past review decisions across project tasks.</span>
            </div>

            <?php if (empty($historyItems)): ?>
                <?php
                    $variant = 'card';
                    $icon    = 'scroll-text';
                    $heading = 'No review history yet';
                    $subtext = 'Decisions you make on submitted tasks will be recorded here permanently.';
                ?>
                <?php include __DIR__ . '/../partials/empty-state.php'; ?>
            <?php else: ?>
                <table class="review-history-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Round</th>
                            <th>Decision</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyItems as $row): ?>
                            <?php $meta = approvalDecisionMeta($row['decision']); ?>
                            <tr>
                                <td><?= htmlspecialchars($row['task']) ?></td>
                                <td>Round <?= (int) $row['round'] ?></td>
                                <td><span class="badge badge--<?= $meta['tone'] ?>"><?= htmlspecialchars($meta['label']) ?></span></td>
                                <td><?= htmlspecialchars(date('M j, Y', strtotime($row['date']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <p class="review-footnote">Review decisions trigger immediate notifications to task assignees.</p>
</div>