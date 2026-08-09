<?php
// Expects: $members, $pendingApprovals, $currentProjectName
$soloNoRequests = count($members) <= 1 && empty($pendingApprovals);
?>
<div class="team-page">

    <div class="team-header">
        <div>
            <h1>Team Members</h1>
            <p>Manage who has access to <?= htmlspecialchars($currentProjectName ?? 'this project') ?> and their roles.</p>
        </div>
        <div class="team-header__actions">
            <a href="#" class="btn-outline">Invite Client</a>
            <a href="#" class="btn-add-member"><?= renderIcon('user-plus') ?> Add Member</a>
        </div>
    </div>

    <?php if ($soloNoRequests): ?>

        <?php
            $variant  = 'card';
            $icon     = 'user-plus';
            $heading  = 'Build your team';
            $subtext  = 'Invite your first collaborator to start delivering together. Set roles, assign tasks, and watch the project grow.';
            $ctaText  = 'Invite Member';
            $ctaHref  = '#';
        ?>
        <?php include __DIR__ . '/../partials/empty-state.php'; ?>

    <?php else: ?>

        <?php if (!empty($pendingApprovals)): ?>
        <div class="card team-pending">
            <div class="card__header">
                <h2 class="card__title">
                    <?= renderIcon('user-check') ?> Pending Approvals
                    <span class="pending-count"><?= count($pendingApprovals) ?></span>
                </h2>
            </div>
            <div class="pending-list">
                <?php foreach ($pendingApprovals as $req): ?>
                    <div class="pending-item">
                        <span class="avatar avatar--<?= avatarColorClass($req['name']) ?>">
                            <?= htmlspecialchars(strtoupper(substr($req['name'], 0, 1) . substr(strrchr($req['name'], ' ') ?: '', 1, 1))) ?>
                        </span>
                        <div class="pending-item__body">
                            <div class="pending-item__name"><?= htmlspecialchars($req['name']) ?></div>
                            <div class="pending-item__email"><?= htmlspecialchars($req['email']) ?></div>
                            <div class="pending-item__meta">
                                Requested Role:
                                <span class="badge badge--<?= memberRoleTone($req['requestedRole']) ?>"><?= memberRoleLabel($req['requestedRole']) ?></span>
                                &nbsp;&bull;&nbsp; Added by <?= htmlspecialchars($req['requestedBy']) ?>
                            </div>
                        </div>
                        <div class="pending-item__actions">
                            <button type="button" class="btn-sm btn-sm--text-danger">Reject</button>
                            <button type="button" class="btn-sm btn-sm--primary">Approve</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card team-table-card">
            <?php if (count($members) > 1): ?>
            <div class="team-toolbar">
                <div class="projects-search">
                    <?= renderIcon('search') ?>
                    <input type="text" id="memberSearch" placeholder="Search by name or email...">
                </div>
                <div class="projects-filters" id="roleFilters">
                    <button type="button" class="filter-pill is-active" data-filter="all">All Roles</button>
                    <button type="button" class="filter-pill" data-filter="manager">Manager</button>
                    <button type="button" class="filter-pill" data-filter="team_lead">Team Lead</button>
                    <button type="button" class="filter-pill" data-filter="developer">Developer</button>
                    <button type="button" class="filter-pill" data-filter="designer">Designer</button>
                    <button type="button" class="filter-pill" data-filter="client">Client</button>
                </div>
                <div class="projects-sort">
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter">
                        <option value="all">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="team-table-scroll">
                <table class="team-table" id="memberTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <?php
                                $initials = strtoupper(substr($member['name'], 0, 1) . substr(strrchr($member['name'], ' ') ?: '', 1, 1));
                            ?>
                            <tr data-name="<?= htmlspecialchars(strtolower($member['name'] . ' ' . $member['email'])) ?>"
                                data-role="<?= htmlspecialchars($member['role']) ?>"
                                data-status="<?= htmlspecialchars($member['status']) ?>">
                                <td>
                                    <div class="member-cell">
                                        <span class="avatar avatar--<?= avatarColorClass($member['name']) ?>"><?= htmlspecialchars($initials) ?></span>
                                        <span class="member-cell__name">
                                            <?= htmlspecialchars($member['name']) ?>
                                            <?php if ($member['user_id'] === $currentUser['user_id']): ?>
                                                <span class="member-cell__you">(You)</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="team-table__muted"><?= htmlspecialchars($member['email']) ?></td>
                                <td>
                                    <span class="badge badge--<?= memberRoleTone($member['role']) ?>"><?= memberRoleLabel($member['role']) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge--<?= $member['status'] === 'active' ? 'success' : 'neutral' ?>">
                                        <?= ucfirst($member['status']) ?>
                                    </span>
                                </td>
                                <td class="team-table__muted"><?= htmlspecialchars(date('M j, Y', strtotime($member['joinedAt']))) ?></td>
                                <td>
                                    <!-- Role changes/deactivation aren't wired up yet. -->
                                    <button type="button" class="icon-btn" aria-label="Manage <?= htmlspecialchars($member['name']) ?>">
                                        <?= renderIcon('ellipsis-vertical') ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="team-table__no-results" id="memberNoResults" hidden>No members match your search.</p>
            </div>
        </div>

    <?php endif; ?>
</div>