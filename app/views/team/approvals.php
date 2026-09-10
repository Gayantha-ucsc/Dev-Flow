<?php
// Expects: $pendingApprovals, $hasTeamLead, $activeRole, $currentProjectName
?>
<div class="team-page">

    <div class="team-header">
        <div>
            <a href="<?= url('team') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to Team</a>
            <h1>Member Approval Queue</h1>
            <p>Pending member requests for <?= htmlspecialchars($currentProjectName ?? 'this project') ?> awaiting qualifying approver action.</p>
        </div>
    </div>

    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            <strong>Cross-approval rules</strong>
            When a Manager adds a member, the Team Lead must approve.
            When a Team Lead adds a member, the Manager must approve.
            <?php if (!$hasTeamLead): ?>
                First Team Lead appointments bypass this queue.
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($pendingApprovals)): ?>
        <?php
            $variant  = 'card';
            $icon     = 'circle-check';
            $heading  = 'No pending approvals';
            $subtext  = 'All member requests have been processed. New requests will appear here for the qualifying approver.';
            $ctaText  = 'Back to Team';
            $ctaHref  = 'team';
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>
    <?php else: ?>

        <div class="card team-approvals-card">
            <div class="card__header">
                <h2 class="card__title">
                    <?= renderIcon('user-check') ?> Pending Requests
                    <span class="pending-count"><?= count($pendingApprovals) ?></span>
                </h2>
                <?php if (in_array($activeRole, ['manager', 'team_lead'], true)): ?>
                    <span class="badge badge--primary">You are a qualifying approver</span>
                <?php endif; ?>
            </div>

            <div class="approval-list">
                <?php foreach ($pendingApprovals as $req): ?>
                    <?php
                        $reqRole  = str_contains($req['requestedBy'], 'Manager') ? 'manager' : 'team_lead';
                        $approver = qualifyingApproverLabel($reqRole);
                        $canApprove = ($reqRole === 'manager' && $activeRole === 'team_lead')
                            || ($reqRole === 'team_lead' && $activeRole === 'manager');
                    ?>
                    <div class="approval-item" data-request-id="<?= (int) $req['user_id'] ?>">
                        <span class="avatar avatar--<?= avatarColorClass($req['name']) ?>">
                            <?= htmlspecialchars(strtoupper(substr($req['name'], 0, 1) . substr(strrchr($req['name'], ' ') ?: '', 1, 1))) ?>
                        </span>
                        <div class="approval-item__body">
                            <div class="approval-item__top">
                                <div class="approval-item__name"><?= htmlspecialchars($req['name']) ?></div>
                                <span class="badge badge--warning">Pending</span>
                            </div>
                            <div class="approval-item__email"><?= htmlspecialchars($req['email']) ?></div>
                            <div class="approval-item__meta">
                                <span>Requested Role: <span class="badge badge--<?= memberRoleTone($req['requestedRole']) ?>"><?= memberRoleLabel($req['requestedRole']) ?></span></span>
                                <span>Added by <?= htmlspecialchars($req['requestedBy']) ?></span>
                                <span>Requested <?= htmlspecialchars(date('M j, Y', strtotime($req['requestedAt']))) ?></span>
                            </div>
                            <div class="approval-item__approver">
                                <?= renderIcon('user-check') ?>
                                Awaiting approval from: <strong><?= htmlspecialchars($approver) ?></strong>
                                <?php if (!$canApprove): ?>
                                    <span class="approval-item__note">(not your action)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="approval-item__actions">
                            <?php if ($canApprove): ?>
                                <button type="button" class="btn-sm btn-sm--text-danger js-reject-request">Reject</button>
                                <button type="button" class="btn-sm btn-sm--primary js-approve-request">Approve</button>
                            <?php else: ?>
                                <span class="approval-item__waiting">Waiting for <?= htmlspecialchars($approver) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php endif; ?>
</div>