<?php
// Expects: $currentProjectName, $summary, $milestones

$plural = fn(int $n, string $word) => $n . ' ' . $word . ($n === 1 ? '' : 's');
?>
<div class="client-pay" id="paymentPage" data-view="client">

    <div class="client-pay__header">
        <h1>Payment schedule</h1>
        <p>Here's what is due for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
    </div>

    <?php if (empty($milestones)): ?>
        <?php
            $variant = 'card';
            $icon    = 'payment';
            $heading = 'No payments scheduled yet';
            $subtext = 'Your project manager will add the payment schedule here once it is agreed.';
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>
    <?php else: ?>

    <div class="card client-pay-summary">
        <div class="client-pay-summary__top">
            <div>
                <span class="client-pay-summary__label">Total paid</span>
                <div class="client-pay-summary__paid">
                    <strong data-sum="collected"><?= formatMoney($summary['collected']) ?></strong>
                    <span>of <span data-sum="scheduled"><?= formatMoney($summary['scheduled']) ?></span></span>
                </div>
            </div>
            <div class="client-pay-summary__meta">
                <strong data-sum="percentMeta"><?= (int) $summary['percentCollected'] ?>% paid</strong>
                <span data-sum="countMeta"><?= (int) $summary['paidCount'] ?> of <?= $plural($summary['count'], 'instalment') ?> paid</span>
            </div>
        </div>
        <div class="progress-bar client-pay-summary__bar">
            <div class="progress-bar__fill" data-sum="collectedBar" style="width: <?= (int) $summary['percentCollected'] ?>%;"></div>
        </div>
        <div class="client-pay-tiles">
            <div class="client-pay-tile">
                <span class="client-pay-tile__label">Paid</span>
                <strong data-sum="tilePaid"><?= formatMoney($summary['collected']) ?></strong>
            </div>
            <div class="client-pay-tile client-pay-tile--due">
                <span class="client-pay-tile__label">Payment due</span>
                <strong data-sum="tileDue"><?= formatMoney($summary['outstanding']) ?></strong>
            </div>
            <div class="client-pay-tile">
                <span class="client-pay-tile__label">Upcoming</span>
                <strong data-sum="tileUpcoming"><?= formatMoney($summary['notRequested']) ?></strong>
            </div>
        </div>
    </div>

    <div class="client-pay-list__head">
        <h2>Milestone schedule</h2>
        <span><?= $plural($summary['count'], 'planned payment') ?></span>
    </div>

    <?php foreach ($milestones as $i => $m): ?>
        <?php
            $stage  = $m['stage'] ?? '';
            $amount = formatMoney($m['amount']);
        ?>
        <article class="card client-pay-card client-pay-card--<?= htmlspecialchars($m['status']) ?>"
                 data-status="<?= htmlspecialchars($m['status']) ?>"
                 data-amount="<?= htmlspecialchars((string) $m['amount']) ?>"
                 data-description="<?= htmlspecialchars($m['description']) ?>">
            <div class="client-pay-card__main">
                <div class="client-pay-card__top">
                    <div class="client-pay-card__tags">
                        <?php if ($stage !== ''): ?>
                            <span class="badge badge--neutral"><?= htmlspecialchars($stage) ?></span>
                        <?php endif; ?>
                        <span class="client-pay-card__number">Milestone #<?= $i + 1 ?></span>
                    </div>
                    <span class="badge badge--<?= $m['statusTone'] ?>" data-slot="badge"><?= htmlspecialchars($m['statusLabel']) ?></span>
                </div>

                <div class="client-pay-card__row">
                    <div>
                        <h3><?= htmlspecialchars($m['description']) ?></h3>
                        <div class="client-pay-card__due" data-slot="due">
                            <?= renderIcon('calendar') ?>
                            <?php if ($m['status'] === 'paid'): ?>
                                <span>Was due <?= date('M j, Y', strtotime($m['dueDate'])) ?></span>
                            <?php else: ?>
                                <span>Due <?= date('M j, Y', strtotime($m['dueDate'])) ?></span>
                                <?php if ($m['due']['label'] !== ''): ?>
                                    <span class="payment-due__sub payment-due__sub--<?= $m['due']['tone'] ?>"><?= htmlspecialchars($m['due']['label']) ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="client-pay-card__amount"><?= $amount ?></div>
                </div>

                <?php if ($m['status'] === 'requested'): ?>
                    <div data-slot="actions">
                        <?php if ($m['hasFailedAttempt']): ?>
                            <div class="client-pay-card__notice">
                                <?= renderIcon('circle-alert') ?>
                                <span>Your last payment attempt was not completed. You can try again.</span>
                            </div>
                        <?php endif; ?>
                        <button type="button" class="btn-sm btn-sm--primary client-pay-card__pay" data-action="pay">
                            <?= renderIcon('lock') ?> <?= $m['hasFailedAttempt'] ? 'Try again' : 'Pay now' ?> (<?= $amount ?>)
                        </button>
                    </div>
                <?php else: ?>
                    <div data-slot="actions"></div>
                <?php endif; ?>
            </div>

            <div data-slot="footer">
                <?php if ($m['status'] === 'paid'): ?>
                    <div class="client-pay-card__footer client-pay-card__footer--paid">
                        <?= renderIcon('circle-check') ?>
                        <span>Paid on <?= date('M j, Y', strtotime($m['payment']['paidAt'])) ?> (Ref: <?= htmlspecialchars($m['payment']['reference']) ?>)</span>
                    </div>
                <?php elseif ($m['status'] === 'pending'): ?>
                    <div class="client-pay-card__footer">
                        <?= renderIcon('clock') ?>
                        <span>Not requested yet. Your project manager will request this payment when it is due.</span>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php endif; ?>

    <div class="card client-pay-help">
        <span class="client-pay-help__icon"><?= renderIcon('chat') ?></span>
        <div class="client-pay-help__body">
            <strong>Questions about a payment?</strong>
            <p>Message your project team in the project chat.</p>
        </div>
        <a href="<?= url('chat') ?>" class="btn-outline">Open chat</a>
    </div>

    <p class="client-pay-secure"><?= renderIcon('lock') ?> Payments are processed securely through our payment gateway. DevFlow never stores your card details.</p>
</div>

<script>
    window.PAYMENT_ICONS = {
        circleCheck: <?= iconJson('circle-check') ?>
    };
</script>

<div class="modal-overlay" data-modal="payment-pay" hidden>
    <div class="modal-backdrop" data-modal-close></div>
    <div class="modal-box modal-box--form">
        <div class="modal-box__header">
            <h2>Continue to payment</h2>
            <button type="button" class="icon-btn" data-modal-close aria-label="Close"><?= renderIcon('x') ?></button>
        </div>
        <div class="modal-box__body">
            <p class="modal-subtext">You will be taken to our secure payment gateway to pay <strong id="payAmount"></strong> for <strong id="payDescription"></strong>. You will return here once the payment is complete.</p>
            <div class="modal-box__footer">
                <button type="button" class="btn-sm" data-modal-close>Cancel</button>
                <button type="button" class="btn-sm btn-sm--primary" id="confirmPay">Continue to payment</button>
            </div>
        </div>
    </div>
</div>