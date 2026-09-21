<?php
// Expects: $currentProjectName, $ledger, $counts
?>
<div class="payment-page" id="paymentPage" data-view="history">

    <div class="payment-header">
        <div>
            <h1>Payment</h1>
            <p>Every milestone and its gateway payment record for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>.</p>
        </div>
    </div>

    <nav class="payment-tabs" aria-label="Payment sections">
        <a href="<?= url('payment') ?>" class="filter-pill">Schedule</a>
        <a href="<?= url('payment/history') ?>" class="filter-pill is-active">History</a>
    </nav>

    <div class="card reports-table-card">
        <div class="reports-toolbar">
            <div class="projects-search">
                <?= renderIcon('search') ?>
                <input type="text" id="paySearch" placeholder="Search by milestone or reference...">
            </div>
            <div class="projects-filters" id="payFilters">
                <button type="button" class="filter-pill is-active" data-filter="all">All (<?= (int) $counts['all'] ?>)</button>
                <button type="button" class="filter-pill" data-filter="completed">Completed (<?= (int) $counts['completed'] ?>)</button>
                <button type="button" class="filter-pill" data-filter="failed">Failed (<?= (int) $counts['failed'] ?>)</button>
                <button type="button" class="filter-pill" data-filter="awaiting">Awaiting (<?= (int) $counts['awaiting'] ?>)</button>
                <button type="button" class="filter-pill" data-filter="not_requested">Not requested (<?= (int) $counts['not_requested'] ?>)</button>
            </div>
        </div>

        <div class="reports-table-scroll">
            <table class="reports-table payment-table" id="paymentTable" data-filter-attr="group">
                <thead>
                    <tr>
                        <th>Milestone</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Gateway Reference</th>
                        <th>Paid On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ledger as $m): ?>
                        <?php
                            $stage     = $m['stage'] ?? '';
                            $reference = $m['payment']['reference'] ?? '';
                            $paidAt    = $m['payment']['paidAt'] ?? null;
                        ?>
                        <tr data-group="<?= htmlspecialchars($m['group']) ?>"
                            data-search="<?= htmlspecialchars(mb_strtolower($m['description'] . ' ' . $stage . ' ' . $reference)) ?>">
                            <td>
                                <div class="payment-desc"><?= htmlspecialchars($m['description']) ?></div>
                                <?php if ($stage !== ''): ?>
                                    <span class="payment-sub"><?= htmlspecialchars($stage) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="payment-amount"><?= formatMoney($m['amount']) ?></td>
                            <td>
                                <span class="badge badge--<?= $m['groupTone'] ?>"><?= htmlspecialchars($m['groupLabel']) ?></span>
                                <?php if ($m['canRetry']): ?>
                                    <span class="payment-sub">Client can retry</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reference !== ''): ?>
                                    <span class="payment-ref"><?= htmlspecialchars($reference) ?></span>
                                <?php else: ?>
                                    <span class="reports-table__muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($paidAt): ?>
                                    <div class="payment-due">
                                        <span><?= date('M j, Y', strtotime($paidAt)) ?></span>
                                        <span class="payment-due__sub"><?= date('g:i A', strtotime($paidAt)) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="reports-table__muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="reports-table__no-results" id="payEmpty" data-empty-text="No payment activity yet." <?= empty($ledger) ? '' : 'hidden' ?>>No payment activity yet.</p>
        </div>

        <p class="payment-note">Card and bank details are handled entirely by the payment gateway. Only the status and gateway reference are kept here.</p>
    </div>
</div>