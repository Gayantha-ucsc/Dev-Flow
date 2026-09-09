<?php
// Expects: $members, $currentProjectName
$projectClients = array_values(array_filter($members, fn($m) => $m['role'] === 'client'));
$clientCount    = count($projectClients);
?>
<div class="team-page team-form-page invite-client-page">

    <div class="team-header">
        <div>
            <a href="<?= url('team') ?>" class="back-link"><?= renderIcon('arrow-left') ?> Back to Team</a>
            <h1>Invite Client</h1>
            <p>Invite client contacts for <?= htmlspecialchars($currentProjectName ?? 'this project') ?>. A project can have one or more clients depending on the case.</p>
        </div>
    </div>

    <?php if ($clientCount > 0): ?>
    <div class="team-banner team-banner--info">
        <?= renderIcon('info') ?>
        <div>
            <strong><?= $clientCount ?> client<?= $clientCount === 1 ? '' : 's' ?> on this project</strong>
            You can invite additional clients anytime — for example a main contact plus stakeholders or approvers from the client side.
        </div>
    </div>
    <?php endif; ?>

    <div class="team-form-grid">
        <div class="card team-form-card team-form-card--compact">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user-plus') ?> Client Invitation</h2>
            </div>
            <form class="team-form-card__body" id="inviteClientForm">
                <div class="form-group">
                    <label for="clientName">Client Name</label>
                    <input type="text" id="clientName" name="name" required placeholder="Full name">
                </div>
                <div class="form-group">
                    <label for="clientEmail">Client Email</label>
                    <input type="email" id="clientEmail" name="email" required placeholder="client@company.com">
                </div>
                <div class="form-group">
                    <label for="clientCompany">Company (optional)</label>
                    <input type="text" id="clientCompany" name="company" placeholder="Company name">
                </div>
                <div class="form-group">
                    <label for="clientMessage">Personal Message (optional)</label>
                    <textarea id="clientMessage" name="message" rows="4" placeholder="Welcome message included in the invitation email..."></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-row">
                        <input type="checkbox" name="grant_chat" checked>
                        <span>Grant access to Client Chat Room upon acceptance</span>
                    </label>
                </div>
                <div class="form-actions">
                    <a href="<?= url('team') ?>" class="btn-sm">Cancel</a>
                    <button type="submit" class="btn-add-member">Send Invitation</button>
                </div>
            </form>
        </div>

        <div class="card team-form-card team-form-card--compact">
            <div class="card__header">
                <h2 class="card__title"><?= renderIcon('user') ?> Project Clients</h2>
                <?php if ($clientCount > 0): ?>
                    <span class="pending-count"><?= $clientCount ?></span>
                <?php endif; ?>
            </div>
            <div class="team-form-card__body client-roster-panel">
                <?php if ($clientCount > 0): ?>
                    <p class="form-hint client-roster-panel__intro">Clients currently on this project.</p>
                    <ul class="client-roster">
                        <?php foreach ($projectClients as $client): ?>
                            <?php
                                $initials = strtoupper(substr($client['name'], 0, 1) . substr(strrchr($client['name'], ' ') ?: '', 1, 1));
                            ?>
                            <li class="client-roster__item">
                                <span class="avatar avatar--<?= avatarColorClass($client['name']) ?>"><?= htmlspecialchars($initials) ?></span>
                                <div class="client-roster__body">
                                    <div class="client-roster__name"><?= htmlspecialchars($client['name']) ?></div>
                                    <div class="client-roster__email"><?= htmlspecialchars($client['email']) ?></div>
                                    <div class="client-roster__meta">
                                        Joined <?= htmlspecialchars(date('M j, Y', strtotime($client['joinedAt']))) ?>
                                    </div>
                                </div>
                                <span class="badge badge--<?= $client['status'] === 'active' ? 'success' : 'neutral' ?>">
                                    <?= ucfirst($client['status']) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="client-info-panel">
                        <p class="client-info-panel__lead">No clients on this project yet. Use the form on the left to send the first invitation.</p>
                        <ul class="client-perks">
                            <li><?= renderIcon('chat') ?> Clients join the dedicated Client Chat Room</li>
                            <li><?= renderIcon('review') ?> Can review milestones and deliverables</li>
                            <li><?= renderIcon('user-check') ?> Multiple clients allowed when the project needs it</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>