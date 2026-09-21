<?php
// Expects: $currentProjectName, $canManage, $isClosed, $summary, $milestones, $stageOptions

$plural = fn(int $n, string $word) => $n . ' ' . $word . ($n === 1 ? '' : 's');
?>
<div class="payment-page" id="paymentPage" data-view="schedule" data-can-manage="<?= $canManage ? '1' : '0' ?>">

    <div class="payment-header">
        <div>
            <h1>Payment</h1>
            <p>
                <?= $canManage
                    ? 'Plan instalments and request payments for ' . htmlspecialchars($currentProjectName ?? 'this project') . '.'
                    : 'Instalment schedule and payment status for ' . htmlspecialchars($currentProjectName ?? 'this project') . '.' ?>
            </p>
        </div>
        <?php if ($canManage): ?>
            <div class="payment-header__actions">
                <button type="button" class="btn-sm btn-sm--primary payment-btn" id="addMilestoneBtn">
                    <?= renderIcon('plus') ?> Add Milestone
                </button>
            </div>
        <?php endif; ?>
    </div>

    <nav class="payment-tabs" aria-label="Payment sections">
        <a href="<?= url('payment') ?>" class="filter-pill is-active">Schedule</a>
        <a href="<?= url('payment/history') ?>" class="filter-pill">History</a>
    </nav>

    <?php if (!$canManage): ?>
        <div class="team-banner team-banner--info">
            <?= renderIcon('info') ?>
            <div>
                <?php if ($isClosed): ?>
                    <strong>This project is closed.</strong> The payment schedule is read-only.
                <?php else: ?>
                    <strong>View only.</strong> The Manager sets up the schedule and requests payments.
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="dash-stats payment-stats">
        <div class="card stat-card">
            <div class="stat-card__top">
                <span class="stat-card__label">Total Scheduled</span>
                <span class="stat-card__icon"><?= renderIcon('receipt') ?></span>
            </div>
            <div class="stat-card__value" data-sum="scheduled"><?= formatMoney($summary['scheduled']) ?></div>
            <div class="stat-card__meta" data-sum="scheduledMeta"><?= $plural($summary['count'], 'milestone') ?></div>
        </div>

        <div class="card stat-card">
            <div class="stat-card__top">
                <span class="stat-card__label">Requested</span>
                <span class="stat-card__icon"><?= renderIcon('arrow-up-right') ?></span>
            </div>
            <div class="stat-card__value" data-sum="requested"><?= formatMoney($summary['requested']) ?></div>
            <div class="stat-card__meta" data-sum="requestedMeta"><?= formatMoney($summary['notRequested']) ?> not yet requested</div>
        </div>

        <div class="card stat-card stat-card--success">
            <div class="stat-card__top">
                <span class="stat-card__label">Collected</span>
                <span class="stat-card__icon"><?= renderIcon('circle-check') ?></span>
            </div>
            <div class="stat-card__value" data-sum="collected"><?= formatMoney($summary['collected']) ?></div>
            <div class="stat-card__meta" data-sum="collectedMeta"><?= (int) $summary['percentCollected'] ?>% of scheduled</div>
            <div class="progress-bar">
                <div class="progress-bar__fill" data-sum="collectedBar" style="width: <?= (int) $summary['percentCollected'] ?>%;"></div>
            </div>
        </div>

        <div class="card stat-card stat-card--primary">
            <div class="stat-card__top">
                <span class="stat-card__label">Outstanding</span>
                <span class="stat-card__icon"><?= renderIcon('clock') ?></span>
            </div>
            <div class="stat-card__value" data-sum="outstanding"><?= formatMoney($summary['outstanding']) ?></div>
            <div class="stat-card__meta" data-sum="outstandingMeta">
                <?= $summary['requestedCount'] > 0 ? $summary['requestedCount'] . ' awaiting client payment' : 'Nothing outstanding' ?>
            </div>
        </div>
    </div>

    <div class="card reports-table-card">
        <div class="reports-toolbar">
            <div class="projects-search">
                <?= renderIcon('search') ?>
                <input type="text" id="paySearch" placeholder="Search milestones...">
            </div>
            <div class="projects-filters" id="payFilters">
                <button type="button" class="filter-pill is-active" data-filter="all">All (<span data-count="all"><?= (int) $summary['count'] ?></span>)</button>
                <button type="button" class="filter-pill" data-filter="pending">Pending (<span data-count="pending"><?= (int) $summary['pendingCount'] ?></span>)</button>
                <button type="button" class="filter-pill" data-filter="requested">Requested (<span data-count="requested"><?= (int) $summary['requestedCount'] ?></span>)</button>
                <button type="button" class="filter-pill" data-filter="paid">Paid (<span data-count="paid"><?= (int) $summary['paidCount'] ?></span>)</button>
            </div>
        </div>

        <div class="reports-table-scroll">
            <table class="reports-table payment-table" id="paymentTable" data-filter-attr="status">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Linked Stage</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <?php if ($canManage): ?><th></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody id="paymentBody">
                    <?php foreach ($milestones as $m): ?>
                        <?php $stage = $m['stage'] ?? ''; ?>
                        <tr data-id="<?= (int) $m['id'] ?>"
                            data-status="<?= htmlspecialchars($m['status']) ?>"
                            data-amount="<?= htmlspecialchars((string) $m['amount']) ?>"
                            data-stage="<?= htmlspecialchars($stage) ?>"
                            data-due="<?= htmlspecialchars($m['dueDate'] ?? '') ?>"
                            data-description="<?= htmlspecialchars($m['description']) ?>"
                            data-search="<?= htmlspecialchars(mb_strtolower($m['description'] . ' ' . $stage)) ?>">
                            <td class="payment-desc"><?= htmlspecialchars($m['description']) ?></td>
                            <td>
                                <?php if ($stage !== ''): ?>
                                    <span class="badge badge--neutral"><?= htmlspecialchars($stage) ?></span>
                                <?php else: ?>
                                    <span class="reports-table__muted">Not linked</span>
                                <?php endif; ?>
                            </td>
                            <td class="payment-amount"><?= formatMoney($m['amount']) ?></td>
                            <td>
                                <div class="payment-due">
                                    <span><?= !empty($m['dueDate']) ? date('M j, Y', strtotime($m['dueDate'])) : 'Not set' ?></span>
                                    <?php if ($m['due']['label'] !== ''): ?>
                                        <span class="payment-due__sub payment-due__sub--<?= $m['due']['tone'] ?>"><?= htmlspecialchars($m['due']['label']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><span class="badge badge--<?= $m['statusTone'] ?>"><?= htmlspecialchars($m['statusLabel']) ?></span></td>
                            <?php if ($canManage): ?>
                                <td>
                                    <?php if ($m['status'] === 'pending'): ?>
                                        <div class="payment-actions">
                                            <button type="button" class="btn-sm btn-sm--primary" data-action="request">Request payment</button>
                                            <button type="button" class="icon-btn-sm" data-action="edit" aria-label="Edit milestone" title="Edit"><?= renderIcon('pencil') ?></button>
                                            <button type="button" class="icon-btn-sm icon-btn-sm--danger" data-action="delete" aria-label="Remove milestone" title="Remove"><?= renderIcon('trash-2') ?></button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="reports-table__no-results" id="payEmpty" data-empty-text="No milestones scheduled yet." <?= empty($milestones) ? '' : 'hidden' ?>>No milestones scheduled yet.</p>
        </div>

        <?php if ($canManage): ?>
            <p class="payment-note">Milestones can be edited or removed only while pending. Requesting a payment notifies the client right away.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($canManage): ?>
<script>
    window.PAYMENT_ICONS = {
        edit: <?= iconJson('pencil') ?>,
        remove: <?= iconJson('trash-2') ?>
    };
</script>

<div class="modal-overlay" data-modal="payment-milestone" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form payment-modal">
        <div class="modal-box__header">
            <h2 id="milestoneModalTitle">Add Milestone</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <form class="modal-box__body" id="milestoneForm" novalidate>
            <div class="form-group">
                <label for="msDescription">Description</label>
                <input type="text" id="msDescription" maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="msStage">Linked stage <span class="payment-optional">(optional)</span></label>
                <select id="msStage">
                    <option value="">No linked stage</option>
                    <?php foreach ($stageOptions as $stageName): ?>
                        <option value="<?= htmlspecialchars($stageName) ?>"><?= htmlspecialchars($stageName) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="payment-form-row">
                <div class="form-group">
                    <label for="msAmount">Amount ($)</label>
                    <input type="number" id="msAmount" min="0.01" max="9999999999.99" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="msDue">Due date</label>
                    <input type="date" id="msDue" required>
                </div>
            </div>
            <p class="payment-form-error" id="milestoneError" hidden></p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="submit" class="btn-sm btn-sm--primary" id="milestoneSubmit">Add Milestone</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" data-modal="payment-request" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Request Payment</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">Request <strong id="requestAmount"></strong> for <strong id="requestDescription"></strong>? The client is notified right away and the milestone can no longer be edited or removed.</p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="button" class="btn-sm btn-sm--primary" id="confirmRequest">Send Request</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" data-modal="payment-delete" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Remove Milestone</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">Remove <strong id="deleteDescription"></strong> from the payment schedule? This cannot be undone.</p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="button" class="btn-sm btn-sm--danger-outline" id="confirmDelete">Remove</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>