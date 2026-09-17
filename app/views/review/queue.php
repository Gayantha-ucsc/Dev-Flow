<?php
// Expects: $pendingItems, $historyItems, $gates, $canManualReview, $canJointApprove
$pendingCount = count($pendingItems);
$gatesCount   = count($gates);

// Which tab opens by default: prefer whichever queue actually has work in it.
if ($canManualReview && $pendingCount > 0) {
    $defaultTab = 'pending';
} elseif ($canJointApprove && $gatesCount > 0) {
    $defaultTab = 'approvals';
} elseif ($canManualReview) {
    $defaultTab = 'pending';
} else {
    $defaultTab = 'approvals';
}
?>

<div class="review-page">

    <div class="review-header">
        <div>
            <h1>Review Queue</h1>
            <p><?= htmlspecialchars($subtitle) ?></p>
        </div>
    </div>

    <div class="review-tabs" role="tablist">
        <?php if ($canManualReview): ?>
            <button type="button" class="review-tab<?= $defaultTab === 'pending' ? ' is-active' : '' ?>" data-tab="pending" role="tab">
                Pending <span class="review-tab__count"><?= $pendingCount ?></span>
            </button>
        <?php endif; ?>

        <?php if ($canJointApprove): ?>
            <button type="button" class="review-tab<?= $defaultTab === 'approvals' ? ' is-active' : '' ?>" data-tab="approvals" role="tab">
                Approvals <span class="review-tab__count"><?= $gatesCount ?></span>
            </button>
        <?php endif; ?>

        <?php if ($canManualReview): ?>
            <button type="button" class="review-tab" data-tab="history" role="tab">
                History
            </button>
        <?php endif; ?>
    </div>

    <?php if ($canManualReview): ?>
    <div class="review-panel" data-panel="pending" <?= $defaultTab === 'pending' ? '' : 'hidden' ?>>
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
    <?php endif; ?>

    <?php if ($canJointApprove): ?>
    <div class="review-panel" data-panel="approvals" <?= $defaultTab === 'approvals' ? '' : 'hidden' ?>>
        <p class="review-panel__intro">These require sign-off from both the Manager and Team Lead before moving forward.</p>

        <?php if (empty($gates)): ?>
            <?php
                $variant  = 'card';
                $icon     = 'circle-check';
                $heading  = 'No approval gates open';
                $subtext  = 'Stage completions and final deliveries awaiting joint sign-off will appear here.';
            ?>
            <?php include __DIR__ . '/../partials/empty-state.php'; ?>
        <?php else: ?>
            <div class="review-list" id="gateList">
                <?php foreach ($gates as $gate): ?>
                    <div class="card approval-gate" data-gate-id="<?= (int) $gate['id'] ?>" data-gate-type="<?= htmlspecialchars($gate['type']) ?>">
                        <p class="approval-gate__eyebrow"><?= htmlspecialchars(strtoupper($gate['eyebrow'])) ?></p>
                        <h2 class="approval-gate__title"><?= htmlspecialchars($gate['title']) ?></h2>
                        <p class="approval-gate__context"><?= htmlspecialchars($gate['context']) ?></p>

                        <div class="approval-gate__parties">
                            <?php foreach (['manager' => 'Manager', 'teamLead' => 'Team Lead'] as $key => $label): ?>
                                <?php $party = $gate[$key]; $meta = approvalDecisionMeta($party['decision']); ?>
                                <div class="approval-party">
                                    <div class="approval-party__who">
                                        <span class="avatar avatar--<?= avatarColorClass($party['name']) ?>">
                                            <?= htmlspecialchars(initials($party['name'])) ?>
                                        </span>
                                        <div>
                                            <p class="approval-party__name"><?= htmlspecialchars($label) ?> &mdash; <?= htmlspecialchars($party['name']) ?></p>
                                            <p class="approval-party__status-line">
                                                <?php if ($party['decision'] === 'approved'): ?>
                                                    Decided <?= htmlspecialchars($party['decidedAgo']) ?>
                                                <?php elseif ($party['isActionable']): ?>
                                                    <span class="approval-party__status-line--pending">Your review is pending</span>
                                                <?php else: ?>
                                                    Awaiting review
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <?php if ($party['isActionable']): ?>
                                        <div class="approval-party__actions">
                                            <button type="button" class="btn-sm js-gate-reject" data-party="<?= $key ?>">Reject</button>
                                            <button type="button" class="btn-sm btn-sm--primary js-gate-approve" data-party="<?= $key ?>">Approve</button>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge--<?= $meta['tone'] ?> approval-party__badge">
                                            <?php if ($party['decision'] === 'approved'): ?><?= renderIcon('circle-check') ?><?php endif; ?>
                                            <?php if ($party['decision'] === 'pending'): ?><?= renderIcon('clock') ?><?php endif; ?>
                                            <?= htmlspecialchars($meta['label']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="approval-gate__progress">
                            <div class="approval-gate__progress-label">
                                <span class="js-progress-text"><?= (int) $gate['approvedCount'] ?> of 2 approvals received</span>
                                <span class="js-progress-percent"><?= (int) $gate['percent'] ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar__fill js-progress-fill" style="width: <?= (int) $gate['percent'] ?>%"></div>
                            </div>
                            <?php if ($gate['type'] === 'stage'): ?>
                                <p class="approval-gate__progress-note">Stage will automatically advance to Completed once both approvals are recorded.</p>
                            <?php endif; ?>
                        </div>

                        <?php if ($gate['type'] === 'project'): ?>
                            <div class="approval-gate__notice">
                                <?= renderIcon('clock') ?>
                                <div>
                                    <p class="approval-gate__notice-title">Client approval is tracked separately and required in addition to this.</p>
                                    <p class="approval-gate__notice-body">Final delivery must also pass external Client Portal review before project closure.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($canManualReview): ?>
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
    <?php endif; ?>

    <p class="review-footnote">Review decisions trigger immediate notifications to task assignees.</p>
</div>